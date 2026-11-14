<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna aplikasi.
     */
    public function index(Request $request)
    {
        $query = User::latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('cari')) {
            $cari = $request->cari;
            $query->where(function ($q) use ($cari) {
                $q->where('name', 'like', "%{$cari}%")
                  ->orWhere('email', 'like', "%{$cari}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();

        $totalUsers = User::count();
        $totalSuperAdmin = User::where('role', 'super_admin')->count();
        $totalAdminKantor = User::where('role', 'admin_kantor')->count();
        $totalSales = User::where('role', 'sales')->count();

        return view('users.index', compact(
            'users',
            'totalUsers',
            'totalSuperAdmin',
            'totalAdminKantor',
            'totalSales'
        ));
    }

    /**
     * Simpan pengguna baru yang didaftarkan oleh Administrator Utama.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => ['required', Rule::in(['super_admin', 'admin_kantor', 'sales'])],
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 6 karakter.',
            'role.required' => 'Pilih peran / role akses.',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'is_active' => true,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Akun ' . $validated['name'] . ' berhasil dibuat dengan hak akses ' . $validated['role'] . '.');
    }

    /**
     * Perbarui data akun staf / pengguna.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role' => ['required', Rule::in(['super_admin', 'admin_kantor', 'sales'])],
            'password' => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
            'password.min' => 'Kata sandi baru minimal 6 karakter.',
        ]);

        $user->name = $validated['name'];
        $user->email = strtolower($validated['email']);
        
        // Administrator tidak boleh menurunkan role dirinya sendiri jika satu-satunya super admin
        if ($user->id !== auth()->id()) {
            $user->role = $validated['role'];
            $user->is_active = $request->has('is_active') ? (bool) $request->is_active : $user->is_active;
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Data akun ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Aktifkan atau Nonaktifkan akun staf.
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusText = $user->is_active ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()
            ->route('users.index')
            ->with('success', 'Status akun ' . $user->name . ' berhasil ' . $statusText . '.');
    }

    /**
     * Hapus akun pengguna secara permanen.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Akun ' . $name . ' berhasil dihapus dari sistem.');
    }
}
