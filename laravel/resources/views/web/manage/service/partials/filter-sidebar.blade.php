<div class="filter-sidebar">
    <form action="{{ route('manage.service.index') }}" method="GET" class="filter-form">

        <div class="filter-group">
            <label for="service_name">Service Name</label>
            <input
                type="text"
                name="service_name"
                id="service_name"
                value="{{ request('service_name') }}"
                placeholder="e.g. Barber Shop"
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
            <label>duration Range (min)</label>
            <input
                type="number"
                name="duration_min"
                value="{{ request('duration_min') }}"
                placeholder="Min duration"
                min="0"
                step="1"
            >
            <input
                type="number"
                name="duration_max"
                value="{{ request('duration_max') }}"
                placeholder="Max duration"
                min="0"
                step="1"
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

        @if(auth()->user()->isAdmin())
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

            <div class="filter-group" id="roleFilterGroup">
                <label for="role">User Role</label>
                <select name="role" id="role">
                    <option value="">Any role</option>
                    @foreach (['owner' => 'Owner', 'manager' => 'Manager', 'staff' => 'Staff'] as $val => $label)
                        <option value="{{ $val }}" {{ request('role') === $val ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        <div class="filter-actions">
            <button type="submit" class="btn-primary">Apply Filters</button>
            <a href="{{ route('manage.service.index') }}" class="btn-reset">Reset Filters</a>
        </div>

    </form>
</div>
