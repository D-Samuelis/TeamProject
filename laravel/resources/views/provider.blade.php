@extends('layouts.app')

@section('content')
<div class="card">
    <h1>Provider Area</h1>
    <p>You are logged in as a <strong>provider</strong>.</p>

    @can('offer services')
        <button>Offer Service</button>
    @endcan
</div>
@endsection
