<div class="page-container">
    <nav class="breadcrumb">
        <a href="{{ route('business.index') }}">Dashboard</a> / {{ $business->name }}
    </nav>

    <div class="grid-3">
        <div class="space-y-6">
            <!-- Settings -->
            <section class="card">
                <h3>Business Metadata</h3>
                <form action="{{ route('business.update', $business->id) }}" method="POST">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ $business->name }}">
                    <textarea name="description">{{ $business->description }}</textarea>
                    <button class="save-btn">Save Changes</button>
                </form>
            </section>

            <!-- Branches -->
            <section class="card">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <h3>Branches</h3>
                    @can('create', [App\Models\Business\Branch::class, $business])
                        <button type="button" onclick="toggleBranchModal()"
                            style="color:#2563eb; font-size:14px; padding: 8px 20px; background:none; border:1px solid #2563eb; cursor:pointer; border-radius:6px;">
                            + Add
                        </button>
                    @endcan
                </div>

                <div class="branch-list">
                    @foreach ($business->branches as $branch)
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; margin-top:12px; padding:12px; border:1px solid #d1d5db; border-radius:6px; {{ $branch->trashed() ? 'background-color: #fef2f2; border-style: dashed; opacity: 0.7;' : '' }}">
                            <div>
                                <p class="branch-name" style="margin:0;">{{ $branch->name }}</p>
                                <span class="branch-type"
                                    style="font-size: 12px; color: #6b7280;">{{ ucfirst($branch->type) }}</span>
                            </div>

                            <div style="display:flex; gap:8px;">
                                @if (!$branch->trashed())
                                    @can('update', $branch)
                                        <form action="{{ route('branch.update', $branch->id) }}"
                                            method="POST">
                                            @csrf
                                            @method('PUT')

                                            <input type="hidden" name="business_id" value="{{ $business->id }}">
                                            <input type="hidden" name="is_active" value="0">

                                            <label
                                                style="display:flex; align-items:center; gap:8px; font-size:14px; cursor:pointer;">
                                                <input type="checkbox" name="is_active" value="1"
                                                    onchange="this.form.submit()" {{ $branch->is_active ? 'checked' : '' }}>
                                                <span style="font-size: 12px;">Active Status</span>
                                            </label>
                                        </form>
                                    @endcan

                                    {{-- Edit Permission --}}
                                    @can('update', $branch)
                                        <button type="button" onclick='toggleBranchModal(@json($branch))'
                                            style="background:#6366f1; color:white; padding:6px 12px; border-radius:6px; cursor:pointer; border:none; font-size:13px;">
                                            Edit
                                        </button>
                                    @endcan

                                    {{-- Delete Permission --}}
                                    @can('delete', $branch)
                                        <form action="{{ route('branch.delete', $branch->id) }}"
                                            method="POST" onsubmit="return confirm('Are you sure?');">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="branch-delete-btn">Delete</button>
                                        </form>
                                    @endcan
                                @else
                                    @can('update', $branch)
                                        <form action="{{ route('branch.restore', $branch->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                style="background:#10b981; color:white; padding:6px 12px; border-radius:6px; cursor:pointer; border:none; font-size:13px;">
                                                Restore
                                            </button>
                                        </form>
                                    @endcan
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <div id="branchModal"
                style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center; padding:20px;">
                <div class="card"
                    style="width:100%; max-width:500px; max-height:90vh; overflow-y:auto; position:relative;">
                    <button type="button" onclick="toggleBranchModal()"
                        style="position:absolute; right:20px; top:20px; background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>

                    <form method="POST" action="">
                        @csrf
                        <input type="hidden" name="_method" id="methodOverride" value="POST">
                        <input type="hidden" name="business_id" value="{{ $business->id }}">

                        <h2 style="margin-bottom:20px;">Create Branch</h2>

                        <div style="display:flex; flex-direction:column; gap:12px;">
                            {{-- Global Error Banner --}}
                            @if ($errors->any())
                                <div class="branch-error-banner"
                                    style="background: #fef2f2; border: 1px solid #fee2e2; color: #b91c1c; padding: 10px; border-radius: 6px; margin-bottom: 15px; font-size: 13px;">
                                    <strong>Whoops!</strong> Please check the form below.
                                </div>
                            @endif

                            <label style="font-size:14px; font-weight:500;">Name</label>
                            <input type="text" name="name" placeholder="Branch Name" required
                                style="{{ $errors->has('name') ? 'border-color: #ef4444;' : '' }}"
                                value="{{ old('name') }}">
                            @error('name')
                                <span class="branch-error-message"
                                    style="color: #ef4444; font-size: 12px; margin-top: -8px;">{{ $message }}</span>
                            @enderror

                            <label style="font-size:14px; font-weight:500;">Type</label>
                            <select name="type" required
                                style="width:100%; padding:8px; border-radius:6px; border:1px solid {{ $errors->has('type') ? '#ef4444' : '#d1d5db' }};">
                                <option value="physical" {{ old('type') == 'physical' ? 'selected' : '' }}>Physical
                                </option>
                                <option value="online" {{ old('type') == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="hybrid" {{ old('type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                            </select>

                            <input type="hidden" name="is_active" value="0">
                            <label
                                style="display:flex; align-items:center; gap:8px; font-size:14px; font-weight:500; margin-top:10px; cursor:pointer;">
                                <input type="checkbox" name="is_active" id="branch_is_active" value="1">
                                Active Status
                            </label>

                            <label style="font-size:14px; font-weight:500; margin-top: 10px;">Address Details</label>

                            <input type="text" name="address_line_1" placeholder="Address Line 1"
                                style="{{ $errors->has('address_line_1') ? 'border-color: #ef4444;' : '' }}"
                                value="{{ old('address_line_1') }}">
                            @error('address_line_1')
                                <span class="branch-error-message"
                                    style="color: #ef4444; font-size: 12px; margin-top: -8px;">{{ $message }}</span>
                            @enderror

                            <input type="text" name="address_line_2" placeholder="Address Line 2"
                                value="{{ old('address_line_2') }}">

                            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                                <div>
                                    <input type="text" name="city" placeholder="City"
                                        style="width:100%; {{ $errors->has('city') ? 'border-color: #ef4444;' : '' }}"
                                        value="{{ old('city') }}">
                                    @error('city')
                                        <span class="branch-error-message"
                                            style="color: #ef4444; font-size: 11px;">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div>
                                    <input type="text" name="postal_code" placeholder="Postal Code"
                                        style="width:100%; {{ $errors->has('postal_code') ? 'border-color: #ef4444;' : '' }}"
                                        value="{{ old('postal_code') }}">
                                    @error('postal_code')
                                        <span class="branch-error-message"
                                            style="color: #ef4444; font-size: 11px;">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <input type="text" name="country" placeholder="Country"
                                style="{{ $errors->has('country') ? 'border-color: #ef4444;' : '' }}"
                                value="{{ old('country') }}">
                            @error('country')
                                <span class="branch-error-message"
                                    style="color: #ef4444; font-size: 12px; margin-top: -8px;">{{ $message }}</span>
                            @enderror

                            <div style="margin-top:20px; display:flex; gap:12px;">
                                <button type="submit" class="save-btn" style="flex:2;">Save Branch</button>
                                <button type="button" onclick="toggleBranchModal()"
                                    style="flex:1; background:#f3f4f6; color:#374151; padding:8px 0; border:1px solid #d1d5db; border-radius:6px; cursor:pointer;">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Services -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            <section class="card">
                <h3>Services & Branch Availability</h3>

                @foreach ($business->services as $service)
                    <div class="service-card">
                        <div class="service-header">
                            <div class="service-header">
                                <a href="{{ route('service.show', $service->id) }}">{{ $service->name }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        </div>

        <div class="space-y-6">
            <section class="card">
                <h3>Team Management</h3>

                <form action="{{ route('business.assign', $business->id) }}" method="POST"
                    style="margin-bottom: 20px;">
                    @csrf
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="font-size: 12px; font-weight: bold; color: #6b7280;">Add Member by Email</label>
                        <input type="email" name="email" placeholder="staff@example.com" required>

                        <select name="role">
                            <option value="manager">Manager</option>
                            <option value="staff">Staff</option>
                        </select>

                        <button class="save-btn" style="background: #2563eb;">Assign & Notify</button>
                    </div>
                </form>

                <hr style="border: 0; border-top: 1px solid #e5e7eb; margin: 20px 0;">

                <div class="member-list">
                    <p style="font-size: 12px; font-weight: bold; color: #9ca3af; text-transform: uppercase;">Current
                        Members</p>
                    @foreach ($business->users as $user)
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #f3f4f6;">
                            <div style="flex-grow: 1;">
                                <p style="margin: 0; font-size: 14px; font-weight: 500;">{{ $user->name }}</p>

                                @if ($user->pivot->role === 'owner')
                                    <span style="font-size: 11px; color: #6b7280;">Owner</span>
                                @else
                                    {{-- Role Update Form --}}
                                    <form action="{{ route('business.users.update', [$business->id, $user->id]) }}"
                                        method="POST" style="display: inline-flex; align-items: center; gap: 5px;">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" onchange="this.form.submit()"
                                            style="font-size: 11px; padding: 2px 4px; border: 1px solid #d1d5db; border-radius: 4px;">
                                            <option value="manager"
                                                {{ $user->pivot->role === 'manager' ? 'selected' : '' }}>Manager
                                            </option>
                                            <option value="staff"
                                                {{ $user->pivot->role === 'staff' ? 'selected' : '' }}>Staff</option>
                                        </select>
                                    </form>
                                @endif
                            </div>

                            @if ($user->pivot->role !== 'owner')
                                <form action="{{ route('business.users.delete', [$business->id, $user->id]) }}"
                                    method="POST" onsubmit="return confirm('Remove this user from the business?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        style="color: #ef4444; background: none; border: none; font-size: 12px; cursor: pointer; padding: 0;">
                                        Remove
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </section>
        </div>
    </div>
</div>


<script>
    function toggleBranchModal(branch = null) {
        const modal = document.getElementById('branchModal');
        const form = modal.querySelector('form');
        const title = modal.querySelector('h2');
        const submitBtn = modal.querySelector('.save-btn');
        const methodInput = document.getElementById('methodOverride');
        const activeCheckbox = document.getElementById('branch_is_active');

        const isClosing = modal.style.display === 'flex';

        if (isClosing) {
            form.reset();

            form.querySelectorAll('input, select, textarea').forEach(el => {
                el.style.borderColor = '#d1d5db';
            });

            form.querySelectorAll('.branch-error-message').forEach(el => {
                el.style.display = 'none';
            });
            const banner = form.querySelector('.branch-error-banner');
            if (banner) banner.style.display = 'none';

            modal.style.display = 'none';
            return;
        }

        if (branch) {
            title.innerText = 'Edit Branch';
            submitBtn.innerText = 'Update Branch';
            form.action = `/branches/${branch.id}`;
            methodInput.value = 'PUT';

            form.name.value = branch.name;
            form.type.value = branch.type;
            form.address_line_1.value = branch.address_line_1 || '';
            form.address_line_2.value = branch.address_line_2 || '';
            form.city.value = branch.city || '';
            form.postal_code.value = branch.postal_code || '';
            form.country.value = branch.country || '';
            activeCheckbox.checked = branch.is_active == 1;
        } else {
            title.innerText = 'Create Branch';
            submitBtn.innerText = 'Create Branch';
            form.action = "{{ route('branch.store') }}";
            methodInput.value = 'POST';
            form.reset();
            activeCheckbox.checked = true;
        }

        modal.style.display = 'flex';
    }

    @if ($errors->any())
        window.onload = function() {
            const branchErrorKeys = ['name', 'type', 'address_line_1', 'city', 'postal_code', 'country'];
            const hasBranchErrors = @json($errors->keys()).some(key => branchErrorKeys.includes(key));

            if (hasBranchErrors) {
                toggleBranchModal();
            }
        };
    @endif
</script>


<style>
    .page-container {
        padding: 32px;
        max-width: 1280px;
        margin: 0 auto;
        font-family: sans-serif;
    }

    .breadcrumb {
        margin-bottom: 16px;
        font-size: 14px;
        color: #6b7280;
    }

    .breadcrumb a {
        text-decoration: none;
        color: inherit;
    }

    .breadcrumb a:hover {
        color: #2563eb;
    }

    /* Grid Layout */
    .grid-3 {
        display: grid;
        grid-template-columns: 1fr;
        gap: 32px;
    }

    @media(min-width: 1024px) {
        .grid-3 {
            grid-template-columns: 1fr 2fr;
        }
    }

    .space-y-6>*+* {
        margin-top: 24px;
    }

    /* Card */
    .card {
        background: white;
        padding: 24px;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .card>form {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    /* Headers */
    .card h3 {
        font-weight: bold;
        color: #374151;
        margin-bottom: 16px;
    }

    /* Form Inputs */
    input[type="text"],
    textarea,
    input[type="number"] {
        width: 100%;
        padding: 8px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        font-size: 14px;
        box-sizing: border-box;
    }

    textarea {
        resize: vertical;
    }

    button {
        cursor: pointer;
        border: none;
        border-radius: 6px;
    }

    button.save-btn {
        width: 100%;
        background: #111827;
        color: white;
        padding: 8px 0;
    }

    /* Branches */
    .branch-list>div {
        padding: 12px;
        margin-top: 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        transition: border 0.2s;
        cursor: pointer;
    }

    .branch-list>div:hover {
        border-color: #93c5fd;
    }

    .branch-name {
        font-weight: 500;
    }

    .branch-type {
        font-size: 12px;
        color: #6b7280;
        text-transform: uppercase;
    }

    /* Services */
    .service-card {
        margin-bottom: 24px;
        padding: 16px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
    }

    .service-card form {
        display: flex;
        flex-direction: column;
    }

    .service-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 16px;
    }

    .service-header input[type="text"] {
        font-weight: bold;
        background: transparent;
        border: none;
        padding: 0;
        font-size: 16px;
    }

    .service-header button {
        font-size: 12px;
        color: #2563eb;
        background: none;
        padding: 0;
    }

    .service-grid {
        display: grid;
        grid-template-columns: repeat(3, auto);
        gap: 16px;
        font-size: 12px;
        margin-bottom: 16px;
    }

    .service-grid label {
        display: flex;
        flex-direction: column;
    }

    .service-availability {
        border-top: 1px solid #d1d5db;
        padding-top: 16px;
    }

    .branch-checkbox {
        display: inline-flex;
        align-items: center;
        padding: 4px 12px;
        border-radius: 9999px;
        border: 1px solid #d1d5db;
        font-size: 12px;
        margin: 4px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .branch-checkbox input {
        display: none;
    }

    .branch-checkbox.checked {
        background: #bfdbfe;
        border-color: #93c5fd;
        color: #1d4ed8;
    }

    .branch-checkbox span {
        margin-left: 0;
    }

    .flex-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .branch-delete-btn {
        background-color: #ef4444;
        /* red-500 */
        color: white;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.875rem;
        /* text-sm */
        border: none;
        cursor: pointer;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        transition: background-color 0.2s ease;
    }

    .branch-delete-btn:hover {
        background-color: #dc2626;
        /* red-600 */
    }
</style>
