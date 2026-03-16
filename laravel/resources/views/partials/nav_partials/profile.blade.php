<div class="profile">
    <button id="profileButton" class="profile__button">
        <div class="profile__card">
            <div class="profile__icon" id="profileIcon">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="profile__info" id="profileInfo">
                <div class="profile__name">{{ auth()->user()->name ?? 'Guest User' }}</div>
                <div class="profile__role">{{ auth()->check() ? 'Client' : '' }}</div>
            </div>
        </div>
    </button>
</div>

<div class="profile-menu hidden" id="profileMenuContent">
    <h3 class="profile-menu__title">Profile</h3>

    <div class="profile-menu__content">
        <div class="profile-menu__overview">
            <div class="profile-menu__avatar-wrapper">
                <div class="profile-menu__avatar">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
            <div class="profile-menu__user-details">
                <div class="profile-menu__name">{{ auth()->user()->name ?? 'Guest User' }}</div>
                <div class="profile-menu__email">{{ auth()->user()->email ?? 'user@gmail.com' }}</div>
            </div>
        </div>

        <div class="profile-menu__status">
            <div class="profile-menu__badge">Client</div>
            <div class="profile-menu__date">Joined:
                @auth
                    {{ auth()->user()->created_at->format('d.m.Y') }}
                @endauth
            </div>
        </div>

        <div class="profile-menu__divider"></div>

        <div class="profile-menu__theme-toggle theme-toggle">
            <div class="theme-toggle__slider" id="themeSlider"></div>
            <button class="theme-toggle__btn" data-theme="light">
                <i class="fa-regular fa-sun"></i>
            </button>
            <button class="theme-toggle__btn" data-theme="dark">
                <i class="fa-regular fa-moon"></i>
            </button>
            <button class="theme-toggle__btn" data-theme="system">
                <i class="fa-solid fa-desktop"></i>
            </button>
        </div>

        <div class="profile-menu__divider"></div>

        <div class="profile-menu__options">
            <div class="profile-menu__option" id="lightModeOption">
                <a href="/" class="profile-menu__link">Booking</a>
            </div>
            @auth
                <div class="profile-menu__option" id="lightModeOption">
                    <a href="/my-appointments" class="profile-menu__link">My Appointments</a>
                </div>
                <div class="profile-menu__option" id="lightModeOption">
                    <a href="/profile" class="profile-menu__link">My Profile</a>
                </div>
                <div class="profile-menu__option" id="darkModeOption">
                    <form method="POST" action="/logout" class="profile-menu__form">
                        @csrf
                        <button type="submit" class="profile-menu__logout-btn">Logout</button>
                    </form>
                </div>
            @endauth
            @guest
                <div class="profile-menu__option" id="lightModeOption">
                    <a href="/login" class="profile-menu__link">Sign-In</a>
                </div>
            @endguest
        </div>
    </div>
</div>
