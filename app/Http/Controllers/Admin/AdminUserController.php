<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** "Manajemen Admin": the only place administrator accounts are created (there is no public registration). */
class AdminUserController extends Controller
{
    private const PER_PAGE = 10;

    public function index(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);

        return view('admin.admins.index', [
            'admins' => User::query()->search($filters['q'] ?? '')->orderBy('name')->paginate(self::PER_PAGE)->withQueryString(),
            'filters' => $filters,
            'stats' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'inactive' => User::where('is_active', false)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        return view('admin.admins.form', ['admin' => new User(['is_active' => true])]);
    }

    public function store(AdminUserRequest $request): RedirectResponse
    {
        User::create($request->validated() + ['is_active' => true]);

        return redirect()->route('admin.admins.index')->with('toast', 'Akun administrator berhasil dibuat.');
    }

    public function edit(User $user): View
    {
        return view('admin.admins.form', ['admin' => $user]);
    }

    public function update(AdminUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('admin.admins.index')->with('toast', 'Data administrator berhasil diperbarui.');
    }

    public function toggle(Request $request, User $user): RedirectResponse
    {
        if ($user->is_active && ($user->is($request->user()) || $user->isLastActiveAdmin())) {
            return back()->with('toast', $user->is($request->user())
                ? 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
                : 'Administrator aktif terakhir tidak dapat dinonaktifkan.');
        }

        $user->update(['is_active' => ! $user->is_active]);

        return back()->with('toast', $user->is_active ? 'Administrator diaktifkan.' : 'Administrator dinonaktifkan.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('toast', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->isLastActiveAdmin()) {
            return back()->with('toast', 'Administrator aktif terakhir tidak dapat dihapus.');
        }

        $user->delete();

        return back()->with('toast', 'Akun administrator dihapus.');
    }
}
