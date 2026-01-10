@extends('layouts.auth')

@section('Login_title', 'Login')

@section('login_content')
<div class="d-flex align-items-center justify-content-center vh-100">
    <div class="card shadow p-4 login-card" style="min-width: 400px;">
        <h3 class="mb-4 text-center">Login</h3>

        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" autocomplete="on">
            @csrf
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" value="1" class="form-check-input" id="remember" 
                       {{ old('remember') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Remember Me</label>
            </div>
            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>
</div>
@endsection
