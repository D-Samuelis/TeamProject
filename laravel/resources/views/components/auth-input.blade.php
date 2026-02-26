@props([
    'id', 
    'name', 
    'label', 
    'type' => 'text', 
    'placeholder' => '', 
    'list' => null,
    'info' => false
])

<div class="auth-form__group">
    <label for="{{ $id }}" class="auth-form__label">{{ $label }}</label>
    
    <div class="input-wrapper">
        <input 
            type="{{ $type }}" 
            id="{{ $id }}" 
            name="{{ $name }}" 
            class="auth-form__input" 
            placeholder="{{ $placeholder }}"
            @if($list) list="{{ $list }}" @endif
        >
        
        @if($type === 'password')
            <button type="button" class="password-toggle" tabindex="-1">
                <i class="fa-regular fa-eye"></i>
            </button>
        @endif
    </div>

    <div class="auth-form__error invalid-input-field"></div>

    @if($info)
        <div class="auth-form__info">
            <i class="fa-solid fa-circle-info"></i>
            <span class="info-label">Title Info</span>
            <div class="info-tooltip"></div>
        </div>
    @endif
    
    @if($list)
        <datalist id="{{ $list }}"></datalist>
    @endif
</div>