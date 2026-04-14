<a href="{{ route('book.business', [
    'businessId' => $item->business->id,
    'branch_id' => $item->id,
    'ref' => 'search',
    'target' => 'branch'
]) }}" class="card-link">
    <div class="card branch-card booking-branch-card">
        <div class="card-body">
            <div class="js-search-data" hidden>
                {{ $item->name }}
                {{ $item->type }}
                {{ $item->business->name }}
                {{ $item->address_line_1 }}
                {{ $item->address_line_2 }}
                {{ $item->postal_code }}
                {{ $item->city }}
                {{ $item->country }}
            </div>

            <div class="branch-card-top">
                @if ($item->type)
                    <span class="branch-type branch-type--{{ strtolower($item->type) }}">
                        {{ ucfirst($item->type) }}
                    </span>
                @endif
            </div>

            <h3 class="card-title">{{ $item->name }}</h3>

            <div class="branch-info">
                @if ($item->address_line_1 || $item->address_line_2 || $item->city || $item->postal_code || $item->country)
                    <p class="search-card-address">
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

            <p class="card-subtitle">
                Provided by: {{ $item->business->name }}
            </p>
        </div>
    </div>
</a>