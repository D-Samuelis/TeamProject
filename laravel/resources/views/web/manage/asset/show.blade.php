@section("breadcrumb-{$asset->id}", $asset->name)

@extends('web.layouts.app')

@section('title', 'Bexora | My Businesses')

@section('content')

<script>
    window.BE_DATA = {
        csrf: '{{ csrf_token() }}',
        asset: @json($asset),
        allBranches: @json($branches),
        allServices: @json($services),
        canUpdate: @json(auth()->user()?->can('update', $asset)),
        routes: {
            branchStore: '{{ route("manage.branch.store") }}',
            deleteAsset: "{{ route('manage.asset.delete', ':id') }}",
            restoreAsset: "{{ route('manage.asset.restore', ':id') }}",
            deleteRule: '{{ route("manage.rule.delete", ":id") }}'
        }
    };
</script>

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
                        <button class="business-info-card__edit-btn" type="button" data-modal-target="edit-business-modal">
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
                @if($asset->branch)
                    @php
                        $c = branchColor($asset->branch->id);
                        $branchServices = $asset->services->filter(
                            fn($s) => $s->branches->contains('id', $asset->branch->id)
                        );
                    @endphp

                    {{-- Branch row (folder) --}}
                    <div class="service-item" style="background:{{ $c['bg'] }};border-left:3px solid {{ $c['border'] }};border-radius:0 6px 6px 0;">
                        <div class="member-info">
                            <span class="member-name" style="color:{{ $c['text'] }};">{{ $asset->branch->name }}</span>
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
                @endif
            </div>
        </section>

    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
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

                            @if (!$asset->trashed())
                                {{-- AK JE ASSET AKTÍVNY: Zobrazíme dropdown --}}
                                <div class="dropdown branch-dropdown">
                                    <button class="branch-dropdown__trigger" type="button">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                        <span>Asset Actions</span>
                                    </button>

                                    <div class="branch-dropdown__menu">

                                        {{-- Status toggle --}}
                                        <form action="{{ route('manage.asset.update', $asset->id) }}" method="POST">
                                            @csrf @method('PUT')
                                            <input type="hidden" name="name" value="{{ $asset->name }}">
                                            <input type="hidden" name="branch_id" value="{{ $asset->branch_id }}">
                                            @foreach($asset->services as $service)
                                                <input type="hidden" name="service_ids[]" value="{{ $service->id }}">
                                            @endforeach
                                            <input type="hidden" name="description" value="{{ $asset->description }}">
                                            <input type="hidden" name="is_active" value="{{ $asset->is_active ? 0 : 1 }}">
                                            <button type="submit" class="branch-dropdown__item">
                                                <i class="fa-solid fa-circle status-dot {{ $asset->is_active ?? true ? 'text-green' : 'text-yellow' }}"></i>
                                                Status:
                                                <div class="status__badge {{ $asset->is_active ?? true ? 'bg__badge-green' : 'bg__badge-yellow' }}">
                                                    {{ $asset->is_active ?? true ? 'Active' : 'Inactive' }}
                                                </div>
                                            </button>
                                        </form>

                                        {{-- Add Rule --}}
                                        @can('update', $asset)
                                            <button type="button" class="branch-dropdown__item" data-modal-target="create-rule-modal">
                                                <i class="fa-solid fa-plus"></i> Add Rule
                                            </button>
                                        @endcan

                                        <div class="branch-dropdown__divider"></div>

                                        {{-- Archive Asset --}}
                                        @can('delete', $asset)
                                            <button type="button" class="branch-dropdown__item delete-action js-archive-asset-btn"
                                                data-id="{{ $asset->id }}"
                                                data-name="{{ $asset->name }}">
                                                <i class="fa-solid fa-box-archive"></i> Archive Asset
                                            </button>
                                        @endcan

                                    </div>
                                </div>
                            @else
                                <form action="{{ route('manage.asset.restore', $asset->id) }}" method="POST">
                                    @csrf @method('POST')
                                    <button type="submit" class="branch-restore-btn">
                                        <i class="fa-solid fa-rotate-left"></i> Restore Asset
                                    </button>
                                </form>
                            @endif

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
                <div class="rule-panel" data-reorder-url="{{ route('manage.rule.reorder_all') }}">

                    @php $sortedRules = $asset->rules->sortBy('priority'); @endphp

                    @forelse($sortedRules as $rule)
                        @php
                            $rs = is_string($rule->rule_set) ? json_decode($rule->rule_set, true) : $rule->rule_set;
                            $rs = $rs['days'] ?? $rs;
                            $isFirst = $loop->first;
                            $isLast  = $loop->last;
                        @endphp

                        <div data-rule-id="{{ $rule->id }}" class="rule-card js-rule-card filterable-rule" style="flex-direction: column;">

                            <div class="rule-card__header" style="width: 100%;">
                                <div class="rule-card__left">

                                    <div class="rule-card__reorder">

                                        <div class="rule-card__reorder-buttons">
                                            @can('update', $asset)
                                                <form method="POST" action="{{ route('manage.rule.reorder', $rule->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="up">
                                                    <button type="submit" class="rule-card__reorder-btn {{ $isFirst ? 'rule-card__reorder-btn--disabled' : '' }}" {{ $isFirst ? 'disabled' : '' }}>▲</button>
                                                </form>
                                            @endcan

                                            @can('update', $asset)
                                                <form method="POST" action="{{ route('manage.rule.reorder', $rule->id) }}">
                                                    @csrf
                                                    <input type="hidden" name="direction" value="down">
                                                    <button type="submit" class="rule-card__reorder-btn {{ $isLast ? 'rule-card__reorder-btn--disabled' : '' }}" {{ $isLast ? 'disabled' : '' }}>▼</button>
                                                </form>
                                            @endcan
                                        </div>

                                        <div class="rule-card__priority js-priority-label"><span>#</span>{{ $rule->priority }}</div>

                                    </div>

                                    <div class="rule-card__meta js-search-data">
                                        <div>
                                            <strong class="rule-card__title">{{ $rule->title }}</strong>
                                            @if($rule->description)
                                                <p class="rule-card__description">{{ $rule->description }}</p>
                                            @endif
                                        </div>
                                        <div class="rule-card__dates-row" style="display: flex; gap: 1rem; margin-top: 5px;">
                                            <span class="rule-card__dates">
                                                <span>from:</span> {{ $rule->valid_from ? \Carbon\Carbon::parse($rule->valid_from)->format('d.m.Y') : '–' }}
                                            </span>
                                            <span class="rule-card__dates">
                                                <span>to:</span> {{ $rule->valid_to ? \Carbon\Carbon::parse($rule->valid_to)->format('d.m.Y') : '–' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="rule-card__actions">
                                    <div class="dropdown branch-dropdown">
                                        <button class="branch-dropdown__trigger" type="button">
                                            <i class="fa-solid fa-ellipsis-vertical"></i> Rule Actions
                                        </button>
                                        <div class="branch-dropdown__menu">
                                            @can('update', $asset)
                                                <button type="button" class="branch-dropdown__item js-edit-rule-btn"
                                                        data-rule='@json($rule)'>
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit Rule
                                                </button>
                                            @endcan
                                            <div class="branch-dropdown__divider"></div>
                                            {{-- Delete Rule --}}
                                            @can('update', $asset)
                                                <button type="button" class="branch-dropdown__item delete-action js-delete-rule-btn"
                                                    data-rule-id="{{ $rule->id }}"
                                                    data-rule-title="{{ $rule->title }}">
                                                    <i class="fa-solid fa-trash"></i> Delete Rule
                                                </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rule-card__schedule">
                                <div class="rule-card__schedule-grid" style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                                    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $i => $dayName)
                                        <div class="rule-card__schedule-item {{ empty($rs[$i]) ? 'is-closed' : '' }}" style="display: flex; align-items: center; gap: 10px;">
                                            <span class="day-label" style="min-width: 35px; font-weight: bold;">{{ $dayName }}</span>
                                            <div class="day-line" style="flex-grow: 1; border-bottom: 1px dashed var(--color-border-light); opacity: 0.5;"></div>
                                            <span class="day-time">
                                                @if(empty($rs[$i]))
                                                    <span class="rule-card__day-hours--closed">closed</span>
                                                @else
                                                    {{ collect($rs[$i])->map(fn($r) => $r['from_time'].'–'.$r['to_time'])->join(', ') }}
                                                @endif
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- 3. FOOTER: Toggle tlačidlo na spodku karty --}}
                            <button class="rule-card__expand-trigger js-rule-expand" type="button">
                                <i class="fa-solid fa-chevron-down"></i>
                                <span>Show Schedule</span>
                            </button>
                        </div>

                    @empty
                        <p class="rule-panel__empty">No rules yet. Add one to define working hours.</p>
                    @endforelse

                </div>
            </div>
        </main>
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


