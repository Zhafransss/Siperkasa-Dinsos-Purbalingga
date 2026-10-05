<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyNipRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StatusController extends Controller
{
    private const PER_PAGE = 5;

    public function index(Request $request): View|RedirectResponse
    {
        $employee = Employee::find($request->session()->get('employee_id'));

        if (! $employee) {
            return view('status.index', ['employee' => null]);
        }

        $search = trim($request->validate(['q' => ['nullable', 'string', 'max:100']])['q'] ?? '');

        $bookings = $employee->bookings()
            ->with('vehicle')
            ->matching($search, $employee)
            ->latest()
            ->orderByDesc('id') // stable order for bookings filed in the same second
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        // A stale link such as ?page=9 after bookings were cancelled or the search changed: go back to page one.
        if ($bookings->isEmpty() && $bookings->currentPage() > 1) {
            return redirect()->route('status.index', array_filter(['q' => $search]));
        }

        return view('status.index', [
            'employee' => $employee,
            'bookings' => $bookings,
            'search' => $search,
        ]);
    }

    public function login(VerifyNipRequest $request): RedirectResponse
    {
        $employee = Employee::findActiveByNip($request->validated('nip'));

        if (! $employee) {
            return back()->withInput()->withErrors([
                'nip' => 'NIP tidak terdaftar dalam Database Pegawai Dinas Kesehatan atau status NIP non-aktif. Harap hubungi Pengelola Admin Dinkes.',
            ]);
        }

        $request->session()->put('employee_id', $employee->id);

        return redirect()->route('status.index');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('employee_id');

        return redirect()->route('status.index');
    }
}
