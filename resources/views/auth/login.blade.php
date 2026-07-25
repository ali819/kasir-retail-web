@extends('layouts.auth')
@section('title', 'Login')

@push('head')
<script src="{{ asset('template/qrcode/qrcode.min.js') }}"></script>
@endpush

@section('content')
    @include('partials.auth-logo', ['qr' => true])

    @if (session('status'))
        <div class="alert alert-info py-2 mb-3">{{ session('status') }}</div>
    @endif

    <form action="{{ route('login') }}" method="POST" autocomplete="off">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Masukan email anda .." required>
        </div>
        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" name="password" id="password" placeholder="Masukan password .." required>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
                <input class="form-check-input primary" type="checkbox" name="remember" id="remember_me">
                <label class="form-check-label text-dark" for="remember_me">Ingat info login ?</label>
            </div>
            <a class="text-primary fw-medium" href="{{ route('password.request') }}">Lupa password ?</a>
        </div>
        <button type="submit" id="submitButton" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Masuk</button>
        @if (\App\Models\User::count() === 0)
        <div class="d-flex align-items-center justify-content-center">
            <p class="fs-4 mb-0 fw-medium">Belum ada akun ?</p>
            <a class="text-primary fw-medium ms-2" href="{{ route('register') }}">Buat akun admin</a>
        </div>
        @endif
    </form>
@endsection

@section('modals')
    @include('partials.qr-modal')
@endsection
