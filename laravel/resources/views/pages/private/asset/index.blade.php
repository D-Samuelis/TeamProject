{{-- resources/views/pages/private/asset/index.blade.php --}}

@if(session('success'))
    <p style="color: green;">{{ session('success') }}</p>
@endif

{{-- Asset list --}}
<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
        <h1>Assets</h1>
        <button onclick="document.getElementById('createAssetModal').style.display='flex'">
            + New Asset
        </button>
    </div>

    @forelse($assets as $asset)
        <div>
            <a href="{{ route('asset.show', $asset->id) }}">{{ $asset->name }}</a>
            <span style="color: #888;">{{ $asset->description }}</span>
        </div>
    @empty
        <p>No assets yet.</p>
    @endforelse
</div>

{{-- ── Create Modal ──────────────────────────────────────────────────────── --}}
<div id="createAssetModal"
     style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:1000; align-items:center; justify-content:center;">

    <div style="background:#fff; border-radius:8px; padding:2rem; width:100%; max-width:560px; max-height:90vh; overflow-y:auto; position:relative;">

        <button onclick="document.getElementById('createAssetModal').style.display='none'"
                style="position:absolute; top:1rem; right:1rem; background:none; border:none; font-size:1.25rem; cursor:pointer;">
            &times;
        </button>

        <h2 style="margin-bottom:1.5rem;">New Asset</h2>

        <form method="POST" action="{{ route('asset.store') }}">
            @csrf

            {{-- Name --}}
            <div style="margin-bottom:1rem;">
                <label for="create_name">Name <span style="color:red;">*</span></label><br>
                <input type="text"
                       id="create_name"
                       name="name"
                       value="{{ old('name') }}"
                       style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"
                       required>
                @error('name') <p style="color:red; font-size:13px;">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div style="margin-bottom:1rem;">
                <label for="create_description">Description</label><br>
                <textarea id="create_description"
                          name="description"
                          rows="3"
                          style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">{{ old('description') }}</textarea>
                @error('description') <p style="color:red; font-size:13px;">{{ $message }}</p> @enderror
            </div>

            {{-- Branches --}}
            <div style="margin-bottom:1rem;">
                <label>Branches</label>
                <div style="border:1px solid #ccc; border-radius:4px; padding:8px; max-height:160px; overflow-y:auto;">
                    @forelse($branches as $branch)
                        <label style="display:block; padding:4px 0; cursor:pointer;">
                            <input type="checkbox"
                                   name="branch_ids[]"
                                   value="{{ $branch->id }}"
                                   {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                            {{ $branch->name }}
                            <span style="font-size:12px; color:#888;">({{ $branch->business->name }})</span>
                        </label>
                    @empty
                        <p style="color:#888; font-size:13px; margin:0;">No branches available.</p>
                    @endforelse
                </div>
                @error('branch_ids') <p style="color:red; font-size:13px;">{{ $message }}</p> @enderror
            </div>

            {{-- Services --}}
            <div style="margin-bottom:1.5rem;">
                <label>Services</label>
                <p style="font-size:12px; color:#888; margin:2px 0 6px;">Only services linked to at least one selected branch can be chosen.</p>
                <div style="border:1px solid #ccc; border-radius:4px; padding:8px; max-height:160px; overflow-y:auto;">
                    @forelse($services as $service)
                        <label style="display:block; padding:4px 0; cursor:pointer;">
                            <input type="checkbox"
                                   name="service_ids[]"
                                   value="{{ $service->id }}"
                                   {{ in_array($service->id, old('service_ids', [])) ? 'checked' : '' }}>
                            {{ $service->name }}
                            <span style="font-size:12px; color:#888;">({{ implode(', ', $service->branches->pluck('name')->toArray()) }})</span>
                        </label>
                    @empty
                        <p style="color:#888; font-size:13px; margin:0;">No services available.</p>
                    @endforelse
                </div>
                @error('service_ids') <p style="color:red; font-size:13px;">{{ $message }}</p> @enderror
            </div>

            <div style="display:flex; gap:8px; justify-content:flex-end;">
                <button type="button"
                        onclick="document.getElementById('createAssetModal').style.display='none'"
                        style="padding:8px 16px; border:1px solid #ccc; background:#fff; border-radius:4px; cursor:pointer;">
                    Cancel
                </button>
                <button type="submit"
                        style="padding:8px 16px; background:#1a1a1a; color:#fff; border:none; border-radius:4px; cursor:pointer;">
                    Create Asset
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Re-open modal on validation error --}}
@if($errors->any())
<script>
    document.getElementById('createAssetModal').style.display = 'flex';
</script>
@endif

{{-- Close modal on backdrop click --}}
<script>
    document.getElementById('createAssetModal').addEventListener('click', function(e) {
        if (e.target === this) this.style.display = 'none';
    });
</script>
