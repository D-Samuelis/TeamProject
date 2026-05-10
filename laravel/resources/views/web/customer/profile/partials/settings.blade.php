<div class="profile-section">
    <div class="profile-section__header profile-section__header--actions-only">
        <button class="btn-primary" type="button" data-profile-modal="settings">
            <i class="fa-solid fa-pen"></i>
            <span>Edit settings</span>
        </button>
    </div>

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

    <div class="danger-zone" id="delete-account-prompt">
        <div>
            <div class="danger-zone__title">Danger zone</div>
            <div class="danger-zone__desc">Permanently remove your account and related profile data.</div>
        </div>

        <button class="danger-zone__button danger-zone__button--danger" type="button"
            onclick="document.getElementById('delete-confirm').classList.remove('hidden'); document.getElementById('delete-account-prompt').classList.add('hidden')">
            <i class="fa-solid fa-trash"></i>
            <span>Delete account</span>
        </button>
    </div>

    <form id="delete-confirm" action="{{ route('profile.destroy') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')

        <div class="danger-zone danger-zone--confirm">
            <div>
                <div class="danger-zone__title">Are you sure?</div>
                <div class="danger-zone__desc">
                    This action is irreversible. All your data will be permanently deleted and upcoming appointments
                    cancelled.
                    If you own a business, you must delete or transfer it first.
                </div>

                @if (session('error'))
                    <div class="danger-zone__error">{{ session('error') }}</div>
                @endif

                <div class="danger-zone__input-wrap">
                    <input type="password" name="password" placeholder="Confirm your password"
                        class="danger-zone__input @error('password') input--error @enderror" autocomplete="current-password">
                    @error('password')
                        <span class="input__error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="danger-zone__actions">
                <button type="submit" class="danger-zone__button danger-zone__button--danger">
                    <i class="fa-solid fa-trash"></i>
                    <span>Delete</span>
                </button>
                <button type="button" class="danger-zone__button danger-zone__button--secondary"
                    onclick="document.getElementById('delete-confirm').classList.add('hidden'); document.getElementById('delete-account-prompt').classList.remove('hidden')">
                    Cancel
                </button>
            </div>
        </div>
    </form>
</div>
