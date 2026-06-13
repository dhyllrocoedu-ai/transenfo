@extends('layouts.app')

@section('title', 'Register Driver')

@section('content')
<h1 class="h3 mb-4">Register Driver</h1>
<div class="card stat-card">
    <div class="card-body">
        <form method="POST" action="{{ route('drivers.store') }}">
            @csrf
            @include('drivers._form')
            <button type="submit" class="btn btn-primary">Save Driver</button>
            <a href="{{ route('drivers.index') }}" class="btn btn-link">Cancel</a>
        </form>
    </div>
</div>
@endsection
