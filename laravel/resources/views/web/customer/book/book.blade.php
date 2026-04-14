@extends('web.layouts.app')

@section('title', 'Bexora | Book')

@section('content')

<div id="bookCard" style="width: 100%">

    @if($mode === 'business')
        @include('web.customer.book.partials.business')
    @elseif($mode === 'service')
        @include('web.customer.book.partials.service')
    @elseif($mode === 'asset')
        @include('web.customer.book.partials.asset')
    @endif

</div>

@vite('resources/js/pages/booking/entry.js')

@endsection