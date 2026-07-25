@extends('layouts.main')
@section('content')
@section('title', 'Kelola User')

<style>
    .table-responsive { padding-bottom: 8px; }
</style>

{{-- MAIN --}}
<div class="container-fluid">
    <div class="card bg-light-info shadow-none position-relative overflow-hidden">
        <div class="card-body px-4 py-3">
            <div class="row align-items-center">
                <div class="col-9">
                    <h4 class="fw-semibold mb-8">Kelola User</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a class="text-muted" href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">Kelola User</li>
                        </ol>
                    </nav>
                </div>
                <div class="col-3"></div>
            </div>
        </div>
        <div class="col-12">
            <div class="progress">
                <div class="progress-bar progress-bar-striped bg-primary progress-bar-animated animasiProgressBar" role="progressbar" style="width: 0%"></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="alert customize-alert text-primary border border-primary" role="alert">
                <div class="font-medium">
                    <i class="ti ti-info-circle fs-5 me-2 flex-shrink-0"></i>
                    Kelola user <b>&nbsp;Admin&nbsp;</b>, <b>&nbsp;Staf Stok&nbsp;</b> &amp; <b>&nbsp;Kasir&nbsp;</b>. Sistem menjaga <b>&nbsp;minimal 1 admin&nbsp;</b> selalu ada.
                </div>
            </div>
        </div>
    </div>

    <div class="w-100 position-relative overflow-hidden">
        <div class="py-3">
            <div class="row g-2 align-items-end">
                <div class="col-md-9 col-12">
                    <label for="cariNamaUser" style="margin-bottom:5px;"><b>Cari User</b></label>
                    <input type="search" class="form-control" id="cariNamaUser" placeholder="Ketik nama / email..">
                </div>
                <div class="col-md-3 col-12">
                    <button type="button" class="btn btn-primary col-12 btnTambahUser" style="font-weight:bold"><i class="ti ti-user-plus"></i>&nbsp; Tambah User</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle text-nowrap mb-0" id="tabel_user" style="width:100%;">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH --}}
<div class="modal fade" id="ModalTambahUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title">Tambah User</h4></div>
            <form id="FormTambahUser" autocomplete="off">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="mb-1"><b>Nama</b></label>
                            <input type="text" class="form-control" name="name" id="tbhNamaUser" placeholder="Nama lengkap .." required>
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Email</b></label>
                            <input type="email" class="form-control" name="email" id="tbhEmailUser" placeholder="email@toko.com" required>
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Role</b></label>
                            <select class="form-control" name="role" id="tbhRoleUser" required>
                                <option value="kasir">Kasir</option>
                                <option value="stok">Staf Stok</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="mb-1"><b>Password</b></label>
                            <input type="password" class="form-control" name="password" id="tbhPasswordUser" placeholder="Min. 6 karakter" required>
                        </div>
                        <div class="col-md-6">
                            <label class="mb-1"><b>Konfirmasi Password</b></label>
                            <input type="password" class="form-control" name="password_confirmation" id="tbhPasswordConfirmUser" placeholder="Ulangi password" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal&emsp;<i class="ti ti-x"></i></button>
                    <button type="submit" class="btn btn-primary btnKonfirmasiTambahUser"><i class="ti ti-check"></i>&emsp;Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL UPDATE --}}
