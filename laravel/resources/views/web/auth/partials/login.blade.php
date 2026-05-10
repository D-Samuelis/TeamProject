<form action="{{ route('login') }}" method="POST" class="auth-form no-validate">
    @csrf

    <div class="auth-header">

        <div class="auth-header__title">
            Welcome back!
        </div>

        <div class="auth-header__dash">

        </div>

    </div>

    <div class="auth-form__content auth-form__content--login">

        <div class="auth-form__column auth-form__column--login">

            <x-auth-input id="login_email" name="email" label="Email Address" type="email" 
                          placeholder="john@example.com" />

            <x-auth-input id="login_password" name="password" label="Password" type="password" 
                          placeholder="••••••••" />

        </div>

    </div>

    <div class="auth-form__helper">
        <label class="auth-form__checkbox-wrapper">
            <input type="checkbox" name="remember" class="auth-form__checkbox">
            <span>Remember me</span>
        </label>
        <a href="" class="auth-form__link">Forgot password?</a>
    </div>

    <button type="submit" class="auth-form__submit-card welcome-card welcome-card--appointments">
        <h2 class="welcome-card__title">Sign In</h2>
        <div class="welcome-card__icon"><i class="fa-solid fa-arrow-right-to-bracket"></i></div>
    </button>

    <div class="auth-swap">
        <button type="button" id="switchToRegister" class="welcome-card welcome-card--manual">
            <h2 class="welcome-card__title">Create an account</h2>
            <p class="welcome-card__description">Don't have an account yet? Register here to access all features.</p>
            <div class="welcome-card__icon"><i class="fa-solid fa-user-plus"></i></div>
        </button>
    </div>
</form>