{{-- resources/views/pages/private/service/show.blade.php --}}

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <div>
        <h1 style="{{ $service->trashed() ? 'text-decoration:line-through;color:#991b1b;' : '' }}">
            {{ $service->name }}
            @if ($service->trashed())
                <span style="font-size:14px;color:red;">(Deleted)</span>
            @endif
        </h1>
        <p style="color:#888;font-size:13px;margin:4px 0 0;">
            {{ $service->base_duration_minutes }}min
            · €{{ number_format($service->base_price, 2) }}
            @if ($service->location_type)
                · {{ ucfirst($service->location_type) }}
            @endif
        </p>
    </div>

    <div style="display:flex;gap:8px;">
        @if ($service->trashed())
            @can('restore', $service)
                <form method="POST" action="{{ route('manage.service.restore', $service->id) }}">
                    @csrf @method('PATCH')
                    <button type="submit" style="background:#2563eb;color:white;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;">
                        Restore Service
                    </button>
                </form>
            @endcan
        @else
            @can('update', $service)
                <button onclick="openModal('editServiceModal')">Edit</button>
            @endcan
            @can('delete', $service)
                <form method="POST" action="{{ route('manage.service.delete', $service->id) }}"
                      onsubmit="return confirm('Delete this service?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="color:red;background:none;border:none;cursor:pointer;">Delete</button>
                </form>
            @endcan
        @endif
    </div>
</div>

@if ($service->description)
    <p style="margin-bottom:1rem;">{{ $service->description }}</p>
@endif

<p style="margin-bottom:0.5rem;">
    <span style="font-size:12px;padding:2px 8px;border-radius:20px;
                 background:{{ $service->is_active ? '#d1fae5' : '#fee2e2' }};
                 color:{{ $service->is_active ? '#065f46' : '#991b1b' }};">
        {{ $service->is_active ? 'Active' : 'Inactive' }}
    </span>
</p>

<div style="margin-top:1rem;">
    <strong>Business:</strong>
    <a href="{{ route('manage.business.show', $service->business->id) }}">{{ $service->business->name }}</a>
</div>

{{-- Branch instances ─────────────────────────────────────────────────────
     $service->branches uses wherePivotNull('deleted_at'), so only active
     BranchService instances appear. The pivot carries the BranchService id
     for future deep-link to the instance detail page.
──────────────────────────────────────────────────────────────────────── --}}
<div style="margin-top:0.5rem;">
    <strong>Branches:</strong>
    @forelse($service->branches as $b)
        <span>
            <a href="{{ route('manage.branch.show', $b->id) }}">{{ $b->name }}</a>
            {{-- Show custom overrides if any are set on this instance --}}
            @if ($b->pivot->custom_price || $b->pivot->custom_duration_minutes)
                <span style="font-size:11px;color:#888;">
                    ({{ $b->pivot->custom_price ? '€'.number_format($b->pivot->custom_price, 2) : '' }}
                    {{ $b->pivot->custom_duration_minutes ? $b->pivot->custom_duration_minutes.'min' : '' }})
                </span>
            @endif
            @can('assign', [$service, $b])
                <form method="POST"
                      action="{{ route('manage.service.branch.unassign', [$service->id, $b->id]) }}"
                      style="display:inline;"
                      onsubmit="return confirm('Remove this branch?')">
                    @csrf @method('DELETE')
                    <button type="submit" style="color:red;background:none;border:none;cursor:pointer;font-size:11px;">✕</button>
                </form>
            @endcan
        </span>
        @unless ($loop->last), @endunless
    @empty
        <span style="color:#888;">None assigned</span>
    @endforelse
</div>

{{-- Assign to additional branches --}}
@if ($branches->isNotEmpty())
    @php $assignedBranchIds = $service->branches->pluck('id'); @endphp
    @php $assignableBranches = $branches->whereNotIn('id', $assignedBranchIds); @endphp
    @if ($assignableBranches->isNotEmpty())
        <div style="margin-top:0.5rem;">
            <strong>Assign to branch:</strong>
            @foreach ($assignableBranches as $b)
                @can('assign', [$service, $b])
                    <form method="POST"
                          action="{{ route('manage.service.branch.assign', [$service->id, $b->id]) }}"
                          style="display:inline;">
                        @csrf
                        <button type="submit"
                                style="background:none;border:1px solid #ccc;border-radius:4px;padding:2px 8px;cursor:pointer;font-size:12px;">
                            + {{ $b->name }}
                        </button>
                    </form>
                @endcan
            @endforeach
        </div>
    @else
        <p style="color:#888;font-size:13px;margin-top:0.5rem;">All branches already assigned.</p>
    @endif
@endif


{{-- ══════════════════════════════════════════════════════════════════
     MODAL: Edit Service (template fields only — no branch checkboxes)
══════════════════════════════════════════════════════════════════ --}}
<div id="editServiceModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editServiceModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">Edit Service</h2>
        <form method="POST" action="{{ route('manage.service.update', $service->id) }}">
            @csrf @method('PUT')
            @include('pages.service.partials.service-form', [
                'prefix'               => 'edit',
                'service'              => $service,
                'businesses'           => $businesses,
                'branches'             => $branches,
                'showBranchAssignment' => false,
            ])
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('editServiceModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@include('pages.service.partials.modal-styles-scripts')

@if ($errors->any())
    <script>openModal('editServiceModal');</script>
@endif
