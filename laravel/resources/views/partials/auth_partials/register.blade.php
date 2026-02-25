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
                <input type="text" id="register_name" name="name" class="auth-form__input" placeholder="John Doe">
                <div class="auth-form__error invalid-input-field"></div>
            </div>

            <div class="auth-form__group">
                <label for="register_title_prefix" class="auth-form__label">Title Before Name</label>
                <input type="text" id="register_title_prefix" name="title_prefix" class="auth-form__input" placeholder="Bc." list="titles_before_list">
                <datalist id="titles_before_list"></datalist>
                <div class="auth-form__error invalid-input-field"></div>
                
                <div class="auth-form__info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span class="info-label">Title Info</span>
                    <div class="info-tooltip"></div>
                </div>
            </div>

            <div class="auth-form__group">
                <label for="register_email" class="auth-form__label">Email Address</label>
                <input type="email" id="register_email" name="email" class="auth-form__input" placeholder="john@example.com">
                <div class="auth-form__error invalid-input-field"></div>
            </div>

            <div class="auth-form__group">
                <label for="register_country" class="auth-form__label">Country</label>
                <input type="text" id="register_country" name="country" class="auth-form__input" placeholder="Slovakia">
                <div class="auth-form__error invalid-input-field"></div>
            </div>

            <div class="auth-form__group">
                <label for="register_password" class="auth-form__label">Password</label>
                <div class="input-wrapper">
                    <input type="password" id="register_password" name="password" class="auth-form__input" placeholder="Min. 8 characters">
                    <button type="button" class="password-toggle" tabindex="-1">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                <div class="auth-form__error invalid-input-field"></div>
            </div>

        </div>

        <div class="auth-form__column">

            <div class="auth-form__group">
                <label for="register_birth" class="auth-form__label">Birth Date</label>
                <input type="date" id="register_birth" name="birth" class="auth-form__input">
                <div class="auth-form__error invalid-input-field"></div>
            </div>

            <div class="auth-form__group">
                <label for="register_title_suffix" class="auth-form__label">Title After Name</label>
                <input type="text" id="register_title_suffix" name="title_suffix" class="auth-form__input" placeholder="PhD." list="titles_after_list">
                <datalist id="titles_after_list"></datalist>
                <div class="auth-form__error invalid-input-field"></div>
                
                <div class="auth-form__info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span class="info-label">Title Info</span>
                    <div class="info-tooltip"></div>
                </div>
            </div>

            <div class="auth-form__group">
                <label for="register_phone" class="auth-form__label">Phone Number</label>
                <input type="text" id="register_phone" name="phone" class="auth-form__input" placeholder="+421 962 124 745">
                <div class="auth-form__error invalid-input-field"></div>
            </div>

            <div class="auth-form__group">
                <label for="register_city" class="auth-form__label">City</label>
                <input type="text" id="register_city" name="city" class="auth-form__input" placeholder="Bratislava">
                <div class="auth-form__error invalid-input-field"></div>
            </div>

            <div class="auth-form__group">
                <label for="register_password_confirmation" class="auth-form__label">Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="register_password_confirmation" name="password_confirmation" class="auth-form__input" placeholder="••••••••">
                    <button type="button" class="password-toggle" tabindex="-1">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
                <div class="auth-form__error invalid-input-field"></div>
            </div>

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

    <button type="submit" class="auth-form__submit auth-form__submit--primary">
        <span>Create Account</span>
        <i class="fa-solid fa-user-plus"></i>
    </button>
</form>