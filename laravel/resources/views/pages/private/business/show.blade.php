@extends('layouts.app')

@section('content')
<script>
    window.BE_DATA = {
        business: @json($business),
        routes: {
            update:        "{{ route('business.update', $business->id) }}",
            branchStore:   "{{ route('branch.store') }}",
            branchUpdate:  "{{ route('branch.update', ':id') }}",
            branchDelete:  "{{ route('branch.delete', ':id') }}",
            branchRestore: "{{ route('branch.restore', ':id') }}",
            assignUser:    "{{ route('business.assign', $business->id) }}",
            updateUser:    "{{ route('business.users.update', [$business->id, ':id']) }}",
            deleteUser:    "{{ route('business.users.delete', [$business->id, ':id']) }}",
        },
        csrf: "{{ csrf_token() }}"
    };
</script>

@php
    $allMembers = collect($business->users);
    foreach ($business->branches as $branch) {
        $allMembers = $allMembers->concat($branch->users);
    }
    foreach ($business->services as $service) {
        $allMembers = $allMembers->concat($service->users);
    }
    $allMembers = $allMembers->sortBy('name');

    $owners   = $allMembers->filter(fn($u) => $u->pivot->role === 'owner');
    $managers = $allMembers->filter(fn($u) => $u->pivot->role === 'manager');
    $staff    = $allMembers->filter(fn($u) => $u->pivot->role === 'staff');
@endphp

