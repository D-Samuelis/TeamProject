@extends('web.layouts.app')

@section('title', 'Bexora | Assets')

@section('content')

<script>
    window.BE_DATA = {
        csrf: '{{ csrf_token() }}',
        assets: @json($assets),
        allBranches: @json($branches),
        allServices: @json($services),
        routes: {
            store: "{{ route('manage.asset.store') }}",
            show: "{{ route('manage.asset.show', ':id') }}",
            update: "{{ route('manage.asset.update', ':id') }}",
            deleteAsset: "{{ route('manage.asset.delete', ':id') }}",
            restoreAsset: "{{ route('manage.asset.restore', ':id') }}"
        },
        csrf: "{{ csrf_token() }}"
    };
</script>

<div class="business">
    <aside class="business__sidebar">
        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Management</h3>
            <div id="managementList" class="dropdown__mini-list">
                <a href="{{ route('manage.asset.index') }}" class="business__nav-link is-active">
                    <i class="fa-solid fa-list"></i><span>All Assets</span>
                </a>
                <button type="button" class="business__nav-link" data-modal-target="create-asset-modal">
                    <i class="fa-solid fa-plus"></i><span>New Asset</span>
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
                <h2 class="business-header__title">My Assets</h2>

                <div class="business-info">
                    <div class="stat-item stat-item--all">
                        <i class="fa-solid fa-layer-group"></i>
                        <div id="countAll">{{ $assets->count() }}</div> Total assets
                    </div>
                    <div class="stat-item stat-item--published">
                        <i class="fa-solid fa-circle-check"></i>
                        <div id="countPublished">{{ $assets->count() }}</div> Published
                    </div>
                    <div class="stat-item stat-item--hidden">
                        <i class="fa-solid fa-eye-slash"></i>
                        <div id="countHidden">{{ $assets->count() }}</div> Hidden
                    </div>
                    <div class="stat-item stat-item--deleted">
                        <i class="fa-solid fa-trash"></i>
                        <div id="countDeleted">{{ $assets->count() }}</div> Archived
                    </div>
                </div>
            </div>

            <div class="business__header-right">
                <div class="business__header-right-section_1"> </div>
                <div class="business__header-right-section_2">
                    <div class="list-view__search-wrapper">
                        <div class="search-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="assetSearchInput" placeholder="Search assets...">
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="business__body-wrapper">
            <div id="assetTableContainer" class="list-view__body-wrapper">
            </div>
        </div>
    </main>
</div>

@vite('resources/js/pages/assets/entry.js')

@endsection
