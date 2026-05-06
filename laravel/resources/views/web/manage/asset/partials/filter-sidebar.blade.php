<div class="filter-sidebar">
    <form action="{{ route('manage.asset.index') }}" method="GET" class="filter-form">

        <div class="filter-group">
            <label for="asset_name">Asset Name</label>
            <input
                type="text"
                name="asset_name"
                id="asset_name"
                value="{{ request('asset_name') }}"
                placeholder="e.g. chair #1"
                autocomplete="off"
            >
        </div>

        <div class="filter-group">
            <label for="description">Description</label>
            <input
                type="text"
                name="description"
                id="description"
                value="{{ request('description') }}"
                placeholder="Keyword in description"
                autocomplete="off"
            >
        </div>

        <div class="filter-group">
            <label>Status</label>
            <div class="checkbox-list">
                @foreach (['active' => 'Active', 'inactive' => 'Inactive', 'deleted' => 'Archived'] as $val => $label)
                    <label class="checkbox-item checkbox-item--custom">
                        <input
                            type="checkbox"
                            name="statuses[]"
                            value="{{ $val }}"
                            {{ in_array($val, request('statuses', [])) ? 'checked' : '' }}
                        >
                        <span class="checkbox-item__box"></span>
                        <span class="checkbox-item__text">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="filter-group">
            <label>Filter by Service</label>

            <div class="service-search">
                <input
                    type="text"
                    id="serviceSearchInput"
                    class="user-search__input"
                    placeholder="Search by name or description..."
                    autocomplete="off"
                >
                <div id="serviceSearchDropdown" class="user-search__dropdown hidden"></div>
                <input type="hidden" name="service_id" id="serviceIdInput" value="{{ request('service_id') }}">
            </div>

            @if(request('service_id'))
                <div class="user-search__selected" id="selectedServiceBadge">
                        <span id="selectedServiceLabel">
                            {{ $selectedService ? $selectedService->name : 'Service #' . request('service_id') }}
                        </span>
                    <button type="button" id="clearServiceBtn" class="user-search__clear">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @else
                <div class="user-search__selected hidden" id="selectedServiceBadge">
                    <span id="selectedServiceLabel"></span>
                    <button type="button" id="clearServiceBtn" class="user-search__clear">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="{{ route('manage.asset.index') }}" class="btn-reset">Reset Filters</a>
        </div>

    </form>
</div>
