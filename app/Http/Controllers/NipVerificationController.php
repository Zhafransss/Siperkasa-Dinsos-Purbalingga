<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyNipRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class NipVerificationController extends Controller
{
    /** Step 1 of the booking wizard: check the NIP against the active employee directory. */
    public function __invoke(VerifyNipRequest $request): JsonResponse
    {
        $employee = Employee::findActiveByNip($request->validated('nip'));

        if (! $employee) {
            return response()->json([
                'message' => 'NIP tidak terdaftar dalam Database Pegawai Dinas Kesehatan atau status NIP non-aktif. Harap hubungi Pengelola Admin Dinkes.',
            ], 422);
        }

        $request->session()->put('employee_id', $employee->id);

        return response()->json(['name' => $employee->name, 'bidang' => $employee->bidangShort()]);
    }
}
