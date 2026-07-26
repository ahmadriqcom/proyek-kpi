<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('permissions')->orderBy('id', 'asc')->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $permissions = Permission::all()->groupBy('menu_key');
        return view('users.create', compact('permissions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:50|unique:users,username',
            'nik' => 'required|string|numeric|digits_between:1,10|unique:users,nik',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['super_admin', 'management', 'operator'])],
            'grade_level' => 'required|integer|min:1|max:9',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'nik.required' => 'Nomor Induk Karyawan (NIK) wajib diisi.',
            'nik.numeric' => 'NIK harus berupa karakter angka.',
            'nik.digits_between' => 'NIK maksimal 10 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar untuk pengguna lain.',
            'username.required' => 'Username akun wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan oleh pengguna lain.',
            'email.unique' => 'Email ini sudah terdaftar.',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);

        if ($user->role === 'super_admin') {
            $allPermissionIds = Permission::pluck('id')->toArray();
            $user->permissions()->sync($allPermissionIds);
        } else {
            $user->permissions()->sync($request->input('permissions', []));
        }

        return redirect()->route('users.index')
            ->with('success', "Pengguna baru [{$user->username}] (NIK: {$user->nik}) berhasil dibuat.");
    }

    public function edit(int $id): View
    {
        $user = User::with('permissions')->findOrFail($id);
        $permissions = Permission::all()->groupBy('menu_key');
        $userPermissionIds = $user->permissions->pluck('id')->toArray();

        return view('users.edit', compact('user', 'permissions', 'userPermissionIds'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'nik' => ['required', 'string', 'numeric', 'digits_between:1,10', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['super_admin', 'management', 'operator'])],
            'grade_level' => 'required|integer|min:1|max:9',
            'password' => 'nullable|string|min:6',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ], [
            'nik.required' => 'Nomor Induk Karyawan (NIK) wajib diisi.',
            'nik.numeric' => 'NIK harus berupa karakter angka.',
            'nik.digits_between' => 'NIK maksimal 10 digit angka.',
            'nik.unique' => 'NIK ini sudah terdaftar untuk pengguna lain.',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        if ($user->role === 'super_admin') {
            $allPermissionIds = Permission::pluck('id')->toArray();
            $user->permissions()->sync($allPermissionIds);
        } else {
            $user->permissions()->sync($request->input('permissions', []));
        }

        $user->load('permissions');

        return redirect()->route('users.index')
            ->with('success', "Data pengguna [{$user->username}] (NIK: {$user->nik}) berhasil diperbarui.");
    }

    public function destroy(int $id): RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna berhasil dihapus.');
    }
}