<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

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

        if (!container) return;

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

    function clearRangeErrors(containerId) {
        document.querySelectorAll(`#${containerId} .range-error`).forEach(el => el.remove());
    }

    function showRangeError(row, message) {
        // Remove any existing error on this row first
        const existing = row.querySelector('.range-error');
        if (existing) existing.remove();

        const err = document.createElement('span');
        err.className = 'range-error';
        err.style.cssText = 'color:red;font-size:12px;display:block;margin-top:2px;';
        err.textContent = message;
        row.insertAdjacentElement('afterend', err);
    }

    function timeToMinutes(t) {
        const [h, m] = t.split(':').map(Number);
        return h * 60 + m;
    }

    function serializeSchedule(prefix) {
        const containerId = `${prefix}_schedule_builder`;
        clearRangeErrors(containerId);

        const result = { days: {} };
        let hasError = false;

        DAY_NAMES.forEach((_, i) => {
            const cb = document.getElementById(`${containerId}_open_${i}`);
            if (!cb || !cb.checked) { result.days[i] = []; return; }

            const rows = Array.from(
                document.querySelectorAll(`#${containerId}_ranges_${i} .range-row`)
            );

            const accepted = [];   // only ranges that passed ALL checks
            const dayRanges = [];  // what goes into result

            rows.forEach((row) => {
                const [fromInput, toInput] = row.querySelectorAll('input[type=time]');
                const from = fromInput.value;
                const to   = toInput.value;

                if (!from || !to) return;

                const fromMin = timeToMinutes(from);
                const toMin   = timeToMinutes(to);

                if (fromMin >= toMin) {
                    showRangeError(row, `Start time (${from}) must be before end time (${to}).`);
                    hasError = true;
                    return;
                }

                const overlap = accepted.find(r => fromMin < r.toMin && toMin > r.fromMin);
                if (overlap) {
                    showRangeError(row, `Overlaps with ${overlap.from}–${overlap.to}.`);
                    hasError = true;
                    return;
                }

                accepted.push({ fromMin, toMin, from, to });
                dayRanges.push({ from_time: from, to_time: to });
            });

            result.days[i] = dayRanges;
        });

        if (hasError) return false;

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
@if($errors->hasAny(['title','valid_from','valid_to','rule_set', 'rule_set.days']))
    <script>openModal('createRuleModal');</script>
@endif

@endsection
