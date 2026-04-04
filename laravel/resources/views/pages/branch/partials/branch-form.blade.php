{{--
    Reusable branch form fields.
    $prefix     : 'create' or 'edit'
    $branch     : existing Branch model or null
    $businesses : collection of businesses for the dropdown
--}}

{{-- Business --}}
<div style="margin-bottom:1rem;">
    <label>Business <span style="color:red;">*</span></label><br>
    <input type="text" name="business_name" value="{{ $branch->business->name ?? '' }}" style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>

    @error('business_id') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Name --}}
<div style="margin-bottom:1rem;">
    <label>Name <span style="color:red;">*</span></label><br>
    <input type="text" name="name"
           value="{{ old('name', $branch->name ?? '') }}"
           style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>
    @error('name') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Type --}}
<div style="margin-bottom:1rem;">
    <label>Type <span style="color:red;">*</span></label><br>
    <select name="type" id="{{ $prefix }}_type"
            onchange="toggleAddressFields('{{ $prefix }}')"
            style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;" required>
        <option value="">— Select type —</option>
        @foreach(['physical','online','hybrid'] as $t)
            <option value="{{ $t }}" {{ old('type', $branch->type ?? '') == $t ? 'selected' : '' }}>
                {{ ucfirst($t) }}
            </option>
        @endforeach
    </select>
    @error('type') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

{{-- Address fields (shown for physical/hybrid) --}}
<div id="{{ $prefix }}_address_fields">
    <div style="margin-bottom:1rem;">
        <label>Address line 1</label><br>
        <input type="text" name="address_line_1"
               value="{{ old('address_line_1', $branch->address_line_1 ?? '') }}"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
        @error('address_line_1') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
    </div>
    <div style="margin-bottom:1rem;">
        <label>Address line 2</label><br>
        <input type="text" name="address_line_2"
               value="{{ old('address_line_2', $branch->address_line_2 ?? '') }}"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:1rem;">
        <div>
            <label>City</label><br>
            <input type="text" name="city"
                   value="{{ old('city', $branch->city ?? '') }}"
                   style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
            @error('city') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
        </div>
        <div>
            <label>Postal code</label><br>
            <input type="text" name="postal_code"
                   value="{{ old('postal_code', $branch->postal_code ?? '') }}"
                   style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
            @error('postal_code') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
        </div>
    </div>
    <div style="margin-bottom:1rem;">
        <label>Country</label><br>
        <input type="text" name="country"
               value="{{ old('country', $branch->country ?? '') }}"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
        @error('country') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Is active --}}
<div style="margin-bottom:1rem;">
    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
        <input type="checkbox" name="is_active" value="1"
               {{ old('is_active', $branch->is_active ?? true) ? 'checked' : '' }}>
        Active
    </label>
</div>

<script>
function toggleAddressFields(prefix) {
    const type = document.getElementById(prefix + '_type').value;
    const fields = document.getElementById(prefix + '_address_fields');
    fields.style.display = (type === 'physical' || type === 'hybrid') ? 'block' : 'none';
}
// Run on load to set initial state
document.addEventListener('DOMContentLoaded', () => toggleAddressFields('{{ $prefix }}'));
</script>
