<?php

namespace App\Http\Requests\Admin;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const MAX_ATTEMPTS = 5;

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nip' => ['required', 'digits:18'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'nip.required' => 'NIP wajib diisi.',
            'nip.digits' => 'NIP harus terdiri dari 18 digit angka.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    /**
     * Sign in with NIP + password. Only active accounts can sign in, and a wrong NIP, a wrong password and a
     * deactivated account all give the same message so the form cannot be used to discover which NIPs are admins.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $attempt = Auth::attempt(
            ['nip' => $this->string('nip')->toString(), 'password' => $this->string('password')->toString(), 'is_active' => true],
            $this->boolean('remember'),
        );

        if (! $attempt) {
            RateLimiter::hit($this->throttleKey(), 60);

            throw ValidationException::withMessages(['nip' => 'NIP atau kata sandi salah, atau akun tidak aktif.']);
        }

        RateLimiter::clear($this->throttleKey());
    }

    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'nip' => "Terlalu banyak percobaan masuk. Coba lagi dalam {$seconds} detik.",
        ]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('nip')->toString()).'|'.$this->ip());
    }
}
