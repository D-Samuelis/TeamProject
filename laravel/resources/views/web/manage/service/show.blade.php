@section("breadcrumb-{$service->id}", $service->name)

@extends('web.layouts.app')

@section('title', 'Bexora | Service Info')

@section('content')
@php
    $serviceJsData = [
        'id' => $service->id,
        'name' => $service->name,
        'description' => $service->description,
        'duration_minutes' => $service->duration_minutes,
        'price' => $service->price,
        'location_type' => $service->location_type,
        'is_active' => (bool) $service->is_active,
        'requires_manual_acceptance' => (bool) $service->requires_manual_acceptance,
        'cancellation_period' => App\Application\Service\Services\DurationParser::fromMinutes($service->cancellation_period_minutes),
        'deleted_at' => $service->deleted_at,
        'business_id' => $service->business_id,
        'business' => $service->business ? ['id' => $service->business->id, 'name' => $service->business->name] : null,
        'branches' => $service->branches->map(fn ($branch) => [
            'id' => $branch->id,
            'name' => $branch->name,
            'city' => $branch->city,
            'business_id' => $branch->business_id,
        ])->values(),
        'assets' => $service->assets->map(fn ($asset) => [
            'id' => $asset->id,
            'name' => $asset->name,
            'branch_id' => $asset->branch_id,
            'branch' => $asset->branch ? ['id' => $asset->branch->id, 'name' => $asset->branch->name] : null,
        ])->values(),
    ];

    $branchesJsData = $branches->map(fn ($branch) => [
        'id' => $branch->id,
        'name' => $branch->name,
        'city' => $branch->city,
        'business_id' => $branch->business_id,
    ])->values();
@endphp

<script>
    window.BE_DATA = {
        csrf: '{{ csrf_token() }}',
        canUpdate: @json(auth()->user()?->can('update', $service)),
        service: @json($serviceJsData),
        branches: @json($branchesJsData),
        routes: {
            update: '{{ route("manage.service.update", $service->id) }}',
            delete: '{{ route("manage.service.delete", $service->id) }}'
        },
        toolbar: {
            centerGroups: [
                {
                    groupId: 'service-status',
                    actions: [
                        {
                            label: `Status: ${@json($service->is_active) ? 'Active' : 'Inactive'}`,
                            icon: @json($service->is_active) ? 'fa-circle text-green' : 'fa-circle text-yellow',
                            isForm: true,
                            action: '{{ route("manage.service.update", $service->id) }}',
                            hiddenFields: [
                                { name: 'is_active', value: @json($service->is_active) ? 0 : 1 },
                                { name: '_method', value: 'PUT' }
                            ]
                        }
                    ]
                },
                {
                    groupId: 'service-manage',
                    hasDivider: true,
                    actions: [
                        {
                            label: 'Edit Service',
                            icon: 'fa-gear',
                            modal: 'edit-service-modal',
                            serviceData: @json($serviceJsData)
                        },
                        {
                            label: 'Archive',
                            icon: 'fa-box-archive',
                            class: 'delete-action',
                            id: @json($service->id),
                            modal: 'archive-service-modal'
                        }
                    ]
                }
            ],
            rightAction: {
                label: 'Ask Bexi',
                icon: 'fa-message'
            }
        }
    };
</script>

@php
    $statusVariant = $service->trashed() ? 'red' : ($service->is_active ? 'green' : 'yellow');
    $statusLabel = $service->trashed() ? 'Archived' : ($service->is_active ? 'Active' : 'Inactive');
    $locationLabel = $service->location_type ? ucfirst($service->location_type) : 'Not specified';
    $cancellationLabel = $service->cancellation_period_minutes
        ? App\Application\Service\Services\DurationParser::fromMinutes($service->cancellation_period_minutes)
        : 'No restriction';
    $acceptanceLabel = $service->requires_manual_acceptance ? 'Manual approval' : 'Automatic';
    $businessId = $service->business->id;
    $currentCancellation = $service->cancellation_period_minutes
        ? App\Application\Service\Services\DurationParser::fromMinutes($service->cancellation_period_minutes)
        : '';
    $assignedBranchIds = $service->branches->pluck('id');
    $visibleAssets = $service->assets->filter(fn ($asset) => $assignedBranchIds->contains($asset->branch_id));
