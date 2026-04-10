{{-- resources/views/pages/private/branch/index.blade.php --}}

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h1>Branches</h1>
    <button onclick="openModal('createBranchModal')">+ New Branch</button>
</div>

@forelse($branches as $branch)
    <div
        style="border:1px solid #ddd;border-radius:6px;padding:1rem;margin-bottom:0.75rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <a href="{{ route('manage.branch.show', $branch->id) }}" style="font-weight:500;">{{ $branch->name }}</a>
            <span style="font-size:12px;color:#888;margin-left:8px;">{{ ucfirst($branch->type) }}</span>
            @if ($branch->city)
                <span style="font-size:12px;color:#aaa;margin-left:4px;">· {{ $branch->city }}</span>
            @endif
        </div>
        <div style="display:flex;gap:6px;align-items:center;">
            <span
                style="font-size:12px;padding:2px 8px;border-radius:20px;background:{{ $branch->is_active ? '#d1fae5' : '#fee2e2' }};color:{{ $branch->is_active ? '#065f46' : '#991b1b' }};">
                {{ $branch->is_active ? 'Active' : 'Inactive' }}
            </span>
            @if ($branch->trashed())
                @can('restore', $branch)
                    <form method="POST" action="{{ route('manage.branch.restore', $branch->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit"
                            style="color:#2563eb;background:none;border:none;cursor:pointer;font-size:13px;">Restore</button>
                    </form>
                @endcan
            @else
                @can('delete', $branch)
                    <form method="POST" action="{{ route('manage.branch.delete', $branch->id) }}"
                        onsubmit="return confirm('Delete this branch?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                            style="color:red;background:none;border:none;cursor:pointer;font-size:13px;">Delete</button>
                    </form>
                @endcan
            @endif
        </div>
    </div>
@empty
    <p style="color:#888;">No branches yet.</p>
@endforelse

<div id="createBranchModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('createBranchModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">New Branch</h2>
        <form method="POST" action="{{ route('manage.branch.store') }}">
            @csrf
            @include('web.manage.branch.web.layouts.partials.branch-form', [
                'prefix' => 'create',
                'branch' => null,
                'businesses' => $businesses,
            ])
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('createBranchModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Branch</button>
            </div>
        </form>
    </div>
</div>

@include('web.manage.branch.web.layouts.partials.modal-styles-scripts')

@if ($errors->any())
    <script>
        openModal('createBranchModal');
    </script>
@endif
