<div class="card service-card">
    <div class="card-body">
        <h3 class="card-title">{{ $item->name }}</h3>

        <span class="card-price">{{ number_format($item->price, 2) }} €</span>
    </div>

    <p class="card-subtitle">
        Provided by:
        <a href="{{ route('business.book', $item->branch->business->id) }}">
            {{ $item->branch->business->name }}
            <small>({{ $item->branch->name }})</small>
        </a>
    </p>

    <div class="card-meta">
        <span class="meta-item">⏱ {{ $item->duration }} min</span>
        <span class="meta-item">📍 {{ ucfirst($item->location_type) }}</span>
    </div>

    <div class="card-actions">
        <a href="{{ route('service.book', $item->id) }}" class="btn-primary">
            Book Now
        </a>
    </div>
</div>
