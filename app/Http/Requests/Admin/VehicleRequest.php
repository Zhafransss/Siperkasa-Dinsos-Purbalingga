<?php

namespace App\Http\Requests\Admin;

use App\Enums\FuelType;
use App\Enums\PhotoSide;
use App\Enums\VehicleCategory;
use App\Enums\VehicleStatus;
use App\Models\Vehicle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

/** Create and update share the same rules; on update the plate may stay as it is. */
class VehicleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Plates are compared case-insensitively and stored upper-case with single spaces ("r 1234 pa" -> "R 1234 PA").
        if (is_string($this->plate)) {
            $this->merge(['plate' => preg_replace('/\s+/', ' ', mb_strtoupper(trim($this->plate)))]);
        }
    }

    public function rules(): array
    {
        /** @var Vehicle|null $vehicle */
        $vehicle = $this->route('vehicle');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:255'],
            'brand_model' => ['required', 'string', 'max:255'],
            'plate' => ['required', 'string', 'max:20', Rule::unique('vehicles', 'plate')->ignore($vehicle?->id)],
            'year' => ['required', 'integer', 'between:1990,'.(now()->year + 1)],
            'fuel_type' => ['required', Rule::enum(FuelType::class)],
            'category' => ['required', Rule::enum(VehicleCategory::class)],
            'status' => ['required', Rule::enum(VehicleStatus::class)],
            'capacity' => ['nullable', 'integer', 'between:1,60'],
            'odometer_km' => ['required', 'integer', 'between:0,9999999'],
            'color' => ['required', 'string', 'max:50'],
            'last_serviced_on' => ['nullable', 'date_format:Y-m-d', 'before_or_equal:today'],
            'photos' => ['nullable', 'array'],
        ];

        // One optional file per side: JPG/PNG up to 5 MB (the form's own hint).
        foreach (PhotoSide::cases() as $side) {
            $rules['photos.'.$side->value] = ['nullable', File::image()->types(['jpg', 'jpeg', 'png'])->max(5 * 1024)];
        }

        return $rules;
    }

    public function attributes(): array
    {
        $labels = [
            'name' => 'Nama Kendaraan',
            'description' => 'Deskripsi Singkat',
            'brand_model' => 'Merk / Model',
            'plate' => 'Nomor Plat',
            'year' => 'Tahun Kendaraan',
            'fuel_type' => 'Tipe Bahan Bakar',
            'category' => 'Kategori',
            'status' => 'Status',
            'capacity' => 'Kapasitas Penumpang',
            'odometer_km' => 'Odometer',
            'color' => 'Warna Kendaraan',
            'last_serviced_on' => 'Terakhir Servis',
        ];

        foreach (PhotoSide::cases() as $side) {
            $labels['photos.'.$side->value] = 'Foto '.$side->label();
        }

        return $labels;
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'plate.unique' => 'Nomor plat ini sudah terdaftar pada kendaraan lain.',
            'integer' => ':attribute harus berupa angka.',
            'between' => ':attribute di luar rentang yang diperbolehkan.',
            'enum' => ':attribute tidak valid.',
            'last_serviced_on.before_or_equal' => 'Tanggal terakhir servis tidak boleh di masa depan.',
            'date_format' => ':attribute tidak valid.',
            'photos.*.image' => ':attribute harus berupa gambar JPG atau PNG.',
            'photos.*.mimes' => ':attribute harus berformat JPG atau PNG.',
            'photos.*.max' => ':attribute maksimal 5MB.',
            'photos.*.uploaded' => ':attribute gagal diunggah (maksimal 5MB, format JPG/PNG).',
        ];
    }
}
