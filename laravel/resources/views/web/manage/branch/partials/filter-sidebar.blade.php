<div class="filter-sidebar">
    <form action="{{ route('manage.branch.index') }}" method="GET" class="filter-form">

        <div class="filter-group">
            <label for="branch_name">Branch Name</label>
            <input
                type="text"
                name="branch_name"
                id="branch_name"
                value="{{ request('branch_name') }}"
                placeholder="e.g. Barber Shop"
                autocomplete="off"
            >
        </div>

        <div class="filter-group">
            <label for="country">Country</label>
            <input
                type="text"
                name="country"
                id="country"
                value="{{ request('country') }}"
                placeholder="Slovakia"
                autocomplete="off"
            >
        </div>

        <div class="filter-group">
            <label for="city">City</label>
            <input
                type="text"
                name="city"
                id="city"
                value="{{ request('city') }}"
                placeholder="Bratislava"
                autocomplete="off"
            >
        </div>

        <div class="filter-group">
            <label for="address">Address</label>
            <input
                type="text"
                name="address"
                id="address"
                value="{{ request('address') }}"
                placeholder="Ilkovicova 3"
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
            <label>Type</label>
            <div class="checkbox-list">
                @foreach (['online' => 'Online', 'hybrid' => 'Hybrid', 'physical' => 'Physical'] as $val => $label)
                    <label class="checkbox-item checkbox-item--custom">
                        <input
                            type="checkbox"
                            name="types[]"
                            value="{{ $val }}"
                            {{ in_array($val, request('types', [])) ? 'checked' : '' }}
                        >
                        <span class="checkbox-item__box"></span>
                        <span class="checkbox-item__text">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="filter-group">
            <label>Filter by Business</label>

            <div class="business-search">
                <input
                    type="text"
                    id="businessSearchInput"
                    class="user-search__input"
                    placeholder="Search by name or description..."
                    autocomplete="off"
                >
                <div id="businessSearchDropdown" class="user-search__dropdown hidden"></div>
                <input type="hidden" name="business_id" id="businessIdInput" value="{{ request('business_id') }}">
            </div>

            @if(request('business_id'))
                <div class="user-search__selected" id="selectedBusinessBadge">
                        <span id="selectedBusinessLabel">
                            {{ $selectedBusiness ? $selectedBusiness->name : 'Business #' . request('business_id') }}
                        </span>
                    <button type="button" id="clearBusinessBtn" class="user-search__clear">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @else
                <div class="user-search__selected hidden" id="selectedBusinessBadge">
                    <span id="selectedBusinessLabel"></span>
                    <button type="button" id="clearBusinessBtn" class="user-search__clear">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            @endif
        </div>

        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="{{ route('manage.branch.index') }}" class="btn-reset">Reset Filters</a>
        </div>

    </form>
</div>
