@extends('layouts.app')

@section('title', 'Bexora | My Businesses')

@section('content')
<div class="business">
    {{-- SIDEBAR - Ostáva rovnaký --}}
    <aside class="business__sidebar">
        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Management</h3>
            <div id="managementList" class="dropdown__mini-list">
                <a href="{{ route('business.index') }}" class="business__nav-link is-active">
                    <i class="fa-solid fa-list"></i><span>All Businesses</span>
                </a>
                <button type="button" class="business__nav-link" data-modal-target="create-business-modal">
                    <i class="fa-solid fa-plus"></i><span>New Business</span>
                </button>
            </div>
        </section>
    </aside>

    <main class="business__main">
        <header class="business__header-wrapper">
            <div class="business__header-info">
                <h2 class="timeline-header__title">My Businesses</h2>
                <div class="timeline-info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Total Units: <strong id="businessTotalCount">0</strong></span>
                </div>
            </div>

            <div class="business__search-wrapper">
                <div class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="businessSearchInput" placeholder="Search name, description or status...">
                </div>
            </div>
        </header>

        <div class="business__body-wrapper">
            {{-- SEM SA VYKRESLÍ TABUĽKA --}}
            <div id="businessTableContainer" class="list-view__body-wrapper"></div>
        </div>
    </main>
</div>

<div id="create-business-modal" class="business-modal hidden">
    <div class="business-modal__overlay"></div>
    <div class="business-modal__content">
        <div class="business-modal__header mb-4">
            <h2 class="timeline-header__title">Create New Business</h2>
        </div>
        <form method="POST" action="{{ route('business.store') }}">
            @csrf
            <div class="business__search-container mb-3" style="width: 100%">
                <input type="text" name="name" placeholder="Business Name" required>
            </div>
            <div class="business__search-container mb-4" style="width: 100%; height: auto;">
                <textarea name="description" placeholder="Description (optional)" style="width: 100%; border: none; background: transparent; outline: none; padding: 5px; min-height: 80px;"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="business__nav-link is-active" style="border: none; cursor: pointer;">
                    Save Business
                </button>
                <button type="button" class="business__nav-link modal-close-trigger" style="border: none; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    window.BE_DATA = {
        businesses: @json($activeBusinesses->merge($deletedBusinesses)),
        routes: {
            restore: "{{ route('business.restore', ':id') }}",
            delete: "{{ route('business.delete', ':id') }}",
            update: "{{ route('business.update', ':id') }}",
            show: "{{ route('business.show', ':id') }}"
        },
        csrf: "{{ csrf_token() }}"
    };
</script>

@vite('resources/js/pages/businesses/entry.js')
@endsection