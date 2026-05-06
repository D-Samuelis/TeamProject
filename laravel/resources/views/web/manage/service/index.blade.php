@extends('web.layouts.app')

@section('title', 'Bexora | My Services')

@section('content')
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
            <a href="{{ route('manage.service.index') }}" class="business__sidebar-link business__sidebar-link--active">
                <i class="fa-solid fa-bell-concierge"></i>
                @if(auth()->user() && auth()->user()->isAdmin())
                    Services
                @else
                    My Services
                @endif
            </a>
            <a href="{{ route('manage.asset.index') }}" class="business__sidebar-link">
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
                    <h2 class="business-header__title">My Services</h2>
                    <div class="business-info">
                        <div class="stat-item stat-item--all">
                            <i class="fa-solid fa-layer-group"></i>
                            <div id="countAll">0</div> Total services
                        </div>
                        <div class="stat-item stat-item--published">
                            <i class="fa-solid fa-circle-check"></i>
                            <div id="countActive">0</div> Active
                        </div>
                        <div class="stat-item stat-item--hidden">
                            <i class="fa-solid fa-eye-slash"></i>
                            <div id="countInactive">0</div> Inactive
                        </div>
                        <div class="stat-item stat-item--deleted">
                            <i class="fa-solid fa-trash"></i>
                            <div id="countDeleted">0</div> Archived
                        </div>
                    </div>
                </div>

                <div class="business__header-right">
                    <div class="business__header-right-section_1"></div>
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
                <div id="serviceTableContainer" class="list-view__body-wrapper"></div>
            </div>
        </main>
        @include('components.ui.toolbar')
    </div>
</div>

{{-- Šablóny pre ľavú časť Toolbaru (Status filtre) --}}
<template id="tpl-status-filters">
    <div id="statusFilterContainer">
        {{-- Sem JS vloží Checkboxy (Active/Inactive/Archived) cez initServiceStatusFilters --}}
    </div>
</template>

@php
    $beData = json_encode([
        'services'   => $services->merge($deletedServices ?? []),
        'businesses' => $businesses->map(fn($b) => ['id' => $b->id, 'name' => $b->name])->values(),
        'branches'   => $branches->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'city' => $b->city, 'business_id' => $b->business_id])->values(),
        'categories' => $categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values(),
        'routes'     => [
            'store'   => route('manage.service.store'),
            'restore' => route('manage.service.restore', ':id'),
            'delete'  => route('manage.service.delete', ':id'),
            'update'  => route('manage.service.update', ':id'),
            'show'    => route('manage.service.show', ':id'),
        ],
        'csrf' => csrf_token(),
        'toolbar' => [
            'centerGroups' => [
                [
                    'groupId' => 'manage',
                    'hasDivider' => false,
                    'actions' => [
                        [
                            'label' => 'Create Service',
                            'icon' => 'fa-plus',
                            'modal' => 'create-service-modal'
                        ]
                    ]
                ]
            ],
            'rightAction' => [
                'label' => 'Ask Bexi',
                'icon' => 'fa-message'
            ]
        ]
    ]);
@endphp

<script>
    window.BE_DATA = {!! $beData !!};
</script>

@vite('resources/js/pages/services/entry.js')
@endsection

<div id="tpl-service-filters" style="display: none;">
    @include('components.statuses_service')
</div>