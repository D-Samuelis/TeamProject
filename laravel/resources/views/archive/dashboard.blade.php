@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Dashboard</h1>

    <p>Your roles:</p>
    @foreach(auth()->user()->roles as $role)
        <span class="badge {{ $role->name }}">{{ $role->name }}</span>
    @endforeach
</div>

<div class="card">
    <p>Your permissions:</p>
    <ul>
        @foreach(auth()->user()->getAllPermissions() as $permission)
            <li>{{ $permission->name }}</li>
        @endforeach
    </ul>
</div>
@endsection
