@extends('layouts.app')

@section('title', 'Register Vehicle')

@section('content')
<h1 class="h3 mb-4">Register Vehicle</h1>
<div class="card stat-card"><div class="card-body">
    <form method="POST" action="{{ route('vehicles.store') }}">@csrf
        @include('vehicles._form')
        <button type="submit" class="btn btn-primary">Save Vehicle</button>
        <a href="{{ route('vehicles.index') }}" class="btn btn-link">Cancel</a>
    </form>
</div></div>
@endsection
