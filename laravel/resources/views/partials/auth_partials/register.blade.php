<form action="{{ route('register') }}" method="POST" class="auth-form">
    @csrf

    <div class="auth-header">

        <div class="auth-header__title">
            Registration
        </div>

        <div class="auth-header__dash">

        </div>

    </div>

    <div class="auth-form__content">

        <div class="auth-form__column">

            <div class="auth-form__group">
                <label for="register_name" class="auth-form__label">Full Name</label>
                <input type="text" id="register_name" name="name" class="auth-form__input" placeholder="John Doe" required>
            </div>

            <div class="auth-form__group">
                <label for="register_email" class="auth-form__label">Email Address</label>
                <input type="email" id="register_email" name="email" class="auth-form__input" placeholder="john@example.com" required>
            </div>

            <div class="auth-form__group">
                <label for="register_country" class="auth-form__label">Country</label>
                <input type="text" id="register_country" name="country" class="auth-form__input" placeholder="Slovakia" required>
            </div>

            <div class="auth-form__group">
                <label for="register_password" class="auth-form__label">Password</label>
                <input type="password" id="register_password" name="password" class="auth-form__input" placeholder="Min. 8 characters" required>
            </div>

        </div>

        <div class="auth-form__column">

            <div class="auth-form__group">
                <label for="register_provider" class="auth-form__label">Service Provider</label>
                <input type="text" id="register_provider" name="provider" class="auth-form__input" placeholder="Yes" required>
            </div>

            <div class="auth-form__group">
                <label for="register_phone" class="auth-form__label">Phone Number</label>
                <input type="text" id="register_phone" name="phone" class="auth-form__input" placeholder="+421 962 124 745" required>
            </div>

            <div class="auth-form__group">
                <label for="register_city" class="auth-form__label">City</label>
                <input type="text" id="register_city" name="city" class="auth-form__input" placeholder="Bratislava" required>
            </div>

            <div class="auth-form__group">
                <label for="register_password_confirmation" class="auth-form__label">Confirm Password</label>
                <input type="password" id="register_password_confirmation" name="password_confirmation" class="auth-form__input" placeholder="••••••••" required>
            </div>

        </div>

    </div>

    <button type="submit" class="auth-form__submit auth-form__submit--primary">
        <span>Create Account</span>
        <i class="fa-solid fa-user-plus"></i>
    </button>
</form>