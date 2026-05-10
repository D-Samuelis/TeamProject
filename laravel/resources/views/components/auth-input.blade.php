@props([
    'id', 
    'name', 
    'label', 
    'type' => 'text', 
    'placeholder' => '', 
    'list' => null,
    'info' => false,
    'required' => false,
    'pattern' => null,
    'maxlength' => null,
])

<div class="auth-form__group">
    <label for="{{ $id }}" class="auth-form__label">
        {{ $label }}
        @if($required)
            <span class="required-marker">*</span>
        @endif
    </label>
    
    <div class="input-wrapper {{ $type === 'date' ? 'input-wrapper--date' : '' }}">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            class="auth-form__input" 
            placeholder="{{ $placeholder }}"
            @if($list) list="{{ $list }}" @endif
            @if($required) required @endif
            @if($pattern) pattern="{{ $pattern }}" @endif
            @if($maxlength) maxlength="{{ $maxlength }}" @endif
        >
        
        @if($type === 'password')
            <button type="button" class="password-toggle" tabindex="-1">
                <i class="fa-regular fa-eye"></i>
            </button>
        @endif

        @if($type === 'date')
            <button type="button" class="date-picker-toggle" tabindex="-1" aria-label="Open date picker">
                <i class="fa-regular fa-calendar"></i>
            </button>
        @endif
    </div>

    <div class="auth-form__error invalid-input-field"></div>

    @if($info)
        <div class="auth-form__info active">
            <i class="fa-solid fa-circle-info"></i>
            <span class="info-label">Title Info</span>
            <div class="info-tooltip">You may be asked to provide proof of your diploma later.</div>
        </div>
    @endif
    
    @if($list)
        <datalist id="{{ $list }}"></datalist>
    @endif
</div>