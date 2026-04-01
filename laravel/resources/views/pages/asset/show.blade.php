@extends('layouts.app')

@section('title', 'Bexora | My Businesses')

@section('content')

@if(session('success'))
    <p style="color:green; position:absolute; z-index: 10000;">{{ session('success') }}</p>
@endif

<div class="business">

    <aside class="business__sidebar">

        {{-- Asset Info Section --}}
        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Asset Info</h3>
            <div id="assetInfo" class="dropdown__mini-list">
                <div class="business-info-card">
                    <p class="business-info-card__name">{{ $asset->name }}</p>
                    <p class="business-info-card__desc">
                        {{ Str::limit($asset->description, 80) }}
                        @if(strlen($asset->description ?? '') > 80)
                            <a href="#" class="read-more-trigger" data-full="{{ e($asset->description) }}">read more</a>
                        @endif
                    </p>
                    @can('update', $asset)
                        <button class="business-info-card__edit-btn" type="button" onclick="openModal('editAssetModal')">
                            <i class="fa-solid fa-gear"></i> Manage Asset
                        </button>
                    @endcan
                </div>
            </div>
        </section>

        @php
        function branchColor(int $id): array {
            $hue    = ($id * 137.508) % 360;
            $bg     = "hsl({$hue}, 70%, 94%)";
            $border = "hsl({$hue}, 55%, 40%)";
            $text   = "hsl({$hue}, 55%, 28%)";
            $dot    = "hsl({$hue}, 60%, 55%)";
            return compact('bg', 'border', 'text', 'dot');
        }
        @endphp

        {{-- Branches & Services Tree --}}
        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Branches & Services</h3>
            <div id="branchesList" class="dropdown__mini-list">
                @forelse($asset->branches as $b)
                    @php
                        $c = branchColor($b->id);
                        $branchServices = $asset->services->filter(
                            fn($s) => $s->branches->contains('id', $b->id)
                        );
                    @endphp

                    {{-- Branch row (folder) --}}
                    <div class="service-item" style="background:{{ $c['bg'] }};border-left:3px solid {{ $c['border'] }};border-radius:0 6px 6px 0;">
                        <div class="member-info">
                            <span class="member-name" style="color:{{ $c['text'] }};">{{ $b->name }}</span>
                        </div>
                    </div>

                    {{-- Services under this branch --}}
                    @forelse($branchServices as $s)
                        <div class="branch-item">
                            <div class="icon">
                                <i class="fa-solid fa-circle" style="color:{{ $c['dot'] }};font-size:6px;"></i>
                            </div>
                            <div class="member-info">
                                <span class="member-name">{{ $s->name }}</span>
                            </div>
                        </div>
                    @empty
                        <p style="font-size:12px;color:#bbb;font-style:italic;padding-left:24px;">No services</p>
                    @endforelse
                @empty
                    <p style="color:#888;font-size:13px;padding:8px;">None</p>
                @endforelse
            </div>
        </section>

    </aside>

    <main class="business__main">

        <header class="business__header-wrapper">
            <div class="business__header-corner">
                <div class="view-switcher">
                    <button class="view-switcher__btn active"><i class="fa-solid fa-list-check"></i> Rules</button>
                </div>
            </div>

            <div class="business__header-info">
                <div class="business__header-info-text">
                    <div class="breadcrumbs">
                        <a href="{{ route('manage.business.index') }}">Dashboard</a> / {{ $asset->name }}
                    </div>
                    <h2 class="business-header__title">Asset Rules</h2>
                </div>
            </div>

            <div class="business__header-right">

                {{-- ── Asset Actions (status badge + dropdown) ── --}}
                <div class="business__header-right-section_1">
                    <div style="display:flex;align-items:center;gap:8px;">

                        {{-- DROPDOWN: Asset Actions --}}
                        <div class="dropdown branch-dropdown">
                            <button class="branch-dropdown__trigger" type="button">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                <span>Asset Actions</span>
                            </button>

                            <div class="branch-dropdown__menu">

                                {{-- DUMMY status toggle — no BE yet --}}
                                <button type="button" class="branch-dropdown__item" disabled title="Coming soon">
                                    <i class="fa-solid fa-circle status-dot {{ $asset->is_active ?? true ? 'text-green' : 'text-yellow' }}"></i>
                                    Status:
                                    <div class="status__badge {{ $asset->is_active ?? true ? 'bg__badge-green' : 'bg__badge-yellow' }}">
                                        {{ $asset->is_active ?? true ? 'Active' : 'Inactive' }}
                                    </div>
                                </button>

                                {{-- Add Rule --}}
                                @can('update', $asset)
                                    <button type="button"
                                            class="branch-dropdown__item"
                                            onclick="openModal('createRuleModal')">
                                        <i class="fa-solid fa-plus"></i> Add Rule
                                    </button>
                                @endcan

                                {{-- Manage Asset --}}
                                @can('update', $asset)
                                    <button type="button"
                                            class="branch-dropdown__item"
                                            onclick="openModal('editAssetModal')">
                                        <i class="fa-solid fa-gear"></i> Manage Asset
                                    </button>
                                @endcan

                                <div class="branch-dropdown__divider"></div>

                                {{-- Archive Asset --}}
                                @can('destroy', $asset)
                                    <form method="POST" action="{{ route('manage.asset.delete', $asset->id) }}"
                                          onsubmit="return confirm('Archive this asset?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="branch-dropdown__item delete-action">
                                            <i class="fa-solid fa-box-archive"></i> Archive Asset
                                        </button>
                                    </form>
                                @endcan

                            </div>
                        </div>

                    </div>
                </div>

                <div class="business__header-right-section_2">
                    <div class="list-view__search-wrapper">
                        <div class="search-container">
                            <i class="fa-solid fa-magnifying-glass"></i>
                            <input type="text" id="ruleSearchInput" placeholder="Search rules...">
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="business__body-wrapper asset-rules">
            <div class="business__panel" data-reorder-url="{{ route('manage.rule.reorder_all') }}">


                @php $sortedRules = $asset->rules->sortBy('priority'); @endphp

                @forelse($sortedRules as $rule)
                    @php
                        $rs = is_string($rule->rule_set) ? json_decode($rule->rule_set, true) : $rule->rule_set;
                        $rs = $rs['days'] ?? $rs;
                        $isFirst = $loop->first;
                        $isLast  = $loop->last;
                    @endphp
                        <div data-rule-id="{{ $rule->id }}" 
                            class="rule-card-item"
                            style="border:1px solid #ddd; border-radius:6px; padding:1rem; margin-bottom:1rem; background: #fff; position: relative;">
                        <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                            <div style="display:flex;align-items:flex-start;gap:10px;">
                                <div style="display:flex;flex-direction:column;align-items:center;gap:2px;flex-shrink:0;">
                                    @can('update', $asset)
                                        <form method="POST" action="{{ route('manage.rule.reorder', $rule->id) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="up">
                                            <button type="submit"
                                                    {{ $isFirst ? 'disabled' : '' }}
                                                    style="background:none;border:none;cursor:{{ $isFirst ? 'default' : 'pointer' }};color:{{ $isFirst ? '#ddd' : '#555' }};font-size:12px;padding:0;line-height:1;">▲</button>
                                        </form>
                                    @endcan
                                    <span class="js-priority-label" style="font-size:11px;font-weight:600;color:#888;background:#f3f4f6;border-radius:4px;padding:1px 6px;">
                                        #{{ $rule->priority }}
                                    </span>
                                    @can('update', $asset)
                                        <form method="POST" action="{{ route('manage.rule.reorder', $rule->id) }}">
                                            @csrf
                                            <input type="hidden" name="direction" value="down">
                                            <button type="submit"
                                                    {{ $isLast ? 'disabled' : '' }}
                                                    style="background:none;border:none;cursor:{{ $isLast ? 'default' : 'pointer' }};color:{{ $isLast ? '#ddd' : '#555' }};font-size:12px;padding:0;line-height:1;">▼</button>
                                        </form>
                                    @endcan
                                </div>

                                <div>
                                    <strong>{{ $rule->title }}</strong>
                                    @if($rule->description)
                                        <p style="color:#888;font-size:13px;margin:2px 0;">{{ $rule->description }}</p>
                                    @endif
                                    <p style="font-size:12px;color:#aaa;margin:4px 0 0;">
                                        {{ $rule->valid_from ? \Carbon\Carbon::parse($rule->valid_from)->format('d.m.Y') : '–' }}
                                        →
                                        {{ $rule->valid_to ? \Carbon\Carbon::parse($rule->valid_to)->format('d.m.Y') : '–' }}
                                    </p>
                                </div>
                            </div>

                            <div style="display:flex;gap:6px;flex-shrink:0;">
                                @can('update', $asset)
                                    <button onclick='openEditRuleModal({{ $rule->id }}, @json($rule))'>Edit</button>
                                @endcan
                                @can('update', $asset)
                                    <form method="POST" action="{{ route('manage.rule.delete', $rule->id) }}"
                                          onsubmit="return confirm('Delete this rule?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="color:red;">Delete</button>
                                    </form>
                                @endcan
                            </div>
                        </div>

                        <div style="margin-top:0.75rem;font-size:13px;display:flex;flex-wrap:wrap;gap:8px;padding-left:34px;">
                            @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $i => $dayName)
                                <span>
                                    <strong>{{ $dayName }}:</strong>
                                    @if(empty($rs[$i]))
                                        <span style="color:#aaa;">closed</span>
                                    @else
                                        {{ collect($rs[$i])->map(fn($r) => $r['from_time'].'–'.$r['to_time'])->join(', ') }}
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p style="color:#888;">No rules yet. Add one to define working hours.</p>
                @endforelse

            </div>
        </div>
    </main>
