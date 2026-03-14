{{-- resources/views/pages/private/branch/show.blade.php --}}

@if(session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <div>
        <h1>{{ $branch->name }}</h1>
        <p style="color:#888;font-size:13px;margin:4px 0 0;">
            {{ ucfirst($branch->type) }}
            @if($branch->city) · {{ $branch->city }} @endif
            @if($branch->country) · {{ $branch->country }} @endif
        </p>
    </div>
    <div style="display:flex;gap:8px;">
        <button onclick="openModal('editBranchModal')">Edit</button>
        <form method="POST" action="{{ route('branch.delete', $branch->id) }}"
              onsubmit="return confirm('Delete this branch?')">
            @csrf @method('DELETE')
            <button type="submit" style="color:red;background:none;border:none;cursor:pointer;">Delete</button>
        </form>
    </div>
</div>

{{-- Details --}}
<div style="margin-bottom:2rem;">
    @if($branch->address_line_1)
        <p>{{ $branch->address_line_1 }}{{ $branch->address_line_2 ? ', '.$branch->address_line_2 : '' }}</p>
        <p>{{ $branch->postal_code }} {{ $branch->city }}, {{ $branch->country }}</p>
    @endif
    <p style="margin-top:6px;">
        <span style="font-size:12px;padding:2px 8px;border-radius:20px;background:{{ $branch->is_active ? '#d1fae5' : '#fee2e2' }};color:{{ $branch->is_active ? '#065f46' : '#991b1b' }};">
            {{ $branch->is_active ? 'Active' : 'Inactive' }}
        </span>
    </p>
</div>

<div style="margin-top:1rem;">
    <strong>Business:</strong><a href="{{ route('business.show', $branch->business->id) }}">{{ $branch->business->name }}</a>
</div>

{{-- Services --}}
<div style="margin-top:0.5rem;">
    <strong>Services:</strong>
    @forelse($branch->services as $s)
        <a href="{{ route('service.show', $s->id) }}">{{ $s->name }}</a>@unless($loop->last), @endunless
    @empty
        <span style="color:#888;">None assigned</span>
    @endforelse
</div>

{{-- Assets --}}
<div style="margin-top:0.5rem;">
    <strong>Assets:</strong>
    @forelse($branch->assets as $a)
        <a href="{{ route('asset.show', $a->id) }}">{{ $a->name }}</a>@unless($loop->last), @endunless
    @empty
        <span style="color:#888;">None assigned</span>
    @endforelse
</div>


{{-- ══════════════════════════════════════════════════════════════════
     MODAL: Edit Branch
══════════════════════════════════════════════════════════════════ --}}
<div id="editBranchModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('editBranchModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">Edit Branch</h2>

        <form method="POST" action="{{ route('branch.update', $branch->id) }}">
            @csrf @method('PUT')
            @include('pages.private.branch.partials.branch-form', ['prefix' => 'edit', 'branch' => $branch, 'businesses' => $businesses])

            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('editBranchModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>

@include('pages.private.branch.partials.modal-styles-scripts')

@if($errors->any())
<script>openModal('editBranchModal');</script>
@endif
