@extends('web.layouts.app')

@section('title', 'Bexora | My Profile')

@section('content')
<div class="profile-page">
    <aside class="profile-page__sidebar">
        <section class="profile-page__user-panel">
            <div class="profile-page__avatar">
                <i class="fa-regular fa-user"></i>
            </div>

            <div class="profile-page__identity">
                <div class="profile-page__name">{{ auth()->user()->name }}</div>
                <div class="profile-page__email">{{ auth()->user()->email }}</div>
            </div>
        </section>

        <section class="profile-page__navigation">
            <button class="profile-page__nav-link is-active" type="button" data-section-target="personal">
                <i class="fa-solid fa-id-card"></i>
                <span>Personal info</span>
            </button>

            <button class="profile-page__nav-link" type="button" data-section-target="settings">
                <i class="fa-solid fa-sliders"></i>
                <span>Settings</span>
            </button>
        </section>
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />

        <main class="profile-page__main">
            <header class="business__header-wrapper business__header-wrapper--simple">
                <div class="business__header-corner"></div>

                <div class="business__header-info">
                    <h2 class="business-header__title">My Profile</h2>

                    <div class="business-info">
                        <div class="stat-item stat-item--all">
                            <i class="fa-solid fa-user"></i>
                            <div>{{ auth()->user()->is_admin ? 'Admin' : 'Client' }}</div>
                            Account type
                        </div>

                        <div class="stat-item stat-item--published">
                            <i class="fa-solid fa-calendar-days"></i>
                            <div>{{ auth()->user()->created_at ? auth()->user()->created_at->format('d.m.Y') : '-' }}</div>
                            Joined
                        </div>

                        <div class="stat-item stat-item--hidden">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                            <div>{{ auth()->user()->updated_at ? auth()->user()->updated_at->format('d.m.Y') : '-' }}</div>
                            Last updated
                        </div>
                    </div>
                </div>

                <div class="business__header-right">
                    <div class="business__header-right-section_1"></div>
                    <div class="business__header-right-section_2"></div>
                </div>
            </header>

            <div class="profile-page__body">
                <section class="profile-page__panel is-active" data-section-panel="personal">
                    @include('web.customer.profile.partials.personal-info')
                </section>

                <section class="profile-page__panel" data-section-panel="settings">
                    @include('web.customer.profile.partials.settings')
                </section>
            </div>
        </main>
    </div>
</div>

<script>
    window.PROFILE_DATA = {
        routes: {
            update: "{{ route('profile.update') }}",
            settings: "{{ route('profile.settings') }}"
        },
        csrf: "{{ csrf_token() }}",
        user: {{ \Illuminate\Support\Js::from([
            'name' => old('name', auth()->user()->name),
            'email' => old('email', auth()->user()->email),
            'title_prefix' => old('title_prefix', auth()->user()->title_prefix),
            'title_suffix' => old('title_suffix', auth()->user()->title_suffix),
            'phone_number' => old('phone_number', auth()->user()->phone_number),
            'birth_date' => old('birth_date', auth()->user()->birth_date ? auth()->user()->birth_date->format('Y-m-d') : ''),
            'city' => old('city', auth()->user()->city),
            'country' => old('country', auth()->user()->country),
            'gender' => old('gender', auth()->user()->gender),
            'notify_email' => (bool) old('notify_email', auth()->user()->notify_email),
            'notify_sms' => (bool) old('notify_sms', auth()->user()->notify_sms),
            'is_visible' => (bool) old('is_visible', auth()->user()->is_visible),
        ]) }}
    };
</script>

@vite('resources/js/pages/profile/entry.js')
@endsection
