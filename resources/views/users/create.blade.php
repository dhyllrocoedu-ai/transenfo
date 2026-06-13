@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<h1 class="h3 mb-4">Add User</h1>
<div class="card stat-card"><div class="card-body">
    <form method="POST" action="{{ route('users.store') }}">@csrf
        @include('users._form')
        <button type="submit" class="btn btn-primary">Create User</button>
        <a href="{{ route('users.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div></div>
@endsection
