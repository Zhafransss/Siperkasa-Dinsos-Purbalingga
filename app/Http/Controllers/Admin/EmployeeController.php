<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\EmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'bidang' => ['nullable', 'string', 'max:100'],
        ]);

        $employees = Employee::query()
            ->search($filters['q'] ?? '')
            ->when($filters['bidang'] ?? null, fn ($q, $bidang) => $q->where('bidang', $bidang))
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('admin.employees.index', [
            'employees' => $employees,
            'filters' => $filters,
            'bidangOptions' => Employee::query()->distinct()->orderBy('bidang')->pluck('bidang'),
            'stats' => [
                'total' => Employee::count(),
                'active' => Employee::where('is_active', true)->count(),
                'inactive' => Employee::where('is_active', false)->count(),
                'units' => Employee::distinct()->count('bidang'),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.employees.form', $this->formData(new Employee(['is_active' => true])));
    }

    public function store(EmployeeRequest $request): RedirectResponse
    {
        Employee::create($request->validated() + ['is_active' => true]);

        return redirect()->route('admin.employees.index')->with('toast', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Employee $employee): View
    {
        return view('admin.employees.form', $this->formData($employee));
    }

    public function update(EmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated() + ['is_active' => $request->boolean('is_active')]);

        return redirect()->route('admin.employees.index')->with('toast', 'Data pegawai berhasil diperbarui.');
    }

    /** Deactivated employees can no longer pass the NIP check on the booking page; their history stays. */
    public function toggle(Employee $employee): RedirectResponse
    {
        $employee->update(['is_active' => ! $employee->is_active]);

        return back()->with('toast', $employee->is_active ? 'Pegawai diaktifkan.' : 'Pegawai dinonaktifkan.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        if ($employee->bookings()->exists()) {
            return back()->with('toast', 'Pegawai memiliki riwayat peminjaman dan tidak dapat dihapus. Nonaktifkan saja bila sudah tidak bertugas.');
        }

        $employee->delete();

        return redirect()->route('admin.employees.index')->with('toast', 'Pegawai berhasil dihapus.');
    }

    private function formData(Employee $employee): array
    {
        return [
            'employee' => $employee,
            // Existing units are suggested in a datalist; a new unit can still be typed.
            'bidangOptions' => Employee::query()->distinct()->orderBy('bidang')->pluck('bidang'),
        ];
    }
}
