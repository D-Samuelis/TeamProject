@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Client Area</h1>
    <p>You are logged in as a <strong>client</strong>.</p>

    @can('create projects')
        <button>Create Project</button>
    @endcan
</div>
@endsection
