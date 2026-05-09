<div class="filter-sidebar">
    <form action="{{ $form_action }}" method="GET" class="filter-form">

        <div class="filter-group">
            <label for="service_name">Service Name</label>
            <input
                type="text"
                name="service_name"
                id="service_name"
                value="{{ request('service_name') }}"
                placeholder="e.g. Haircut"
            >
        </div>

        <div class="filter-group">
            <label>Status</label>
            <div class="checkbox-list">
                @foreach (['pending' => 'Pending', 'confirmed' => 'Confirmed', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $val => $label)
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
            <label>Date Range</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}">
            <input type="date" name="date_to"   value="{{ request('date_to') }}">
        </div>

        <div class="filter-group">
            <label>Time Range</label>
            <input type="time" name="time_from" value="{{ request('time_from') }}">
            <input type="time" name="time_to"   value="{{ request('time_to') }}">
        </div>

        <div class="filter-group">
            <label>Price Range (€)</label>
            <input
                type="number"
                name="price_min"
                value="{{ request('price_min') }}"
                placeholder="Min price"
                min="0"
                step="1"
            >
            <input
                type="number"
                name="price_max"
                value="{{ request('price_max') }}"
                placeholder="Max price"
                min="0"
                step="1"
            >
        </div>

        <div class="filter-group">
            <label>Duration (min)</label>
            <input
                type="number"
                name="duration_max"
                value="{{ request('duration_max') }}"
                placeholder="e.g. 60"
                min="0"
                step="5"
            >
        </div>

        @if($show_user_filter)
            <div class="filter-group">
                <label>Filter by User</label>

                <div class="user-search">
                    <input
                        type="text"
                        id="userSearchInput"
                        class="user-search__input"
                        placeholder="Search by name or email..."
                        autocomplete="off"
                    >
                    <div id="userSearchDropdown" class="user-search__dropdown hidden"></div>
                    <input type="hidden" name="user_id" id="userIdInput" value="{{ request('user_id') }}">
                </div>

                {{-- Show currently selected user if filter is active --}}
                @if(request('user_id'))
                    <div class="user-search__selected" id="selectedUserBadge">
                        <span id="selectedUserLabel">
                            {{ $selectedUser ? $selectedUser->name . ' (' . $selectedUser->email . ')' : 'User #' . request('user_id') }}
                        </span>
                        <button type="button" id="clearUserBtn" class="user-search__clear">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @else
                    <div class="user-search__selected hidden" id="selectedUserBadge">
                        <span id="selectedUserLabel"></span>
                        <button type="button" id="clearUserBtn" class="user-search__clear">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                @endif
            </div>
        @endif

        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="{{ $form_action }}" class="btn-reset">Reset Filters</a>
        </div>

    </form>
</div>
