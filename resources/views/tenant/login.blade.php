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

<!-- Debug to verify the error message is being set -->
@if(session('error'))
    <!-- Hidden debug info -->
    <div style="display: none;" id="debug-info">
        Error: "{{ session('error') }}"
        Is target error: {{ session('error') == 'Invalid credentials for this tenant.' ? 'Yes' : 'No' }}
    </div>
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

        @if(session('error') && session('error') != 'Invalid credentials for this tenant.')
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

<!-- Ensure jQuery is loaded before showing modal -->
@if(!isset($jQueryLoaded))
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endif

<!-- Simple Error Modal -->
@if(session('error') == 'Invalid credentials for this tenant.')
<!-- Modal HTML structure -->
<div class="modal fade" id="invalidCredentialsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Authentication Error</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <div class="my-3">
                    <i class="fas fa-exclamation-circle text-danger" style="font-size: 60px;"></i>
                </div>
                <p class="lead">Invalid credentials for this tenant.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Try Again</button>
            </div>
        </div>
    </div>
</div>

<!-- Script to explicitly show the modal -->
<script>
    // Wait for document ready and ensure jQuery is loaded
    window.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            try {
                $('#invalidCredentialsModal').modal('show');
                console.log('Modal should be showing now');
            } catch(err) {
                console.error('Error showing modal:', err);
            }
        }, 500); // Small delay to ensure everything is loaded
    });
</script>
@endif

@endsection