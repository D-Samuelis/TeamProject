{{-- Šablóna pre Connections v Toolbare --}}
<template id="tpl-connections">
    <div class="dropdown-mini-list dropdown-mini-list--toolbar service__connections">
        {{-- Business --}}
        <div class="service-settings__sidebar-group">
            <p class="service-settings__sidebar-group-title">Business</p>
            @if($service->business)
                <a href="{{ route('manage.business.show', $service->business->id) }}" class="team-member-item service-settings__sidebar-link">
                    <div class="member-info">
                        <span class="member-name">{{ $service->business->name }}</span>
                        <span class="member-role">Open business detail</span>
                    </div>
                    <i class="fa-solid fa-briefcase"></i>
                </a>
            @else
                <div class="team-member-item service-settings__sidebar-link--muted">
                    <span class="member-role">No business linked</span>
                </div>
            @endif
        </div>

        {{-- Branches --}}
        <div class="service-settings__sidebar-group">
            <p class="service-settings__sidebar-group-title">Branches</p>
            @forelse($service->branches as $branch)
                <a href="{{ route('manage.branch.show', $branch->id) }}" class="team-member-item service-settings__sidebar-link">
                    <div class="member-info">
                        <span class="member-name">{{ $branch->name }}</span>
                        <span class="member-role">{{ $branch->city ?: 'Open branch detail' }}</span>
                    </div>
                    <i class="fa-solid fa-location-dot"></i>
                </a>
            @empty
                <div class="team-member-item service-settings__sidebar-link service-settings__sidebar-link--muted">
                    <span class="member-role">No branches assigned</span>
                </div>
            @endforelse
        </div>

        {{-- Assets --}}
        <div class="service-settings__sidebar-group">
            <p class="service-settings__sidebar-group-title">Assets</p>
            @forelse($service->assets as $asset)
                <a href="{{ route('manage.asset.show', $asset->id) }}" class="team-member-item service-settings__sidebar-link">
                    <div class="member-info">
                        <span class="member-name">{{ $asset->name }}</span>
                        <span class="member-role">{{ $asset->branch?->name ?: 'Open asset detail' }}</span>
                    </div>
                    <i class="fa-regular fa-gem"></i>
                </a>
            @empty
                <div class="team-member-item service-settings__sidebar-link service-settings__sidebar-link--muted">
                    <span class="member-role">No assets connected</span>
                </div>
            @endforelse
        </div>
    </div>
</template>