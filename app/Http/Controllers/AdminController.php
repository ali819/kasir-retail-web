<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //
    public function dashboard()
    {
        // Menggunakan query builder untuk mengambil total jumlah pembelian per tanggal
        $chartData = DB::table('data_pembelian_detail')
        ->select(DB::raw('DATE(created_at) as tanggal'), DB::raw('SUM(total_harga) as jumlah'))
        ->where('created_at', '>=', now()->subDays(6)->format('Y-m-d')) // Ambil data 6 hari terakhir
        ->groupBy('tanggal')
        ->orderBy('tanggal', 'DESC') // Urutkan berdasarkan tanggal secara desc
        ->get();

        // Buat array tanggal dengan subDay(6)
        $arrayTanggal = collect(range(0, 5))->map(function ($index) {
            return now()->subDays($index)->format('Y-m-d');
        });
        $arrayTanggalIso = collect(range(0, 5))->map(function ($index) {
            return now()->subDays($index)->isoFormat('D MMMM');
        });

        // Buat array yValues, isi dengan 0 jika tanggal tidak ditemukan
        $jumlahValues = $arrayTanggal->map(function ($date) use ($chartData) {
            $chartItem = $chartData->firstWhere('tanggal', Carbon::parse($date)->format('Y-m-d'));
            return $chartItem ? $chartItem->jumlah : "0";
        })->toArray();

        // Mengelompokkan data untuk chart
        $xValues = $arrayTanggalIso->toArray();
        $yValues = $jumlahValues;

        $totalPenjualan = 'Rp ' . number_format(array_sum($jumlahValues), 0, ',', '.');
        $totalTransaksi = DB::table('data_pembelian_detail')
            ->select('id_transaksi') 
            ->whereDate('created_at', '>=', now()->subDays(5)->format('Y-m-d'))
            ->groupBy('id_transaksi')
            ->get()
            ->count();
        $totalBarangTerjual =  DB::table('data_pembelian_detail')
            ->whereDate('created_at', '>=', now()->subDays(5)->format('Y-m-d'))
            ->sum('qty'); 

        // produk terlaris
        $topProducts = DB::table('data_pembelian_detail')
        ->select('nama_barang', DB::raw('SUM(qty) as total_qty'))
        ->where('created_at', '>=', now()->subDays(6)->format('Y-m-d'))
        ->groupBy('nama_barang')
        ->orderByDesc('total_qty')
        ->limit(5)
        ->get(); 

        // nominal (omzet) hanya untuk admin; role lain disembunyikan
        $isAdmin = auth()->user()->isAdmin();
        if (!$isAdmin) {
            $yValues = null;          // sembunyikan grafik omzet (Rp)
            $totalPenjualan = null;   // sembunyikan KPI omzet
        }

        return view('menu.dashboard', compact('xValues', 'yValues','totalPenjualan','totalTransaksi','totalBarangTerjual','topProducts','isAdmin'));
    }

    public function kasir()
    {
        return view('menu.kasir');
    }

    // grid produk kasir (paginated + search)
    public function kasir_produk(Request $request)
    {
        $q = trim((string) $request->q);
        $page = (int) ($request->page ?: 1);
        if ($page < 1) { $page = 1; }
        $per = (int) ($request->per ?: 500);
        if ($per < 1) { $per = 500; }
        if ($per > 1000) { $per = 1000; }

        // harga_mulai via subquery (hindari N+1)
        $sub = DB::table('satuan_jual')->select('id_barang', DB::raw('MIN(harga) as harga_mulai'))->groupBy('id_barang');
        $query = DB::table('stok_barang as b')
            ->leftJoinSub($sub, 'sj', 'sj.id_barang', '=', 'b.id')
            ->select('b.id', 'b.nama_barang', 'b.barcode', 'b.gambar', 'b.satuan_dasar', 'b.stok', DB::raw('COALESCE(sj.harga_mulai, 0) as harga_mulai'));
        if ($q !== '') {
            $query->where('b.nama_barang', 'like', '%'.$q.'%');
        }
        $total = (clone $query)->count();
        $items = $query->orderBy('b.nama_barang')
            ->offset(($page - 1) * $per)->limit($per)->get();

        // ambil semua satuan jual utk produk di chunk ini (1 query, hindari N+1)
        $ids = $items->pluck('id')->all();
        $satuanGrouped = DB::table('satuan_jual')
            ->whereIn('id_barang', $ids)
            ->orderBy('konversi')
            ->get(['id_barang', 'nama_satuan', 'harga', 'konversi'])
            ->groupBy('id_barang');

        foreach ($items as $it) {
            $it->harga_mulai = (int) $it->harga_mulai;
            $it->gambar_url = $it->gambar ? asset('uploads/barang/'.$it->gambar) : '';
            $it->satuan = collect($satuanGrouped->get($it->id, []))->map(function ($r) {
                return ['nama_satuan' => $r->nama_satuan, 'harga' => (int) $r->harga, 'konversi' => (int) $r->konversi];
            })->values();
        }

        return response()->json([
            'data' => $items,
            'page' => $page,
            'hasMore' => ($page * $per) < $total,
            'total' => $total,
        ]);
    }

    // cari barang berdasarkan barcode (untuk scan di kasir)
    public function kasir_barcode(Request $request)
    {
        $code = trim((string) $request->code);
        if ($code === '') {
            return response()->json(['kode' => 404, 'pesan' => 'Barcode kosong!']);
        }
        $barang = DB::table('stok_barang')->where('barcode', $code)->first();
        if (!$barang) {
            return response()->json(['kode' => 404, 'pesan' => 'Barcode "'.$code.'" tidak terdaftar!']);
        }
        $satuan = DB::table('satuan_jual')->where('id_barang', $barang->id)->orderBy('konversi')->get(['nama_satuan', 'harga', 'konversi']);
        if ($satuan->count() < 1) {
            return response()->json(['kode' => 404, 'pesan' => 'Satuan jual barang belum diatur!']);
        }
        return response()->json([
            'kode' => 200,
            'id' => $barang->id,
            'nama_barang' => $barang->nama_barang,
            'gambar_url' => $barang->gambar ? asset('uploads/barang/'.$barang->gambar) : '',
            'satuan' => $satuan,
        ]);
    }
    public function data_transaksi()
    {
        return view('menu.data_transaksi');
    }
    public function kelola_barang()
    {
        return view('menu.kelola_barang');
    }

    // 
    public function tabel_stok(Request $request)
    {
        if($request->ajax()) {
            $stok = $request->stok;
            $data = DB::table('stok_barang')->select('*');
            if($stok == 1) {
                $data->where('stok', '>', 0);
            } else if($stok == 2) {
                $data->where('stok', '<', 1);
            }
            return Datatables::of($data)
                ->addIndexColumn()
                ->addColumn('satuan_jual', function ($row) {
                    return DB::table('satuan_jual')
                        ->where('id_barang', $row->id)
                        ->orderBy('konversi')
                        ->get(['id', 'nama_satuan', 'harga', 'konversi']);
                })
                ->addColumn('gambar_url', function ($row) {
                    return $row->gambar ? asset('uploads/barang/'.$row->gambar) : '';
                })
                ->make(true);
        }
    }

    private function UpperFirstText($text) {
        return ucwords(strtolower(trim($text)));
    }

    // upload gambar barang ke public/uploads/barang
    private function uploadGambarBarang($file) {
        $dir = public_path('uploads/barang');
        if (!is_dir($dir)) { @mkdir($dir, 0755, true); }
        $name = 'barang-'.time().'-'.mt_rand(1000, 9999).'.'.strtolower($file->getClientOriginalExtension());
        $file->move($dir, $name);
        return $name;
    }
    private function hapusGambarBarang($nama) {
        if ($nama) {
            $path = public_path('uploads/barang/'.$nama);
            if (is_file($path)) { @unlink($path); }
        }
    }

    // parsing & validasi daftar satuan jual dari request
    private function parseSatuanJual($input)
    {
        if (!is_array($input) || count($input) < 1) {
            return ['ok' => false, 'pesan' => 'Minimal harus ada 1 satuan jual!'];
        }
        $rows = [];
        $namaList = [];
        foreach ($input as $item) {
            $nama = isset($item['nama_satuan']) ? $this->UpperFirstText($item['nama_satuan']) : '';
            $harga = isset($item['harga']) ? $item['harga'] : '';
            $konversi = isset($item['konversi']) ? $item['konversi'] : 1;

            if ($nama === '') {
                return ['ok' => false, 'pesan' => 'Nama satuan tidak boleh kosong!'];
            }
            if ($harga === '' || !is_numeric($harga)) {
                return ['ok' => false, 'pesan' => 'Harga satuan "'.$nama.'" harus angka!'];
            }
            if ($konversi === '' || !is_numeric($konversi) || $konversi < 1) {
                $konversi = 1;
            }
            if (in_array($nama, $namaList)) {
                return ['ok' => false, 'pesan' => 'Satuan "'.$nama.'" tidak boleh duplikat!'];
            }
            $namaList[] = $nama;
            $rows[] = [
                'id' => (isset($item['id']) && $item['id'] !== '') ? $item['id'] : null,
                'nama_satuan' => $nama,
                'harga' => (int) $harga,
                'konversi' => (int) $konversi,
            ];
        }
        return ['ok' => true, 'rows' => $rows];
    }

    public function tambah_barang_baru(Request $request)
    {
        $messages = [
            'nama_barang.required' => 'Nama barang tidak boleh kosong!',
            'nama_barang.unique' => 'Nama barang sudah ada!',
            'satuan_dasar.required' => 'Satuan dasar tidak boleh kosong!',
        ];
        $validator = Validator::make($request->all(), [
            'nama_barang' => ['required', 'unique:stok_barang,nama_barang'],
            'satuan_dasar' => ['required'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], $messages);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        $parsed = $this->parseSatuanJual($request->input('satuan'));
        if (!$parsed['ok']) {
            return response()->json(['kode' => 500, 'pesan' => $parsed['pesan']]);
        }

        $barcode = trim((string) $request->barcode);
        $barcode = $barcode === '' ? null : $barcode;
        if ($barcode !== null && DB::table('stok_barang')->where('barcode', $barcode)->exists()) {
            return response()->json(['kode' => 500, 'pesan' => 'Barcode sudah dipakai barang lain!']);
        }

        $stok = $request->stok;
        if ($stok == null || !is_numeric($stok)) { $stok = 0; }
        $gambar = $request->hasFile('gambar') ? $this->uploadGambarBarang($request->file('gambar')) : null;
        $timestamp = Carbon::now();

        try {
            DB::beginTransaction();

            $idBarang = DB::table('stok_barang')->insertGetId([
                'nama_barang' => $request->nama_barang,
                'barcode' => $barcode,
                'satuan_dasar' => $this->UpperFirstText($request->satuan_dasar),
                'stok' => $stok,
                'gambar' => $gambar,
                'keterangan' => $request->keterangan,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);

            $insert = [];
            foreach ($parsed['rows'] as $r) {
                $insert[] = [
                    'id_barang' => $idBarang,
                    'nama_satuan' => $r['nama_satuan'],
                    'harga' => $r['harga'],
                    'konversi' => $r['konversi'],
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
            DB::table('satuan_jual')->insert($insert);

            DB::commit();
            return response()->json(['kode' => 200, 'pesan' => 'Berhasil ditambahkan!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response($e->getMessage(), 500);
        }
    }

    public function hapus_data_barang(Request $request)
    {
        $id = $request->id;
        if ($id == null) { return abort(500); }
        $gambar = DB::table('stok_barang')->where('id', $id)->value('gambar');
        try {
            DB::beginTransaction();
            DB::table('stok_barang')->where('id', $id)->delete();
            DB::table('satuan_jual')->where('id_barang', $id)->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return response($e->getMessage(), 500);
        }
        $this->hapusGambarBarang($gambar);
        return response()->json(['kode' => 200, 'pesan' => 'Berhasil dihapus!']);
    }

    public function update_barang(Request $request)
    {
        $messages = [
            'id.required' => 'ID tidak boleh kosong, silahkan muat ulang halaman!',
            'nama_barang.required' => 'Nama barang tidak boleh kosong!',
            'satuan_dasar.required' => 'Satuan dasar tidak boleh kosong!',
        ];
        $validator = Validator::make($request->all(), [
            'id' => ['required'],
            'nama_barang' => ['required'],
            'satuan_dasar' => ['required'],
            'gambar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], $messages);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        $id = $request->id;
        $parsed = $this->parseSatuanJual($request->input('satuan'));
        if (!$parsed['ok']) {
            return response()->json(['kode' => 500, 'pesan' => $parsed['pesan']]);
        }

        $check = DB::table('stok_barang')->where('nama_barang', $request->nama_barang)->whereNotIn('id', [$id])->first();
        if ($check) {
            return response()->json(['kode' => 500, 'pesan' => 'Nama barang sudah ada!']);
        }

        $barcode = trim((string) $request->barcode);
        $barcode = $barcode === '' ? null : $barcode;
        if ($barcode !== null && DB::table('stok_barang')->where('barcode', $barcode)->whereNotIn('id', [$id])->exists()) {
            return response()->json(['kode' => 500, 'pesan' => 'Barcode sudah dipakai barang lain!']);
        }

        $stok = $request->stok;
        if ($stok == null || !is_numeric($stok)) { $stok = 0; }
        $timestamp = Carbon::now();

        // gambar: upload baru / hapus / biarkan
        $gambarLama = DB::table('stok_barang')->where('id', $id)->value('gambar');
        $gambarUpdate = [];
        if ($request->hapus_gambar == '1') {
            $this->hapusGambarBarang($gambarLama);
            $gambarUpdate['gambar'] = null;
        } elseif ($request->hasFile('gambar')) {
            $this->hapusGambarBarang($gambarLama);
            $gambarUpdate['gambar'] = $this->uploadGambarBarang($request->file('gambar'));
        }

        try {
            DB::beginTransaction();

            DB::table('stok_barang')->where('id', $id)->update(array_merge([
                'nama_barang' => $request->nama_barang,
                'barcode' => $barcode,
                'satuan_dasar' => $this->UpperFirstText($request->satuan_dasar),
                'stok' => $stok,
                'keterangan' => $request->keterangan,
                'updated_at' => $timestamp,
            ], $gambarUpdate));

            $keepIds = [];
            foreach ($parsed['rows'] as $r) {
                if ($r['id']) {
                    DB::table('satuan_jual')->where('id', $r['id'])->where('id_barang', $id)->update([
                        'nama_satuan' => $r['nama_satuan'],
                        'harga' => $r['harga'],
                        'konversi' => $r['konversi'],
                        'updated_at' => $timestamp,
                    ]);
                    $keepIds[] = $r['id'];
                } else {
                    $newId = DB::table('satuan_jual')->insertGetId([
                        'id_barang' => $id,
                        'nama_satuan' => $r['nama_satuan'],
                        'harga' => $r['harga'],
                        'konversi' => $r['konversi'],
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ]);
                    $keepIds[] = $newId;
                }
            }
            DB::table('satuan_jual')->where('id_barang', $id)->whereNotIn('id', $keepIds)->delete();

            DB::commit();
            return response()->json(['kode' => 200, 'pesan' => 'Berhasil diperbarui!']);
        } catch (\Exception $e) {
            DB::rollback();
            return response($e->getMessage(), 500);
        }
    }

    public function detail_data_barang(Request $request)
    {
        $id = $request->id;
        if ($id == null) {
            return response()->json(['kode' => 404, 'pesan' => 'ID barang tidak boleh kosong!']);
        }
        $barang = DB::table('stok_barang')->where('id', $id)->first();
        if (!$barang) {
            return response()->json(['kode' => 404, 'pesan' => 'ID barang tidak ditemukan!']);
        }
        $satuan = DB::table('satuan_jual')->where('id_barang', $id)->orderBy('konversi')->get(['nama_satuan', 'harga', 'konversi']);
        if ($satuan->count() < 1) {
            return response()->json(['kode' => 404, 'pesan' => 'Satuan jual barang belum ditambahkan!']);
        }
        return response()->json([
            'kode' => 200,
            'nama_barang' => $barang->nama_barang,
            'satuan_dasar' => $barang->satuan_dasar,
            'gambar_url' => $barang->gambar ? asset('uploads/barang/'.$barang->gambar) : '',
            'satuan' => $satuan,
        ]);
    }

    public function konfirmasi_pembelian_barang(Request $request)
    {

        // Mendapatkan data dinamis dari request
        $dynamicTabelBelanja = $request->input('dynamicTabelBelanja');
        $total_belanja = $request->total_belanja;
        $total_bayar = $request->total_bayar;
        $kembalian = $request->kembalian;
        $pembeli = $request->pembeli;

        // pelanggan relasional (opsional): nama diambil dari master agar konsisten
        $id_pelanggan = $request->id_pelanggan ?: null;
        if ($id_pelanggan) {
            $pel = DB::table('pelanggan')->where('id', $id_pelanggan)->first();
            if ($pel) { $pembeli = $pel->nama; } else { $id_pelanggan = null; }
        }

        // DB transaction 
        try {
            // Memulai transaksi database
            DB::beginTransaction();

            $timestamp = Carbon::now();

            // generate id transaksi: TRX-{YYMMDD}-{urutan 4 digit}, mis. TRX-260725-0001
            // counter atomik & bebas race (baris counter di-lock DB di dalam transaksi ini)
            $ymd = $timestamp->format('ymd');
            DB::statement(
                "INSERT INTO nomor_transaksi (tanggal, urutan) VALUES (?, 1)
                 ON DUPLICATE KEY UPDATE urutan = urutan + 1",
                [$ymd]
            );
            $urutan = DB::table('nomor_transaksi')->where('tanggal', $ymd)->value('urutan');
            $id_transaksi = 'TRX-'.$ymd.'-'.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);

            // ambil data
            $data_pembelian = [];
            $data_pembelian_detail = [];
            $list_data_belanja = [];

            // total belanja dihitung ulang di server (jangan percaya nilai dari client)
            $total_belanja = 0;

            foreach ($dynamicTabelBelanja as $index => $data) {
                $index++;
                $id_barang = $data['id_barang'];
                $satuan = $data['satuan'];
                $total_qty = $data['total_qty'];

                // Validasi qty
                if (empty($total_qty) || $total_qty <= 0) {
                    DB::rollback();
                    return response()->json([
                        'kode' => 500,
                        'pesan' => 'Jumlah beli barang tidak boleh kosong!',
                    ]);
                }

                // Ambil barang dari database sebagai sumber kebenaran harga
                $barang = DB::table('stok_barang')->where('id', $id_barang)->first();
                if (!$barang) {
                    DB::rollback();
                    return response()->json([
                        'kode' => 500,
                        'pesan' => 'Barang tidak ditemukan, silahkan muat ulang halaman!',
                    ]);
                }
                $nama_barang = $barang->nama_barang;

                // Ambil satuan jual resmi dari database (harga & konversi)
                $satuanRow = DB::table('satuan_jual')
                    ->where('id_barang', $id_barang)
                    ->where('nama_satuan', $satuan)
                    ->first();
                if (!$satuanRow) {
                    DB::rollback();
                    return response()->json([
                        'kode' => 500,
                        'pesan' => 'Satuan "'.$satuan.'" untuk barang "'.$nama_barang.'" tidak valid!',
                    ]);
                }
                $harga = $satuanRow->harga;

                if ($harga <= 0) {
                    DB::rollback();
                    return response()->json([
                        'kode' => 500,
                        'pesan' => 'Harga barang "'.$nama_barang.'" belum diatur!',
                    ]);
                }

                $total_harga = $harga * $total_qty;
                $total_belanja += $total_harga;

                $list_data_belanja[] = [
                    'nomor' => $index,
                    'nama_barang' => $nama_barang,
                    'total_qty' => $total_qty,
                    'satuan' => $satuan,
                    'harga' => $harga,
                ];

                $data_pembelian_detail[] = [
                    'id_transaksi' => $id_transaksi,
                    'nama_barang' => $nama_barang,
                    'satuan' => $satuan,
                    'qty' => $total_qty,
                    'harga' => $harga,
                    'total_harga' => $total_harga,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];

                // Kurangi stok (dalam satuan dasar) = qty x konversi, jika stok dilacak (> 0)
                if ($barang->stok > 0) {
                    $dikurangi = $total_qty * $satuanRow->konversi;
                    if ($dikurangi > $barang->stok) {
                        DB::rollback();
                        return response()->json([
                            'kode' => 500,
                            'pesan' => 'Stok "'.$nama_barang.'" tidak mencukupi! Sisa: '.$barang->stok.' '.$barang->satuan_dasar.'.',
                        ]);
                    }
                    DB::table('stok_barang')->where('id', $id_barang)->update([
                        'stok' => $barang->stok - $dikurangi,
                    ]);
                }
            }

            // === DISKON (opsional) — sumber kebenaran dari DB, bukan dari client ===
            $subtotal = $total_belanja; // jumlah item sebelum diskon
            $diskon_nama = null;
            $diskon_potongan = 0;
            if ($request->id_diskon) {
                $diskon = DB::table('diskon')->where('id', $request->id_diskon)->where('aktif', 1)->first();
                if ($diskon) {
                    $diskon_potongan = $diskon->tipe === 'persen'
                        ? (int) floor($subtotal * $diskon->nilai / 100)
                        : (int) $diskon->nilai;
                    $diskon_potongan = max(0, min($diskon_potongan, $subtotal)); // tak boleh > subtotal
                    $diskon_nama = $diskon->nama.($diskon->tipe === 'persen' ? ' ('.$diskon->nilai.'%)' : '');
                }
            }
            // total_belanja = nilai SETELAH diskon (uang riil yang harus dibayar)
            $total_belanja = $subtotal - $diskon_potongan;

            // Validasi bayar >= total setelah diskon
            if ((int) $total_bayar < $total_belanja) {
                DB::rollback();
                return response()->json([
                    'kode' => 500,
                    'pesan' => 'Total bayar kurang dari total belanja!',
                ]);
            }
            $total_bayar = (int) $total_bayar;
            $kembalian = $total_bayar - $total_belanja;

            // kasir yang memproses transaksi
            $id_user = Auth::id();
            $nama_kasir = Auth::user()->name ?? '';

            //
            $data_pembelian[] = [
                'id_transaksi' => $id_transaksi,
                'pembeli' => $pembeli,
                'id_pelanggan' => $id_pelanggan,
                'id_user' => $id_user,
                'nama_kasir' => $nama_kasir,
                'subtotal' => $subtotal,
                'diskon_nama' => $diskon_nama,
                'diskon_potongan' => $diskon_potongan,
                'total_belanja' => $total_belanja,
                'total_bayar' => $total_bayar,
                'kembalian' => $kembalian,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ];

            // insert 
            DB::table('data_pembelian')->insert($data_pembelian);
            DB::table('data_pembelian_detail')->insert($data_pembelian_detail);

            // nota pembelian
            // android (rawbt)
            $dataToko = $this->informasiToko();
            $nama_toko = $dataToko['nama_toko'];
            $alamat_toko = $dataToko['alamat_toko'];
            $no_telp = $dataToko['no_telp'];
            $footer_nota = $dataToko['footer_nota'];
            $logo_url = $dataToko['logo_url'];
            // nama kasir di struk hanya bila diaktifkan di Pengaturan Toko
            $kasir_nota = $dataToko['tampilkan_kasir'] ? $nama_kasir : '';
            $nota_pembelian = $this->HTMLNotaPembelian($list_data_belanja, $id_transaksi, $total_belanja, $total_bayar, $kembalian, $timestamp, $pembeli, $nama_toko, $alamat_toko, $no_telp, $footer_nota, $logo_url, $kasir_nota, $subtotal, $diskon_nama, $diskon_potongan);
            // pc / windows (recta host)
            if(!$pembeli) {
                $pembeli = '-';
            }
            $raw_data = [
                'list_data_belanja' => $list_data_belanja,
                'id_transaksi' => $id_transaksi,
                'subtotal' => $subtotal,
                'diskon_nama' => $diskon_nama,
                'diskon_potongan' => $diskon_potongan,
                'total_belanja' => $total_belanja,
                'total_bayar' => $total_bayar,
                'kembalian' => $kembalian,
                'timestamp' => $timestamp,
                'pembeli' => $pembeli,
                'kasir' => $kasir_nota,
                'nama_toko' => $nama_toko,
                'alamat_toko' => $alamat_toko,
                'no_telp' => $no_telp,
                'footer_nota' => $footer_nota,
                'logo_url' => $logo_url,
            ];

            // Commit transaksi jika berhasil
            DB::commit();
            // 
            return response()->json([
                'kode' => 200,
                'nota_pembelian' => $nota_pembelian,
                'raw_data' => $raw_data,
                'pesan' => 'Pembelian berhasil dikonfirmasi!'
            ]);

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi pengecualian
            DB::rollback();

            // Membaca pesan error dari pengecualian
            $errorMessage = $e->getMessage();

            // Mengirim pesan error sebagai respons HTTP 500
            return response($errorMessage, 500);
        }
    }

    private function formatRupiah($amount)
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
    private function HTMLNotaPembelian($list_data_belanja, $id_transaksi, $total_belanja, $total_bayar, $kembalian, $timestamp, $pembeli, $nama_toko, $alamat_toko, $no_telp = '', $footer_nota = '', $logo_url = '', $kasir = '', $subtotal = 0, $diskon_nama = null, $diskon_potongan = 0) {

        // cek nama pembeli
        if(!$pembeli) {
            $pembeli = '-';
        }

        // Jumlah dash yang diinginkan
        $jumlah_dash = 16;
        // Buat string dash dengan mengulang karakter '-' sebanyak jumlah yang diinginkan
        $dash = str_repeat('- ', $jumlah_dash);

        // header nota pembelian
        $nota_pembelian = 
        '
            <div class="col-12">'.($logo_url ? '
            <div style="text-align:center; margin-top:10px;"><img src="'.$logo_url.'" alt="logo" style="max-width:180px; max-height:90px;"></div>' : '').'
            <h5>
                <br>
                <span id="notaNamaToko">'.$nama_toko.'</span> - <span id="notaAlamat">'.$alamat_toko.'</span>'.($no_telp ? ' - <span id="notaNoTelp">'.$no_telp.'</span>' : '').'
            </h5>
            <p>
            '.$dash.'
                <br>
                <span id="notaIdTransaksi">'.$id_transaksi.'</span> <br>
                <span id="notaTanggalPembelian">'.$timestamp.'</span>'.($kasir ? ' <br>
                KASIR : <span id="notaKasir">'.$kasir.'</span>' : '').'
            </p>
            <p>
                '.$dash.'
                <br>
        ';

        // foreach list belanjaan
        foreach($list_data_belanja as $item) {

            $nomor = $item['nomor'];
            $nama_barang = $item['nama_barang'];
            $total_qty = $item['total_qty'];
            $satuan = $item['satuan'];
            $harga = $item['harga'];
            $sub_total_harga = $total_qty * $harga;

            $nota_pembelian .='
                    '.$nama_barang.' ('.$satuan.')
                    <br>
                    '.$total_qty.' x '.$harga.' : '.$this->formatRupiah($sub_total_harga).'
                    <br>
                    <br>
            ';
        }

        // footer nota pembelian
        $nota_pembelian .=' 
                    '.$dash.'
                    <br>
                    PEMBELI : <span id="notaNamaPembeli">'.$pembeli.'</span>
                    <br>
                    <br>
                    '.$dash.'
                    <br>'.($diskon_potongan > 0 ? '
                    SUBTOTAL : <span id="notaSubtotal">'.$this->formatRupiah($subtotal).'</span>
                    <br>
                    DISKON'.($diskon_nama ? ' ('.$diskon_nama.')' : '').' : <span id="notaDiskon">- '.$this->formatRupiah($diskon_potongan).'</span>
                    <br>' : '').'
                    TOTAL : <span id="notaTotalBelanja">'.$this->formatRupiah($total_belanja).'</span>
                    <br>
                    BAYAR : <span id="notaTotalBayar">'.$this->formatRupiah($total_bayar).'</span>
                    <br>
                    KEMBALI : <span id="notaKembalian">'.$this->formatRupiah($kembalian).'</span>
                    <br>
                    <br>
                    '.$dash.'
                    <br>'.($footer_nota ? '
                    <br>
                    <span id="notaFooter">'.$footer_nota.'</span>
                    <br>' : '').'
                </p>
            </div>
        ';

        return $nota_pembelian;

    }

    public function tabel_data_transaksi(Request $request)
    {
        if($request->ajax()) {
            $data = DB::table('data_pembelian')->select('*');
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('formatted_date', function ($row) {
                        // Format the 'created_at' column as "Senin, 23 Desember 2024 10:10:10"
                        return Carbon::parse($row->created_at)->isoFormat('dddd, D MMMM YYYY HH:mm:ss');
                    })
                    ->make(true);
        }
    }

    public function detail_data_transaksi(Request $request)
    {
        $id_transaksi = $request->id_transaksi;
        if($id_transaksi == null) {
            return abort(404);
        }
        $data_pembelian = DB::table('data_pembelian')->where('id_transaksi',$id_transaksi)->first();
        $detail_pembelian = DB::table('data_pembelian_detail')->where('id_transaksi',$id_transaksi)->get();
        if($detail_pembelian->count() <= 0) {
            return response()->json([
                'kode' => 404,
                'pesan' => 'List barang tidak ditemukan!',
            ]);
        }
        if(!$data_pembelian) {
            return response()->json([
                'kode' => 404,
                'pesan' => 'ID transaksi tidak ditemukan!',
            ]);
        }
        
        //
        $tanggal = $data_pembelian->created_at;
        $total_belanja = $data_pembelian->total_belanja;
        $total_bayar = $data_pembelian->total_bayar;
        $kembalian = $data_pembelian->kembalian;
        $list = $detail_pembelian;
        //

        return response()->json([
            'kode' => 200,
            'tanggal' => $tanggal,
            'pembeli' => $data_pembelian->pembeli ?: '-',
            'kasir' => $data_pembelian->nama_kasir ?: '-',
            'total_belanja' => $total_belanja,
            'total_bayar' => $total_bayar,
            'kembalian' => $kembalian,
            'list' => $list,
            'pesan' => 'Menampilkan detail pembelian.',
        ]);

        

    }

    public function hapus_data_transaksi(Request $request)
    {

        $list_id = $request->input('list_id_transaksi');
        if (empty($list_id)) { 
            return abort(404);
        }

        // DB transaction 
        try {
            // Memulai transaksi database
            DB::beginTransaction();

            // hapus data dari 2 tabel
            Db::table('data_pembelian')->whereIn('id_transaksi',$list_id)->delete();
            Db::table('data_pembelian_detail')->whereIn('id_transaksi',$list_id)->delete();

            // Commit transaksi jika berhasil
            DB::commit();
            // 
            return response()->json([
                'kode' => 200,
                'pesan' => 'Data berhasil dihapus!',
            ]);

        } catch (\Exception $e) {
            // Rollback transaksi jika terjadi pengecualian
            DB::rollback();

            // Membaca pesan error dari pengecualian
            $errorMessage = $e->getMessage();

            // Mengirim pesan error sebagai respons HTTP 500
            return response($errorMessage, 500);
        }

    }

    public function nota_pembelian_html(Request $request)
    {
        $id_transaksi = $request->id_transaksi;
        if($id_transaksi == null) {
            return abort(404);
        }

        // ambil data
        $data_pembelian = DB::table('data_pembelian')->where('id_transaksi',$id_transaksi)->first();
        $data_pembelian_detail = DB::table('data_pembelian_detail')->where('id_transaksi',$id_transaksi)->get();
        if(!$data_pembelian) {
            return response()->json([
                'kode' => 404,
                'pesan' => 'ID transaksi pembelian tidak di temukan!',
            ]);
        }
        if($data_pembelian_detail->count() <= 0) {
            return response()->json([
                'kode' => 404,
                'pesan' => 'Detail pembelian tidak di temukan!',
            ]);
        }

        // variabel di kirim
        $list_data_belanja = [];
        foreach($data_pembelian_detail as $index => $item) {
            $index++;
            $list_data_belanja[] = [
                'nomor' => $index,
                'nama_barang' => $item->nama_barang,
                'total_qty' => $item->qty,
                'satuan' => $item->satuan,
                'harga' => $item->harga,
            ];

        }
        // 
        $id_transaksi = $data_pembelian->id_transaksi;
        $total_belanja = $data_pembelian->total_belanja;
        $total_bayar = $data_pembelian->total_bayar;
        $kembalian = $data_pembelian->kembalian;
        $timestamp = $data_pembelian->created_at;
        $pembeli = $data_pembelian->pembeli;
        if(!$pembeli) {
            $pembeli = '-';
        }
        // diskon (data lama: null/0)
        $subtotal = $data_pembelian->subtotal ?? $total_belanja;
        $diskon_nama = $data_pembelian->diskon_nama ?? null;
        $diskon_potongan = (int) ($data_pembelian->diskon_potongan ?? 0);

        $dataToko = $this->informasiToko();
        $kasir = $dataToko['tampilkan_kasir'] ? ($data_pembelian->nama_kasir ?: '') : '';
        $nama_toko = $dataToko['nama_toko'];
        $alamat_toko = $dataToko['alamat_toko'];
        $no_telp = $dataToko['no_telp'];
        $footer_nota = $dataToko['footer_nota'];
        $logo_url = $dataToko['logo_url'];

        // android (rawbt)
        $nota_pembelian = $this->HTMLNotaPembelian($list_data_belanja, $id_transaksi, $total_belanja, $total_bayar, $kembalian, $timestamp, $pembeli, $nama_toko, $alamat_toko, $no_telp, $footer_nota, $logo_url, $kasir, $subtotal, $diskon_nama, $diskon_potongan);
        // pc / windows (recta host)
        $raw_data = [
            'list_data_belanja' => $list_data_belanja,
            'id_transaksi' => $id_transaksi,
            'subtotal' => $subtotal,
            'diskon_nama' => $diskon_nama,
            'diskon_potongan' => $diskon_potongan,
            'total_belanja' => $total_belanja,
            'total_bayar' => $total_bayar,
            'kembalian' => $kembalian,
            'timestamp' => $timestamp,
            'pembeli' => $pembeli,
            'kasir' => $kasir,
            'nama_toko' => $nama_toko,
            'no_telp' => $no_telp,
            'footer_nota' => $footer_nota,
            'alamat_toko' => $alamat_toko,
            'logo_url' => $logo_url,
        ];

        return response()->json([
            'kode' => 200,
            'nota_pembelian' => $nota_pembelian,
            'raw_data' => $raw_data,
            'pesan' => 'Mencetak nota pembelian',
        ]);
    }

    public function tabel_detail_pembelian(Request $request) 
    {
        if($request->ajax()) {
            $data = DB::table('data_pembelian_detail')->select('*');
            // filter rentang tanggal (opsional)
            if ($request->tgl_mulai) {
                $data->whereDate('created_at', '>=', $request->tgl_mulai);
            }
            if ($request->tgl_selesai) {
                $data->whereDate('created_at', '<=', $request->tgl_selesai);
            }
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('formatted_date', function ($row) {
                        // Format the 'created_at' column as "Senin, 23 Desember 2024 10:10:10"
                        return Carbon::parse($row->created_at)->isoFormat('dddd, D MMMM YYYY HH:mm:ss');
                    })
                    ->make(true);
        }
    }
    public function hitung_data_pembelian(Request $request)
    {
        $mulai = $request->tanggal_mulai;
        $selesai = $request->tanggal_selesai;
        if ($mulai == null || $selesai == null) {
            return response()->json([
                'kode' => 404,
                'pesan' => 'Tanggal mulai & selesai wajib diisi!',
            ]);
        }
        // jaga urutan (mulai <= selesai)
        if ($mulai > $selesai) {
            [$mulai, $selesai] = [$selesai, $mulai];
        }

        $data = DB::table('data_pembelian_detail')->select('*')
            ->whereDate('created_at', '>=', $mulai)
            ->whereDate('created_at', '<=', $selesai)
            ->get();
        $total_omzet = $data->sum('total_harga');
        $total_transaksi = $data->groupBy('id_transaksi')->count();
        $barang_terjual = $data->sum('qty');

        $range = Carbon::parse($mulai)->isoFormat('D MMM YYYY') . ' — ' . Carbon::parse($selesai)->isoFormat('D MMM YYYY');

        return response()->json([
            'kode' => 200,
            'total_omzet' => 'Rp ' . number_format($total_omzet, 0, ',', '.'),
            'total_transaksi' => $total_transaksi,
            'barang_terjual' => $barang_terjual,
            'pesan' => 'Data : '.$range,
        ]);
    }

    // ================= STATISTIK PELANGGAN =================

    // tabel statistik per pelanggan (jumlah transaksi, total barang, total belanja)
    public function statistik_pelanggan_tabel(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('pelanggan')->select(
                'pelanggan.id',
                'pelanggan.nama',
                'pelanggan.no_telp',
                DB::raw("(SELECT COUNT(*) FROM data_pembelian dp WHERE dp.id_pelanggan = pelanggan.id) as jumlah_transaksi"),
                DB::raw("(SELECT COALESCE(SUM(dp.total_belanja),0) FROM data_pembelian dp WHERE dp.id_pelanggan = pelanggan.id) as total_belanja"),
                DB::raw("(SELECT COALESCE(SUM(dpd.qty),0) FROM data_pembelian_detail dpd INNER JOIN data_pembelian dp ON dp.id_transaksi = dpd.id_transaksi WHERE dp.id_pelanggan = pelanggan.id) as total_barang")
            );
            return DataTables::of($data)
                ->addIndexColumn()
                ->make(true);
        }
    }

    // detail transaksi milik 1 pelanggan (untuk modal statistik) — bebas N+1
    public function pelanggan_transaksi(Request $request)
    {
        $id = $request->id_pelanggan;
        $pel = DB::table('pelanggan')->where('id', $id)->first();
        if (!$pel) {
            return response()->json(['kode' => 404, 'pesan' => 'Pelanggan tidak ditemukan!']);
        }

        // filter rentang tanggal (opsional) — biar hitungan ringan walau data banyak
        $mulai = $request->mulai;
        $selesai = $request->selesai;
        if ($mulai && $selesai && $mulai > $selesai) {
            [$mulai, $selesai] = [$selesai, $mulai];
        }

        // paginasi: tampilkan 10 terbaru, sisanya lewat "Muat lebih" (offset)
        $limit = 10;
        $offset = max(0, (int) $request->offset);

        $base = DB::table('data_pembelian')->where('id_pelanggan', $id);
        if ($mulai) {
            $base->whereDate('created_at', '>=', $mulai);
        }
        if ($selesai) {
            $base->whereDate('created_at', '<=', $selesai);
        }

        $totalTransaksi = (clone $base)->count();

        // hanya 10 baris per permintaan (bukan seluruh riwayat)
        $trx = (clone $base)->orderByDesc('created_at')
            ->offset($offset)->limit($limit)
            ->get(['id_transaksi', 'created_at', 'total_belanja']);

        // qty untuk 10 baris ini saja (1 query, bebas N+1)
        $ids = $trx->pluck('id_transaksi')->all();
        $qtyMap = count($ids)
            ? DB::table('data_pembelian_detail')->whereIn('id_transaksi', $ids)
                ->select('id_transaksi', DB::raw('SUM(qty) as qty'))
                ->groupBy('id_transaksi')->pluck('qty', 'id_transaksi')
            : collect();

        $list = $trx->map(function ($t) use ($qtyMap) {
            return [
                'id_transaksi' => $t->id_transaksi,
                'tanggal' => Carbon::parse($t->created_at)->isoFormat('D MMM YYYY HH:mm'),
                'total_belanja' => (int) $t->total_belanja,
                'qty' => (int) ($qtyMap[$t->id_transaksi] ?? 0),
            ];
        })->values();

        $resp = [
            'kode' => 200,
            'list' => $list,
            'total_transaksi' => $totalTransaksi,
            'has_more' => ($offset + $limit) < $totalTransaksi,
        ];

        // ringkasan (agregat penuh) hanya dihitung saat halaman pertama — hemat
        if ($offset === 0) {
            $totalBarang = (int) DB::table('data_pembelian as dp')
                ->join('data_pembelian_detail as dpd', 'dpd.id_transaksi', '=', 'dp.id_transaksi')
                ->where('dp.id_pelanggan', $id)
                ->when($mulai, fn ($q) => $q->whereDate('dp.created_at', '>=', $mulai))
                ->when($selesai, fn ($q) => $q->whereDate('dp.created_at', '<=', $selesai))
                ->sum('dpd.qty');

            $resp['nama'] = $pel->nama;
            $resp['no_telp'] = $pel->no_telp ?: '-';
            $resp['total_belanja'] = (int) (clone $base)->sum('total_belanja');
            $resp['total_barang'] = $totalBarang;
        }

        return response()->json($resp);
    }

    // ================= EXPORT LAPORAN (Excel / xlsx) =================

    // helper: isi 1 sheet (judul + header + baris) & rapikan
    private function isiSheet($sheet, string $tabTitle, string $judul, array $headers, array $rows, array $rupiahCols = [])
    {
        $sheet->setTitle($tabTitle);
        $Coord = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::class;
        $Fill = \PhpOffice\PhpSpreadsheet\Style\Fill::class;
        $colCount = count($headers);
        $lastCol = $Coord::stringFromColumnIndex($colCount);

        // judul
        $sheet->setCellValue('A1', $judul);
        $sheet->mergeCells('A1:'.$lastCol.'1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // header (baris 3)
        foreach ($headers as $i => $h) {
            $sheet->setCellValue($Coord::stringFromColumnIndex($i + 1).'3', $h);
        }
        $sheet->getStyle('A3:'.$lastCol.'3')->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A3:'.$lastCol.'3')->getFill()->setFillType($Fill::FILL_SOLID)->getStartColor()->setRGB('5D87FF');

        // data (mulai baris 4)
        $r = 4;
        foreach ($rows as $row) {
            $c = 1;
            foreach ($row as $val) {
                $sheet->setCellValueExplicit(
                    $Coord::stringFromColumnIndex($c).$r,
                    $val,
                    is_numeric($val) && !in_array($c, [1], true)
                        ? \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_NUMERIC
                        : \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                );
                $c++;
            }
            $r++;
        }
        $lastRow = max(3, $r - 1);
        // border tipis pada tabel
        $sheet->getStyle('A3:'.$lastCol.$lastRow)->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        // format rupiah
        foreach ($rupiahCols as $ci) {
            $col = $Coord::stringFromColumnIndex($ci);
            $sheet->getStyle($col.'4:'.$col.$lastRow)->getNumberFormat()->setFormatCode('#,##0');
        }
        // auto width
        for ($i = 1; $i <= $colCount; $i++) {
            $sheet->getColumnDimension($Coord::stringFromColumnIndex($i))->setAutoSize(true);
        }
    }

    public function export_laporan(Request $request)
    {
        $mulai = $request->mulai;
        $selesai = $request->selesai;
        if (!$mulai || !$selesai) {
            return response('Tanggal mulai & selesai wajib diisi.', 422);
        }
        if ($mulai > $selesai) { [$mulai, $selesai] = [$selesai, $mulai]; }

        // ---- Query agregat (semua bebas N+1) ----
        $transaksi = DB::table('data_pembelian')
            ->whereDate('created_at', '>=', $mulai)->whereDate('created_at', '<=', $selesai)
            ->orderBy('created_at')
            ->get(['id_transaksi', 'created_at', 'pembeli', 'nama_kasir', 'total_belanja', 'total_bayar', 'kembalian']);

        $detail = DB::table('data_pembelian_detail')
            ->whereDate('created_at', '>=', $mulai)->whereDate('created_at', '<=', $selesai)
            ->orderBy('created_at')
            ->get(['id_transaksi', 'created_at', 'nama_barang', 'satuan', 'qty', 'harga', 'total_harga']);

        $rekapProduk = DB::table('data_pembelian_detail')
            ->whereDate('created_at', '>=', $mulai)->whereDate('created_at', '<=', $selesai)
            ->select('nama_barang', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(total_harga) as total_omzet'))
            ->groupBy('nama_barang')->orderByDesc('total_omzet')->get();

        $rekapKasir = DB::table('data_pembelian')
            ->whereDate('created_at', '>=', $mulai)->whereDate('created_at', '<=', $selesai)
            ->select(DB::raw("COALESCE(NULLIF(nama_kasir,''),'-') as nama_kasir"), DB::raw('COUNT(*) as jml'), DB::raw('SUM(total_belanja) as omzet'))
            ->groupBy('nama_kasir')->orderByDesc('omzet')->get();

        $rekapPelanggan = DB::table('data_pembelian')
            ->whereDate('created_at', '>=', $mulai)->whereDate('created_at', '<=', $selesai)
            ->select(DB::raw("COALESCE(NULLIF(pembeli,''),'Umum') as pembeli"), DB::raw('COUNT(*) as jml'), DB::raw('SUM(total_belanja) as belanja'))
            ->groupBy('pembeli')->orderByDesc('belanja')->get();

        $rekapHarian = DB::table('data_pembelian')
            ->whereDate('created_at', '>=', $mulai)->whereDate('created_at', '<=', $selesai)
            ->select(DB::raw('DATE(created_at) as tgl'), DB::raw('COUNT(*) as jml'), DB::raw('SUM(total_belanja) as omzet'))
            ->groupBy('tgl')->orderBy('tgl')->get();

        // ---- Ringkasan ----
        $totalOmzet = (int) $transaksi->sum('total_belanja');
        $totalTransaksi = $transaksi->count();
        $totalBarang = (int) $detail->sum('qty');
        $jumlahPelanggan = $rekapPelanggan->count();
        $rataTransaksi = $totalTransaksi > 0 ? (int) round($totalOmzet / $totalTransaksi) : 0;
        $dataToko = $this->informasiToko();

        $fmtTgl = fn($t) => Carbon::parse($t)->isoFormat('D MMM YYYY HH:mm');
        $fmtTglSingkat = fn($t) => Carbon::parse($t)->isoFormat('D MMM YYYY');
        $periode = $fmtTglSingkat($mulai).' s/d '.$fmtTglSingkat($selesai);

        // ---- Bangun workbook ----
        $ss = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

        // Sheet 1: Ringkasan
        $s = $ss->getActiveSheet();
        $s->setTitle('Ringkasan');
        $s->setCellValue('A1', 'LAPORAN PENJUALAN — '.$dataToko['nama_toko']);
        $s->mergeCells('A1:B1');
        $s->getStyle('A1')->getFont()->setBold(true)->setSize(15);
        $ringkasan = [
            ['Periode', $periode],
            ['Total Omzet (Rp)', $totalOmzet],
            ['Jumlah Transaksi', $totalTransaksi],
            ['Barang Terjual', $totalBarang],
            ['Jumlah Pelanggan', $jumlahPelanggan],
            ['Rata-rata / Transaksi (Rp)', $rataTransaksi],
            ['Dibuat', Carbon::now()->isoFormat('D MMM YYYY HH:mm')],
        ];
        $r = 3;
        foreach ($ringkasan as $row) {
            $s->setCellValue('A'.$r, $row[0]);
            $s->setCellValue('B'.$r, $row[1]);
            $s->getStyle('A'.$r)->getFont()->setBold(true);
            $r++;
        }
        $s->getStyle('B4')->getNumberFormat()->setFormatCode('#,##0');
        $s->getStyle('B9')->getNumberFormat()->setFormatCode('#,##0');
        $s->getColumnDimension('A')->setAutoSize(true);
        $s->getColumnDimension('B')->setAutoSize(true);

        // Sheet 2: Per Transaksi
        $rows = $transaksi->map(fn($t) => [
            $t->id_transaksi, $fmtTgl($t->created_at), $t->pembeli ?: '-', $t->nama_kasir ?: '-',
            (int) $t->total_belanja, (int) $t->total_bayar, (int) $t->kembalian,
        ])->toArray();
        $this->isiSheet($ss->createSheet(), 'Per Transaksi', 'Per Transaksi — '.$periode,
            ['ID Transaksi', 'Tanggal', 'Pembeli', 'Kasir', 'Total Belanja', 'Bayar', 'Kembalian'],
            $rows, [5, 6, 7]);

        // Sheet 3: Detail Item
        $rows = $detail->map(fn($d) => [
            $d->id_transaksi, $fmtTgl($d->created_at), $d->nama_barang, $d->satuan,
            (int) $d->qty, (int) $d->harga, (int) $d->total_harga,
        ])->toArray();
        $this->isiSheet($ss->createSheet(), 'Detail Item', 'Detail Item — '.$periode,
            ['ID Transaksi', 'Tanggal', 'Nama Barang', 'Satuan', 'Qty', 'Harga', 'Total'],
            $rows, [6, 7]);

        // Sheet 4: Rekap Produk
        $rows = $rekapProduk->map(fn($p) => [$p->nama_barang, (int) $p->total_qty, (int) $p->total_omzet])->toArray();
        $this->isiSheet($ss->createSheet(), 'Rekap Produk', 'Rekap Produk — '.$periode,
            ['Nama Barang', 'Qty Terjual', 'Omzet'], $rows, [3]);

        // Sheet 5: Rekap Kasir
        $rows = $rekapKasir->map(fn($k) => [$k->nama_kasir, (int) $k->jml, (int) $k->omzet])->toArray();
        $this->isiSheet($ss->createSheet(), 'Rekap Kasir', 'Rekap Kasir — '.$periode,
            ['Kasir', 'Jumlah Transaksi', 'Omzet'], $rows, [3]);

        // Sheet 6: Rekap Pelanggan
        $rows = $rekapPelanggan->map(fn($p) => [$p->pembeli, (int) $p->jml, (int) $p->belanja])->toArray();
        $this->isiSheet($ss->createSheet(), 'Rekap Pelanggan', 'Rekap Pelanggan — '.$periode,
            ['Pelanggan', 'Jumlah Transaksi', 'Total Belanja'], $rows, [3]);

        // Sheet 7: Rekap Harian
        $rows = $rekapHarian->map(fn($h) => [$fmtTglSingkat($h->tgl), (int) $h->jml, (int) $h->omzet])->toArray();
        $this->isiSheet($ss->createSheet(), 'Rekap Harian', 'Rekap Harian — '.$periode,
            ['Tanggal', 'Jumlah Transaksi', 'Omzet'], $rows, [3]);

        $ss->setActiveSheetIndex(0);

        $namaFile = 'Laporan-Penjualan-'.$mulai.'_sd_'.$selesai.'.xlsx';
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($ss);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $namaFile, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    private function informasiToko() {
        // baca dari tabel 'pengaturan'; fallback ke default bila belum ada / tabel belum dimigrasi
        try {
            $rows = DB::table('pengaturan')
                ->whereIn('key', ['nama_toko', 'alamat_toko', 'no_telp', 'footer_nota', 'logo_toko', 'tampilkan_kasir'])
                ->pluck('value', 'key');
            $nama_toko = $rows['nama_toko'] ?? 'NAMA TOKO';
            $alamat_toko = $rows['alamat_toko'] ?? 'ALAMAT';
            $no_telp = $rows['no_telp'] ?? '';
            $footer_nota = $rows['footer_nota'] ?? '';
            $logo = $rows['logo_toko'] ?? '';
            $tampilkan_kasir = array_key_exists('tampilkan_kasir', $rows->toArray()) ? (int) $rows['tampilkan_kasir'] : 1;
        } catch (\Exception $e) {
            $nama_toko = 'NAMA TOKO';
            $alamat_toko = 'ALAMAT';
            $no_telp = '';
            $footer_nota = '';
            $logo = '';
            $tampilkan_kasir = 1;
        }

        // pastikan file logo masih ada
        if ($logo && !is_file(public_path('uploads/'.$logo))) {
            $logo = '';
        }

        $data = [
            'nama_toko' => $nama_toko,
            'alamat_toko' => $alamat_toko,
            'no_telp' => $no_telp,
            'footer_nota' => $footer_nota,
            'logo' => $logo,
            'logo_url' => $logo ? asset('uploads/'.$logo) : '',
            'tampilkan_kasir' => $tampilkan_kasir,
        ];

        return $data;
    }

    // ================= PENGATURAN (setelan toko) =================

    // simpan pasangan key-value ke tabel 'pengaturan' (upsert)
    private function simpanPengaturan(array $data)
    {
        $timestamp = Carbon::now();
        foreach ($data as $key => $value) {
            $exists = DB::table('pengaturan')->where('key', $key)->exists();
            if ($exists) {
                DB::table('pengaturan')->where('key', $key)->update([
                    'value' => $value,
                    'updated_at' => $timestamp,
                ]);
            } else {
                DB::table('pengaturan')->insert([
                    'key' => $key,
                    'value' => $value,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ]);
            }
        }
    }

    public function pengaturan()
    {
        $data = $this->informasiToko();
        return view('menu.pengaturan', $data);
    }

    public function update_pengaturan(Request $request)
    {
        $messages = [
            'nama_toko.required' => 'Nama toko tidak boleh kosong!',
        ];
        $messages['logo.image'] = 'File logo harus berupa gambar!';
        $messages['logo.mimes'] = 'Logo harus format JPG, PNG, atau WEBP!';
        $messages['logo.max'] = 'Ukuran logo maksimal 2 MB!';
        $validator = Validator::make($request->all(), [
            'nama_toko' => ['required'],
            'alamat_toko' => ['nullable'],
            'no_telp' => ['nullable'],
            'footer_nota' => ['nullable'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], $messages);
        if ($validator->fails()) {
            return response()->json([
                'kode' => 422,
                'pesan' => $validator->errors(),
            ]);
        }

        $this->simpanPengaturan([
            'nama_toko' => $request->nama_toko,
            'alamat_toko' => $request->alamat_toko,
            'no_telp' => $request->no_telp,
            'footer_nota' => $request->footer_nota,
            'tampilkan_kasir' => $request->tampilkan_kasir == '1' ? '1' : '0',
        ]);

        // ===== LOGO (disimpan di public/uploads, bukan storage) =====
        $dir = public_path('uploads');
        $logoLama = DB::table('pengaturan')->where('key', 'logo_toko')->value('value');

        if ($request->hapus_logo == '1') {
            if ($logoLama && is_file($dir.'/'.$logoLama)) {
                @unlink($dir.'/'.$logoLama);
            }
            $this->simpanPengaturan(['logo_toko' => null]);
        } elseif ($request->hasFile('logo')) {
            if (!is_dir($dir)) {
                @mkdir($dir, 0755, true);
            }
            if ($logoLama && is_file($dir.'/'.$logoLama)) {
                @unlink($dir.'/'.$logoLama);
            }
            $file = $request->file('logo');
            $namaFile = 'logo-'.time().'.'.strtolower($file->getClientOriginalExtension());
            $file->move($dir, $namaFile);
            $this->simpanPengaturan(['logo_toko' => $namaFile]);
        }

        return response()->json([
            'kode' => 200,
            'pesan' => 'Pengaturan berhasil disimpan!',
        ]);
    }

    // ================= PENGATURAN EMAIL (SMTP) =================

    // baca konfigurasi email dari tabel 'pengaturan' (password tidak dikembalikan)
    private function pengaturanEmail()
    {
        try {
            $rows = DB::table('pengaturan')
                ->whereIn('key', ['mail_host', 'mail_port', 'mail_username', 'mail_encryption', 'mail_from_address', 'mail_from_name'])
                ->pluck('value', 'key');
            $hasPassword = (bool) DB::table('pengaturan')->where('key', 'mail_password')->value('value');
        } catch (\Exception $e) {
            $rows = collect();
            $hasPassword = false;
        }

        return [
            'mail_host' => $rows['mail_host'] ?? '',
            'mail_port' => $rows['mail_port'] ?? '587',
            'mail_username' => $rows['mail_username'] ?? '',
            'mail_encryption' => $rows['mail_encryption'] ?? 'tls',
            'mail_from_address' => $rows['mail_from_address'] ?? '',
            'mail_from_name' => $rows['mail_from_name'] ?? ($this->informasiToko()['nama_toko'] ?? 'Kasir'),
            'has_password' => $hasPassword,
        ];
    }

    public function pengaturan_email()
    {
        $data = $this->pengaturanEmail();
        return view('menu.pengaturan_email', $data);
    }

    public function update_pengaturan_email(Request $request)
    {
        $messages = [
            'mail_host.required' => 'SMTP Host tidak boleh kosong!',
            'mail_port.required' => 'SMTP Port tidak boleh kosong!',
            'mail_port.numeric' => 'SMTP Port harus angka!',
            'mail_encryption.in' => 'Enkripsi tidak valid!',
            'mail_from_address.required' => 'Email pengirim tidak boleh kosong!',
            'mail_from_address.email' => 'Email pengirim tidak valid!',
            'mail_from_name.required' => 'Nama pengirim tidak boleh kosong!',
        ];
        $validator = Validator::make($request->all(), [
            'mail_host' => ['required'],
            'mail_port' => ['required', 'numeric'],
            'mail_username' => ['nullable'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_address' => ['required', 'email'],
            'mail_from_name' => ['required'],
        ], $messages);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        $save = [
            'mail_host' => trim($request->mail_host),
            'mail_port' => trim($request->mail_port),
            'mail_username' => trim((string) $request->mail_username),
            'mail_encryption' => $request->mail_encryption ?: 'tls',
            'mail_from_address' => trim($request->mail_from_address),
            'mail_from_name' => trim($request->mail_from_name),
        ];
        // password hanya diperbarui bila diisi (kosong = biarkan yang lama)
        if ($request->filled('mail_password')) {
            $save['mail_password'] = Crypt::encryptString($request->mail_password);
        }

        $this->simpanPengaturan($save);

        return response()->json(['kode' => 200, 'pesan' => 'Pengaturan email berhasil disimpan!']);
    }

    public function test_email(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tujuan' => ['required', 'email'],
            'mail_host' => ['required'],
            'mail_port' => ['required', 'numeric'],
            'mail_from_address' => ['required', 'email'],
        ], [
            'tujuan.required' => 'Email tujuan tidak boleh kosong!',
            'tujuan.email' => 'Email tujuan tidak valid!',
            'mail_host.required' => 'Isi & simpan SMTP Host dulu!',
            'mail_from_address.required' => 'Isi email pengirim dulu!',
        ]);
        if ($validator->fails()) {
            return response()->json(['kode' => 422, 'pesan' => $validator->errors()]);
        }

        // password: pakai input bila diisi, kalau tidak ambil yang tersimpan
        $password = $request->mail_password;
        if (!$request->filled('mail_password')) {
            $stored = DB::table('pengaturan')->where('key', 'mail_password')->value('value');
            if ($stored) {
                try { $password = Crypt::decryptString($stored); } catch (\Exception $e) { $password = ''; }
            }
        }
        $encryption = $request->mail_encryption === 'none' ? null : ($request->mail_encryption ?: 'tls');

        // terapkan konfigurasi live untuk uji coba
        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => trim($request->mail_host),
            'mail.mailers.smtp.port' => (int) $request->mail_port,
            'mail.mailers.smtp.username' => trim((string) $request->mail_username),
            'mail.mailers.smtp.password' => $password,
            'mail.mailers.smtp.encryption' => $encryption,
            'mail.from.address' => trim($request->mail_from_address),
            'mail.from.name' => trim((string) $request->mail_from_name) ?: 'Kasir',
        ]);

        try {
            Mail::raw('Ini email percobaan dari aplikasi Kasir. Jika Anda menerima pesan ini, konfigurasi SMTP sudah benar. 🎉', function ($m) use ($request) {
                $m->to($request->tujuan)->subject('Test Email - Aplikasi Kasir');
            });
            return response()->json(['kode' => 200, 'pesan' => 'Email percobaan terkirim ke '.$request->tujuan.'! Cek inbox/spam.']);
        } catch (\Exception $e) {
            return response()->json(['kode' => 500, 'pesan' => 'Gagal kirim: '.$e->getMessage()]);
        }
    }

    // ================= QZ TRAY =================

    // tanda tangani request QZ Tray dengan private key (SHA512withRSA)
    public function qz_sign(Request $request)
    {
        $toSign = (string) $request->input('request', '');
        $keyPath = public_path('qz-tray/private-key.pem');
        if (!is_file($keyPath)) {
            return response('', 500);
        }
        $pkey = openssl_pkey_get_private(file_get_contents($keyPath));
        if (!$pkey) {
            return response('', 500);
        }
        $signature = '';
        openssl_sign($toSign, $signature, $pkey, OPENSSL_ALGO_SHA512);
        return response(base64_encode($signature), 200)
            ->header('Content-Type', 'text/plain');
    }

}
