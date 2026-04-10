@extends('web.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="profile-layout">
    <div class="profile-shell">

        <div class="profile-top">
            <div>
                <h1 class="profile-top__title">My Profile</h1>
                <p class="profile-top__subtitle">Manage your account details, settings and privacy.</p>
            </div>
        </div>

        <div class="profile-dashboard">

            <aside class="profile-sidebar">
                <div class="profile-sidebar__user">
                    <div class="profile-sidebar__avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>

                    <div class="profile-sidebar__identity">
                        <div class="profile-sidebar__name">{{ auth()->user()->name }}</div>
                        <div class="profile-sidebar__email">{{ auth()->user()->email }}</div>
                    </div>
                </div>

                <nav class="profile-nav">
                    <button class="profile-nav__item is-active" type="button" data-section-target="personal">
                        <i class="fa-solid fa-id-card"></i>
                        <span>Personal info</span>
                    </button>

                    <button class="profile-nav__item" type="button" data-section-target="settings">
                        <i class="fa-solid fa-sliders"></i>
                        <span>Settings</span>
                    </button>

                    <button class="profile-nav__item" type="button" data-section-target="privacy">
                        <i class="fa-solid fa-shield-halved"></i>
                        <span>Privacy</span>
                    </button>

                    <button class="profile-nav__item" type="button" data-section-target="security">
                        <i class="fa-solid fa-lock"></i>
                        <span>Security</span>
                    </button>
                    <button class="profile-nav__item" type="button" data-section-target="ratings">
                        <i class="fa-solid fa-star"></i>
                        <span>Ratings</span>
                    </button>

                    <button class="profile-nav__item" type="button" data-section-target="actions">
                        <i class="fa-solid fa-bolt"></i>
                        <span>Quick actions</span>
                    </button>


                </nav>
            </aside>

            <section class="profile-content">

                <div class="profile-panel is-active" data-section-panel="personal">
                    @include('web.customer.profile.partials.personal-info')
                </div>

                <div class="profile-panel" data-section-panel="settings">
                    @include('web.customer.profile.partials.settings')
                </div>

                <div class="profile-panel" data-section-panel="privacy">
                    @include('web.customer.profile.partials.privacy')
                </div>

                <div class="profile-panel" data-section-panel="security">
                    @include('web.customer.profile.partials.security')
                </div>


                <div class="profile-panel" data-section-panel="ratings">
                    @include('web.customer.profile.partials.ratings')
                </div>

                <div class="profile-panel" data-section-panel="actions">
                    @include('web.customer.profile.partials.quick-actions')
                </div>

            </section>
        </div>
    </div>
</div>

@vite('resources/js/pages/profile/entry.js')
@endsection