<div class="business">

    {{-- SIDEBAR --}}
    <aside class="business__sidebar">
        {{-- Business Metadata --}}
        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Business Info</h3>
            <div id="businessInfo" class="dropdown__mini-list">
                <div class="business-info-card">
                    <span class="business-info-card__status filter-item--{{ $business->is_published ? 'green' : 'yellow' }}">
                        {{ $business->is_published ? 'Published' : 'Hidden' }}
                    </span>
                    <p class="business-info-card__name">{{ $business->name }}</p>
                    <p class="business-info-card__desc">
                        {{ Str::limit($business->description, 80) }}
                        @if(strlen($business->description) > 80)
                            <a href="#" class="read-more-trigger" data-full="{{ e($business->description) }}">read more</a>
                        @endif
                    </p>
                    <button
                        class="business-info-card__edit-btn"
                        type="button"
                        data-modal-target="edit-business-modal"
                        title="Edit metadata">
                        <i class="fa-solid fa-gear"></i> Manage Business
                    </button>
                </div>
            </div>
        </section>

        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Branches</h3>
            <div id="branchesList" class="dropdown__mini-list">
                <div class="team-member-item branch-filter-item active" data-filter="all" data-branch-id="" style="cursor: pointer;">
                    <div class="member-info">
                        <span class="member-name">Summary</span>
                        <span class="member-role">View all branches in one place</span>
                    </div>
                </div>

                @can('create', [App\Models\Business\Branch::class, $business])
                    <button class="button-create-branch" type="button" data-modal-target="create-branch-modal">
                        <i class="fa-solid fa-plus"></i> New Branch
                    </button>
                @endcan

                @foreach ($business->branches as $branch)
                    <div class="team-member-item branch-filter-item {{ $branch->trashed() ? 'team-member-item--trashed' : '' }}"
                         data-filter="branch-{{ $branch->id }}"
                         data-branch-id="{{ $branch->id }}"
                         style="cursor: pointer;">
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

        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Manage Employees</h3>
            <div id="manageEmployeesList" class="dropdown__mini-list">
                <form class="manage-empleyees" action="{{ route('business.assign', $business->id) }}" method="POST">
                    @csrf
                    <div class="modal-form__group employees-group">
                        <label class="modal-form__label">Assign To</label>
                        <select id="target-selector" class="modal-form__input"
                            onchange="updateTargetFields(this)">
                            <option value="business" data-id="{{ $business->id }}">Entire Business</option>
                            <optgroup label="Branches">
                                @foreach ($business->branches as $branch)
                                    <option value="branch" data-id="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Services">
                                @foreach ($business->services as $service)
                                    <option value="service" data-id="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>

                    <input type="hidden" name="target_type" id="hidden-target-type" value="business">
                    <input type="hidden" name="target_id"   id="hidden-target-id"   value="{{ $business->id }}">

                    <div class="modal-form__group employees-group">
                        <label class="modal-form__label">Member Email</label>
                        <input type="email" name="email" class="modal-form__input"
                            placeholder="staff@example.com" required>
                    </div>
                    <div class="modal-form__group employees-group">
                        <label class="modal-form__label">Role</label>
                        <select name="role" class="modal-form__input">
                            <option value="manager">Manager</option>
                            <option value="staff">Staff</option>
                        </select>
                    </div>
                    <button type="submit" class="business__nav-link is-active" style="width: 100%;">
                        <i class="fa-solid fa-user-plus"></i> Assign & Notify
                    </button>
                </form>
            </div>
        </section>
    </aside>

    {{-- MAIN --}}
    <main class="business__main">
        <header class="business__header-wrapper">
            <div class="business__header-corner">
                <div class="view-switcher">
                    <button class="view-switcher__btn active" id="showTeam"><i class="fa-solid fa-users"></i> Team</button>
                    <button class="view-switcher__btn" id="showServices"><i class="fa-solid fa-bell-concierge"></i> Services</button>
                </div>
            </div>

            <div class="business__header-info">
                <div class="business__header-info-text">
                    <div class="breadcrumbs">
                        <a href="{{ route('business.index') }}">Dashboard</a> / {{ $business->name }}
                    </div>
                    <h2 class="business-header__title" id="dynamic-title">Business Overview</h2>
                </div>
            </div>

            <div class="business__header-right">
                <div id="branch-header-actions" style="display: none; align-items: center; gap: 6px;">
                    @foreach ($business->branches as $branch)
                        <div class="branch-action-group" data-branch-id="{{ $branch->id }}" style="display: none; align-items: center; gap: 6px;">
                            @if (!$branch->trashed())
                                @can('update', $branch)
                                    <form action="{{ route('branch.update', $branch->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="business_id" value="{{ $business->id }}">
                                        <label class="toggle-label" title="Active Status">
                                            <input type="checkbox" name="is_active" value="1" onchange="this.form.submit()" {{ $branch->is_active ? 'checked' : '' }}>
                                            <span class="toggle-track"></span>
                                        </label>
                                    </form>

                                    <button class="button-icon js-edit-branch" type="button" data-modal-target="edit-branch-modal" data-branch='@json($branch)'>
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                @endcan

                                @can('delete', $branch)
                                    <form action="{{ route('branch.delete', $branch->id) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf @method('DELETE')
                                        <button class="button-icon button-icon--danger" type="submit">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                @endcan
                            @else
                                @can('update', $branch)
                                    <form action="{{ route('branch.restore', $branch->id) }}" method="POST">
                                        @csrf
                                        <button class="button-icon button-icon--success" type="submit">
                                            <i class="fa-solid fa-rotate-left"></i>
                                        </button>
                                    </form>
                                @endcan
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="business__search-wrapper">
                    <div class="business__search-container">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" id="appointmentSearchInput" placeholder="Search client or service...">
                    </div>
                </div>
            </div>
        </header>

        <div class="business__body-wrapper">
            {{-- TEAM VIEW --}}
            <div id="businessTeamView" class="business__panel">
                <div class="dashboard-column dashboard-column--team">
                    <div class="dashboard-card">
                        <div class="card-header"><h3><i class="fa-solid fa-users"></i> Team Management</h3></div>
                        <div class="card-body">
                            @foreach(['Owners' => $owners, 'Managers' => $managers, 'Staff' => $staff] as $title => $collection)
                                <div class="team-section">
                                    <p class="team-section__label">{{ $title }}</p>
                                    @forelse ($collection as $user)
                                        @php
                                            [$modelName, $displayName] = _resolveAssignment($user, $business);
                                            $filterId = ($modelName === 'business') ? 'all' : 'branch-' . $user->pivot->model_id;
                                        @endphp
                                        <div class="team-member-item filterable-member" data-belongs-to="{{ $filterId }}">
                                            <div class="member-info">
                                                <span class="member-name">{{ $user->name }}</span>
                                                <span class="member-role team-member__scope">
                                                    <i class="fa-solid fa-layer-group"></i> {{ $displayName }}
                                                </span>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="team-section__empty">No users.</p>
                                    @endforelse
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- SERVICES VIEW --}}
            <div id="businessServicesView" class="business__panel hidden">
                <div class="dashboard-card">
                    <div class="card-header"><h3><i class="fa-solid fa-bell-concierge"></i> Services</h3></div>
                    <div class="card-body">
                        <div class="services-grid">
                            @foreach ($business->services as $service)
                                <div class="service-item-card filterable-service"
                                    data-belongs-to="{{ $service->branches->pluck('id')->map(fn($id) => 'branch-'.$id)->implode(' ') ?: 'all' }}">
                                    <div class="service-item-card__header"><strong>{{ $service->name }}</strong></div>
                                    <div class="service-item-card__footer">
                                        <a href="{{ route('service.show', $service->id) }}">Edit Schedule</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<style>
    .branch-filter-item.active {
        background: color-mix(in srgb, var(--color-primary), transparent 93%);
        border-left: 5px solid var(--color-primary);
    }
    .branch-filter-item {
        border-left: 5px solid var(--color-border-light);
    }
    .business__header-right { display: flex; align-items: center; gap: 12px; }
