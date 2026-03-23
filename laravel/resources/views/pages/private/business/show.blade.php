@extends('layouts.app')

@section('content')
    <script>
        window.BE_DATA = {
            business: @json($business),
            routes: {
                update: "{{ route('business.update', $business->id) }}",
                branchStore: "{{ route('branch.store') }}",
                branchUpdate: "{{ route('branch.update', ':id') }}",
                branchDelete: "{{ route('branch.delete', ':id') }}",
                branchRestore: "{{ route('branch.restore', ':id') }}",
                assignUser: "{{ route('business.assign', $business->id) }}",
                updateUser: "{{ route('business.users.update', [$business->id, ':id']) }}",
                deleteUser: "{{ route('business.users.delete', [$business->id, ':id']) }}",
            },
            csrf: "{{ csrf_token() }}"
        };
    </script>

    <div class="business">
        <aside class="business__sidebar">

        {{-- Business Metadata --}}
        <section class="business__filters">
            <h3 class="miniLists__subtitle">
                <i class="fa-solid fa-info-circle"></i> Business Info
            </h3>
            <div class="sidebar-info-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 8px;">
                    <div style="flex: 1; min-width: 0;">
                        <p class="sidebar-info-card__name">{{ $business->name }}</p>
                        <p class="sidebar-info-card__desc">
                            {{ Str::limit($business->description, 80) }}
                            @if(strlen($business->description) > 80)
                                <a href="#" class="read-more-trigger" data-full="{{ e($business->description) }}">read more</a>
                            @endif
                        </p>
                    </div>
                    <button
                        class="button-icon"
                        type="button"
                        data-modal-target="edit-business-modal"
                        title="Edit metadata">
                        <i class="fa-solid fa-pen"></i>
                    </button>
                </div>
                <div style="margin-top: 8px;">
                    <span class="status-cell filter-item--{{ $business->is_published ? 'green' : 'yellow' }}">
                        {{ $business->is_published ? 'Published' : 'Hidden' }}
                    </span>
                </div>
            </div>
        </section>

        {{-- Branches --}}
        <section class="business__filters">
            <h3 class="miniLists__subtitle" style="display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fa-solid fa-code-branch"></i> Branches</span>
                @can('create', [App\Models\Business\Branch::class, $business])
                    <button class="button-icon" type="button"
                        data-modal-target="create-branch-modal">
                        <i class="fa-solid fa-plus"></i>
                    </button>
                @endcan
            </h3>

                <div class="team-mini-list">
                    @foreach ($business->branches as $branch)
                        <div class="team-member-item {{ $branch->trashed() ? 'team-member-item--trashed' : '' }}">
                            <div class="member-info">
                                <span class="member-name">{{ $branch->name }}</span>
                                <span class="member-role">
                                    {{ ucfirst($branch->type) }} • {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>

                        <div style="display: flex; gap: 4px; align-items: center;">
                            @if (!$branch->trashed())

                                @can('update', $branch)
                                    {{-- Active toggle --}}
                                    <form action="{{ route('branch.update', $branch->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <input type="hidden" name="business_id" value="{{ $business->id }}">
                                        <input type="hidden" name="is_active" value="0">
                                        <label class="toggle-label" title="Active Status">
                                            <input type="checkbox" name="is_active" value="1"
                                                onchange="this.form.submit()"
                                                {{ $branch->is_active ? 'checked' : '' }}>
                                            <span class="toggle-track"></span>
                                        </label>
                                    </form>

                                    {{-- Edit --}}
                                    <button class="button-icon" type="button"
                                        data-modal-target="edit-branch-modal"
                                        data-branch='@json($branch)'>
                                        <i class="fa-solid fa-pen"></i>
                                    </button>
                                @endcan

                                @can('delete', $branch)
                                    <form action="{{ route('branch.delete', $branch->id) }}" method="POST"
                                        onsubmit="return confirm('Are you sure?')">
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
                    </div>
                @endforeach
            </div>
        </section>

        </aside>

        <main class="business__main">
            <header class="business__header-wrapper">
                <div class="business__header-info">
                    <div class="breadcrumbs">
                        <a href="{{ route('business.index') }}">Dashboard</a> / {{ $business->name }}
                    </div>
                    <h2 class="business-header__title">Business Overview</h2>
                </div>
            </header>

            <div class="business__body-wrapper dashboard-grid">

                {{-- Team Management --}}
                <div class="dashboard-column">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-users"></i> Team Management</h3>
                        </div>
                        <div class="card-body">

                            <form action="{{ route('business.assign', $business->id) }}" method="POST"
                                style="margin-bottom: 1.25rem;">
                                @csrf

                                <div class="modal-form__group">
                                    <label class="modal-form__label">Assign To</label>
                                    <select id="target-selector" class="modal-form__input"
                                        onchange="updateTargetFields(this)">
                                        <option value="business" data-id="{{ $business->id }}">Entire Business</option>

                                        <optgroup label="Branches">
                                            @foreach ($business->branches as $branch)
                                                <option value="branch" data-id="{{ $branch->id }}">{{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Services">
                                            @foreach ($business->services as $service)
                                                <option value="service" data-id="{{ $service->id }}">{{ $service->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    </select>
                                </div>

                                <input type="hidden" name="target_type" id="hidden-target-type" value="business">
                                <input type="hidden" name="target_id" id="hidden-target-id" value="{{ $business->id }}">

                                <div class="modal-form__group">
                                    <label class="modal-form__label">Member Email</label>
                                    <input type="email" name="email" class="modal-form__input"
                                        placeholder="staff@example.com" required>
                                </div>

                                <div class="modal-form__group">
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

                            <hr style="border: 0; border-top: 1px solid var(--color-border); margin: 1rem 0;">

                            <p
                                style="font-size: 11px; font-weight: 600; color: var(--color-text-tertiary); text-transform: uppercase; margin-bottom: 8px;">
                                Current Members
                            </p>

                            @php
                                $allMembers = collect($business->users);
                                foreach ($business->branches as $branch) {
                                    $allMembers = $allMembers->concat($branch->users);
                                }
                                foreach ($business->services as $service) {
                                    $allMembers = $allMembers->concat($service->users);
                                }
                                $allMembers = $allMembers->sortBy('name');
                            @endphp

                            @foreach ($allMembers as $user)
                                <div class="team-member-item">
                                    <div class="member-info">
                                        <span class="member-name">{{ $user->name }}</span>

                                        <span class="member-role"
                                            style="font-size: 10px; opacity: 0.7; display: block; margin-bottom: 4px;">
                                            <i class="fa-solid fa-layer-group"></i>
                                            @php
                                                $modelName = class_basename($user->pivot->model_type);

                                                // Logic to find the actual name of the Branch or Service
                                                $displayName = 'Entire Business';
                                                if ($modelName === 'branch') {
                                                    $displayName =
                                                        $business->branches->firstWhere('id', $user->pivot->model_id)
                                                            ->name ?? 'Unknown Branch';
                                                } elseif ($modelName === 'service') {
                                                    $displayName =
                                                        $business->services->firstWhere('id', $user->pivot->model_id)
                                                            ->name ?? 'Unknown Service';
                                                }
                                            @endphp
                                            {{ $modelName }}: <strong>{{ $displayName }}</strong>
                                        </span>

                                        @if ($user->pivot->role === 'owner')
                                            <span class="member-role">Owner</span>
                                        @else
                                            <form
                                                action="{{ route('business.users.update', [$business->id, $user->id]) }}"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <input type="hidden" name="target_type"
                                                    value="{{ strtolower(class_basename($user->pivot->model_type)) }}">
                                                <input type="hidden" name="target_id"
                                                    value="{{ $user->pivot->model_id }}">

                                                <select name="role" onchange="this.form.submit()"
                                                    class="role-select-inline">
                                                    <option value="manager"
                                                        {{ $user->pivot->role === 'manager' ? 'selected' : '' }}>Manager
                                                    </option>
                                                    <option value="staff"
                                                        {{ $user->pivot->role === 'staff' ? 'selected' : '' }}>Staff
                                                    </option>
                                                </select>
                                            </form>
                                        @endif
                                    </div>

                                    @if ($user->pivot->role !== 'owner')
                                        <form action="{{ route('business.users.delete', [$business->id, $user->id]) }}"
                                            method="POST" onsubmit="return confirm('Remove this user assignment?')">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="target_type"
                                                value="{{ strtolower(class_basename($user->pivot->model_type)) }}">
                                            <input type="hidden" name="target_id" value="{{ $user->pivot->model_id }}">

                                            <button class="button-icon button-icon--danger" type="submit">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>

                {{-- Services --}}
                <div class="dashboard-column">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3><i class="fa-solid fa-bell-concierge"></i> Services & Availability</h3>
                        </div>
                        <div class="card-body">
                            <div class="services-grid">
                                @foreach ($business->services as $service)
                                    <div class="service-item-card">
                                        <div class="service-item-card__header">
                                            <strong>{{ $service->name }}</strong>
                                        </div>
                                        <div class="service-item-card__footer">
                                            <a href="{{ route('service.show', $service->id) }}">Edit Schedule</a>
                                            <i class="fa-solid fa-chevron-right"></i>
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

    @vite('resources/js/pages/businesses/entry.js')

    <style>
        .sidebar-info-card__name {
            font-weight: 600;
            font-size: 14px;
            margin: 0 0 4px;
            color: var(--color-text-primary);
        }

        .sidebar-info-card__desc {
            font-size: 12px;
            color: var(--color-text-secondary);
            margin: 0;
            line-height: 1.5;
            word-break: break-word;
        }

        .sidebar-info-card__desc .read-more-trigger {
            color: var(--color-text-info);
            text-decoration: none;
            white-space: nowrap;
        }

        .sidebar-info-card__desc .read-more-trigger:hover {
            text-decoration: underline;
        }

        .team-member-item--trashed {
            opacity: 0.6;
            border-style: dashed !important;
            background: var(--color-background-danger);
        }

        .role-select-inline {
            font-size: 11px;
            padding: 2px 4px;
            border: 1px solid var(--color-border);
            border-radius: 4px;
            background: transparent;
            color: inherit;
        }

        .form-error {
            color: #ef4444;
            font-size: 12px;
            margin-top: -4px;
            display: block;
        }

        .modal-form__input.is-invalid {
            border-color: #ef4444;
        }

        .toggle-label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
    </style>

    <script>
        function updateTargetFields(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];

            document.getElementById('hidden-target-type').value = selectedOption.value;
            document.getElementById('hidden-target-id').value = selectedOption.getAttribute('data-id');
        }
    </script>
@endsection
