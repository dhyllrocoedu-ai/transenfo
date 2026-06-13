@extends('layouts.app')

@section('title', 'Edit Driver')

@section('content')
<h1 class="h3 mb-4">Edit Driver</h1>
<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('drivers.update', $driver) }}">
            @csrf @method('PUT')
            @include('drivers._form', ['driver' => $driver])
            <button type="submit" class="btn btn-primary">Update Driver</button>
            <a href="{{ route('drivers.show', $driver) }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection
