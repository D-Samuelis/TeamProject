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
            update: "{{ route('manage.branch.update', $branch->id) }}",
            delete: "{{ route('manage.branch.delete', $branch->id) }}",
            restore: "{{ route('manage.branch.restore', $branch->id) }}",
            unassignService: "{{ route('manage.service.branch.unassign', [':serviceId', $branch->id]) }}",
            assignService: "{{ route('manage.service.branch.assign', [':serviceId', $branch->id]) }}"
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
                                action: '{{ route("manage.branch.restore", $branch->id) }}'
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
                                    <button type="button" class="branch-dropdown__item" data-modal-target="assign-service-modal">
                                        <i class="fa-solid fa-plus"></i> Assign Service
                                    </button>
                                    <div class="branch-dropdown__divider"></div>
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
                <h3 style="margin-bottom: 1rem; color: var(--color-text-dark);">Active Assets</h3>
                <div class="rule-panel">
                    @forelse($branch->assets as $asset)
                        <div class="rule-card">
                            <div class="rule-card__header">
                                <div class="rule-card__left">
                                    <div class="rule-card__priority">#{{ $loop->iteration }}</div>
                                    <div class="rule-card__meta">
                                        <a href="{{ route('manage.asset.show', $asset->id) }}" class="rule-card__title">{{ $asset->name }}</a>
                                        <p class="rule-card__description">{{ Str::limit($asset->description, 50) }}</p>
                                    </div>
                                </div>
                                <div class="rule-card__actions">
                                    <div class="status__badge {{ $asset->is_active ? 'bg__badge-green' : 'bg__badge-yellow' }}">
                                        {{ $asset->is_active ? 'Active' : 'Inactive' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="rule-panel__empty">No assets assigned to this branch.</p>
                    @endforelse
                </div>

                <h3 style="margin: 2rem 0 1rem; color: var(--color-text-dark);">Linked Services</h3>
                <div class="rule-panel">
                    @forelse($branch->services as $service)
                        <div class="rule-card">
                            <div class="rule-card__header">
                                <div class="rule-card__left">
                                    <div class="rule-card__meta">
                                        <strong class="rule-card__title">{{ $service->name }}</strong>
                                    </div>
                                </div>
                                <div class="rule-card__actions">
                                    @can('assign', [$service, $branch])
                                        <form method="POST" action="{{ route('manage.service.branch.unassign', [$service->id, $branch->id]) }}">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="button-icon button-icon--danger" title="Unassign Service">
                                                <i class="fa-solid fa-link-slash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="rule-panel__empty">No services linked to this branch.</p>
                    @endforelse
                </div>
            </div>
        </main>
        @include('components.ui.toolbar')
    </div>
</div>

@vite('resources/js/pages/branches/entry.js')

@endsection