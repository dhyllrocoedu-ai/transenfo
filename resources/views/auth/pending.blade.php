@extends('layouts.guest')

@section('title', 'Account Pending Approval')

@section('content')
<div class="text-center animate-on-load">
    <div class="mb-4">
        <i class="bi bi-hourglass-split" style="font-size: 4rem; color: var(--bs-warning);"></i>
    </div>
    <h4 class="mb-2">Account Pending Approval</h4>
    <p class="text-muted mb-4">
        Your account is currently pending approval from an administrator.
        You will be notified once your account has been approved.
    </p>
    <div class="alert alert-info text-start" role="alert">
        <i class="bi bi-info-circle me-2"></i>
        While waiting, please ensure your contact information is correct. 
        You will receive a notification once your account is activated.
    </div>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-secondary">Back to Login</button>
    </form>
</div>
@endsection
