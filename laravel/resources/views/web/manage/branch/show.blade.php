@extends('web.layouts.app')

@section('title', 'Bexora | Branch Info')

@section('content')

<script>
    window.BE_DATA = {
        csrf: '{{ csrf_token() }}',
        branch: @json($branch),
        businesses: @json($businesses),
        allServices: @json($services),
        routes: {
            branchUpdate: "{{ route('manage.branch.update', $branch->id) }}",
            branchDelete: "{{ route('manage.branch.delete', $branch->id) }}",
            restore: "{{ route('manage.branch.restore', $branch->id) }}",
            unassignService: "{{ route('manage.service.branch.unassign', [':serviceId', $branch->id]) }}",
            assignService: "{{ route('manage.service.branch.assign', [':serviceId', $branch->id]) }}",
            showService: "{{ route('manage.service.show', ':serviceId') }}"
        },
        toolbar: {
            showStatus: false,
            centerGroups: [
                {
                    groupId: 'danger-zone',
                    actions: [
                        @if($branch->trashed())
                            {
                                    label: 'Restore Branch',
                                    icon: 'fa-rotate-left',
                                    isForm: true,
                                    action: '{{ route('manage.branch.restore', $branch->id) }}',
                                    hiddenFields: [{
                                        name: "_method",
                                        value: "PATCH"
                                    }]
                                }
                        @else
                            {
                                label: 'Archive Branch',
                                icon: 'fa-box-archive',
                                modal: 'archive-branch-modal',
                                class: 'toolbar__action-button--danger',
                                id: '{{ $branch->id }}',
                                name: '{{ $branch->name }}'
                            }
                        @endif
                    ]
                }
            ]
        }
    };
</script>

<div class="business">
    <aside class="business__sidebar">
        
        @include('components.partials.dashboard_sidebar_info', ['active' => 'branches'])

        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Branch Info</h3>
            <div id="branchInfo" class="dropdown__mini-list">
                <div class="business-info-card">
                    <p class="business-info-card__name">{{ $branch->name }}</p>
                    <div class="business-info-card__address" style="font-size: 13px; color: var(--color-text-light); margin-bottom: 12px;">
                        <p><i class="fa-solid fa-map-pin"></i> {{ $branch->address_line_1 }}</p>
                        <p>{{ $branch->postal_code }} {{ $branch->city }}</p>
                        <p>{{ $branch->country }}</p>
                    </div>
                    <p class="business-info-card__desc">
                        Part of <strong>{{ $branch->business->name }}</strong> business.
                    </p>
                    @can('update', $branch)
                        <button class="business-info-card__edit-btn" type="button" data-modal-target="edit-branch-modal">
                            <i class="fa-solid fa-gear"></i> Manage Branch
                        </button>
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
                        <button class="view-switcher__btn active"><i class="fa-solid fa-layer-group"></i> Resources</button>
                    </div>
                </div>

                <div class="business__header-info">
                    <div class="business__header-info-text">
                        <h2 class="business-header__title">Connected Assets & Services</h2>
                    </div>
                </div>

                <div class="business__header-right">
                    <div class="business__header-right-section_1">
                        @if (!$branch->trashed())
                            <div class="dropdown branch-dropdown">
                                <button class="branch-dropdown__trigger" type="button">
                                    <i class="fa-solid fa-ellipsis-vertical"></i> <span>Branch Actions</span>
                                </button>
                                <div class="branch-dropdown__menu">
                                    <button type="button" class="branch-dropdown__item delete-action" data-modal-target="archive-branch-modal">
                                        <i class="fa-solid fa-box-archive"></i> Archive Branch
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </header>

            <div class="business__body-wrapper">
                <!-- 1. ASSIGN PANEL -->
                <section class="service-assigner-panel">
                    <h3 style="margin-bottom: 1rem; font-size: 16px;">Link Services</h3>
                    
                    @php
                        $availableServices = $services->filter(fn($s) => !$branch->services->contains($s->id));
                    @endphp

                    <div id="assign-container">
                        <div id="multiselect-wrapper" style="{{ $availableServices->count() > 0 ? '' : 'display: none;' }}">
                            <select id="serviceMultiselect" multiple class="custom-multiselect">
                                @foreach($availableServices as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                            <button id="btnAssignServices" class="btn-assign-full" disabled>
                                <i class="fa-solid fa-link"></i> Link Selected Services
                            </button>
                        </div>
                        
                        <div id="empty-select-message" class="select-empty-message" style="{{ $availableServices->count() > 0 ? 'display: none;' : '' }}">
                            All services are already linked.
                        </div>
                    </div>
                </section>

                <h3 style="margin-bottom: 0.8rem; font-size: 16px;">Linked Services</h3>
                <div id="linkedServicesList">
                    @foreach($branch->services as $service)
                        <div class="service-row" data-id="{{ $service->id }}">
                            <a href="{{ route('manage.service.show', $service->id) }}" class="service-card-link">
                                <i class="fa-solid fa-bell-concierge" style="margin-right: 12px; color: var(--color-primary); font-size: 16px;"></i>
                                <span class="service-card__title">{{ $service->name }}</span>
                            </a>
                            <div class="service-card__actions">
                                <form method="POST" action="{{ route('manage.service.branch.unassign', [$service->id, $branch->id]) }}" class="js-unassign-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="button-icon--danger" title="Unlink">
                                        <i class="fa-solid fa-link-slash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </main>
        @include('components.ui.toolbar')
    </div>
</div>

@vite('resources/js/pages/branches/entry.js')

@endsection