<?php

namespace App\Services;

use App\Enums\PhotoSide;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Keeps the four photo slots of a vehicle (one file per side) on the "vehicle_photos" disk under public/uploads/vehicles.
 * File names are generated; the client's file name is never used.
 */
class VehiclePhotoStore
{
    /** @param  array<string, UploadedFile|null>  $files  keyed by PhotoSide value; empty slots are ignored */
    public function store(Vehicle $vehicle, array $files): void
    {
        foreach (PhotoSide::cases() as $side) {
            $file = $files[$side->value] ?? null;

            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->storeAs((string) $vehicle->id, $side->value.'-'.Str::random(16).'.'.$file->guessExtension(), VehiclePhoto::DISK);

            $existing = $vehicle->photos()->where('side', $side)->first();
            if ($existing) {
                $this->deleteFile($existing->path);
                $existing->update(['path' => $path]);
            } else {
                $vehicle->photos()->create(['side' => $side, 'path' => $path]);
            }
        }
    }

    public function delete(VehiclePhoto $photo): void
    {
        $this->deleteFile($photo->path);
        $photo->delete();
    }

    /** Removes every photo file of a vehicle (rows go away with the vehicle itself). */
    public function deleteAll(Vehicle $vehicle): void
    {
        Storage::disk(VehiclePhoto::DISK)->deleteDirectory((string) $vehicle->id);
    }

    private function deleteFile(string $path): void
    {
        Storage::disk(VehiclePhoto::DISK)->delete($path);
    }
}
