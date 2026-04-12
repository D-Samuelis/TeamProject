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
                    </nav>
                </aside>

                <section class="profile-content">

                    <div class="profile-panel is-active" data-section-panel="personal">
                        @include('web.customer.profile.partials.personal-info')
                    </div>

                    <div class="profile-panel" data-section-panel="settings">
                        @include('web.customer.profile.partials.settings')
                    </div>

                </section>
            </div>
        </div>
    </div>

    @vite('resources/js/pages/profile/entry.js')
@endsection
