<div class="profile-section">
    <div class="profile-section__header">
        <div>
            <h3 class="profile-section__title">Personal information</h3>
            <p class="profile-section__subtitle">Review your account details and contact information.</p>
        </div>

        <button class="btn-primary" type="button" data-profile-modal="personal">
            <i class="fa-solid fa-pen"></i>
            <span>Edit profile</span>
        </button>
    </div>

    @if ($errors->any())
        <div class="form-alert form-alert--error">
            {{ $errors->first('current_password') ?: 'Please check your profile details and try saving again.' }}
        </div>
    @elseif (session('success'))
        <div class="form-alert form-alert--success">{{ session('success') }}</div>
    @endif

    <div class="profile-form-block">
        <h4 class="profile-form-block__title">Basic details</h4>

        <div class="profile-info-grid">
            <div class="profile-info-item">
                <span class="profile-info-item__label">Title before name</span>
                <span class="profile-info-item__value">{{ auth()->user()->title_prefix ?: '-' }}</span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">Title after name</span>
                <span class="profile-info-item__value">{{ auth()->user()->title_suffix ?: '-' }}</span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">Full name</span>
                <span class="profile-info-item__value">{{ auth()->user()->name }}</span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">Email</span>
                <span class="profile-info-item__value">{{ auth()->user()->email }}</span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">Phone</span>
                <span class="profile-info-item__value">{{ auth()->user()->phone_number ?: '-' }}</span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">Birth date</span>
                <span class="profile-info-item__value">
                    {{ auth()->user()->birth_date ? auth()->user()->birth_date->format('d.m.Y') : '-' }}
                </span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">City</span>
                <span class="profile-info-item__value">{{ auth()->user()->city ?: '-' }}</span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">Country</span>
                <span class="profile-info-item__value">{{ auth()->user()->country ?: '-' }}</span>
            </div>

            <div class="profile-info-item">
                <span class="profile-info-item__label">Gender</span>
                <span class="profile-info-item__value">{{ auth()->user()->gender ? ucfirst(auth()->user()->gender) : '-' }}</span>
            </div>
        </div>
    </div>
</div>
