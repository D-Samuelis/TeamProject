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
                    <button style="color:#2563eb; font-size:14px; padding: 8px 20px;">+ Add</button>
                </div>
                <div class="branch-list">
                    @foreach ($business->branches as $branch)
                        <div>
                            <p class="branch-name">{{ $branch->name }}</p>
                            <span class="branch-type">{{ $branch->type }}</span>

                            <form
                                action="{{ route('branch.delete', ['businessId' => $business->id, 'branchId' => $branch->id]) }}"
                                method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this branch?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="branch-delete-btn">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </section>
        </div>

        <!-- Services -->
        <div style="display:flex; flex-direction:column; gap:24px;">
            <section class="card">
                <h3>Services & Branch Availability</h3>

                @foreach ($business->services as $service)
                    <div class="service-card">
                        <form action="{{ route('service.update', [$business->id, $service->id]) }}" method="POST">
                            @csrf @method('PUT')
                            <div class="service-header">
                                <input type="text" name="name" value="{{ $service->name }}">
                                <button>Update Service</button>
                            </div>

                            <div class="service-grid">
                                <div>
                                    <label>Price</label>
                                    $<input type="number" name="price" value="{{ $service->price }}">
                                </div>
                                <div>
                                    <label>Duration</label>
                                    <input type="number" name="duration_minutes"
                                        value="{{ $service->duration_minutes }}"> min
                                </div>
                            </div>

                            <div class="service-availability">
                                <p
                                    style="font-size:10px; font-weight:bold; color:#9ca3af; text-transform:uppercase; margin-bottom:8px;">
                                    Available At:</p>
                                <div class="flex-wrap">
                                    @foreach ($business->branches as $branch)
                                        @php
                                            $checked = $service->branches->contains($branch->id);
                                        @endphp

                                        <div
                                            class="flex items-center justify-between mb-2 p-2 border rounded-lg hover:bg-gray-50">
                                            <label
                                                class="flex items-center space-x-2 cursor-pointer {{ $checked ? 'text-blue-600 font-semibold' : '' }}">
                                                <input type="checkbox" name="branch_ids[]" value="{{ $branch->id }}"
                                                    {{ $checked ? 'checked' : '' }} onchange="this.form.submit()"
                                                    class="form-checkbox h-4 w-4 text-blue-600">
                                                <span>{{ $branch->name }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach

            </section>
        </div>

    </div>
</div>

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
