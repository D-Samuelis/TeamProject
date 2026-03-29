@extends('layouts.app')

@section('title', 'Bexora | My Businesses')

@section('content')
<div class="business">
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
        <section class="business__status-filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Status</h3>
            <div id="statusList" class="dropdown__mini-list">
                
            </div>
        </section>
    </aside>

    <main class="business__main">
        <header class="business__header-wrapper business__header-wrapper--simple">
            <div class="business__header-corner"></div>

            <div class="business__header-info">
                <div class="business__header-info">
                    <h2 class="business-header__title">My Businesses</h2>
                    
                    <div class="business-info">
                        <div class="stat-item stat-item--all">
                            <i class="fa-solid fa-layer-group"></i>
                            <div id="countAll">0</div> Total businesses
                        </div>
                        <div class="stat-item stat-item--published">
                            <i class="fa-solid fa-circle-check"></i>
                            <div id="countPublished">0</div> Published
                        </div>
                        <div class="stat-item stat-item--hidden">
                            <i class="fa-solid fa-eye-slash"></i>
                            <div id="countHidden">0</div> Hidden
                        </div>
                        <div class="stat-item stat-item--deleted">
                            <i class="fa-solid fa-trash"></i>
                            <div id="countDeleted">0</div> Archived
                        </div>
                    </div>
                </div>
            </div>

            <div class="business__header-right">
                <div class="business__header-right-section_1"> </div>
                <div class="business__header-right-section_2">
                    <div class="list-view__search-wrapper">
                        <div class="search-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="businessSearchInput" placeholder="Search businesses...">
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="business__body-wrapper">
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
            store: "{{ route('business.store') }}",
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