@extends('web.layouts.app')

@section('title', 'Bexora | My Services')

@section('content')
<div class="business">
    <aside class="business__sidebar">

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