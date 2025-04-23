@extends('tenant.layouts.app')

@section('title', 'Login')

@section('body-class', 'hold-transition login-page')

@section('content')
<!-- Check for and remove show_approval_modal session if email is a student email -->
@if(session('show_approval_modal') && old('email') && Str::contains(old('email'), '@student.buksu.edu.ph'))
    @php
        session()->forget('show_approval_modal');
    @endphp
@endif

<div class="login-box">
    <div class="login-logo">
        <a href="#"><b>{{ tenant('id') }}</b></a>
    </div>

    <div class="login-box-body">
        <p class="login-box-msg">Sign in to {{ tenant('id') }}.{{ env('CENTRAL_DOMAIN') }}</p>
        <div class="text-center mb-3">
            <span class="badge bg-info">Students & Staff Portal</span>
        </div>

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

        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0 ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('tenant.login.post') }}" id="loginForm">
            @csrf
            <div class="form-group">
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') }}" required>
                <small class="form-text text-muted">
                    Students: Use your @student.buksu.edu.ph email
                </small>
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
      

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const emailInput = document.getElementById('email');
        const loginForm = document.getElementById('loginForm');
        
        if (emailInput && loginForm) {
            // Check if email is student email
            const checkStudentEmail = function() {
                const email = emailInput.value || '';
                if (email.includes('@student.buksu.edu.ph')) {
                    console.log('Student email detected:', email);
                    // Set data attribute on form
                    loginForm.setAttribute('data-student-email', 'true');
                    
                    // Store in sessionStorage
                    sessionStorage.setItem('isStudentEmail', 'true');
                    
                    // Hide modal if it's currently shown
                    const modalElement = document.getElementById('tenantApprovalModal');
                    if (modalElement) {
                        const bsModal = bootstrap.Modal.getInstance(modalElement);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    }
                } else {
                    loginForm.removeAttribute('data-student-email');
                    sessionStorage.removeItem('isStudentEmail');
                }
            };
            
            // Check on input
            emailInput.addEventListener('input', checkStudentEmail);
            
            // Check on initial load
            checkStudentEmail();
        }
    });
</script>
@endsection