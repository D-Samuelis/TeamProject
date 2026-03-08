<div class="profile-card">
    <div class="profile-card__title">Personal information</div>

    <div class="profile-section__intro">
        View and update your personal details and contact information.
    </div>


    <div class="profile-subsection">
        <div class="profile-subsection__title">details</div>

        <div class="profile-info">
            <div class="profile-info__row">
                <span class="profile-info__label">Title before name</span>
                <span class="profile-info__value">{{ auth()->user()->title_prefix ?? '—' }}</span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">Full name</span>
                <span class="profile-info__value">{{ auth()->user()->name ?? '—' }}</span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">Title after name</span>
                <span class="profile-info__value">{{ auth()->user()->title_suffix ?? '—' }}</span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">Email</span>
                <span class="profile-info__value">{{ auth()->user()->email ?? '—' }}</span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">Phone</span>
                <span class="profile-info__value">{{ auth()->user()->phone_number ?? '—' }}</span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">City</span>
                <span class="profile-info__value">{{ auth()->user()->city ?? '—' }}</span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">Country</span>
                <span class="profile-info__value">{{ auth()->user()->country ?? '—' }}</span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">Birth date</span>
                <span class="profile-info__value">
                    {{ auth()->user()->birth_date ? \Carbon\Carbon::parse(auth()->user()->birth_date)->format('d.m.Y') : '—' }}
                </span>
            </div>

            <div class="profile-info__row">
                <span class="profile-info__label">Gender</span>
                <span class="profile-info__value">{{ auth()->user()->gender ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="profile-divider"></div>

   <div class="profile-subsection">
    <div class="profile-subsection__title">Edit details</div>

    <form class="form form-profile-info" method="POST" action="{{ route('profile.update') }}">
        @csrf

        <div class="form-grid">
            <div class="form__group">
                <label class="form__label">Title before name</label>
                <input
                    class="form__input"
                    type="text"
                    name="title_prefix"
                    list="titles_before_list"
                    value="{{ old('title_prefix', auth()->user()->title_prefix) }}"
                >
                <div class="form-error"></div>
            </div>

            <div class="form__group">
                <label class="form__label">Title after name</label>
                <input
                    class="form__input"
                    type="text"
                    name="title_suffix"
                    list="titles_after_list"
                    value="{{ old('title_suffix', auth()->user()->title_suffix) }}"
                >
                <div class="form-error"></div>
            </div>

            <div class="form__group">
                <label class="form__label">Full name</label>
                <input
                    class="form__input"
                    type="text"
                    name="name"
                    value="{{ old('name', auth()->user()->name) }}"
                >
                <div class="form-error"></div>
            </div>

            <div class="form__group">
                <label class="form__label">Email</label>
                <input
                    class="form__input"
                    type="email"
                    name="email"
                    value="{{ old('email', auth()->user()->email) }}"
                >
                <div class="form-error"></div>
            </div>

            <div class="form__group">
                <label class="form__label">Phone</label>
                <input
                    class="form__input"
                    type="text"
                    name="phone_number"
                    value="{{ old('phone_number', auth()->user()->phone_number) }}"
                >
                <div class="form-error"></div>
            </div>

            <div class="form__group">
                <label class="form__label">City</label>
                <input
                    class="form__input"
                    type="text"
                    name="city"
                    value="{{ old('city', auth()->user()->city) }}"
                >
                <div class="form-error"></div>
            </div>

            <div class="form__group">
                <label class="form__label">Country</label>
                <input
                    class="form__input"
                    type="text"
                    name="country"
                    value="{{ old('country', auth()->user()->country) }}"
                >
                <div class="form-error"></div>
            </div>
        </div>

        <div class="section-actions">
            <button class="btn-primary" type="submit">Save changes</button>
        </div>
    </form>

    <datalist id="titles_before_list"></datalist>
    <datalist id="titles_after_list"></datalist>

    
</div>

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