</div>


{{-- MODAL: Edit Asset --}}
<div id="editAssetModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editAssetModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">Edit Asset</h2>

        <form method="POST" action="{{ route('manage.asset.update', $asset->id) }}">
            @csrf @method('PUT')

            <div style="margin-bottom:1rem;">
                <label>Name <span style="color:red;">*</span></label><br>
                <input type="text" name="name" value="{{ old('name', $asset->name) }}"
                       style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>
                @error('name') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label>Description</label><br>
                <textarea name="description" rows="3"
                          style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">{{ old('description', $asset->description) }}</textarea>
                @error('description') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1rem;">
                <label>Branches</label>
                <div style="border:1px solid #ccc;border-radius:4px;padding:8px;max-height:150px;overflow-y:auto;">
                    @foreach($branches as $branch)
                        @php $currentBranchIds = old('branch_ids', $asset->branches->pluck('id')->toArray()); @endphp
                        <label style="display:block;padding:3px 0;cursor:pointer;">
                            <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                                {{ in_array($branch->id, $currentBranchIds) ? 'checked' : '' }}>
                            {{ $branch->name }}
                        </label>
                    @endforeach
                </div>
                @error('branch_ids') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="margin-bottom:1.5rem;">
                <label>Services</label>
                <p style="font-size:12px;color:#888;margin:2px 0 6px;">Only services linked to at least one selected branch.</p>
                <div style="border:1px solid #ccc;border-radius:4px;padding:8px;max-height:150px;overflow-y:auto;">
                    @foreach($services as $service)
                        @php $currentServiceIds = old('service_ids', $asset->services->pluck('id')->toArray()); @endphp
                        <label style="display:block;padding:3px 0;cursor:pointer;">
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}"
                                {{ in_array($service->id, $currentServiceIds) ? 'checked' : '' }}>
                            {{ $service->name }}
                            <span style="font-size:12px;color:#aaa;">({{ $service->branches->pluck('name')->join(', ') }})</span>
                        </label>
                    @endforeach
                </div>
                @error('service_ids') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" onclick="closeModal('editAssetModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>


{{-- MODAL: Create Rule --}}
<div id="createRuleModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('createRuleModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">New Rule</h2>

        <form method="POST" action="{{ route('manage.rule.store') }}" onsubmit="return serializeSchedule('create')">
            @csrf
            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
            <input type="hidden" name="rule_set" id="create_rule_set_input">

            @include('pages.asset.partials.rule-form', ['prefix' => 'create', 'rule' => null])

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('createRuleModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Rule</button>
            </div>
        </form>
    </div>
</div>


{{-- MODAL: Edit Rule --}}
<div id="editRuleModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editRuleModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">Edit Rule</h2>

        <form method="POST" id="editRuleForm" onsubmit="return serializeSchedule('edit')">
            @csrf @method('PUT')
            <input type="hidden" name="rule_set" id="edit_rule_set_input">

            @include('pages.asset.partials.rule-form', ['prefix' => 'edit', 'rule' => null])

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('editRuleModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>


<style>
    .modal-backdrop { position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center; }
    .modal-box      { background:#fff;border-radius:8px;padding:2rem;width:100%;max-width:640px;max-height:90vh;overflow-y:auto;position:relative; }
    .modal-close    { position:absolute;top:1rem;right:1rem;background:none;border:none;font-size:1.25rem;cursor:pointer; }
    .btn-primary    { padding:8px 16px;background:#1a1a1a;color:#fff;border:none;border-radius:4px;cursor:pointer; }
    .btn-secondary  { padding:8px 16px;background:#fff;border:1px solid #ccc;border-radius:4px;cursor:pointer; }
    .schedule-day        { margin-bottom:0.75rem; }
    .schedule-day-header { display:flex;align-items:center;gap:8px;margin-bottom:4px; }
    .schedule-day-header label { font-size:14px;font-weight:500;min-width:36px; }
    .ranges-list    { padding-left:44px;display:flex;flex-direction:column;gap:4px; }
    .range-row      { display:flex;align-items:center;gap:6px; }
    .range-row input[type=time] { padding:4px 6px;border:1px solid #ccc;border-radius:4px;font-size:13px; }
    .btn-add-range  { font-size:12px;color:#2563eb;background:none;border:none;cursor:pointer;padding:2px 0; }
    .btn-del-range  { font-size:13px;color:#aaa;background:none;border:none;cursor:pointer; }
    .btn-del-range:hover { color:red; }
</style>


@vite('resources/js/pages/assets/entry.js')


<script>
    const DAY_NAMES = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

    function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) el.style.display = 'none'; });
    });

    function buildScheduleUI(containerId, initialData) {
        const container = document.getElementById(containerId);
        container.innerHTML = '';
        const days = (initialData && initialData.days) ? initialData.days : {};
        DAY_NAMES.forEach((name, i) => {
            const ranges = days[i] || [];
            const isOpen = ranges.length > 0;
            const dayDiv = document.createElement('div');
            dayDiv.className = 'schedule-day';
            dayDiv.innerHTML = `
            <div class="schedule-day-header">
                <input type="checkbox" id="${containerId}_open_${i}" ${isOpen ? 'checked' : ''}
                    onchange="toggleDay('${containerId}', ${i})">
                <label for="${containerId}_open_${i}">${name}</label>
                <button type="button" class="btn-add-range"
                    id="${containerId}_add_${i}"
                    style="${isOpen ? '' : 'display:none'}"
                    onclick="addRange('${containerId}', ${i})">+ add range</button>
            </div>
            <div class="ranges-list" id="${containerId}_ranges_${i}"></div>
        `;
            container.appendChild(dayDiv);
            if (isOpen) {
                ranges.forEach(r => addRange(containerId, i, r.from_time, r.to_time));
            }
        });
    }

    function toggleDay(containerId, i) {
        const cb     = document.getElementById(`${containerId}_open_${i}`);
        const list   = document.getElementById(`${containerId}_ranges_${i}`);
        const addBtn = document.getElementById(`${containerId}_add_${i}`);
        if (cb.checked) {
            addBtn.style.display = '';
            addRange(containerId, i, '08:00', '17:00');
        } else {
            list.innerHTML       = '';
            addBtn.style.display = 'none';
        }
    }

    function addRange(containerId, i, from = '08:00', to = '17:00') {
        const list = document.getElementById(`${containerId}_ranges_${i}`);
        const row  = document.createElement('div');
        row.className = 'range-row';
        row.innerHTML = `
        <input type="time" value="${from}">
        <span style="font-size:12px;color:#aaa;">–</span>
        <input type="time" value="${to}">
        <button type="button" class="btn-del-range" onclick="this.parentElement.remove()">✕</button>
    `;
        list.appendChild(row);
    }

    function serializeSchedule(prefix) {
        const result = { days: {} };
        DAY_NAMES.forEach((_, i) => {
            const cb = document.getElementById(`${prefix}_schedule_builder_open_${i}`);
            if (!cb || !cb.checked) { result.days[i] = []; return; }
            const rows = document.querySelectorAll(`#${prefix}_schedule_builder_ranges_${i} .range-row`);
            result.days[i] = Array.from(rows)
                .map(row => {
                    const t = row.querySelectorAll('input[type=time]');
                    return { from_time: t[0].value, to_time: t[1].value };
                })
                .filter(r => r.from_time && r.to_time && r.from_time < r.to_time);
        });
        document.getElementById(`${prefix}_rule_set_input`).value = JSON.stringify(result);
        return true;
    }

    function openEditRuleModal(ruleId, rule) {
        document.getElementById('editRuleForm').action = `/manage/rules/${ruleId}`;
        document.getElementById('edit_title').value       = rule.title        ?? '';
        document.getElementById('edit_description').value = rule.description  ?? '';
        document.getElementById('edit_valid_from').value  = rule.valid_from   ? rule.valid_from.substring(0,10) : '';
        document.getElementById('edit_valid_to').value    = rule.valid_to     ? rule.valid_to.substring(0,10)   : '';
        let rs = rule.rule_set;
        if (typeof rs === 'string') rs = JSON.parse(rs);
        buildScheduleUI('edit_schedule_builder', rs);
        openModal('editRuleModal');
    }

    document.addEventListener('DOMContentLoaded', () => {
        buildScheduleUI('create_schedule_builder', null);
    });
</script>

@if($errors->hasAny(['name','description','branch_ids','service_ids']))
    <script>openModal('editAssetModal');</script>
@endif
@if($errors->hasAny(['title','valid_from','valid_to','rule_set']))
    <script>openModal('createRuleModal');</script>
@endif

@endsection