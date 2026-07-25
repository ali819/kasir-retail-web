<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class KelolaUserController extends Controller
{
    // role yang boleh dibuat/diubah lewat menu ini
    private array $roleBoleh = [User::ROLE_ADMIN, User::ROLE_STOK, User::ROLE_KASIR];

    // jumlah admin yang tersisa (untuk jaga minimal 1 admin)
    private function jumlahAdmin(): int
    {
        return User::where('role', User::ROLE_ADMIN)->count();
    }

    public function index()
    {
        return view('menu.kelola_user');
    }

    public function tabel(Request $request)
    {
        if ($request->ajax()) {
            $data = User::query()->select('id', 'name', 'email', 'role', 'created_at');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('role_label', function ($row) {
                    return User::roleOptions()[$row->role] ?? $row->role;
                })
                ->addColumn('is_self', function ($row) {
                    return $row->id === Auth::id();
                })
                ->addColumn('is_admin', function ($row) {
                    return $row->role === User::ROLE_ADMIN;
                })
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $messages = [
            'name.required'     => 'Nama tidak boleh kosong!',
            'email.required'    => 'Email tidak boleh kosong!',
            'email.email'       => 'Format email tidak valid!',
            'email.unique'      => 'Email sudah dipakai user lain!',
            'role.required'     => 'Role wajib dipilih!',
            'role.in'           => 'Role tidak valid!',
            'password.required' => 'Password tidak boleh kosong!',
            'password.min'      => 'Password minimal 6 karakter!',
            'password.confirmed'=> 'Konfirmasi password tidak cocok!',
        ];
        $validator = Validator::make($request->all(), [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'role'     => ['required', 'in:' . implode(',', $this->roleBoleh)],
            'password' => ['required', 'min:6', 'confirmed'],
        ], $messages);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        User::create([
            'name'     => trim($request->name),
            'email'    => strtolower(trim($request->email)),
            'role'     => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return response()->json(['kode' => 200, 'pesan' => 'User berhasil ditambahkan!']);
    }

    public function update(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        if (!$user) {
            return response()->json(['kode' => 404, 'pesan' => 'User tidak ditemukan!']);
        }

        $messages = [
            'name.required' => 'Nama tidak boleh kosong!',
            'email.required'=> 'Email tidak boleh kosong!',
            'email.email'   => 'Format email tidak valid!',
            'email.unique'  => 'Email sudah dipakai user lain!',
            'role.required' => 'Role wajib dipilih!',
            'role.in'       => 'Role tidak valid!',
        ];
        $validator = Validator::make($request->all(), [
            'id'    => ['required'],
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $id],
            'role'  => ['required', 'in:' . implode(',', $this->roleBoleh)],
        ], $messages);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        // jaga: tidak boleh mengubah role akun sendiri
        if ($user->id === Auth::id() && $request->role !== $user->role) {
            return response()->json(['kode' => 500, 'pesan' => 'Anda tidak bisa mengubah role akun sendiri!']);
        }

        // jaga: jangan menurunkan role admin terakhir
        if ($user->isAdmin() && $request->role !== User::ROLE_ADMIN && $this->jumlahAdmin() <= 1) {
            return response()->json(['kode' => 500, 'pesan' => 'Minimal harus ada 1 admin! Angkat admin lain dulu.']);
        }

        $user->update([
            'name'  => trim($request->name),
            'email' => strtolower(trim($request->email)),
            'role'  => $request->role,
        ]);

        return response()->json(['kode' => 200, 'pesan' => 'User berhasil diperbarui!']);
    }

    public function resetPassword(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        if (!$user) {
            return response()->json(['kode' => 404, 'pesan' => 'User tidak ditemukan!']);
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'min:6'],
        ], [
            'password.required' => 'Password baru tidak boleh kosong!',
            'password.min'      => 'Password minimal 6 karakter!',
        ]);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        $user->update(['password' => Hash::make($request->password)]);

        return response()->json(['kode' => 200, 'pesan' => 'Password ' . $user->name . ' berhasil direset!']);
    }

    public function destroy(Request $request)
    {
        $id = $request->id;
        $user = User::find($id);
        if (!$user) {
            return response()->json(['kode' => 404, 'pesan' => 'User tidak ditemukan!']);
        }
        if ($user->id === Auth::id()) {
            return response()->json(['kode' => 500, 'pesan' => 'Tidak bisa menghapus akun sendiri!']);
        }
        if ($user->isAdmin() && $this->jumlahAdmin() <= 1) {
            return response()->json(['kode' => 500, 'pesan' => 'Minimal harus ada 1 admin! Tidak bisa menghapus admin terakhir.']);
        }

        $user->delete();

        return response()->json(['kode' => 200, 'pesan' => 'User berhasil dihapus!']);
    }
}
