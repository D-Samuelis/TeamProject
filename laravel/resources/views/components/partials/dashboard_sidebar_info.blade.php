<div class="business__sidebar-header">
    <div class="display-column">
        @if(auth()->user() && auth()->user()->isAdmin())
            <div class="business__sidebar-type">ADMIN</div>
            <div class="business__sidebar-description">
                You are currently in the admin dashboard. Here you can manage everything from one place.
            </div>
        @else
            <div class="business__sidebar-type">MANAGER</div>
            <div class="business__sidebar-description">
                You are currently in the manager dashboard. Here you can manage your assigned resources.
            </div>
        @endif
    </div>
</div>

<div class="business__sidebar-links">
    {{-- Businesses --}}
    <a href="{{ route('manage.business.index') }}" 
       class="business__sidebar-link {{ ($active ?? '') === 'businesses' ? 'business__sidebar-link--active' : '' }}">
        <i class="fa-solid fa-layer-group"></i>
        {{ auth()->user()->isAdmin() ? 'Businesses' : 'My Businesses' }}
    </a>

    {{-- Branches --}}
    <a href="{{ route('manage.branch.index') }}" 
       class="business__sidebar-link {{ ($active ?? '') === 'branches' ? 'business__sidebar-link--active' : '' }}">
        <i class="fa-solid fa-location-dot"></i>
        {{ auth()->user()->isAdmin() ? 'Branches' : 'My Branches' }}
    </a>

    {{-- Services --}}
    <a href="{{ route('manage.service.index') }}" 
       class="business__sidebar-link {{ ($active ?? '') === 'services' ? 'business__sidebar-link--active' : '' }}">
        <i class="fa-solid fa-bell-concierge"></i>
        {{ auth()->user()->isAdmin() ? 'Services' : 'My Services' }}
    </a>

    {{-- Assets --}}
    <a href="{{ route('manage.asset.index') }}" 
       class="business__sidebar-link {{ ($active ?? '') === 'assets' ? 'business__sidebar-link--active' : '' }}">
        <i class="fa-regular fa-gem"></i>
        {{ auth()->user()->isAdmin() ? 'Assets' : 'My Assets' }}
    </a>
</div>

{{-- Špeciálna admin sekcia --}}
@if(auth()->user() && auth()->user()->isAdmin())
    <div class="business__sidebar-links">
        {{-- Users --}}
        <a href="#" 
           class="business__sidebar-link {{ ($active ?? '') === 'users' ? 'business__sidebar-link--active' : '' }}">
            <i class="fa-solid fa-user-group"></i> {{-- Opravil som ikonu na skupinu používateľov --}}
            Users
        </a>

        {{-- Categories --}}
        <a href="{{ route('admin.categories.index') }}" 
           class="business__sidebar-link {{ ($active ?? '') === 'categories' ? 'business__sidebar-link--active' : '' }}">
            <i class="fa-solid fa-tags"></i> {{-- Opravil som ikonu na tagy, nech sa to nebije s Business --}}
            Categories
        </a>
    </div>
@endif