@endphp

<div class="business service-settings-page">
    <aside class="business__sidebar">
        @include('components.partials.dashboard_sidebar_info', ['active' => 'services'])

        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Service Info</h3>
            <div id="serviceInfo" class="dropdown__mini-list">
                <div class="business-info-card">
                    <span class="business-info-card__status filter-item--{{ $statusVariant }}">
                        {{ $statusLabel }}
                    </span>
                    <p class="business-info-card__name">{{ $service->name }}</p>
                    <p class="business-info-card__desc">
                        {{ Str::limit($service->description ?: 'No description added yet.', 110) }}
                    </p>
                    @can('update', $service)
                        @if (!$service->trashed())
                            <button class="business-info-card__edit-btn" type="button" data-modal-target="edit-service-modal">
                                <i class="fa-solid fa-gear"></i> Manage Service
                            </button>
                        @endif
                    @endcan
                </div>
            </div>
        </section>
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />

        <main class="business__main">
            <header class="business__header-wrapper">
                <div class="business__header-corner">
                    <div class="view-switcher">
                        <button class="view-switcher__btn active" id="showServiceCore" type="button">
                            <i class="fa-solid fa-circle-info"></i> Info
                        </button>
                        @if (!$service->trashed())
                            <button class="view-switcher__btn" id="showServiceBranches" type="button">
                                <i class="fa-solid fa-diagram-project"></i> Branches
                            </button>
                        @endif
                    </div>
                </div>

                <div class="business__header-info">
                    <div class="business__header-info-text">
                        <div class="breadcrumbs">
                            <a href="{{ route('manage.service.index') }}">Services</a> / {{ $service->name }}
                        </div>
                    </div>
                </div>

                <div class="business__header-right">
                    <div class="business__header-right-section_1">
                        @if ($service->trashed())
                            @can('restore', $service)
                                <form action="{{ route('manage.service.restore', $service->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="branch-restore-btn">
                                        <i class="fa-solid fa-rotate-left"></i> Restore Service
                                    </button>
                                </form>
                            @endcan
                        @else
                            <div class="dropdown branch-dropdown">
                                <button class="branch-dropdown__trigger" type="button">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                    <span>Service Actions</span>
                                </button>

                                <div class="branch-dropdown__menu">
                                    <form action="{{ route('manage.service.update', $service->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="business_id" value="{{ $businessId }}">
                                        <input type="hidden" name="cancellation_period" value="{{ $currentCancellation }}">
                                        <input type="hidden" name="requires_manual_acceptance" value="{{ $service->requires_manual_acceptance ? 1 : 0 }}">
                                        <input type="hidden" name="is_active" value="{{ $service->is_active ? 0 : 1 }}">
                                        <button type="submit" class="branch-dropdown__item">
                                            <i class="fa-solid fa-circle status-dot {{ $service->is_active ? 'text-green' : 'text-yellow' }}"></i>
                                            Status:
                                            <div class="status__badge {{ $service->is_active ? 'bg__badge-green' : 'bg__badge-yellow' }}">
                                                {{ $service->is_active ? 'Active' : 'Inactive' }}
                                            </div>
                                        </button>
                                    </form>

                                    <form action="{{ route('manage.service.update', $service->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="business_id" value="{{ $businessId }}">
                                        <input type="hidden" name="duration_minutes" value="{{ $service->duration_minutes }}">
                                        <input type="hidden" name="price" value="{{ $service->price }}">
                                        @if($service->location_type)
                                            <input type="hidden" name="location_type" value="{{ $service->location_type }}">
                                        @endif
                                        <input type="hidden" name="cancellation_period" value="{{ $currentCancellation }}">
                                        <input type="hidden" name="is_active" value="{{ $service->is_active ? 1 : 0 }}">
                                        <input type="hidden" name="requires_manual_acceptance" value="{{ $service->requires_manual_acceptance ? 0 : 1 }}">
                                        <button type="submit" class="branch-dropdown__item">
                                            <i class="fa-solid fa-user-check"></i>
                                            Acceptance:
                                            <div class="status__badge {{ $service->requires_manual_acceptance ? 'bg__badge-yellow' : 'bg__badge-green' }}">
                                                {{ $service->requires_manual_acceptance ? 'Manual' : 'Automatic' }}
                                            </div>
                                        </button>
                                    </form>

                                    <div class="branch-dropdown__divider"></div>

                                    @can('delete', $service)
                                        <button
                                            type="button"
                                            class="branch-dropdown__item delete-action js-archive-service-btn"
                                            data-id="{{ $service->id }}"
                                            data-name="{{ $service->name }}"
                                            data-modal-target="archive-service-modal">
                                            <i class="fa-solid fa-box-archive"></i> Archive Service
                                        </button>
                                    @endcan
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="business__header-right-section_2 service-settings__header-summary"></div>
                </div>
            </header>

            <div class="business__body-wrapper service-settings__body">
                <div id="serviceCoreView" class="business__panel">
                    <section class="service-settings__overview">
                        <div class="service-settings__overview-main">
                            <div class="service-settings__overview-title-row">
                                <div>
                                    <span class="service-settings__fact-label">Service</span>
                                    <h3>{{ $service->name }}</h3>
                                </div>
                                <span class="status__badge {{ $service->is_active ? 'bg__badge-green' : 'bg__badge-yellow' }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>

                            <p class="service-settings__description">
                                {{ $service->description ?: 'No description added yet.' }}
                            </p>
                        </div>

                        <div class="service-settings__facts-grid">
                            <div class="service-settings__info-tile">
                                <i class="fa-regular fa-clock"></i>
                                <span class="service-settings__fact-label">Duration</span>
                                <strong>{{ $service->duration_minutes }} min</strong>
                            </div>

                            <div class="service-settings__info-tile">
                                <i class="fa-solid fa-euro-sign"></i>
                                <span class="service-settings__fact-label">Price</span>
                                <strong>{{ number_format($service->price, 2) }} EUR</strong>
                            </div>

                            <div class="service-settings__info-tile">
                                <i class="fa-solid fa-location-dot"></i>
                                <span class="service-settings__fact-label">Type</span>
                                <strong>{{ $locationLabel }}</strong>
                            </div>

                            <div class="service-settings__info-tile">
                                <i class="fa-solid fa-calendar-xmark"></i>
                                <span class="service-settings__fact-label">Cancellation</span>
                                <strong>{{ $cancellationLabel }}</strong>
                            </div>

                            <div class="service-settings__info-tile">
                                <i class="fa-solid fa-user-check"></i>
                                <span class="service-settings__fact-label">Acceptance</span>
                                <strong>{{ $acceptanceLabel }}</strong>
                            </div>

                            <div class="service-settings__info-tile">
                                <i class="fa-solid fa-diagram-project"></i>
                                <span class="service-settings__fact-label">Assigned Branches</span>
                                <strong>{{ $service->branches->count() }}</strong>
                            </div>
                        </div>
                    </section>
                </div>

                @if (!$service->trashed())
                    <div id="serviceBranchesView" class="business__panel hidden">
                        <div class="service-settings__grid">
                            <section class="service-settings__connections">
                                <div class="service-settings__inline-card">
                                    <div class="service-settings__inline-card-head service-settings__inline-card-head--readonly">
                                        <div>
                                            <span class="service-settings__fact-label">Assigned Branches</span>
                                            <strong>{{ $service->branches->count() }}</strong>
                                            <p class="service-settings__helper-text">
                                                Assigned branches define where the service can be offered. Use the actions below to add or remove this service from a branch.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="service-settings__assigned-branches">
                                        @forelse($branches->where('business_id', $businessId) as $branch)
                                            @php $isAssigned = $service->branches->contains('id', $branch->id); @endphp
                                            <div class="service-settings__assigned-branch {{ $isAssigned ? 'is-assigned' : '' }}">
                                                <a href="{{ route('manage.branch.show', $branch->id) }}" class="service-settings__assigned-branch-link">
                                                    <i class="fa-solid fa-location-dot"></i>
                                                    <span>
                                                        <strong>{{ $branch->name }}</strong>
                                                        @if($branch->city)
                                                            <small>{{ $branch->city }}</small>
                                                        @endif
                                                    </span>
                                                </a>

                                                @can('assign', [$service, $branch])
                                                    @if($isAssigned)
                                                        <form method="POST" action="{{ route('manage.service.branch.unassign', [$service->id, $branch->id]) }}">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="service-settings__branch-action service-settings__branch-action--remove">
                                                                Remove
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('manage.service.branch.assign', [$service->id, $branch->id]) }}">
                                                            @csrf
                                                            <button type="submit" class="service-settings__branch-action service-settings__branch-action--assign">
                                                                Assign
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        @empty
                                            <div class="service-settings__branch-empty">
                                                No branches available for this business.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                <div class="service-settings__connection-map">
                                    <div class="service-settings__connection-map-head">
                                        <span class="service-settings__fact-label">Business structure</span>
                                    </div>

                                    <a href="{{ route('manage.business.show', $service->business->id) }}" class="service-settings__tree-node service-settings__tree-node--business service-settings__tree-link">
                                        <span class="service-settings__connection-icon">
                                            <i class="fa-solid fa-briefcase"></i>
                                        </span>
                                        <div>
                                            <span class="service-settings__fact-label">Business</span>
                                            <strong>{{ $service->business->name }}</strong>
                                        </div>
                                    </a>

                                    <div class="service-settings__connection-tree">
                                        @forelse($service->branches as $branch)
                                            @php
                                                $branchAssets = $service->assets->where('branch_id', $branch->id);
                                            @endphp
                                            <div class="service-settings__tree-branch">
                                                <a href="{{ route('manage.branch.show', $branch->id) }}" class="service-settings__tree-node service-settings__tree-node--branch service-settings__tree-link">
                                                    <span class="service-settings__connection-icon">
                                                        <i class="fa-solid fa-location-dot"></i>
                                                    </span>
                                                    <div>
                                                        <span class="service-settings__fact-label">Branch</span>
                                                        <strong>{{ $branch->name }}</strong>
                                                        @if($branch->city)
                                                            <small>{{ $branch->city }}</small>
                                                        @endif
                                                    </div>
                                                </a>

                                                <div class="service-settings__tree-services">
                                                    <div class="service-settings__tree-node service-settings__tree-node--service">
                                                        <span class="service-settings__connection-icon">
                                                            <i class="fa-solid fa-screwdriver-wrench"></i>
                                                        </span>
                                                        <div>
                                                            <span class="service-settings__fact-label">Service</span>
                                                            <strong>{{ $service->name }}</strong>
                                                        </div>
                                                    </div>

                                                    <div class="service-settings__tree-assets">
                                                        @forelse($branchAssets as $asset)
                                                            <a href="{{ route('manage.asset.show', $asset->id) }}" class="service-settings__tree-node service-settings__tree-node--asset">
                                                                <span class="service-settings__connection-icon">
                                                                    <i class="fa-regular fa-gem"></i>
                                                                </span>
                                                                <div>
                                                                    <span class="service-settings__fact-label">Asset</span>
                                                                    <strong>{{ $asset->name }}</strong>
                                                                </div>
                                                            </a>
                                                        @empty
                                                            <a href="{{ route('manage.asset.index') }}" class="service-settings__tree-empty service-settings__tree-empty--link">
                                                                No assets connected to this service in this branch.
                                                                <span>Open My Assets</span>
                                                            </a>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="service-settings__tree-empty">
                                                No branches assigned to this service yet.
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                @endif
            </div>
        </main>
        @include('components.ui.toolbar')
    </div>
</div>

@vite('resources/js/pages/services/entry.js')
@endsection

<div id="tpl-connections" style="display: none;">
    @include('components.connections_service', ['service' => $service])
</div>
