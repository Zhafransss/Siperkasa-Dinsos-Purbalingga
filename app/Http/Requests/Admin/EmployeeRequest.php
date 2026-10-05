<?php

namespace App\Http\Requests\Admin;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // NIP is typed with spaces in official documents ("1990 0101 2015 01 1 001"); store digits only.
        if (is_string($this->nip)) {
            $this->merge(['nip' => preg_replace('/\s+/', '', $this->nip)]);
        }
    }

    public function rules(): array
    {
        /** @var Employee|null $employee */
        $employee = $this->route('employee');

        return [
            'nip' => ['required', 'digits:18', Rule::unique('employees', 'nip')->ignore($employee?->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email:rfc', 'max:255', Rule::unique('employees', 'email')->ignore($employee?->id)],
            'bidang' => ['required', 'string', 'max:100'],
            'jabatan' => ['nullable', 'string', 'max:150'],
            'pangkat' => ['nullable', 'string', 'max:100'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nip' => 'NIP',
            'name' => 'Nama Lengkap',
            'email' => 'Email',
            'bidang' => 'Bidang / Unit Kerja',
            'jabatan' => 'Jabatan',
            'pangkat' => 'Pangkat / Golongan',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
            'nip.unique' => 'NIP ini sudah terdaftar.',
            'email.unique' => 'Email ini sudah dipakai pegawai lain.',
            'email.email' => 'Format email tidak valid.',
        ];
    }
}
