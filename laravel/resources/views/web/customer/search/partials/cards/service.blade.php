<a href="{{ route('book.service', ['businessId' => $item->business->id, 'serviceId' => $item->id, 'ref' => 'search', 'target' => 'service']) }}" class="card-link">
    <div class="card service-card booking-service-card">
        <div class="card-body service-card-body">
            @php
                $firstBranch = $item->branches->first();
                $branchesCount = $item->branches->count();
            @endphp
            @php
                $locationType = strtolower($item->location_type);
                $locationTypeLabel = [
                    'branch' => 'Physical',
                    'online' => 'Online',
                    'hybrid' => 'Hybrid',
                ][$locationType] ?? ucfirst($locationType);
                $locationTypeClass = in_array($locationType, ['online', 'hybrid'], true) ? $locationType : 'physical';
            @endphp

            <div class="js-search-data" hidden>
                {{ $item->name }}
                {{ $item->description }}
                {{ $item->category?->name }}
                {{ $item->location_type }}
                {{ $item->business->name }}
                {{ $item->duration_minutes }}
                {{ $item->price }}
                @if ($firstBranch)
                    {{ $firstBranch->address_line_1 }}
                    {{ $firstBranch->address_line_2 }}
                    {{ $firstBranch->postal_code }}
                    {{ $firstBranch->city }}
                    {{ $firstBranch->country }}
                @endif
            </div>

            <div class="service-card-top">
                <span class="service-location-badge service-location-badge--{{ $locationTypeClass }}">
                    {{ $locationTypeLabel }}
                </span>
            </div>

            <div class="card-header">
                <h3 class="card-title">{{ $item->name }}</h3>
                <span class="card-price">{{ number_format($item->price, 2) }} €</span>
            </div>

            @if ($item->description)
                <p class="card-description">{{ $item->description }}</p>
            @endif

            @if (strtolower($item->location_type) !== 'online')
                <div class="branch-info service-branch-info">
                    @if ($branchesCount === 1 && $firstBranch)
                        <p class="search-card-address service-card-address">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>
                                {{ implode(', ', array_filter([
                                    $firstBranch->address_line_1,
                                    $firstBranch->address_line_2,
                                    trim(($firstBranch->postal_code ?? '') . ' ' . ($firstBranch->city ?? '')),
                                    $firstBranch->country
                                ])) }}
                            </span>
                        </p>
                    @elseif ($branchesCount > 1)
                        <p class="search-card-address service-card-address">
                            <i class="fa-solid fa-location-dot"></i>
                            <span>Available at {{ $branchesCount }} locations</span>
                        </p>
                    @else
                        <div class="service-card-address-placeholder"></div>
                    @endif
                </div>
            @else
                <div class="service-card-address-placeholder"></div>
            @endif

            <p class="card-subtitle">
                Provided by: {{ $item->business->name }}
            </p>

            <div class="card-meta service-card-meta">
                <span class="meta-item service-duration">
                    <i class="fa-regular fa-clock"></i>
                    {{ $item->duration_minutes }} min
                </span>
            </div>

            @if ($item->category)
                <div class="card-categories">
                    <span class="card-categories__label">Category:</span>
                    <div class="card-categories__list">
                        <span class="category-badge">
                            {{ $item->category->name }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</a>