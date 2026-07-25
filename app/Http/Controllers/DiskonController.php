<?php

namespace App\Http\Controllers;

use App\Models\Diskon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DiskonController extends Controller
{
    public function index()
    {
        return view('menu.kelola_diskon');
    }

    public function tabel(Request $request)
    {
        if ($request->ajax()) {
            $data = Diskon::query()->select('id', 'nama', 'tipe', 'nilai', 'aktif');
            return DataTables::of($data)->addIndexColumn()->make(true);
        }
    }

    private function rules(): array
    {
        return [
            'nama'  => ['required', 'string', 'max:255'],
            'tipe'  => ['required', 'in:persen,fixed'],
            'nilai' => ['required', 'integer', 'min:1'],
            'aktif' => ['nullable'],
        ];
    }

    private function validasiNilai($tipe, $nilai)
    {
        if ($tipe === 'persen' && $nilai > 100) {
            return 'Diskon persen maksimal 100%!';
        }
        return null;
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), [
            'nama.required'  => 'Nama diskon tidak boleh kosong!',
            'tipe.in'        => 'Tipe diskon tidak valid!',
            'nilai.required' => 'Nilai diskon tidak boleh kosong!',
            'nilai.integer'  => 'Nilai diskon harus angka!',
            'nilai.min'      => 'Nilai diskon minimal 1!',
        ]);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }
        if ($pesan = $this->validasiNilai($request->tipe, (int) $request->nilai)) {
            return response()->json(['kode' => 500, 'pesan' => $pesan]);
        }

        Diskon::create([
            'nama'  => trim($request->nama),
            'tipe'  => $request->tipe,
            'nilai' => (int) $request->nilai,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        return response()->json(['kode' => 200, 'pesan' => 'Diskon berhasil ditambahkan!']);
    }

    public function update(Request $request)
    {
        $d = Diskon::find($request->id);
        if (!$d) {
            return response()->json(['kode' => 404, 'pesan' => 'Diskon tidak ditemukan!']);
        }
        $validator = Validator::make($request->all(), array_merge(['id' => ['required']], $this->rules()), [
            'nama.required'  => 'Nama diskon tidak boleh kosong!',
            'nilai.required' => 'Nilai diskon tidak boleh kosong!',
            'nilai.min'      => 'Nilai diskon minimal 1!',
        ]);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }
        if ($pesan = $this->validasiNilai($request->tipe, (int) $request->nilai)) {
            return response()->json(['kode' => 500, 'pesan' => $pesan]);
        }

        $d->update([
            'nama'  => trim($request->nama),
            'tipe'  => $request->tipe,
            'nilai' => (int) $request->nilai,
            'aktif' => $request->aktif ? 1 : 0,
        ]);

        return response()->json(['kode' => 200, 'pesan' => 'Diskon berhasil diperbarui!']);
    }

    public function destroy(Request $request)
    {
        $d = Diskon::find($request->id);
        if (!$d) {
            return response()->json(['kode' => 404, 'pesan' => 'Diskon tidak ditemukan!']);
        }
        $d->delete();
        return response()->json(['kode' => 200, 'pesan' => 'Diskon berhasil dihapus!']);
    }

    // dipakai layar kasir: daftar diskon aktif
    public function aktif()
    {
        $data = Diskon::where('aktif', 1)->orderBy('nama')->get(['id', 'nama', 'tipe', 'nilai']);
        return response()->json(['data' => $data]);
    }
}
