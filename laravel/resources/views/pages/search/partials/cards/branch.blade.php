<div class="card branch-card">
    <div class="card-body">
        <h3 class="card-title">{{ $item->name }}</h3>
        <p class="card-subtitle">{{ $item->business->name }}</p>

        <div class="branch-info">
            <p><strong>Address:</strong> {{ $item->address_line_1 . ', ' . $item->city }}</p>

            @if (isset($item->phone))
                <p><strong>Phone:</strong> {{ $item->phone }}</p>
            @endif
        </div>

        <div class="card-actions">
            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($item->address_line_1 . ' ' . $item->city) }}"
                target="_blank" class="btn-secondary">
                Get Directions
            </a>

            <a href="{{ route('business.book', $item->business->id) }}" class="btn-link">Visit Shop</a>
        </div>
    </div>
</div>
