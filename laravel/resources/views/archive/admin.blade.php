@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Admin Panel</h1>
    <p>You are logged in as an <strong>admin</strong>.</p>

    @can('manage users')
        <button>Manage Users</button>
    @endcan
</div>
@endsection
