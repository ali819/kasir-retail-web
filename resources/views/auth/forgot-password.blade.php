@extends('layouts.auth')
@section('title', 'Lupa Password')

@section('content')
    @include('partials.auth-logo')

    <p class="text-center text-muted mb-4">Masukkan email akun Anda. Kami akan mengirim tautan untuk mengatur ulang password.</p>

    @if (session('status'))
        <div class="alert alert-success py-2 mb-3">{{ session('status') }}</div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" autocomplete="off">
        @csrf
        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Masukan email anda .." required autofocus>
        </div>
        <button type="submit" class="btn btn-primary w-100 py-8 mb-4 rounded-2">Kirim Tautan Reset</button>
        <div class="d-flex align-items-center justify-content-center">
            <p class="fs-4 mb-0 fw-medium">Ingat password ?</p>
            <a class="text-primary fw-medium ms-2" href="{{ route('login') }}">Kembali masuk</a>
        </div>
    </form>
@endsection
