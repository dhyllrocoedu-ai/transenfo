@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<h1 class="h3 mb-4">Edit User — {{ $user->name }}</h1>
<div class="card stat-card"><div class="card-body">
    <form method="POST" action="{{ route('users.update', $user) }}">@csrf @method('PUT')
        @include('users._form')
        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="{{ route('users.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div></div>
@endsection
