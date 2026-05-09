<form action="{{ route('register') }}" method="POST" class="auth-form">
    @csrf

    <div class="auth-header">

        <div class="auth-header__title">
            Create a brand new Account
        </div>

        <div class="auth-header__dash">

        </div>

    </div>

    <div class="auth-form__content auth-form__content--register">

        <div class="auth-form__row">
            <x-auth-input id="register_name" name="name" label="Full Name" placeholder="John Doe" />
            <x-auth-input id="register_birth" name="birth_date" label="Birth Date" type="date" />
        </div>

        <div class="auth-form__row">
            <x-auth-input id="register_title_prefix" name="title_prefix" label="Title Before Name [Optional]"
                          placeholder="Bc." list="titles_before_list" info="true" />
            <x-auth-input id="register_title_suffix" name="title_suffix" label="Title After Name [Optional]"
                          placeholder="PhD." list="titles_after_list" info="true" />
        </div>

        <div class="auth-form__row">
            <x-auth-input id="register_email" name="email" label="Email Address" type="email"
                          placeholder="john@example.com" />
            <x-auth-input id="register_phone" name="phone_number" label="Phone Number" placeholder="+421 000 000 000" />
        </div>

        <div class="auth-form__row">
            <x-auth-input id="register_country" name="country" label="Country" placeholder="Slovakia" />
            <x-auth-input id="register_city" name="city" label="City" placeholder="Bratislava" />
        </div>

        <div class="auth-form__row">
            <x-auth-input id="register_password" name="password" label="Password" type="password"
                          placeholder="Min. 8 characters" />
            <x-auth-input id="register_password_confirmation" name="password_confirmation"
                          label="Confirm Password" type="password" placeholder="********" />
        </div>

    </div>

    <div class="gender-selection">
        <span class="gender-selection__title">Gender</span>
        <div class="gender-selection__row">
            <div id="genderSlider" class="gender-slider"></div>

            <input type="radio" name="gender" id="male" value="male" class="gender-radio" checked>
            <label for="male" class="gender-label"><i class="fa-solid fa-mars"></i></label>

            <input type="radio" name="gender" id="female" value="female" class="gender-radio">
            <label for="female" class="gender-label"><i class="fa-solid fa-venus"></i></label>

            <input type="radio" name="gender" id="other" value="other" class="gender-radio">
            <label for="other" class="gender-label"><i class="fa-solid fa-transgender"></i></label>

            <input type="radio" name="gender" id="none" value="none" class="gender-radio">
            <label for="none" class="gender-label"><i class="fa-solid fa-user-slash"></i></label>
        </div>
    </div>

    <button type="submit" class="auth-form__submit-card welcome-card welcome-card--appointments">
        <h2 class="welcome-card__title">Create Account</h2>
        <div class="welcome-card__icon"><i class="fa-solid fa-user-plus"></i></div>
    </button>

    <div class="auth-swap">
        <button type="button" id="switchToLogin" class="welcome-card welcome-card--manual">
            <h2 class="welcome-card__title">Already registered?</h2>
            <p class="welcome-card__description">Sign in to your existing account and manage your appointments.</p>
            <div class="welcome-card__icon"><i class="fa-solid fa-arrow-right-to-bracket"></i></div>
        </button>
    </div>
</form>