<div class="modal fade" id="ModalUpdateUser" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4 class="modal-title" id="updateTitleUser">Ubah User</h4></div>
            <form id="FormUpdateUser" autocomplete="off">
                @csrf
                <input type="hidden" name="id" id="updateIdUser">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="mb-1"><b>Nama</b></label>
                            <input type="text" class="form-control" name="name" id="updateNamaUser" required>
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Email</b></label>
                            <input type="email" class="form-control" name="email" id="updateEmailUser" required>
                        </div>
                        <div class="col-12">
                            <label class="mb-1"><b>Role</b></label>
                            <select class="form-control" name="role" id="updateRoleUser" required>
                                <option value="kasir">Kasir</option>
                                <option value="stok">Staf Stok</option>
                                <option value="admin">Admin</option>
                            </select>
                            <small id="noteRoleSelf" class="text-warning d-none"><i class="ti ti-lock"></i> Role akun sendiri tidak bisa diubah.</small>
                        </div>
                    </div>
                    <small class="text-muted d-block mt-2">Untuk mengubah password, gunakan tombol <b>reset password</b> di tabel.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Batal&emsp;<i class="ti ti-x"></i></button>
                    <button type="submit" class="btn btn-primary btnKonfirmasiUpdateUser"><i class="ti ti-check"></i>&emsp;Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function escapeHtml(t){ if(t==null) return ''; return String(t).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

    // ====== DATATABLE ======
    var tabel_user;
    $(document).ready(function () {
        tabel_user = $("#tabel_user").DataTable({
            dom: 'lrtip',
            order: [[0, 'ASC']],
            autoWidth: false,
            language: {
                "sProcessing":"Sedang memproses...","sLengthMenu":"Tampilkan _MENU_ entri","sZeroRecords":"Tidak ditemukan data yang sesuai",
                "loadingRecords":"Memuat data, mohon tunggu ...","sInfo":"Total : <b>_TOTAL_</b> entri","sInfoEmpty":"Menampilkan : <b>0</b> entri",
                "sInfoFiltered":"","sSearch":"Cari : ",
                "oPaginate":{"sFirst":"Pertama","sPrevious":"<i class='ti ti-chevron-left'></i>","sNext":"<i class='ti ti-chevron-right'></i>","sLast":"Terakhir"}
            },
            serverSide: true,
            ajax: { url: "{{ route('kelola_user_tabel') }}", type: 'GET' },
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                {
                    data: 'name',
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center gap-2">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light-primary text-primary" style="width:40px;height:40px;"><i class="ti ti-user fs-5"></i></span>
                            <div><h6 class="fw-semibold mb-0 fs-4">${escapeHtml(data)}</h6>
                            <span class="text-muted fs-2">${escapeHtml(row.email)}</span></div></div>`;
                    }
                },
                {
                    data: 'role',
                    render: function (data, type, row) {
                        var cls = data === 'admin' ? 'bg-light-danger text-danger'
                                : (data === 'stok' ? 'bg-light-warning text-warning' : 'bg-light-primary text-primary');
                        return `<span class="badge ${cls}">${escapeHtml(row.role_label)}</span>`;
                    }
                },
                {
                    data: 'created_at',
                    searchable: false,
                    render: function (data) {
                        if (!data) return '<span class="text-muted">-</span>';
                        var d = new Date(data.replace(' ', 'T'));
                        if (isNaN(d)) return escapeHtml(data);
                        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                    }
                },
                {
                    data: 'id',
                    orderable: false, searchable: false, className: 'text-center',
                    render: function (data, type, row) {
                        var selfBadge = row.is_self
                            ? ' <span class="badge bg-light-success text-success">Anda</span>'
                            : '';
                        // hapus disembunyikan utk akun sendiri
                        var delBtn = row.is_self
                            ? ''
                            : `<button type="button" class="btnHapusUser btn btn-danger btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-id="${data}" title="Hapus"><i class="ti ti-trash"></i></button>`;
                        return `
                        <button type="button" class="btnEditUser btn btn-primary btn-circle btn-lg d-inline-flex align-items-center justify-content-center" title="Ubah"><i class="ti ti-pencil"></i></button>
                        <button type="button" class="btnResetPass btn btn-warning btn-circle btn-lg d-inline-flex align-items-center justify-content-center" data-id="${data}" data-nama="${escapeHtml(row.name)}" title="Reset Password"><i class="ti ti-key"></i></button>
                        ${delBtn}${selfBadge}`;
                    }
                }
            ],
            drawCallback: function () { animasiProgressBar_stop(); }
        });

        let cariTimer = null;
        $('#cariNamaUser').on('input', function () {
            clearTimeout(cariTimer);
            cariTimer = setTimeout(function () { animasiProgressBar_run(); tabel_user.search($('#cariNamaUser').val()).draw(); }, 400);
        });
    });

    // ====== TAMBAH ======
    $(document).on('click', '.btnTambahUser', function () {
        $('#FormTambahUser')[0].reset();
        $('#ModalTambahUser').modal('show');
    });
    $('#FormTambahUser').on('submit', function (e) {
        e.preventDefault();
        customConfirm("Tambah user ?", "Pastikan data sudah benar.").then((confirmed) => {
            if (!confirmed) return;
            var btn = $('.btnKonfirmasiTambahUser').html();
            $('.btnKonfirmasiTambahUser').html('Memproses ..').attr('disabled', true);
            $.ajax({
                type: "POST", url: "{{ route('kelola_user_tambah') }}", data: $(this).serialize(), dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { $('#ModalTambahUser').modal('hide'); tabel_user.draw(); toastSuccess(r.pesan); }
                    else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); },
                complete: function () { $('.btnKonfirmasiTambahUser').html(btn).attr('disabled', false); }
            });
        });
    });

    // ====== EDIT ======
    $(document).on('click', '.btnEditUser', function () {
        var row = tabel_user.row($(this).parents('tr')).data();
        if (!row) { toastError("Data tidak ditemukan, muat ulang halaman!"); return; }
        $('#updateIdUser').val(row.id);
        $('#updateTitleUser').html('Ubah: ' + row.name);
        $('#updateNamaUser').val(row.name);
        $('#updateEmailUser').val(row.email);
        $('#updateRoleUser').val(row.role);
        // role akun sendiri tidak boleh diubah
        if (row.is_self) {
            $('#updateRoleUser').prop('disabled', true);
            $('#noteRoleSelf').removeClass('d-none');
        } else {
            $('#updateRoleUser').prop('disabled', false);
            $('#noteRoleSelf').addClass('d-none');
        }
        $('#ModalUpdateUser').modal('show');
    });
    $('#FormUpdateUser').on('submit', function (e) {
        e.preventDefault();
        customConfirm("Perbarui user ?", "Pastikan data sudah benar.").then((confirmed) => {
            if (!confirmed) return;
            // pastikan role ikut terkirim walau select di-disable (akun sendiri)
            $('#updateRoleUser').prop('disabled', false);
            var payload = $(this).serialize();
            var btn = $('.btnKonfirmasiUpdateUser').html();
            $('.btnKonfirmasiUpdateUser').html('Memproses ..').attr('disabled', true);
            $.ajax({
                type: "POST", url: "{{ route('kelola_user_update') }}", data: payload, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { $('#ModalUpdateUser').modal('hide'); tabel_user.draw(); toastSuccess(r.pesan); }
                    else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); },
                complete: function () { $('.btnKonfirmasiUpdateUser').html(btn).attr('disabled', false); }
            });
        });
    });

    // ====== RESET PASSWORD ======
    $(document).on('click', '.btnResetPass', function () {
        var id = $(this).data('id');
        var nama = $(this).data('nama');
        Swal.fire({
            title: 'Reset Password',
            html: 'Password baru untuk <b>' + escapeHtml(String(nama)) + '</b>',
            input: 'password',
            inputPlaceholder: 'Min. 6 karakter',
            inputAttributes: { autocapitalize: 'off', autocorrect: 'off' },
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: 'Simpan',
            cancelButtonText: 'Batal',
            preConfirm: function (val) {
                if (!val || val.length < 6) {
                    Swal.showValidationMessage('Password minimal 6 karakter');
                    return false;
                }
                return val;
            }
        }).then(function (result) {
            if (!result.isConfirmed) return;
            $.ajax({
                type: "POST", url: "{{ route('kelola_user_reset') }}",
                data: { _token: "{{ csrf_token() }}", id: id, password: result.value }, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { toastSuccess(r.pesan); }
                    else if (r.kode == 422) { errorHandlerToast(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); }
            });
        });
    });

    // ====== HAPUS ======
    $(document).on('click', '.btnHapusUser', function () {
        var id = $(this).data('id');
        customConfirm("Hapus user ?", "User ini tidak bisa login lagi setelah dihapus.").then((confirmed) => {
            if (!confirmed) return;
            $.ajax({
                type: "DELETE", url: "{{ route('kelola_user_hapus') }}",
                data: { _token: "{{ csrf_token() }}", id: id }, dataType: "JSON",
                success: function (r) {
                    if (r.kode == 200) { tabel_user.draw(); toastSuccess(r.pesan); }
                    else { toastError(r.pesan); }
                },
                error: function () { toastError("Oops! Silahkan coba lagi!"); }
            });
        });
    });
</script>

@endsection
