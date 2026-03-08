@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="profile-layout">
    <div class="profile-shell">

        <div class="profile-top">
            <div>
                <h1 class="profile-top__title">My Profile</h1>
                <p class="profile-top__subtitle">Account overview and quick settings.</p>
            </div>

            <div class="profile-top__actions">
                <button class="btn-primary" type="button" data-open-modal="editProfile">
                    Edit profile
                </button>
            </div>
        </div>

        <div class="profile-main">

            <div class="profile-left">

                <div class="profile-card">
                    <div class="profile-card__title">Overview</div>

                    <div class="profile-overview">
                        <div class="profile-overview__avatar">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div>
                            <div class="profile-overview__name">{{ auth()->user()->name }}</div>
                            <div class="profile-overview__email">{{ auth()->user()->email }}</div>
                        </div>
                    </div>

                    <div class="profile-meta">
                        <span class="profile-badge">Client</span>
                        Joined at {{ auth()->user()->created_at?->format('d.m.Y') }}
                    </div>

                    <div class="profile-divider"></div>

                    <div class="profile-info">
                        <div class="profile-info__row">
                            <span class="profile-info__label">Phone</span>
                            <span class="profile-info__value">{{ auth()->user()->phone_number ?? '—' }}</span>
                        </div>

                        <div class="profile-info__row">
                            <span class="profile-info__label">Address</span>
                             {{ trim((auth()->user()->city ?? '') . ', ' . (auth()->user()->country ?? '')) ?: '—' }}
                        </div>

                    </div>
                </div>

                <div class="profile-card">
                    <div class="profile-card__title">Quick actions</div>

                    <div class="profile-actions">
                        <a class="profile-action" href="/">
                            <div class="profile-action__left">
                                <div class="profile-action__icon"><i class="fa-solid fa-book"></i></div>
                                <div>
                                    <div class="profile-action__title">Booking</div>
                                    <div class="profile-action__desc">Create a new booking.</div>
                                </div>
                            </div>
                            <div class="profile-action__arrow"><i class="fa-solid fa-chevron-right"></i></div>
                        </a>

                        <a class="profile-action" href="/myAppointments">
                            <div class="profile-action__left">
                                <div class="profile-action__icon"><i class="fa-solid fa-calendar"></i></div>
                                <div>
                                    <div class="profile-action__title">My appointments</div>
                                    <div class="profile-action__desc">View all your bookings.</div>
                                </div>
                            </div>
                            <div class="profile-action__arrow"><i class="fa-solid fa-chevron-right"></i></div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="profile-right">

                <a class="action-card" href="{{ route('profile.ratings') }}">
                    <div class="action-card__left">
                        <div class="action-card__icon"><i class="fa-solid fa-star"></i></div>
                        <div>
                            <div class="action-card__title">Ratings</div>
                            {{ $average }} average · {{ $count }} reviews
                        </div>
                    </div>
                    <div class="action-card__arrow"><i class="fa-solid fa-chevron-right"></i></div>
                </a>

                <div class="profile-card">
                    <div class="profile-card__title">Notifications</div>

                    <div class="settings">
                        <div class="setting-row">
                            <div>
                                <div class="setting-row__title">Email notifications</div>
                                <div class="setting-row__desc">Send updates to your email.</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="notif-email" checked>
                                <span class="switch__slider"></span>
                            </label>
                        </div>

                        <div class="setting-row">
                            <div>
                                <div class="setting-row__title">SMS notifications</div>
                                <div class="setting-row__desc">Send updates to your phone.</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="notif-sms">
                                <span class="switch__slider"></span>
                            </label>
                        </div>

                        <div class="setting-row">
                            <div>
                                <div class="setting-row__title">Quiet mode</div>
                                <div class="setting-row__desc">Disable all notifications.</div>
                            </div>
                            <label class="switch">
                                <input type="checkbox" id="notif-quiet">
                                <span class="switch__slider"></span>
                            </label>
                        </div>
                    </div>
                </div>

                <button class="action-card" type="button" data-open-modal="editProfile">
                    <div class="action-card__left">
                        <div class="action-card__icon"><i class="fa-solid fa-pen"></i></div>
                        <div>
                            <div class="action-card__title">Edit contact details</div>
                            <div class="action-card__desc">Name, email, phone, address</div>
                        </div>
                    </div>
                    <div class="action-card__arrow"><i class="fa-solid fa-chevron-right"></i></div>
                </button>

                <button class="action-card" type="button" data-open-modal="changePassword">
                    <div class="action-card__left">
                        <div class="action-card__icon"><i class="fa-solid fa-lock"></i></div>
                        <div>
                            <div class="action-card__title">Change password</div>
                            <div class="action-card__desc">Update your password</div>
                        </div>
                    </div>
                    <div class="action-card__arrow"><i class="fa-solid fa-chevron-right"></i></div>
                </button>

            </div>
        </div>
    </div>
</div>




@include('partials.profile_partials.edit-profile-modal')
@include('partials.profile_partials.change-password-modal')

@vite('resources/js/pages/profile/js.js')
@endsection