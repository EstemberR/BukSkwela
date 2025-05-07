@extends('SuperAdmin.layouts.app')

@section('title', 'Profile')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Profile</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('superadmin.account.update-profile') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-initial rounded-circle bg-primary me-3" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $user->name }}</h6>
                            <small class="text-muted">{{ $user->email }}</small>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-2">
                        <small class="text-muted">Role</small>
                        <div>
                            <span class="badge bg-info">{{ ucfirst($user->role) }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Member Since</small>
                        <div>{{ $user->created_at->format('M d, Y') }}</div>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted">Last Updated</small>
                        <div>{{ $user->updated_at->format('M d, Y') }}</div>
                    </div>
                    
                    <hr>
                    
                    <a href="{{ route('superadmin.account.change-password') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-key"></i> Change Password
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 