<form action="{{ route('register') }}" method="POST" class="auth-form">
    @csrf

    <div class="auth-form__group">
        <label for="register_name" class="auth-form__label">Full Name</label>
        <input type="text" id="register_name" name="name" class="auth-form__input" placeholder="John Doe" required>
    </div>

    <div class="auth-form__group">
        <label for="register_email" class="auth-form__label">Email Address</label>
        <input type="email" id="register_email" name="email" class="auth-form__input" placeholder="john@example.com" required>
    </div>

    <div class="auth-form__group">
        <label for="register_password" class="auth-form__label">Password</label>
        <input type="password" id="register_password" name="password" class="auth-form__input" placeholder="Min. 8 characters" required>
    </div>

    <div class="auth-form__group">
        <label for="register_password_confirmation" class="auth-form__label">Confirm Password</label>
        <input type="password" id="register_password_confirmation" name="password_confirmation" class="auth-form__input" placeholder="••••••••" required>
    </div>

    <button type="submit" class="auth-form__submit auth-form__submit--primary">
        <span>Create Account</span>
        <i class="fa-solid fa-user-plus"></i>
    </button>
</form>