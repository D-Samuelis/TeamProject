<div class="profile-card">
    <div class="profile-card__title">Security</div>

    <div class="profile-section__intro">
        Update your password.
    </div>

    @if(session('success'))
        <div class="form-alert form-alert--success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="form-alert form-alert--error">
            Please check the highlighted fields and try again.
        </div>
    @endif

    <form class="form form-password" method="POST" action="{{ route('profile.password.update') }}">
        @csrf

        <div class="form__group">
            <label class="form__label">Current password</label>
            <input class="form__input @error('current_password') input-error @enderror" type="password" name="current_password">

            <div class="form-error @error('current_password') active @enderror">
                @error('current_password')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__group">
            <label class="form__label">New password</label>
            <input class="form__input @error('password') input-error @enderror" type="password" name="password">

            <div class="form-help">
                Password must contain at least 8 characters.
            </div>

            <div class="form-error @error('password') active @enderror">
                @error('password')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="form__group">
            <label class="form__label">Confirm new password</label>
            <input class="form__input @error('password_confirmation') input-error @enderror" type="password" name="password_confirmation">

            <div class="form-error @error('password_confirmation') active @enderror">
                @error('password_confirmation')
                    {{ $message }}
                @enderror
            </div>
        </div>

        <div class="section-actions">
            <button class="btn-primary" type="submit">Update password</button>
        </div>
    </form>
</div>