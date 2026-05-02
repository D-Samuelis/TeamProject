@extends('web.layouts.app')

@section('title', 'Bexora | My Assets')

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
        csrf: "{{ csrf_token() }}",
        toolbar: {
            showStatus: true,
            centerGroups: [
                {
                    groupId: 'manage',
                    actions: [
                        {
                            label: 'Create Asset',
                            icon: 'fa-plus',
                            modal: 'create-asset-modal'
                        }
                    ]
                }
            ],
            rightAction: {
                label: 'Ask Bexi',
                icon: 'fa-message',
                modal: 'xxx'
            }
        }
    };
</script>

<div class="business">
    <aside class="business__sidebar">
        <div class="business__sidebar-header">
            <h2 class="business__sidebar-title">Dashboard</h2>
            <div class="display-column">
                @if(auth()->user() && auth()->user()->isAdmin())
                    <div class="business__sidebar-type">ADMIN</div>
                    <div class="business__sidebar-description">
                        You are currently in the admin dashboard. Here you can manage everything from one place.
                    </div>
                    <div class="business__sidebar-switch">
                        Switch to <button class="switch-dashboard-button" id="switchDashboardButton">Manager view</button>
                    </div>
                @else
                    <div class="business__sidebar-type">MANAGER</div>
                    <div class="business__sidebar-description">
                        You are currently in the manager dashboard. Here you can manage your assigned resources.
                    </div>
                    <div class="business__sidebar-switch">
                        Switch to <button class="switch-dashboard-button" id="switchDashboardButton">Admin view</button>
                    </div>
                @endif
            </div>
        </div>
        <div class="business__sidebar-links">
            <a href="{{ route('manage.business.index') }}" class="business__sidebar-link">
                <i class="fa-solid fa-layer-group"></i>
                @if(auth()->user() && auth()->user()->isAdmin())
                    Businesses
                @else
                    My Businesses
                @endif
            </a>
            <a href="{{ route('manage.branch.index') }}" class="business__sidebar-link">
                <i class="fa-solid fa-location-dot"></i>
                @if(auth()->user() && auth()->user()->isAdmin())
                    Branches
                @else
                    My Branches
                @endif
            </a>
            <a href="{{ route('manage.service.index') }}" class="business__sidebar-link">
                <i class="fa-solid fa-bell-concierge"></i>
                @if(auth()->user() && auth()->user()->isAdmin())
                    Services
                @else
                    My Services
                @endif
            </a>
            <a href="{{ route('manage.asset.index') }}" class="business__sidebar-link business__sidebar-link--active">
                <i class="fa-regular fa-gem"></i>
                @if(auth()->user() && auth()->user()->isAdmin())
                    Assets
                @else
                    My Assets
                @endif
            </a>
        </div>

        @if(auth()->user() && auth()->user()->isAdmin())
            <div class="business__sidebar-links">
                <a href="#" class="business__sidebar-link">
                    <i class="fa-solid fa-location-dot"></i>
                    Users
                </a>

                <a href="{{ route('admin.categories.index') }}" class="business__sidebar-link">
                    <i class="fa-solid fa-layer-group"></i>
                    Categories
                </a>
            </div>
        @endif
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
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
        @include('components.ui.toolbar')
    </div>
</div>

@vite('resources/js/pages/assets/entry.js')

@endsection

<div id="tpl-status-filters" style="display: none;">
    @include('components.statuses_asset')
</div>
