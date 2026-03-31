<a href="{{ route('business.book', $item->id) }}" class="card-link">
    <div class="card booking-business-card">
        <div class="card-body">
            <h3 class="card-title">{{ $item->name }}</h3>
            <p class="card-description">{{ Str::limit($item->description, 100) }}</p>

            <div class="business-card-stats">
                <div class="business-stat business-stat--services">
                    <div class="business-stat__icon">
                        <i class="fa-solid fa-scissors"></i>
                    </div>
                    <div class="business-stat__content">
                        <span class="business-stat__value">{{ $item->services->count() }}</span>
                        <span class="business-stat__label">Services</span>
                    </div>
                </div>

                <div class="business-stat business-stat--locations">
                    <div class="business-stat__icon">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div class="business-stat__content">
                        <span class="business-stat__value">{{ $item->branches->count() }}</span>
                        <span class="business-stat__label">Branches</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</a>