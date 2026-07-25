<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class PelangganController extends Controller
{
    public function index()
    {
        return view('menu.kelola_pelanggan');
    }

    public function tabel(Request $request)
    {
        if ($request->ajax()) {
            $data = Pelanggan::query()->select('id', 'nama', 'no_telp', 'alamat', 'keterangan', 'created_at');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('total_transaksi', function ($row) {
                    // dihitung sekali per baris halaman (bukan N+1 seluruh tabel)
                    return DB::table('data_pembelian')->where('id_pelanggan', $row->id)->count();
                })
                ->make(true);
        }
    }

    private function rules($id = null): array
    {
        // nama unik (abaikan diri sendiri saat edit)
        $unique = $id ? 'unique:pelanggan,nama,'.$id : 'unique:pelanggan,nama';
        return [
            'nama'       => ['required', 'string', 'max:255', $unique],
            'no_telp'    => ['nullable', 'string', 'max:30'],
            'alamat'     => ['nullable', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function pesanValidasi(): array
    {
        return [
            'nama.required' => 'Nama pelanggan tidak boleh kosong!',
            'nama.unique'   => 'Nama pelanggan sudah terdaftar!',
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules(), $this->pesanValidasi());
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        $p = Pelanggan::create([
            'nama'       => trim($request->nama),
            'no_telp'    => trim((string) $request->no_telp) ?: null,
            'alamat'     => trim((string) $request->alamat) ?: null,
            'keterangan' => trim((string) $request->keterangan) ?: null,
        ]);

        return response()->json([
            'kode' => 200,
            'pesan' => 'Pelanggan berhasil ditambahkan!',
            'data' => ['id' => $p->id, 'nama' => $p->nama, 'no_telp' => $p->no_telp],
        ]);
    }

    public function update(Request $request)
    {
        $p = Pelanggan::find($request->id);
        if (!$p) {
            return response()->json(['kode' => 404, 'pesan' => 'Pelanggan tidak ditemukan!']);
        }
        $validator = Validator::make($request->all(), array_merge(['id' => ['required']], $this->rules($request->id)), $this->pesanValidasi());
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        $p->update([
            'nama'       => trim($request->nama),
            'no_telp'    => trim((string) $request->no_telp) ?: null,
            'alamat'     => trim((string) $request->alamat) ?: null,
            'keterangan' => trim((string) $request->keterangan) ?: null,
        ]);

        return response()->json(['kode' => 200, 'pesan' => 'Pelanggan berhasil diperbarui!']);
    }

    public function destroy(Request $request)
    {
        $p = Pelanggan::find($request->id);
        if (!$p) {
            return response()->json(['kode' => 404, 'pesan' => 'Pelanggan tidak ditemukan!']);
        }
        // transaksi lama tetap utuh (nama pembeli sudah disnapshot); hanya lepas relasi
        DB::table('data_pembelian')->where('id_pelanggan', $p->id)->update(['id_pelanggan' => null]);
        $p->delete();

        return response()->json(['kode' => 200, 'pesan' => 'Pelanggan berhasil dihapus!']);
    }

    // dipakai Select2 di kasir (searchable, remote)
    public function search(Request $request)
    {
        $q = trim((string) $request->q);
        $query = Pelanggan::query()->select('id', 'nama', 'no_telp');
        if ($q !== '') {
            $query->where('nama', 'like', '%'.$q.'%')
                  ->orWhere('no_telp', 'like', '%'.$q.'%');
        }
        $items = $query->orderBy('nama')->limit(20)->get();

        $results = $items->map(function ($p) {
            $text = $p->nama.($p->no_telp ? ' ('.$p->no_telp.')' : '');
            return ['id' => $p->id, 'text' => $text, 'nama' => $p->nama];
        });

        return response()->json(['results' => $results]);
    }

    // tambah cepat pelanggan dari layar kasir
    public function quickStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama' => ['required', 'string', 'max:255', 'unique:pelanggan,nama'],
            'no_telp' => ['nullable', 'string', 'max:30'],
        ], $this->pesanValidasi());
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }
        $p = Pelanggan::create([
            'nama' => trim($request->nama),
            'no_telp' => trim((string) $request->no_telp) ?: null,
        ]);
        return response()->json([
            'kode' => 200,
            'pesan' => 'Pelanggan baru ditambahkan!',
            'id' => $p->id,
            'text' => $p->nama.($p->no_telp ? ' ('.$p->no_telp.')' : ''),
        ]);
    }
}
