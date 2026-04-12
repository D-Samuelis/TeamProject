<div class="profile-card">
    <div class="profile-card__title">Settings</div>

    <div class="profile-section__intro">
        Control how you receive updates and manage your privacy.
    </div>

    @if (session('success'))
        <div class="form-alert form-alert--success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('profile.settings') }}">
        @csrf
        @method('PATCH')

        <input type="hidden" name="notify_email" value="0">
        <input type="hidden" name="notify_sms" value="0">
        <input type="hidden" name="is_visible" value="0">

        <div class="profile-subsection">
            <div class="profile-subsection__title">Notifications</div>
            <div class="settings">
                <div class="setting-row">
                    <div>
                        <div class="setting-row__title">Email notifications</div>
                        <div class="setting-row__desc">Receive updates by email.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="notif-email" name="notify_email" value="1"
                            {{ auth()->user()->notify_email ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </div>

                <div class="setting-row">
                    <div>
                        <div class="setting-row__title">SMS notifications</div>
                        <div class="setting-row__desc">Receive updates by SMS.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" id="notif-sms" name="notify_sms" value="1"
                            {{ auth()->user()->notify_sms ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="profile-divider"></div>

        <div class="profile-subsection">
            <div class="profile-subsection__title">Privacy</div>
            <div class="settings">
                <div class="setting-row">
                    <div>
                        <div class="setting-row__title">Visible account</div>
                        <div class="setting-row__desc">Allow your profile to be visible in the system.</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="is_visible" value="1"
                            {{ auth()->user()->is_visible ? 'checked' : '' }}>
                        <span class="switch__slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <div class="profile-divider"></div>

        <div class="danger-zone">
            <div class="danger-zone__title">Danger zone</div>
            <div class="danger-zone__desc">Caution: This action is irreversible.</div>
            <div class="section-actions">
                <button class="btn-danger" type="button">Delete account</button>
            </div>
        </div>

        <div class="section-actions" style="margin-top: 2rem;">
            <button class="btn-primary" type="submit">Save settings</button>
        </div>
    </form>
</div>
