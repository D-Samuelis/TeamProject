@section("breadcrumb-{$business->id}", $business->name)

@extends('web.layouts.app')

@section('title', 'Bexora | Business Info')

@section('content')

    <script>
        window.BE_DATA = {
            business: @json($business),
            routes: {
                update: "{{ route('manage.business.update', $business->id) }}",
                delete: "{{ route('manage.business.delete', $business->id) }}",
                restore: "{{ route('manage.business.restore', $business->id) }}",
                branchStore: "{{ route('manage.branch.store') }}",
                branchUpdate: "{{ route('manage.branch.update', ':id') }}",
                branchDelete: "{{ route('manage.branch.delete', ':id') }}",
                branchRestore: "{{ route('manage.branch.restore', ':id') }}",
                assignUser: "{{ route('manage.business.assign', $business->id) }}",
                updateUser: "{{ route('manage.business.users.update', [$business->id, ':id']) }}",
                deleteUser: "{{ route('manage.business.users.delete', [$business->id, ':id']) }}",
            },
            csrf: "{{ csrf_token() }}",
            toolbar: {
                centerGroups: [{
                        groupId: 'danger-zone',
                        actions: [
                            @if ($business->trashed())
                                {
                                    label: 'Restore Business',
                                    icon: 'fa-rotate-left',
                                    isForm: true,
                                    toastTitle: 'Business restored',
                                    toastType: 'success',
                                    toastText: 'The business is successfully restored.',
                                    action: '{{ route('manage.business.restore', $business->id) }}',
                                    hiddenFields: [{
                                        name: '_method',
                                        value: 'PATCH'
                                    }]
                                }
                            @else
                                {
                                    label: 'Archive Business',
                                    icon: 'fa-box-archive',
                                    modal: 'archive-business-modal',
                                    class: 'toolbar__action-button--danger',
                                    id: '{{ $business->id }}',
                                    name: '{{ $business->name }}'
                                }
                            @endif
                        ]
                    },
                    {
                        groupId: 'manage',
                        hasDivider: true,
                        actions: [{
                                label: 'Add Branch',
                                icon: 'fa-code-branch',
                                modal: 'create-branch-modal'
                            },
                            {
                                label: 'Assign User',
                                icon: 'fa-user-plus',
                                modal: 'assign-user-modal'
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

        console.log('BE_DATA:', window.BE_DATA.business);
    </script>

    @php
        // Zozbieraj všetky priradenia (bez unique!) zachovaj pivot pre každé
        $allMembers = collect();

        foreach ($business->users as $u) {
            $allMembers->push($u);
        }
        foreach ($business->branches as $branch) {
            foreach ($branch->users as $u) {
                $allMembers->push($u);
            }
        }
        foreach ($business->services as $service) {
            foreach ($service->users as $u) {
                $allMembers->push($u);
            }
        }

        // Zoskup podľa user ID + role, zozbieraj všetky belongs-to hodnoty
        $memberMap = [];
        foreach ($allMembers as $u) {
            $key = $u->id . '_' . $u->pivot->role;
            if (!isset($memberMap[$key])) {
                $memberMap[$key] = [
                    'user'       => $u,
                    'role'       => $u->pivot->role,
                    'belongs'    => collect(),
                    'subtext'    => [],
                ];
            }
            $modelName = strtolower(class_basename($u->pivot->model_type));
            if ($modelName === 'business') {
                $memberMap[$key]['belongs']->push('all');
                $memberMap[$key]['subtext'][] = 'Entire Business';
            } elseif ($modelName === 'branch') {
                $memberMap[$key]['belongs']->push('branch-' . $u->pivot->model_id);
                $branchName = $business->branches->firstWhere('id', $u->pivot->model_id)?->name ?? 'Unknown Branch';
                $memberMap[$key]['subtext'][] = $branchName;
            } elseif ($modelName === 'service') {
                $service = $business->services->firstWhere('id', $u->pivot->model_id);
                if ($service) {
                    foreach ($service->branches as $b) {
                        $memberMap[$key]['belongs']->push('branch-' . $b->id);
                        $memberMap[$key]['subtext'][] = $b->name; // ← branch názov, nie service názov
                    }
                }
                // Ak service nemá žiadnu branch, fallback
                if ($service && $service->branches->isEmpty()) {
                    $memberMap[$key]['belongs']->push('all');
                    $memberMap[$key]['subtext'][] = 'Entire Business';
                }
            }
        }

        $owners   = collect($memberMap)->filter(fn($m) => $m['role'] === 'owner');
        $managers = collect($memberMap)->filter(fn($m) => $m['role'] === 'manager');
        $staff    = collect($memberMap)->filter(fn($m) => $m['role'] === 'staff');
    @endphp

    @php
        $stateColor = match($business->state->value) {
            'approved', 'published' => 'state-green',
            'pending', 'draft' => 'state-yellow',
        };
    @endphp

    <div class="business">

        {{-- SIDEBAR --}}
        <aside class="business__sidebar">

            @include('components.partials.dashboard_sidebar_info', ['active' => 'businesses'])

            {{-- Branches Section --}}
            <section class="business__filters">
                <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Branches</h3>
                <div id="branchesList" class="dropdown__mini-list">
                    <div class="team-member-item branch-filter-item active" data-filter="all" data-branch-id=""
                        style="cursor: pointer;">
                        <div class="member-info">
                            <span class="member-name">Summary</span>
                            <span class="member-role">View all branches</span>
                        </div>
                    </div>

                    @can('create', [App\Models\Business\Branch::class, $business])
                        <button class="button-create-branch" type="button" data-modal-target="create-branch-modal">
                            <i class="fa-solid fa-plus"></i> New Branch
                        </button>
                    @endcan

                    @foreach ($business->branches as $branch)
                        <div class="team-member-item branch-filter-item {{ $branch->trashed() ? 'team-member-item--trashed' : '' }} {{ request('branch') == $branch->id ? 'is-active' : '' }}"
                            data-filter="branch-{{ $branch->id }}" data-branch-id="{{ $branch->id }}"
                            data-branch='{{ json_encode($branch) }}' style="cursor: pointer;">
                            <div class="member-info">
                                <span class="member-name">{{ $branch->name }}</span>
                                <span class="member-role">
                                    {{ ucfirst($branch->type) }} • {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        </aside>

        <div class="display-column">
            <x-ui.breadcrumbs />
            <main class="business__main">
                <header class="business__header-wrapper">
                    <div class="business__header-corner">
                        <div class="view-switcher">
                            <button class="view-switcher__btn active" id="showTeam"><i class="fa-solid fa-users"></i>
                                Business</button>
                        </div>
                    </div>

                    <div class="business__header-info">
                        <div class="business__header-info-text">
                            <h2 class="business-header__title" id="dynamic-title">Business Overview</h2>
                        </div>
                    </div>

                    <div class="business__header-right">
                        <div class="business__header-right-section_1">
                            
                        </div>
                        <div class="business__header-right-section_2">
                            <div class="list-view__search-wrapper">
                                <div class="search-container">
                                    <i class="fa-solid fa-magnifying-glass"></i>
                                    <input type="text" id="appointmentSearchInput" placeholder="Search team...">
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="business-layout-container">
                    {{-- HLAVNÝ OBSAH (Stred) --}}
                    <main class="business__main">
                        <div class="business__content-scroller">

                            {{-- BUSINESS INFO CARD (presunuté zo sidebaru) --}}
                            <section class="content-section" id="section-business-info">
                                <div class="business-detail-card">
                                    <div class="business-detail-card__header">
                                        <div class="business-detail-card__title-row">
                                            <h2 class="business-detail-card__name">{{ $business->name }}</h2>
                                            <span class="business-detail-card__status service-item-card__badge--{{ $business->is_published ? 'active' : 'inactive' }}">
                                                {{ $business->is_published ? 'Published' : 'Hidden' }}
                                            </span>
                                        </div>
                                        <p class="business-detail-card__desc">{{ $business->description }}</p>
                                    </div>

                                    <div class="business-detail-card__meta">
                                        @if ($business->category_id)
                                            <div class="business-detail-card__meta-item state-orange">
                                                <span class="business-detail-card__meta-label"><i class="fa-solid fa-tag"></i> Category</span>
                                                <span class="business-detail-card__meta-value">{{ $business->category?->name ?? '—' }}</span>
                                            </div>
                                        @endif
                                        <div class="business-detail-card__meta-item state-purple">
                                            <span class="business-detail-card__meta-label"><i class="fa-solid fa-code-branch"></i> Branches</span>
                                            <span class="business-detail-card__meta-value">{{ $business->branches->count() }}</span>
                                        </div>
                                        <div class="business-detail-card__meta-item state-blue">
                                            <span class="business-detail-card__meta-label"><i class="fa-solid fa-bell-concierge"></i> Services</span>
                                            <span class="business-detail-card__meta-value">{{ $business->services->count() }}</span>
                                        </div>
                                        <div class="business-detail-card__meta-item-divider">
                                        </div>
                                        <div class="business-detail-card__meta-item state-gray">
                                            <span class="business-detail-card__meta-label"><i class="fa-solid fa-calendar"></i> Created</span>
                                            <span class="business-detail-card__meta-value">{{ \Carbon\Carbon::parse($business->created_at)->format('d. M Y') }}</span>
                                        </div>
                                        @if ($business->state)
                                            <div class="business-detail-card__meta-item {{ $stateColor }}">
                                                <span class="business-detail-card__meta-label">
                                                    <i class="fa-solid fa-circle-dot"></i> State
                                                </span>
                                                <span class="business-detail-card__meta-value">
                                                    {{ ucfirst($business->state->value) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="manage-business-actions">Business Actions</div>

                                    <div class="business-detail-card__actions">
                                        <button class="manage-business-button" type="button" data-modal-target="edit-business-modal">
                                            <i class="fa-solid fa-gear"></i> Manage Business
                                        </button>
                                        @if (!$business->trashed())
                                            <button class="archive-business-button" type="button" data-modal-target="archive-business-modal"
                                                data-id="{{ $business->id }}" data-name="{{ $business->name }}">
                                                <i class="fa-solid fa-box-archive"></i> Archive
                                            </button>
                                        @else
                                            <form method="POST" action="{{ route('manage.business.restore', $business->id) }}" style="display:inline">
                                                @csrf
                                                @method('PATCH')
                                                <button class="btn btn--success btn--sm" type="submit">
                                                    <i class="fa-solid fa-rotate-left"></i> Restore
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </section>

                            {{-- SERVICES --}}
                            <section class="content-section" id="section-services">
                                <div class="card-header">
                                    <h3><i class="fa-solid fa-bell-concierge"></i> Services</h3>
                                </div>
                                <div class="services-grid">
                                    @foreach ($business->services as $service)
                                        <div class="service-item-card filterable-service"
                                            data-belongs-to="{{ $service->branches->pluck('id')->map(fn($id) => 'branch-' . $id)->implode(' ') ?: 'all' }}">

                                            <div class="service-item-card__header">
                                                <strong class="service-item-card__name">{{ $service->name }}</strong>
                                                @if ($service->is_active)
                                                    <span class="service-item-card__badge service-item-card__badge--active">Active</span>
                                                @else
                                                    <span class="service-item-card__badge service-item-card__badge--inactive">Inactive</span>
                                                @endif
                                            </div>

                                            @if ($service->description)
                                                <p class="service-item-card__desc">{{ Str::limit($service->description, 80) }}</p>
                                            @endif

                                            <div class="service-item-card__meta">
                                                @if ($service->price)
                                                    <span class="service-item-card__meta-item">
                                                        <i class="fa-solid fa-euro-sign"></i> {{ number_format($service->price, 2) }}
                                                    </span>
                                                @endif
                                                @if ($service->duration_minutes)
                                                    <span class="service-item-card__meta-item">
                                                        <i class="fa-solid fa-clock"></i> {{ $service->duration_minutes }} min
                                                    </span>
                                                @endif
                                                @if ($service->location_type)
                                                    <span class="service-item-card__meta-item">
                                                        <i class="fa-solid fa-location-dot"></i> {{ ucfirst($service->location_type) }}
                                                    </span>
                                                @endif
                                                @if ($service->requires_manual_acceptance)
                                                    <span class="service-item-card__meta-item service-item-card__meta-item--warning">
                                                        <i class="fa-solid fa-hand"></i> Manual approval
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($service->branches->isNotEmpty())
                                                <div class="service-item-card__branches">
                                                    @foreach ($service->branches as $b)
                                                        <span class="service-item-card__branch-tag">{{ $b->name }}</span>
                                                    @endforeach
                                                </div>
                                            @endif

                                            <div class="service-item-card__footer">
                                                <a class="service-manage-button" href="{{ route('manage.service.show', $service->id) }}">
                                                    <i class="fa-solid fa-gear"></i> Manage
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </section>

                        </div>
                    </main>

                    <aside class="business__right-sidebar">
                        <p class="business__sidebar-header-title">Team Members</p>
                        <div class="members-container">
                            @foreach (['Owner' => $owners, 'Manager' => $managers, 'Staff' => $staff] as $roleName => $collection)
                                <div class="member-group" id="group-{{ strtolower($roleName) }}">
                                    <div class="member-group__label">
                                        {{ $roleName }} (<span class="js-count">0</span>)
                                    </div>
                                    
                                    @foreach ($collection as $member)
                                        @php
                                            $belongsAttr = $member['belongs']->unique()->implode(' ') ?: 'all';
                                            $subtexts = array_unique($member['subtext']);
                                            $user = $member['user'];
                                            $modelName = strtolower(class_basename($user->pivot->model_type));
                                            $targetId = $user->pivot->model_id;
                                        @endphp
                                        <div class="member-item filterable-member" data-belongs-to="{{ $belongsAttr }}">
                                            <div class="member-item__avatar">{{ substr($user->name, 0, 1) }}</div>
                                            <div class="member-item__info display-column">
                                                <span class="member-item__name">{{ $user->name }}</span>
                                                <span class="member-item__email">{{ $user->email }}</span>

                                                @if (count($subtexts) <= 1)
                                                    <span class="member-item__subtext">{{ $subtexts[0] ?? 'Entire Business' }}</span>
                                                @else
                                                    <span class="member-item__subtext member-item__branches-trigger"
                                                        data-branches="{{ implode('|', $subtexts) }}">
                                                        {{ count($subtexts) }} branches
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="user-actions-buttons">
                                                <button class="member-item__remove js-remove-user-btn"
                                                    title="Remove"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ $user->name }}"
                                                    data-display-name="{{ $subtexts[0] ?? 'Entire Business' }}"
                                                    data-target-type="{{ $modelName }}"
                                                    data-target-id="{{ $targetId }}">
                                                    <i class="fa-solid fa-user-slash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </aside>
                </div>
            </main>
            @include('components.ui.toolbar')
        </div>
    </div>

    @vite('resources/js/pages/businesses/entry.js')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterItems = document.querySelectorAll('#branchesList .branch-filter-item');
            const members = document.querySelectorAll('.filterable-member');
            const services = document.querySelectorAll('.filterable-service');
            const titleHeader = document.getElementById('dynamic-title');
            const branchDesc = document.getElementById('branchDesc');

            filterItems.forEach(item => {
                item.addEventListener('click', function() {
                    const filter = this.getAttribute('data-filter');
                    const branchId = this.getAttribute('data-branch-id');
                    const branchName = this.querySelector('.member-name')?.innerText || '';
                    const branchData = this.dataset.branch ? JSON.parse(this.dataset.branch) : null;

                    // 1. UI stavy
                    filterItems.forEach(i => i.classList.remove('is-active', 'active'));
                    this.classList.add('is-active');

                    // 2. Update Summary
                    if (filter === 'all') {
                        titleHeader.innerText = 'Business Overview';
                        if (branchDesc) branchDesc.innerText = window.BE_DATA.business.description || '';
                    } else {
                        titleHeader.innerText = 'Branch: ' + branchName;
                        if (branchDesc) branchDesc.innerText = branchData?.description || 'No specific description.';
                    }

                    // 3. Filtrácia členov
                    members.forEach(m => {
                        const belongsTo = (m.getAttribute('data-belongs-to') || '').split(' ');
                        const isVisible = (filter === 'all' || belongsTo.includes('all') || belongsTo.includes(filter));
                        m.style.display = isVisible ? 'flex' : 'none';
                    });

                    // 4. Update počtov a schovanie prázdnych skupín
                    document.querySelectorAll('.member-group').forEach(group => {
                        const visibleInGroup = Array.from(group.querySelectorAll('.filterable-member'))
                                                    .filter(m => m.style.display !== 'none').length;
                        const countSpan = group.querySelector('.js-count');
                        if (countSpan) countSpan.innerText = visibleInGroup;
                        group.style.display = visibleInGroup > 0 ? 'block' : 'none';
                    });

                    // 5. Filtrácia služieb
                    services.forEach(s => {
                        const sBelongs = (s.getAttribute('data-belongs-to') || '').split(' ');
                        s.style.display = (filter === 'all' || sBelongs.includes(filter)) ? 'block' : 'none';
                    });

                    // 6. Sync URL
                    const url = new URL(window.location);
                    branchId ? url.searchParams.set('branch', branchId) : url.searchParams.delete('branch');
                    window.history.replaceState({}, '', url);
                });
            });

            // Spustenie pri načítaní
            const params = new URLSearchParams(window.location.search);
            const bParam = params.get('branch');
            if (bParam) {
                document.querySelector(`.branch-filter-item[data-branch-id="${bParam}"]`)?.click();
            } else {
                document.querySelector('.branch-filter-item[data-filter="all"]')?.click();
            }

            // Branches dropdown portal
            const branchesPopup = document.createElement('ul');
            branchesPopup.className = 'member-item__branches-list';
            branchesPopup.style.display = 'none';
            document.body.appendChild(branchesPopup);

            document.querySelectorAll('.member-item__branches-trigger').forEach(trigger => {
                trigger.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const branches = this.dataset.branches.split('|');
                    const rect = this.getBoundingClientRect();

                    branchesPopup.innerHTML = branches.map(b => `<li>${b}</li>`).join('');
                    branchesPopup.style.display = 'block';
                    branchesPopup.style.position = 'fixed';
                    branchesPopup.style.top = (rect.bottom + 4) + 'px';
                    branchesPopup.style.left = rect.left + 'px';
                });
            });

            document.addEventListener('click', function() {
                branchesPopup.style.display = 'none';
            });
        });
    </script>

    @php
        function _resolveAssignment($user, $business): array
        {
            $modelName = strtolower(class_basename($user->pivot->model_type));
            $displayName = 'Entire Business';
            if ($modelName === 'branch') {
                $displayName = $business->branches->firstWhere('id', $user->pivot->model_id)?->name ?? 'Unknown Branch';
            } elseif ($modelName === 'service') {
                $displayName =
                    $business->services->firstWhere('id', $user->pivot->model_id)?->name ?? 'Unknown Service';
            }
            return [$modelName, $displayName];
        }
    @endphp
@endsection
