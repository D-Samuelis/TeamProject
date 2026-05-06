<div class="filter-sidebar">
    <form action="{{ route('manage.business.index') }}" method="GET" class="filter-form">

        <div class="filter-group">
            <label for="business_name">Business Name</label>
            <input
                type="text"
                name="business_name"
                id="business_name"
                value="{{ request('business_name') }}"
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
            <label>Published</label>
            <div class="checkbox-list">
                @foreach (['yes' => 'Published', 'no' => 'Unpublished'] as $val => $label)
                    <label class="checkbox-item checkbox-item--custom">
                        <input
                            type="radio"
                            name="published"
                            value="{{ $val }}"
                            {{ request('published') === $val ? 'checked' : '' }}
                        >
                        <span class="checkbox-item__box"></span>
                        <span class="checkbox-item__text">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        @if(auth()->user()->isAdmin())
            <div class="filter-group">
                <label>Deleted</label>
                <div class="checkbox-list">
                    @foreach (['only' => 'Deleted only', 'with' => 'Include deleted'] as $val => $label)
                        <label class="checkbox-item checkbox-item--custom">
                            <input
                                type="radio"
                                name="deleted"
                                value="{{ $val }}"
                                {{ request('deleted') === $val ? 'checked' : '' }}
                            >
                            <span class="checkbox-item__box"></span>
                            <span class="checkbox-item__text">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Placeholder: swap App\Models\Category with your real Category model when ready --}}
        <div class="filter-group">
            <label for="category_id">Category</label>
            <select name="category_id" id="category_id">
                <option value="">All categories</option>
                {{-- @foreach (\App\Models\Category::all() as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach --}}
            </select>
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
            <a href="{{ route('manage.business.index') }}" class="btn-reset">Reset Filters</a>
        </div>

    </form>
</div>
