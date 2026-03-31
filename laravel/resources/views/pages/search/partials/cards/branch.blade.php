<a href="{{ route('business.book', $item->business->id) }}" class="card-link">
    <div class="card branch-card booking-branch-card">
        <div class="card-body">
            <div class="branch-card-top">
                @if ($item->type)
                    <span class="branch-type branch-type--{{ strtolower($item->type) }}">
                        {{ ucfirst($item->type) }}
                    </span>
                @endif
            </div>

            <h3 class="card-title">{{ $item->name }}</h3>

            <p class="card-subtitle">
                Provided by: {{ $item->business->name }}
            </p>

            <div class="branch-info">
                @if ($item->address_line_1 || $item->address_line_2 || $item->city || $item->postal_code || $item->country)
                    <p class="branch-address">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>
                            {{ implode(', ', array_filter([
                                $item->address_line_1,
                                $item->address_line_2,
                                trim(($item->postal_code ?? '') . ' ' . ($item->city ?? '')),
                                $item->country
                            ])) }}
                        </span>
                    </p>
                @endif
            </div>

            <div class="card-actions">
                {{-- potom do detailu skor
                @if ($item->city || $item->country)
                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(trim(($item->address_line_1 ?? '') . ' ' . ($item->address_line_2 ?? '') . ' ' . ($item->postal_code ?? '') . ' ' . ($item->city ?? '') . ' ' . ($item->country ?? ''))) }}"
                       target="_blank"
                       class="btn-secondary">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Get Directions</span>
                    </a>
                @endif
                --}}

                <span class="btn-link">Visit Shop</span>
            </div>
        </div>
    </div>
</a>