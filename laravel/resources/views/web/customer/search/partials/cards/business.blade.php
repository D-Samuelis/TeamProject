<a href="{{ route('book.business', ['businessId' => $item->id, 'ref' => 'search', 'target' => 'business']) }}" class="card-link">
    <div class="card booking-business-card">
        <div class="card-body">
            @php
                $categories = $item->branches
                    ->flatMap(fn ($branch) => $branch->services)
                    ->pluck('category')
                    ->filter()
                    ->unique('id')
                    ->values();
            @endphp

            <div class="js-search-data" hidden>
                {{ $item->name }}
                {{ $item->description }}
                {{ $categories->pluck('name')->implode(' ') }}
                {{ $item->services->count() }}
                {{ $item->branches->count() }}
            </div>

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

            @if ($categories->isNotEmpty())
                <div class="card-categories">
                    <span class="card-categories__label">Categories:</span>
                    <div class="card-categories__list">
                        @foreach ($categories as $category)
                            <span class="category-badge">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</a>