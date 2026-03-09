<div class="filter-sidebar">
    <form action="{{ route('manualBooking.index') }}" method="GET" class="filter-form">
        <input type="hidden" name="target" value="{{ $filters->target }}">

        <div class="filter-group">
            <label for="q">Search Keywords</label>
            <input type="text" name="q" id="q" value="{{ $filters->query }}"
                placeholder="Name, description...">
        </div>

        <div class="filter-group">
            <label for="city">City</label>
            <input type="text" name="city" id="city" value="{{ $filters->city }}" placeholder="e.g. Trnava">
        </div>

        @if ($filters->target !== 'branch')
            <div class="filter-group">
                <label for="max_price">Maximum Price (€)</label>
                <input type="number" name="max_price" id="max_price" value="{{ $filters->maxPrice }}" step="0.01">
            </div>
        @endif

        @if ($filters->target === 'service')
            <div class="filter-group">
                <label for="max_duration">Max Duration (Minutes)</label>
                <input type="number" name="max_duration" id="max_duration" value="{{ $filters->maxDuration }}">
            </div>
        @endif

        @if ($filters->target === 'service' || $filters->target === 'business')
            <div class="filter-group">
                <label>Location Type</label>
                <div class="checkbox-list">
                    @foreach (['branch' => 'At Shop', 'online' => 'Online', 'client_address' => 'At My Place'] as $val => $label)
                        <label class="checkbox-item">
                            <input type="checkbox" name="location_types[]" value="{{ $val }}"
                                {{ in_array($val, $filters->locationTypes ?? []) ? 'checked' : '' }}>
                            {{ $label }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="{{ route('manualBooking.index', ['target' => $filters->target]) }}" class="btn-reset">
                Reset Filters
            </a>
        </div>
    </form>
</div>
