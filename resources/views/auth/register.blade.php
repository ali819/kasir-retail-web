@extends('layouts.auth')
@section('title', 'Register')

@push('head')
<script src="{{ asset('template/qrcode/qrcode.min.js') }}"></script>
@endpush

@section('content')
    @include('partials.auth-logo', ['qr' => true])

    <div class="alert customize-alert text-primary border border-primary py-2 mb-4" role="alert">
        <i class="ti ti-info-circle me-1"></i> Akun pertama ini otomatis menjadi <b>Admin</b>. Setelah ini, akun baru dibuat lewat menu <b>Kelola User</b>.
    </div>

    <form action="{{ route('register') }}" method="POST" autocomplete="off">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">Nama</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Masukan nama anda .." required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Masukan email anda .." required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="*****" required>
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Password Konfirmasi</label>
            <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="*****" required>
        </div>
        <button type="submit" id="submitButton" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Daftar</button>
        <div class="d-flex align-items-center justify-content-center">
            <p class="fs-4 mb-0 fw-medium">Sudah daftar ?</p>
            <a class="text-primary fw-medium ms-2" href="{{ route('login') }}">Masuk</a>
        </div>
    </form>
@endsection

@section('modals')
    @include('partials.qr-modal')
@endsection
