<div class="profile">
    <button id="profileButton" class="profile-button">
        <div class="profile-card">
            <div class="profile-icon" id="profileIcon">
                <i class="fa-solid fa-user"></i>
            </div>
            <div class="profile-info" id="profileInfo">
                <div class="profile-name">Guest User</div>
                <div class="profile-role">Client<div>
            </div>
        </div>
    </button>
</div>

<div class="menu-content hidden" id="profileMenuContent">
    <h3 class="menu-title">Profile</h3>
    <div class="menu-item">
        <div class="profile-overview">
            <div class="profile-column">
                <div class="profile-icon-large">
                    <i class="fa-solid fa-user"></i>
                </div>
            </div>
            <div class="profile-column">
                <div class="profile-name">Guest User</div>
                <div class="profile-email">user@gmail.com</div>
            </div>
        </div>
        <div class="user-status">
            <div class="profile-role">Client</div>
            <div class="member-since">Joined at 10.12.2025</div>
        </div>
        <div class="divider"></div>
        <div class="theme-toggle">
            <div class="theme-slider" id="themeSlider"></div>
            <button class="theme-btn" data-theme="light">
                <i class="fa-regular fa-sun"></i>
            </button>
            <button class="theme-btn" data-theme="dark">
                <i class="fa-regular fa-moon"></i>
            </button>
            <button class="theme-btn" data-theme="system">
                <i class="fa-solid fa-desktop"></i>
            </button>
        </div>
        <div class="divider"></div>
        <div class="options">
            <div class="option my-profile-button" id="lightModeOption">
                <a href="/profile">My Profile</a>
            </div>
            <div class="option logout-button" id="darkModeOption">
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>