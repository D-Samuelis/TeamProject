<form action="{{ route('login') }}" method="POST" class="auth-form">
    @csrf
    
    <div class="auth-form__group">
        <label for="login_email" class="auth-form__label">Email Address</label>
        <input type="email" id="login_email" name="email" class="auth-form__input" placeholder="name@example.com" required autofocus>
    </div>

    <div class="auth-form__group">
        <label for="login_password" class="auth-form__label">Password</label>
        <input type="password" id="login_password" name="password" class="auth-form__input" placeholder="••••••••" required>
    </div>

    <div class="auth-form__helper">
        <label class="auth-form__checkbox-wrapper">
            <input type="checkbox" name="remember" class="auth-form__checkbox">
            <span>Remember me</span>
        </label>
        <a href="" class="auth-form__link">Forgot password?</a>
    </div>

    <button type="submit" class="auth-form__submit">
        <span>Sign In</span>
        <i class="fa-solid fa-arrow-right-to-bracket"></i>
    </button>
</form>