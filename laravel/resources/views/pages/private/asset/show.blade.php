{{-- resources/views/pages/private/asset/show.blade.php --}}

@if(session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

{{-- ── Asset detail ──────────────────────────────────────────────────────── --}}
<div style="margin-bottom:2rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <h1>{{ $asset->name }}</h1>
        <div style="display:flex;gap:8px;">
            <button onclick="openModal('editAssetModal')">Edit</button>
            <button onclick="openModal('createRuleModal')">+ Add Rule</button>
            <form method="POST" action="{{ route('asset.delete', $asset->id) }}"
                  onsubmit="return confirm('Delete this asset?')">
                @csrf @method('DELETE')
                <button type="submit" style="color:red;">Delete</button>
            </form>
        </div>
    </div>

    <p>{{ $asset->description }}</p>

    <div style="margin-top:1rem;">
        <strong>Branches:</strong>
        @forelse($asset->branches as $b)
            {{ $b->name }}@unless($loop->last), @endunless
        @empty
            <span style="color:#888;">None</span>
        @endforelse
    </div>

    <div style="margin-top:0.5rem;">
        <strong>Services:</strong>
        @forelse($asset->services as $s)
            {{ $s->name }}@unless($loop->last), @endunless
        @empty
            <span style="color:#888;">None</span>
        @endforelse
    </div>
</div>

{{-- ── Rules list ────────────────────────────────────────────────────────── --}}
<div>
    <h2>Rules</h2>

    @php $sortedRules = $asset->rules->sortBy('priority'); @endphp

    @forelse($sortedRules as $rule)
        @php
            $rs = is_string($rule->rule_set) ? json_decode($rule->rule_set, true) : $rule->rule_set;
            $rs = $rs['days'] ?? $rs;
            $isFirst = $loop->first;
            $isLast  = $loop->last;
        @endphp
        <div style="border:1px solid #ddd;border-radius:6px;padding:1rem;margin-bottom:1rem;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div style="display:flex;align-items:flex-start;gap:10px;">
                    {{-- Priority badge + up/down --}}
                    <div style="display:flex;flex-direction:column;align-items:center;gap:2px;flex-shrink:0;">
                        <form method="POST" action="{{ route('rule.reorder', $rule->id) }}">
                            @csrf
                            <input type="hidden" name="direction" value="up">
                            <button type="submit"
                                    {{ $isFirst ? 'disabled' : '' }}
                                    style="background:none;border:none;cursor:{{ $isFirst ? 'default' : 'pointer' }};color:{{ $isFirst ? '#ddd' : '#555' }};font-size:12px;padding:0;line-height:1;">▲</button>
                        </form>
                        <span style="font-size:11px;font-weight:600;color:#888;background:#f3f4f6;border-radius:4px;padding:1px 6px;">
                            #{{ $rule->priority }}
                        </span>
                        <form method="POST" action="{{ route('rule.reorder', $rule->id) }}">
                            @csrf
                            <input type="hidden" name="direction" value="down">
                            <button type="submit"
                                    {{ $isLast ? 'disabled' : '' }}
                                    style="background:none;border:none;cursor:{{ $isLast ? 'default' : 'pointer' }};color:{{ $isLast ? '#ddd' : '#555' }};font-size:12px;padding:0;line-height:1;">▼</button>
                        </form>
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
                    <button onclick='openEditRuleModal({{ $rule->id }}, @json($rule))'>Edit</button>
                    <form method="POST" action="{{ route('rule.delete', $rule->id) }}"
                          onsubmit="return confirm('Delete this rule?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="color:red;">Delete</button>
                    </form>
                </div>
            </div>

            {{-- Schedule summary --}}
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


{{-- ══════════════════════════════════════════════════════════════════
     MODAL: Edit Asset
══════════════════════════════════════════════════════════════════ --}}
<div id="editAssetModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editAssetModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">Edit Asset</h2>

        <form method="POST" action="{{ route('asset.update', $asset->id) }}">
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


{{-- ══════════════════════════════════════════════════════════════════
     MODAL: Create Rule
══════════════════════════════════════════════════════════════════ --}}
<div id="createRuleModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('createRuleModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">New Rule</h2>

        <form method="POST" action="{{ route('rule.store') }}" onsubmit="return serializeSchedule('create')">
            @csrf
            <input type="hidden" name="asset_id" value="{{ $asset->id }}">
            <input type="hidden" name="rule_set" id="create_rule_set_input">

            @include('pages.private.asset.partials.rule-form', ['prefix' => 'create', 'rule' => null])

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('createRuleModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Rule</button>
            </div>
        </form>
    </div>
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL: Edit Rule
══════════════════════════════════════════════════════════════════ --}}
<div id="editRuleModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editRuleModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">Edit Rule</h2>

        <form method="POST" id="editRuleForm" onsubmit="return serializeSchedule('edit')">
            @csrf @method('PUT')
            <input type="hidden" name="rule_set" id="edit_rule_set_input">

            @include('pages.private.asset.partials.rule-form', ['prefix' => 'edit', 'rule' => null])

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('editRuleModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>


{{-- ── Styles ────────────────────────────────────────────────────────────── --}}
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


{{-- ── JavaScript ───────────────────────────────────────────────────────── --}}
<script>
    const DAY_NAMES = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

    // ── Modal helpers ─────────────────────────────────────────────────────────
    function openModal(id)  { document.getElementById(id).style.display = 'flex'; }
    function closeModal(id) { document.getElementById(id).style.display = 'none'; }

    document.querySelectorAll('.modal-backdrop').forEach(el => {
        el.addEventListener('click', e => { if (e.target === el) el.style.display = 'none'; });
    });

    // ── Schedule builder ──────────────────────────────────────────────────────

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
            list.innerHTML      = '';
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

    // ── Open edit rule modal ──────────────────────────────────────────────────
    function openEditRuleModal(ruleId, rule) {
        document.getElementById('editRuleForm').action = `/rules/${ruleId}`;

        document.getElementById('edit_title').value       = rule.title        ?? '';
        document.getElementById('edit_description').value = rule.description  ?? '';
        document.getElementById('edit_valid_from').value  = rule.valid_from   ? rule.valid_from.substring(0,10)  : '';
        document.getElementById('edit_valid_to').value    = rule.valid_to     ? rule.valid_to.substring(0,10)    : '';

        let rs = rule.rule_set;
        if (typeof rs === 'string') rs = JSON.parse(rs);

        buildScheduleUI('edit_schedule_builder', rs);
        openModal('editRuleModal');
    }

    // ── Init ──────────────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        buildScheduleUI('create_schedule_builder', null);
    });
</script>

{{-- Re-open correct modal on validation error --}}
@if($errors->hasAny(['name','description','branch_ids','service_ids']))
    <script>openModal('editAssetModal');</script>
@endif
@if($errors->hasAny(['title','valid_from','valid_to','rule_set']))
    <script>openModal('createRuleModal');</script>
@endif
