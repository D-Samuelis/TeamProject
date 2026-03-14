{{--
    Reusable rule form fields.
    $prefix : 'create' or 'edit'
    $rule   : existing Rule model (for edit) or null (for create)
--}}

<div style="margin-bottom:1rem;">
    <label>Title <span style="color:red;">*</span></label><br>
    <input type="text"
           id="{{ $prefix }}_title"
           name="title"
           value="{{ old('title', $rule->title ?? '') }}"
           style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;"
           required>
    @error('title') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

<div style="margin-bottom:1rem;">
    <label>Description</label><br>
    <textarea id="{{ $prefix }}_description"
              name="description"
              rows="2"
              style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">{{ old('description', $rule->description ?? '') }}</textarea>
    @error('description') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:1rem;">
    <div>
        <label>Valid from</label><br>
        <input type="date"
               id="{{ $prefix }}_valid_from"
               name="valid_from"
               value="{{ old('valid_from', isset($rule->valid_from) ? \Carbon\Carbon::parse($rule->valid_from)->format('Y-m-d') : '') }}"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
        @error('valid_from') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
    </div>
    <div>
        <label>Valid to</label><br>
        <input type="date"
               id="{{ $prefix }}_valid_to"
               name="valid_to"
               value="{{ old('valid_to', isset($rule->valid_to) ? \Carbon\Carbon::parse($rule->valid_to)->format('Y-m-d') : '') }}"
               style="width:100%;padding:8px;border:1px solid #ccc;border-radius:4px;">
        @error('valid_to') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
    </div>
</div>

{{-- Schedule builder --}}
<div style="margin-bottom:1rem;">
    <label style="display:block;margin-bottom:6px;font-weight:500;">Weekly schedule</label>
    @error('rule_set') <p style="color:red;font-size:13px;">{{ $message }}</p> @enderror
    <div id="{{ $prefix }}_schedule_builder"
         style="border:1px solid #ccc;border-radius:4px;padding:12px;">
        {{-- Populated by buildScheduleUI() in JS --}}
    </div>
</div>
