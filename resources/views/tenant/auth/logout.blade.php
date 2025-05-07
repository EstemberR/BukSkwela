@extends('tenant.layouts.app')

@section('title', 'Logout')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-3 text-center">
                    <form method="POST" action="{{ route('tenant.logout') }}">
                        @csrf
                        <h6 class="mb-3">Are you sure?</h6>
                        <div class="d-flex justify-content-center gap-2">
                            <button type="submit" class="btn btn-sm btn-danger px-3">
                                Logout
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-sm btn-light px-3">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 