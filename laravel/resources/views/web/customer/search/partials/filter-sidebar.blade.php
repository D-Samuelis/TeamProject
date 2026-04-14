
<div class="filter-sidebar">
    <form action="{{ route('search.index') }}" method="GET" class="filter-form">
        <input type="hidden" name="target" value="{{ $filters->target }}">

        <div class="filter-group">
            <label for="q">Search Keywords</label>
            <input
                type="text"
                name="q"
                id="q"
                value="{{ $filters->query }}"
                placeholder="Name, description..."
            >
        </div>

        <div class="filter-group">
            <label for="city">City</label>
            <input
                type="text"
                name="city"
                id="city"
                value="{{ $filters->city }}"
                placeholder="e.g. Trnava"
            >
        </div>

        @if ($filters->target !== 'branch')
            <div class="filter-group">
                <label for="max_price">Maximum Price (€)</label>

                <div class="filter-range-header">
                    <span>0 €</span>
                    <span id="max_price_value">{{ $filters->maxPrice ?? 500 }} €</span>
                </div>

                <input
                    type="range"
                    name="max_price"
                    id="max_price"
                    value="{{ $filters->maxPrice ?? 500 }}"
                    min="0"
                    max="400"
                    step="5"
                    class="filter-range"
                >
            </div>
        @endif

        @if ($filters->target === 'service' || $filters->target === 'business')
            <div class="filter-group">
                <label for="max_duration">Maximum Duration (Minutes)</label>

                <div class="filter-range-header">
                    <span>0 min</span>
                    <span id="max_duration_value">{{ $filters->maxDuration ?? 300 }} min</span>
                </div>

                <input
                    type="range"
                    name="max_duration"
                    id="max_duration"
                    value="{{ $filters->maxDuration ?? 300 }}"
                    min="0"
                    max="300"
                    step="5"
                    class="filter-range"
                >
            </div>
        @endif

        <div class="filter-group">
    <label>Location Type</label>

    <div class="checkbox-list">
        @foreach ([
            'physical' => 'Physical',
            'online' => 'Online',
            'hybrid' => 'Hybrid',
        ] as $val => $label)
            <label class="checkbox-item checkbox-item--custom">
                <input
                    type="checkbox"
                    name="location_types[]"
                    value="{{ $val }}"
                    {{ in_array($val, $filters->locationTypes ?? []) ? 'checked' : '' }}
                >
                <span class="checkbox-item__box"></span>
                <span class="checkbox-item__text">{{ $label }}</span>
            </label>
        @endforeach
    </div>
</div>

        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="{{ route('search.index', ['target' => $filters->target]) }}" class="btn-reset">
                Reset Filters
            </a>
        </div>
    </form>
</div>