@extends('web.layouts.app')

@section('title', 'Bexora | My Services')

@section('content')

    @php
        $jsServices = $services->merge($deletedServices ?? []);
        $jsBusinesses = $businesses->map(fn($b) => ['id' => $b->id, 'name' => $b->name])->values();
        $jsBranches = $branches
            ->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'city' => $b->city, 'business_id' => $b->business_id])
            ->values();
        $jsCategories = $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values();
    @endphp

    <script>
        window.BE_DATA = {
            csrf: '{{ csrf_token() }}',
            services: @json($jsServices),
            businesses: @json($jsBusinesses),
            branches: @json($jsBranches),
            categories: @json($jsCategories),
            routes: {
                store: "{{ route('manage.service.store') }}",
                categoryRequest: "{{ route('manage.service.category.request') }}",
                restore: "{{ route('manage.service.restore', ':id') }}",
                delete: "{{ route('manage.service.delete', ':id') }}",
                update: "{{ route('manage.service.update', ':id') }}",
                show: "{{ route('manage.service.show', ':id') }}",
            },
            toolbar: {
                showStatus: true,
                centerGroups: [{
                    groupId: 'manage',
                    actions: [{
                        label: 'Create Service',
                        icon: 'fa-plus',
                        modal: 'create-service-modal',
                    }]
                }],
                rightAction: {
                    label: 'Ask Bexi',
                    icon: 'fa-message',
                }
            }
        };
    </script>

    <div class="business">
        <aside class="business__sidebar">
            @include('components.partials.dashboard_sidebar_info', ['active' => 'services'])
        </aside>

        <div class="display-column">
            <x-ui.breadcrumbs />

            <main class="business__main">
                <header class="business__header-wrapper business__header-wrapper--simple">
                    <div class="business__header-corner"></div>

                    <div class="business__header-info">
                        <h2 class="business-header__title">My Services</h2>
                        <div class="business-info">
                            <div class="stat-item stat-item--all">
                                <i class="fa-solid fa-layer-group"></i>
                                <div id="countAll">{{ $services->count() }}</div> Total services
                            </div>
                            <div class="stat-item stat-item--published">
                                <i class="fa-solid fa-circle-check"></i>
                                <div id="countActive">{{ $services->where('is_active', true)->count() }}</div> Active
                            </div>
                            <div class="stat-item stat-item--hidden">
                                <i class="fa-solid fa-eye-slash"></i>
                                <div id="countInactive">
                                    {{ $services->where('is_active', false)->whereNull('deleted_at')->count() }}</div>
                                Inactive
                            </div>
                            <div class="stat-item stat-item--deleted">
                                <i class="fa-solid fa-trash"></i>
                                <div id="countDeleted">{{ ($deletedServices ?? collect())->count() }}</div> Archived
                            </div>
                        </div>
                    </div>

                    <div class="business__header-right">
                        <div class="business__header-right-section_2">
                            <div class="list-view__search-wrapper">
                                <div class="search-container">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="text" id="serviceSearchInput" placeholder="Search services...">
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="business__body-wrapper">
                    <div id="serviceTableContainer" class="list-view__body-wrapper">
                        {{-- Rendered via JavaScript --}}
                    </div>
                </div>
            </main>
            @include('components.ui.toolbar')
        </div>
    </div>

    @vite('resources/js/pages/services/entry.js')

@endsection

<div id="tpl-status-filters" style="display: none;">
    @include('components.statuses_service')
</div>
