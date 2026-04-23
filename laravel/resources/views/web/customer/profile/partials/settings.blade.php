<div class="profile-section">
    <div class="profile-section__header">
        <div>
            <h3 class="profile-section__title">Settings</h3>
            <p class="profile-section__subtitle">Review notification preferences and account visibility.</p>
        </div>

        <button class="btn-primary" type="button" data-profile-modal="settings">
            <i class="fa-solid fa-pen"></i>
            <span>Edit settings</span>
        </button>
    </div>

    @if ($errors->any())
        <div class="form-alert form-alert--error">
            Please check your settings and try saving again.
        </div>
    @elseif (session('success'))
        <div class="form-alert form-alert--success">{{ session('success') }}</div>
    @endif

    <div class="profile-form-block">
        <h4 class="profile-form-block__title">Notifications</h4>

        <div class="settings">
            <div class="setting-row">
                <div>
                    <div class="setting-row__title">Email notifications</div>
                    <div class="setting-row__desc">Receive account and booking updates by email.</div>
                </div>
                <span class="profile-status-badge {{ auth()->user()->notify_email ? 'is-on' : 'is-off' }}">
                    {{ auth()->user()->notify_email ? 'On' : 'Off' }}
                </span>
            </div>

            <div class="setting-row">
                <div>
                    <div class="setting-row__title">SMS notifications</div>
                    <div class="setting-row__desc">Receive account and booking updates by SMS.</div>
                </div>
                <span class="profile-status-badge {{ auth()->user()->notify_sms ? 'is-on' : 'is-off' }}">
                    {{ auth()->user()->notify_sms ? 'On' : 'Off' }}
                </span>
            </div>
        </div>
    </div>

    <div class="profile-divider"></div>

    <div class="profile-form-block">
        <h4 class="profile-form-block__title">Privacy</h4>

        <div class="settings">
            <div class="setting-row">
                <div>
                    <div class="setting-row__title">Visible account</div>
                    <div class="setting-row__desc">Allow your profile to be visible in the system.</div>
                </div>
                <span class="profile-status-badge {{ auth()->user()->is_visible ? 'is-on' : 'is-off' }}">
                    {{ auth()->user()->is_visible ? 'Visible' : 'Hidden' }}
                </span>
            </div>
        </div>
    </div>

    <div class="profile-divider"></div>

    {{-- nefunkcne --}}
    <div class="danger-zone">
        <div>
            <div class="danger-zone__title">Danger zone</div>
            <div class="danger-zone__desc">Permanently remove your account and related profile data.</div>
        </div>

        <button class="btn-danger" type="button" disabled>
            <i class="fa-solid fa-trash"></i>
            <span>Delete account</span>
        </button>
    </div>
</div>