</style>

<script>
    function updateTargetFields(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        document.getElementById('hidden-target-type').value = selectedOption.value;
        document.getElementById('hidden-target-id').value   = selectedOption.getAttribute('data-id');
    }

    document.addEventListener('DOMContentLoaded', function () {
        const filterItems   = document.querySelectorAll('#branchesList .branch-filter-item');
        const members       = document.querySelectorAll('.filterable-member');
        const services      = document.querySelectorAll('.filterable-service');
        const titleHeader   = document.getElementById('dynamic-title');
        const headerActions = document.getElementById('branch-header-actions');
        const actionGroups  = document.querySelectorAll('.branch-action-group');
        const targetSelector = document.getElementById('target-selector');

        filterItems.forEach(item => {
            item.addEventListener('click', function () {
                const filter     = this.getAttribute('data-filter');
                const branchId   = this.getAttribute('data-branch-id');
                const branchName = this.querySelector('.member-name').innerText;

                filterItems.forEach(i => i.classList.remove('active'));
                this.classList.add('active');

                titleHeader.innerText = filter === 'all' ? 'Business Overview' : 'Branch: ' + branchName;

                // Toggle Header Actions
                headerActions.style.display = (filter === 'all') ? 'none' : 'flex';
                actionGroups.forEach(g => {
                    g.style.display = g.getAttribute('data-branch-id') === branchId ? 'flex' : 'none';
                });

                // Filter Members & Services
                members.forEach(m => {
                    const belongs = m.getAttribute('data-belongs-to');
                    m.style.display = (filter === 'all' || belongs === filter || belongs === 'all') ? 'flex' : 'none';
                });
                services.forEach(s => {
                    const belongs = s.getAttribute('data-belongs-to') ?? '';
                    s.style.display = (filter === 'all' || belongs === 'all' || belongs.split(' ').includes(filter))
                        ? 'block' : 'none';
                });

                // Sync Target Selector
                if (filter !== 'all' && targetSelector) {
                    const option = Array.from(targetSelector.options).find(o => o.value === 'branch' && o.getAttribute('data-id') === branchId);
                    if (option) {
                        targetSelector.value = 'branch';
                        updateTargetFields(targetSelector);
                    }
                }
            });
        });
    });
</script>

@vite('resources/js/pages/businesses/entry.js')

@php
function _resolveAssignment($user, $business): array {
    $modelName   = strtolower(class_basename($user->pivot->model_type));
    $displayName = 'Entire Business';
    if ($modelName === 'branch') {
        $displayName = $business->branches->firstWhere('id', $user->pivot->model_id)?->name ?? 'Unknown Branch';
    } elseif ($modelName === 'service') {
        $displayName = $business->services->firstWhere('id', $user->pivot->model_id)?->name ?? 'Unknown Service';
    }
    return [$modelName, $displayName];
}
@endphp
@endsection