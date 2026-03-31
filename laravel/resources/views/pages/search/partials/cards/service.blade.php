<a href="{{ route('service.book', $item->id) }}" class="card-link">
    <div class="card service-card booking-service-card">
        <div class="card-body">
            <div class="service-card-top">
                <span class="service-location-badge service-location-badge--{{ strtolower($item->location_type) === 'online' ? 'online' : 'physical' }}">
                    {{ strtolower($item->location_type) === 'online' ? 'Online' : 'Physical' }}
                </span>
            </div>

            <div class="card-header">
                <h3 class="card-title">{{ $item->name }}</h3>
                <span class="card-price">{{ number_format($item->price, 2) }} €</span>
            </div>

            <p class="card-subtitle">
                Provided by: {{ $item->business->name }}
            </p>

            @if ($item->description)
                <p class="card-description">{{ $item->description }}</p>
            @endif

            <div class="card-meta">
                <span class="meta-item service-duration">
    <i class="fa-regular fa-clock"></i>
    {{ $item->duration_minutes }} min
</span>
            </div>
        </div>
    </div>
</a>