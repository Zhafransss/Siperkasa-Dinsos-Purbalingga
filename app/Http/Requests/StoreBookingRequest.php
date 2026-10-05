<?php

namespace App\Http\Requests;

use App\Models\Employee;
use App\Services\BookingWindow;
use Closure;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'digits:18'],
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            // Lead time: filed at least one working day before departure (see BookingWindow).
            'departs_on' => ['bail', 'required', 'date_format:Y-m-d', function (string $attribute, mixed $value, Closure $fail) {
                $earliest = BookingWindow::earliestDeparture();

                if (Carbon::parse($value)->startOfDay()->lt($earliest)) {
                    $fail('Pengajuan paling lambat 1 hari kerja sebelum keberangkatan — hari H tidak dapat diajukan. '
                        .'Tanggal berangkat paling cepat: '.$earliest->translatedFormat('l, j F Y').'.');
                }
            }],
            // Whole days: returning on the day of departure (a same-day trip) is fine.
            'returns_on' => ['required', 'date_format:Y-m-d', 'after_or_equal:departs_on'],
            'destination' => ['required', 'string', 'max:255'],
            'purpose' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
            'vehicle_id.required' => 'Kendaraan belum dipilih — pilih dari Katalog Mobil.',
            'vehicle_id.exists' => 'Kendaraan yang dipilih tidak ditemukan.',
            'departs_on.required' => 'Tanggal Keberangkatan wajib diisi.',
            'departs_on.date_format' => 'Tanggal Keberangkatan tidak valid.',
            'returns_on.required' => 'Tanggal Kembali wajib diisi.',
            'returns_on.date_format' => 'Tanggal Kembali tidak valid.',
            'returns_on.after_or_equal' => 'Tanggal Kembali tidak boleh sebelum Tanggal Keberangkatan.',
            'destination.required' => 'Lokasi Tujuan wajib diisi.',
            'purpose.required' => 'Keperluan wajib diisi.',
        ];
    }

    /** The employee must still be an active record when the form is finally submitted. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->has('nip') || ! $this->filled('nip')) {
                    return;
                }
                if (! Employee::findActiveByNip($this->string('nip')->toString())) {
                    $validator->errors()->add('nip', 'NIP tidak terdaftar dalam Database Pegawai Dinas Kesehatan atau status NIP non-aktif. Harap hubungi Pengelola Admin Dinkes.');
                }
            },
        ];
    }
}
