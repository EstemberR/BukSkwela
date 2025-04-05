@extends('layout.layoutTemplate')

@section('title', 'Super Admin Dashboard')

@section('body-class', 'superadmin-dashboard')

@section('styles')
<style>
    .dashboard-container {
        padding: 20px;
    }
    .card {
        background: white;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .header {
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container dashboard-container">
    <div class="header">
        <h2 class="page-title">Super Admin Dashboard</h2>
    </div>

    <div class="card">
        <h3>Tenancy Management</h3>
        <div class="content">
            <!-- Add your tenancy management content here -->
        </div>
    </div>

    <form method="POST" action="{{ route('superadmin.logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    // Add any dashboard-specific JavaScript here
</script>
@endsection