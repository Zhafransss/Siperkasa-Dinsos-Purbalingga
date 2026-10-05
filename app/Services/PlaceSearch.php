<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Place suggestions for free-text destinations, backed by OpenStreetMap data (Photon geocoder).
 *
 * The suggestions are only a convenience: any failure of the upstream server yields an empty list and the user
 * keeps typing the destination by hand.
 */
class PlaceSearch
{
    public const MIN_LENGTH = 3;

    private const RESULT_LIMIT = 6;

    /** @return list<array{name: string, detail: string, value: string}> */
    public function search(string $query): array
    {
        $query = trim(preg_replace('/\s+/', ' ', $query));

        if (mb_strlen($query) < self::MIN_LENGTH) {
            return [];
        }

        $key = 'places:'.sha1(mb_strtolower($query));
        if (($cached = Cache::get($key)) !== null) {
            return $cached;
        }

        $features = $this->fetch($query);
        if ($features === null) {
            return []; // upstream failed: do not cache, so the next keystroke can retry
        }

        $places = $this->format($features);
        Cache::put($key, $places, config('services.geocoder.cache_seconds'));

        return $places;
    }

    /** @return list<array<string, mixed>>|null raw GeoJSON features, or null when the request failed */
    private function fetch(string $query): ?array
    {
        $config = config('services.geocoder');

        try {
            $response = Http::withUserAgent($config['user_agent'])
                ->acceptJson()
                ->timeout($config['timeout'])
                ->get($config['url'], [
                    'q' => $query,
                    // Ask for extra rows: duplicates are removed below.
                    'limit' => self::RESULT_LIMIT * 2,
                    'lat' => $config['bias_lat'],
                    'lon' => $config['bias_lon'],
                    'bbox' => $config['bbox'],
                ])
                ->throw();
        } catch (ConnectionException|RequestException $e) {
            Log::warning('Place search failed', ['query' => $query, 'error' => $e->getMessage()]);

            return null;
        }

        return $response->json('features') ?? [];
    }

    /**
     * @param  list<array<string, mixed>>  $features
     * @return list<array{name: string, detail: string, value: string}>
     */
    private function format(array $features): array
    {
        $places = [];

        foreach ($features as $feature) {
            $props = $feature['properties'] ?? [];

            $street = trim(($props['street'] ?? '').' '.($props['housenumber'] ?? ''));
            $name = trim($props['name'] ?? '') ?: $street;
            if ($name === '') {
                continue;
            }

            // Secondary line: street, district, city/regency, province — without repeating the name or itself.
            $parts = [];
            foreach ([$street, $props['district'] ?? null, $props['city'] ?? $props['county'] ?? $props['locality'] ?? null, $props['state'] ?? null] as $part) {
                $part = trim((string) $part);
                if ($part !== '' && $part !== $name && ! in_array($part, $parts, true)) {
                    $parts[] = $part;
                }
            }

            $detail = implode(', ', $parts);
            $key = mb_strtolower($name.'|'.$detail);

            // Value written into the form: name plus the two most useful qualifiers, to stay short.
            $places[$key] ??= [
                'name' => $name,
                'detail' => $detail,
                'value' => implode(', ', [$name, ...array_slice($parts, 0, 2)]),
            ];

            if (count($places) === self::RESULT_LIMIT) {
                break;
            }
        }

        return array_values($places);
    }
}
