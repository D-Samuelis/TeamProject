<div class="profile-card">
    <div class="profile-card__title">Personal information</div>

    <div class="profile-section__intro">
        View and update your personal details and contact information below.
    </div>

    @if (session('success'))
        <div class="form-alert form-alert--success">{{ session('success') }}</div>
    @endif

    <form class="form form-profile-info" method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        <div class="profile-subsection">
            <div class="profile-subsection__title">Details</div>

            <div class="form-grid">
                {{-- Title Before --}}
                <div class="form__group">
                    <label class="form__label">Title before name</label>
                    <input class="form__input" type="text" name="title_prefix" list="titles_before_list"
                        value="{{ old('title_prefix', auth()->user()->title_prefix) }}" placeholder="—">
                    <div class="form-error"></div>
                </div>

                {{-- Title After --}}
                <div class="form__group">
                    <label class="form__label">Title after name</label>
                    <input class="form__input" type="text" name="title_suffix" list="titles_after_list"
                        value="{{ old('title_suffix', auth()->user()->title_suffix) }}" placeholder="—">
                    <div class="form-error"></div>
                </div>

                {{-- Full Name --}}
                <div class="form__group">
                    <label class="form__label">Full name</label>
                    <input class="form__input" type="text" name="name"
                        value="{{ old('name', auth()->user()->name) }}" required>
                    <div class="form-error"></div>
                </div>

                {{-- Email --}}
                <div class="form__group">
                    <label class="form__label">Email</label>
                    <input class="form__input" type="email" name="email"
                        value="{{ old('email', auth()->user()->email) }}" required>
                    <div class="form-error"></div>
                </div>

                {{-- Phone --}}
                <div class="form__group">
                    <label class="form__label">Phone</label>
                    <input class="form__input" type="text" name="phone_number"
                        value="{{ old('phone_number', auth()->user()->phone_number) }}" placeholder="—">
                    <div class="form-error"></div>
                </div>

                {{-- Birth Date --}}
                <div class="form__group">
                    <label class="form__label">Birth date</label>
                    <input class="form__input" type="date" name="birth_date"
                        value="{{ old('birth_date', auth()->user()->birth_date ? \Carbon\Carbon::parse(auth()->user()->birth_date)->format('Y-m-d') : '') }}">
                    <div class="form-error"></div>
                </div>

                {{-- City --}}
                <div class="form__group">
                    <label class="form__label">City</label>
                    <input class="form__input" type="text" name="city"
                        value="{{ old('city', auth()->user()->city) }}" placeholder="—">
                    <div class="form-error"></div>
                </div>

                {{-- Country --}}
                <div class="form__group">
                    <label class="form__label">Country</label>
                    <input class="form__input" type="text" name="country"
                        value="{{ old('country', auth()->user()->country) }}" placeholder="—">
                    <div class="form-error"></div>
                </div>

                {{-- Gender --}}
                <div class="form__group">
                    <label class="form__label">Gender</label>
                    <select class="form__input" name="gender">
                        <option value="">Select...</option>
                        <option value="male" {{ old('gender', auth()->user()->gender) == 'male' ? 'selected' : '' }}>
                            Male</option>
                        <option value="female"
                            {{ old('gender', auth()->user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other"
                            {{ old('gender', auth()->user()->gender) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <div class="form-error"></div>
                </div>
            </div>

            {{-- New Password --}}
            <div class="form__group">
                <label class="form__label">New password</label>
                <input class="form__input" type="password" name="password" autocomplete="new-password">
                <div class="form-error"></div>
            </div>

            {{-- Confirm New Password --}}
            <div class="form__group">
                <label class="form__label">Confirm new password</label>
                <input class="form__input" type="password" name="password_confirmation" autocomplete="new-password">
                <div class="form-error"></div>
            </div>

            {{-- Confirm identity --}}
            <div class="form__group">
                <label class="form__label">Current password <span style="color:red">*</span></label>
                <input class="form__input" type="password" name="current_password"
                    placeholder="Enter your password to confirm changes" autocomplete="current-password">
                <div class="form-error"></div>
            </div>

            <div class="section-actions" style="margin-top: 2rem;">
                <button class="btn-primary" type="submit">Save changes</button>
            </div>
        </div>

        <datalist id="titles_before_list"></datalist>
        <datalist id="titles_after_list"></datalist>
    </form>

    <div class="profile-divider"></div>

    <div class="profile-subsection">
        <div class="profile-subsection__title">Account information</div>
        <div class="profile-meta-list">
            <div class="profile-meta-item">
                <span class="profile-meta-item__label">Joined</span>
                <span class="profile-meta-item__value">
                    {{ auth()->user()->created_at ? auth()->user()->created_at->format('d.m.Y H:i') : '—' }}
                </span>
            </div>

            <div class="profile-meta-item">
                <span class="profile-meta-item__label">Last updated</span>
                <span class="profile-meta-item__value">
                    {{ auth()->user()->updated_at ? auth()->user()->updated_at->format('d.m.Y H:i') : '—' }}
                </span>
            </div>
        </div>
    </div>
</div>
