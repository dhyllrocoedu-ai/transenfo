@extends('layouts.app')

@section('title', 'Edit Vehicle')

@section('content')
<h1 class="h3 mb-4">Edit Vehicle — {{ $vehicle->plate_number }}</h1>
<div class="card stat-card"><div class="card-body">
    <form method="POST" action="{{ route('vehicles.update', $vehicle) }}">@csrf @method('PUT')
        @include('vehicles._form')
        <button type="submit" class="btn btn-primary">Update Vehicle</button>
        <a href="{{ route('vehicles.show', $vehicle) }}" class="btn btn-link">Cancel</a>
    </form>
</div></div>
@endsection
