<div class="card service-card">
    <div class="card-body">
        <div class="card-header">
            <h3 class="card-title">{{ $item->name }}</h3>
            <span class="card-price">{{ number_format($item->price, 2) }} €</span>
        </div>

        <p class="card-subtitle">
            Provided by: <a href="{{ route('manualBooking.show', $item->business->id) }}">{{ $item->business->name }}</a>
        </p>

        <div class="card-meta">
            <span class="meta-item">⏱ {{ $item->duration_minutes }} min</span>
            <span class="meta-item">📍 {{ ucfirst($item->location_type) }}</span>
        </div>

        <div class="card-actions">
            <a href="{{ route('manualBooking.show', $item->business->id) }}?service_id={{ $item->id }}"
                class="btn-primary">
                Book Now
            </a>
        </div>
    </div>
</div>
