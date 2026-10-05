<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

function photonResponse(array $features): array
{
    return ['type' => 'FeatureCollection', 'features' => array_map(fn ($p) => ['type' => 'Feature', 'properties' => $p], $features)];
}

beforeEach(fn () => Cache::flush());

it('turns geocoder results into short, de-duplicated suggestions', function () {
    Http::fake(['*' => Http::response(photonResponse([
        ['name' => 'RSUD dr R. Goeteng Taroenadibrata', 'street' => 'Jalan Veteran', 'district' => 'Gunung Sumbul', 'city' => 'Purbalingga', 'state' => 'Jawa Tengah'],
        // Same place twice (the real service does this) must collapse into one suggestion.
        ['name' => 'Puskesmas Kutasari', 'district' => 'Wiranaya', 'city' => 'Kutasari', 'state' => 'Jawa Tengah'],
        ['name' => 'Puskesmas Kutasari', 'district' => 'Wiranaya', 'city' => 'Kutasari', 'state' => 'Jawa Tengah'],
        // A street without a name uses the street as its title; an entry with nothing usable is skipped.
        ['street' => 'Jalan Letjen S Parman', 'city' => 'Purbalingga'],
        ['osm_value' => 'yes'],
    ]))]);

    $places = $this->getJson('/lokasi/cari?q=rsud')->assertOk()->json('places');

    expect($places)->toHaveCount(3)
        ->and($places[0])->toBe([
            'name' => 'RSUD dr R. Goeteng Taroenadibrata',
            'detail' => 'Jalan Veteran, Gunung Sumbul, Purbalingga, Jawa Tengah',
            'value' => 'RSUD dr R. Goeteng Taroenadibrata, Jalan Veteran, Gunung Sumbul',
        ])
        ->and($places[1]['name'])->toBe('Puskesmas Kutasari')
        ->and($places[2]['name'])->toBe('Jalan Letjen S Parman');
});

it('does not repeat the name inside its own detail line', function () {
    Http::fake(['*' => Http::response(photonResponse([
        ['name' => 'Purbalingga', 'city' => 'Purbalingga', 'state' => 'Jawa Tengah'],
    ]))]);

    expect($this->getJson('/lokasi/cari?q=purbalingga')->json('places.0.detail'))->toBe('Jawa Tengah');
});

it('asks the geocoder for Indonesian results biased to Purbalingga and identifies itself', function () {
    Http::fake(['*' => Http::response(photonResponse([]))]);

    $this->getJson('/lokasi/cari?q=puskesmas')->assertOk()->assertJson(['places' => []]);

    Http::assertSent(fn (Request $request) => str_starts_with($request->url(), config('services.geocoder.url'))
        && $request['q'] === 'puskesmas'
        && $request['bbox'] === config('services.geocoder.bbox')
        && (float) $request['lat'] === config('services.geocoder.bias_lat')
        && str_contains($request->header('User-Agent')[0], 'SiPerkasa'));
});

it('does not call the geocoder for queries shorter than three characters', function () {
    Http::fake();

    $this->getJson('/lokasi/cari?q=pu')->assertOk()->assertJson(['places' => []]);

    Http::assertNothingSent();
});

it('caches a query regardless of case and spacing', function () {
    Http::fake(['*' => Http::response(photonResponse([['name' => 'Puskesmas Kutasari']]))]);

    $first = $this->getJson('/lokasi/cari?q=Puskesmas  Kutasari')->json('places');
    $second = $this->getJson('/lokasi/cari?q=puskesmas kutasari')->json('places');

    expect($second)->toBe($first);
    Http::assertSentCount(1);
});

it('returns no suggestions when the geocoder is down, and retries on the next request', function () {
    Http::fakeSequence()
        ->push('upstream error', 500)
        ->push(photonResponse([['name' => 'Puskesmas Kutasari']]));

    $this->getJson('/lokasi/cari?q=kutasari')->assertOk()->assertJson(['places' => []]);
    // The failure was not cached, so the second attempt reaches the geocoder again.
    $this->getJson('/lokasi/cari?q=kutasari')->assertOk()->assertJsonPath('places.0.name', 'Puskesmas Kutasari');

    Http::assertSentCount(2);
});

it('returns no suggestions when the geocoder cannot be reached', function () {
    Http::fake(fn () => throw new ConnectionException('timeout'));

    $this->getJson('/lokasi/cari?q=kutasari')->assertOk()->assertJson(['places' => []]);
});

it('validates the query parameter', function () {
    $this->getJson('/lokasi/cari')->assertStatus(422);
    $this->getJson('/lokasi/cari?q='.str_repeat('a', 101))->assertStatus(422);
});

it('wires the destination field of the booking form to the suggestion endpoint', function () {
    $this->get(route('booking.create'))
        ->assertOk()
        ->assertSee('data-place-search="'.route('places.search').'"', false)
        ->assertSee('Tempat yang tidak muncul tetap dapat ditulis bebas');
});
