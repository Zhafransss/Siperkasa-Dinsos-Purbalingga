<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/** Signs an administrator out as soon as their account is deactivated, even in the middle of a session. */
class EnsureActiveAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && ! $request->user()->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('admin.login')->withErrors(['nip' => 'Akun Anda sudah dinonaktifkan. Hubungi administrator lain.']);
        }

        return $next($request);
    }
}
