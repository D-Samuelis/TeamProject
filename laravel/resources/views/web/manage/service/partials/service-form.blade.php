{{--
    Reusable service form fields.
    $prefix     : 'create' or 'edit'
    $service    : existing Service model or null
    $businesses : collection
    $branches   : collection
--}}

{{-- Business --}}
<div style="margin-bottom:1rem;">
    <label>Business <span style="color:red;">*</span></label><br>
    <select name="business_id" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>
        <option value="">— Select business —</option>
        @foreach($businesses as $business)
            <option value="{{ $business->id }}"
                {{ old('business_id', $service->business_id ?? '') == $business->id ? 'selected' : '' }}>
                {{ $business->name }}
            </option>
        @endforeach
    </select>
    @error('business_id') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Name --}}
<div style="margin-bottom:1rem;">
    <label>Name <span style="color:red;">*</span></label><br>
    <input type="text" name="name"
           value="{{ old('name', $service->name ?? '') }}"
           style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>
    @error('name') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Description --}}
<div style="margin-bottom:1rem;">
    <label>Description</label><br>
    <textarea name="description" rows="3"
              style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">{{ old('description', $service->description ?? '') }}</textarea>
    @error('description') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Duration + Price --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:1rem;">
    <div>
        <label>Duration (minutes) <span style="color:red;">*</span></label><br>
        <input type="number" name="duration_minutes" min="1"
               value="{{ old('duration_minutes', $service->duration_minutes ?? '') }}"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>
        @error('duration_minutes') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
    </div>
    <div>
        <label>Price <span style="color:red;">*</span></label><br>
        <input type="number" name="price" min="0" step="0.01"
               value="{{ old('price', $service->price ?? '') }}"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>
        @error('price') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Location type --}}
<div style="margin-bottom:1rem;">
    <label>Location type</label><br>
    <select name="location_type" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
        <option value="">— None —</option>
        @foreach(['branch','online','hybrid'] as $t)
            <option value="{{ $t }}" {{ old('location_type', $service->location_type ?? '') == $t ? 'selected' : '' }}>
                {{ ucfirst($t) }}
            </option>
        @endforeach
    </select>
    @error('location_type') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Branches --}}
<div style="margin-bottom:1rem;">
    <label>Branches</label>
    <div style="border:1px solid #ccc;border-radius:4px;padding:8px;max-height:150px;overflow-y:auto;">
        @forelse($branches as $branch)
            @php $currentBranchIds = old('branch_ids', isset($service) ? $service->branches->pluck('id')->toArray() : []); @endphp
            <label style="display:block;padding:3px 0;cursor:pointer;">
                <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                    {{ in_array($branch->id, $currentBranchIds) ? 'checked' : '' }}>
                {{ $branch->name }}
                @if($branch->city) <span style="font-size:12px;color:#aaa;">({{ $branch->city }})</span> @endif
            </label>
        @empty
            <p style="color:#888;font-size:13px;margin:0;">No branches available.</p>
        @endforelse
    </div>
    @error('branch_ids') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Is active --}}
<div style="margin-bottom:1rem;">
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $service->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>

<div style="margin-bottom:1rem;">
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
        <input type="checkbox" name="requires_manual_acceptance" value="1"
            {{ old('requires_manual_acceptance', $service->requires_manual_acceptance ?? true) ? 'checked' : '' }}>
        Requires manual acceptance
    </label>
</div>

{{-- Cancellation Period --}}
<div style="margin-bottom:1rem;">
    <label>Cancellation Period</label><br>
    <input type="text" name="cancellation_period"
           value="{{ old('cancellation_period', isset($service) ? App\Application\Service\Services\DurationParser::fromMinutes($service->cancellation_period_minutes) : '') }}"
           placeholder="e.g. 2d 3h, 1w, 90m"
           style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
    <p style="font-size:12px;color:#888;margin-top:4px;">
        How far in advance a customer must cancel. Leave empty for no restriction.<br>
        Supported units: <strong>w</strong> (weeks), <strong>d</strong> (days), <strong>h</strong> (hours), <strong>m</strong> (minutes)
    </p>
    @error('cancellation_period') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>
