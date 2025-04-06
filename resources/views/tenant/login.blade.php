@extends('tenant.layouts.app')

@section('title', 'Login')

@section('body-class', 'hold-transition login-page')

@section('content')
<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>{{ tenant('id') }}</b></a>
    </div>

    <div class="login-box-body">
        <p class="login-box-msg">Sign in to {{ tenant('id') }}.{{ env('CENTRAL_DOMAIN') }}</p>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.login.post') }}">
            @csrf
            <div class="form-group">
                <input type="email" class="form-control" name="email" placeholder="Email" required>
                @error('email')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" name="remember"> Remember Me
                        </label>
                    </div>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </div>
        </form>
        
        <div class="mt-3 text-center">
            <a href="{{ route('tenant.status') }}" class="text-primary">
                <i class="fas fa-info-circle"></i> Check Account Status
            </a>
        </div>
    </div>
</div>
@endsection