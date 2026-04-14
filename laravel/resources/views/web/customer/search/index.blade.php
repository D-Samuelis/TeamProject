@extends('web.layouts.app')

@section('title', 'Bexora | Manual Booking')

@section('content')

    <div class="booking booking--manual-page">
    
    <aside class="booking__sidebar"> 
        <section class="booking__group">
    <h3 class="miniLists__subtitle">
        <i class="fa-solid fa-chevron-down"></i>
        <i class="fa-solid fa-layer-group"></i>
        Booking Target
        
    </h3>

    <div class="dropdown__mini-list" id="targetList">
        @foreach (['business' => 'Business', 'branch' => 'Branches', 'service' => 'Services'] as $key => $label)
            <a href="{{ route('search.index', array_merge(request()->query(), ['target' => $key])) }}"
                class="booking__nav-link {{ $filters->target === $key ? 'is-active' : '' }}">
                <i class="fa-solid @if($key == 'business') fa-shop @elseif($key == 'branch') fa-location-dot @else fa-scissors @endif"></i>
                <span>{{ $label }}</span>
            </a>
        @endforeach
    </div>
</section>

<section class="booking__group">
    <h3 class="miniLists__subtitle">
        <i class="fa-solid fa-chevron-down"></i>
        <i class="fa-solid fa-filter"></i>
        Filters
        
    </h3>

    <div class="booking__filter-wrapper" id="filterList">
        @include('web.customer.search.partials.filter-sidebar')
    </div>
</section>
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
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
                        <input
                            type="text"
                            id="bookingSearch"
                            placeholder="Search {{ $filters->target }}..."
                        >
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
                            @include('web.customer.search.partials.cards.' . $filters->target, [
                                'item' => $item
                            ])
                        @endforeach
                    </div>

                    @if ($results->hasPages())
                        <div class="booking__pagination">
                            @if ($results->onFirstPage())
                                <span class="booking__pagination-button booking__pagination-button--disabled">
                                    &larr;
                                </span>
                            @else
                                <a
                                    href="{{ $results->appends(request()->query())->previousPageUrl() }}"
                                    class="booking__pagination-button"
                                    aria-label="Previous page"
                                >
                                    &larr;
                                </a>
                            @endif

                            <span class="booking__pagination-info">
                                Page {{ $results->currentPage() }} of {{ $results->lastPage() }}
                            </span>

                            @if ($results->hasMorePages())
                                <a
                                    href="{{ $results->appends(request()->query())->nextPageUrl() }}"
                                    class="booking__pagination-button"
                                    aria-label="Next page"
                                >
                                    &rarr;
                                </a>
                            @else
                                <span class="booking__pagination-button booking__pagination-button--disabled">
                                    &rarr;
                                </span>
                            @endif
                        </div>
                    @endif
                @endif
            </div>
        </main> 
    </div>
</div>


@vite('resources/js/pages/search/entry.js')
@endsection