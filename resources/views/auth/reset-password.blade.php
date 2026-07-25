@extends('layouts.auth')
@section('title', 'Reset Password')

@section('content')
    @include('partials.auth-logo')

    <p class="text-center text-muted mb-4">Buat password baru untuk akun Anda.</p>

    <form action="{{ route('password.store') }}" method="POST" autocomplete="off">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $request->email) }}" placeholder="Masukan email anda .." required autofocus>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password Baru</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="*****" required>
        </div>
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="*****" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Simpan Password</button>
        <div class="d-flex align-items-center justify-content-center">
            <a class="text-primary fw-medium" href="{{ route('login') }}">Kembali masuk</a>
        </div>
    </form>
@endsection
