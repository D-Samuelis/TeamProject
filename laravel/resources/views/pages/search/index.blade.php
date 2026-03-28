@extends('layouts.app')

@section('title', 'Bexora | Manual Booking')

@section('content')
<div class="booking"> {{-- Tento wrapper nahrádza .appointments --}}
    
    <aside class="booking__sidebar"> {{-- Sidebar so šírkou 16.75rem --}}
        <section class="booking__group">
            <h3 class="booking__subtitle">
                <i class="fa-solid fa-layer-group"></i>
                Booking Target
            </h3>
            <div class="dropdown__mini-list"> {{-- Tvoja trieda z myAppointments --}}
                @foreach (['business' => 'Shops', 'branch' => 'Locations', 'service' => 'Services'] as $key => $label)
                    <a href="{{ route('search.index', array_merge(request()->query(), ['target' => $key])) }}"
                       class="booking__nav-link {{ $filters->target === $key ? 'is-active' : '' }}">
                        <i class="fa-solid @if($key == 'business') fa-shop @elseif($key == 'branch') fa-location-dot @else fa-scissors @endif"></i>
                        <span>{{ $label }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="booking__group">
            <h3 class="booking__subtitle">
                <i class="fa-solid fa-filter"></i>
                Filters
            </h3>
            <div class="booking__filter-wrapper">
                @include('pages.search.partials.filter-sidebar')
            </div>
        </section>
    </aside>

    <main class="booking__main">
        <header class="booking__header-wrapper">
            <div class="booking__header-info">
                <h2 class="timeline-header__title">Manual Booking</h2>
                <div class="timeline-info">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Select your preferred {{ $filters->target }}</span>
                </div>
            </div>
            
            <div class="booking__search-wrapper">
                <div class="booking__search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Search {{ $filters->target }}...">
                </div>
            </div>
        </header>

        <div class="booking__body-wrapper">
            @if ($results->isEmpty())
                <div class="booking__empty">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <p>No results found for "{{ $filters->target }}"</p>
                </div>
            @else
                <div class="booking-grid">
                    @foreach ($results as $item)
                        @include('pages.search.partials.cards.' . $filters->target, [
                            'item' => $item
                        ])
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</div>
@endsection