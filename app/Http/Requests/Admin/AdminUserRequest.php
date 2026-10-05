<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Create requires a password; on update it is optional (left blank = keep the current one). */
class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->nip)) {
            $this->merge(['nip' => preg_replace('/\s+/', '', $this->nip)]);
        }
    }

    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'nip' => ['required', 'digits:18', Rule::unique('users', 'nip')->ignore($user?->id)],
            'name' => ['required', 'string', 'max:255'],
            'bidang' => ['nullable', 'string', 'max:100'],
            'email' => ['nullable', 'email:rfc', 'max:255', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user ? 'nullable' : 'required', 'string', 'confirmed', Password::min(8)->numbers()->symbols()],
        ];
    }

    public function attributes(): array
    {
        return [
            'nip' => 'NIP (ID Administrator)',
            'name' => 'Nama Lengkap',
            'bidang' => 'Bidang',
            'email' => 'Email',
            'password' => 'Kata sandi',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
            'nip.unique' => 'NIP ini sudah terdaftar sebagai administrator.',
            'email.unique' => 'Email ini sudah dipakai administrator lain.',
            'email.email' => 'Format email tidak valid.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak sama.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'password.numbers' => 'Kata sandi harus mengandung angka.',
            'password.symbols' => 'Kata sandi harus mengandung simbol.',
        ];
    }
}
