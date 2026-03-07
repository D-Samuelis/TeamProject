<!DOCTYPE html>
<html>

<head>
    <title>Test Admin Panel</title>
</head>

<body>

    <h1>Test Admin Panel</h1>

    <!-- ================= BUSINESS ================= -->
    <form method="POST" action="{{ route('test.business.store') }}">
        @csrf
        <h2>Create Business</h2>
        <input type="text" name="name" placeholder="Business Name" required>
        <input type="text" name="description" placeholder="Description">
        <button type="submit">Create Business</button>
    </form>

    <!-- ================= BRANCH ================= -->
    <form method="POST" action="{{ route('test.branch.store') }}">
        @csrf
        <h2>Create Branch</h2>

        <select name="business_id" required>
            <option value="">Select Business</option>
            @foreach($businesses as $business)
            <option value="{{ $business->id }}">{{ $business->name }}</option>
            @endforeach
        </select>

        <input type="text" name="name" placeholder="Branch Name" required>

        <select name="type" required>
            <option value="physical">Physical</option>
            <option value="online">Online</option>
            <option value="hybrid">Hybrid</option>
        </select>

        <input type="text" name="address_line_1" placeholder="Address Line 1">
        <input type="text" name="address_line_2" placeholder="Address Line 2">
        <input type="text" name="city" placeholder="City">
        <input type="text" name="postal_code" placeholder="Postal Code">
        <input type="text" name="country" placeholder="Country">

        <button type="submit">Create Branch</button>
    </form>

    <!-- ================= SERVICE ================= -->
    <form method="POST" action="{{ route('test.service.store') }}">
        @csrf
        <h2>Create Service</h2>

        <select name="business_id" required>
            <option value="">Select Business</option>
            @foreach($businesses as $business)
            <option value="{{ $business->id }}">{{ $business->name }}</option>
            @endforeach
        </select>

        <select name="branch_ids[]" multiple required>
            @foreach($businesses as $business)
            @foreach($business->branches as $branch)
            <option value="{{ $branch->id }}">
                {{ $business->name }} - {{ $branch->name }} ({{ $branch->type }})
            </option>
            @endforeach
            @endforeach
        </select>

        <input type="text" name="name" placeholder="Service Name" required>
        <input type="text" name="description" placeholder="Description">
        <input type="number" name="duration_minutes" placeholder="Duration (minutes)">
        <input type="number" step="0.01" name="price" placeholder="Price">

        <button type="submit">Create Service</button>
    </form>

    <form method="POST" action="{{ route('test.asset.store') }}">
        @csrf
        <h2>Create Asset</h2>

        <label>Select Branches:</label>
        <select name="branch_ids[]" multiple required>
            @foreach($businesses as $business)
                <optgroup label="{{ $business->name }}">
                    @foreach($business->branches as $branch)
                        <option value="{{ $branch->id }}">
                            {{ $branch->name }} ({{ $branch->type }})
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>

        <label>Select Services:</label>
        <select name="service_ids[]" multiple required>
            @foreach($businesses as $business)
                <optgroup label="{{ $business->name }}">
                    @foreach($business->services as $service)
                        <option value="{{ $service->id }}">
                            {{ $service->name }}
                        </option>
                    @endforeach
                </optgroup>
            @endforeach
        </select>

        <input type="text" name="name" placeholder="Asset Name" required>
        <textarea name="description" placeholder="Description"></textarea>

        <button type="submit">Create Asset</button>
    </form>

    <!-- ================= DISPLAY DATA ================= -->
    <h2>Database Overview</h2>

    <div style="display:flex; gap:40px; align-items:flex-start;">

        <!-- ================= NORMAL RECORDS ================= -->
        <div style="flex:1;">
            <h3>Active Businesses</h3>

            @foreach($businesses as $business)
            <div style="border:1px solid #ccc; padding:10px; margin-bottom:20px;">

                <!-- Business info + edit/delete -->
                <strong>Business:</strong> {{ $business->name }}<br>
                <strong>Description:</strong> {{ $business->description ?? '—' }}<br><br>

                <!-- Business CRUD -->
                <form method="POST" action="{{ route('test.business.update', $business->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="text" name="name" value="{{ $business->name }}">
                    <input type="text" name="description" value="{{ $business->description }}">
                    <label>
                        Published: <input type="checkbox" name="is_published" value="1"
                            @if($business->is_published) checked @endif>
                    </label>
                    <button type="submit">Update Business</button>
                </form>
                <form method="POST" action="{{ route('test.business.delete', $business->id) }}"
                    onsubmit="return confirm('Delete this business?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" style="color:white;background:red;padding:6px 10px;border:none;">
                        Delete
                    </button>
                </form>

                <!-- Branches CRUD -->
                <p><strong>Branches:</strong></p>
                <ul>
                    @foreach($business->branches as $branch)
                    <li>
                        {{ $branch->name }} ({{ $branch->type }})
                        <form method="POST" action="{{ route('test.branch.update', $branch->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $branch->name }}">
                            <select name="type">
                                <option value="physical" @if($branch->type=='physical') selected @endif>Physical</option>
                                <option value="online" @if($branch->type=='online') selected @endif>Online</option>
                                <option value="hybrid" @if($branch->type=='hybrid') selected @endif>Hybrid</option>
                            </select>
                            <label>
                                Active: <input type="checkbox" name="is_active" value="1"
                                    @if($branch->is_active) checked @endif>
                            </label>
                            <button type="submit">Update Branch</button>
                        </form>
                        <form method="POST" action="{{ route('test.branch.delete', $branch->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete branch?')">Delete Branch</button>
                        </form>
                    </li>
                    @endforeach
                </ul>

                <!-- Services CRUD -->
                <p><strong>Services:</strong></p>
                <ul>
                    @foreach($business->services as $service)
                    <li>
                        <strong>{{ $service->name }}</strong>
                        — {{ $service->duration_minutes }} min
                        — ${{ $service->price }}

                        <br>
                        Available at:
                        @foreach($service->branches as $branch)
                        <span>{{ $branch->name }} ({{ $branch->type }})</span>@if(!$loop->last), @endif
                        @endforeach

                        <form method="POST" action="{{ route('test.service.update', $service->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="text" name="name" value="{{ $service->name }}">
                            <input type="number" name="duration_minutes" value="{{ $service->duration_minutes }}">
                            <input type="number" step="0.01" name="price" value="{{ $service->price }}">
                            <label>
                                Active: <input type="checkbox" name="is_active" value="1"
                                    @if($service->is_active) checked @endif>
                            </label>
                            <select name="branch_ids[]" multiple>
                                @foreach($business->branches as $branch)
                                <option value="{{ $branch->id }}"
                                    @if($service->branches->contains($branch->id)) selected @endif>
                                    {{ $branch->name }}
                                </option>
                                @endforeach
                            </select>
                            <button type="submit">Update Service / Reassign Branches</button>
                        </form>
                        <form method="POST" action="{{ route('test.service.delete', $service->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete service?')">Delete Service</button>
                        </form>

                        <ul>
                            <h3>Assets</h3>
                            @foreach($service->assets as $asset)
                                <li>
                                    <strong>{{ $asset->name }}</strong>
                                    — {{ $asset->description }}

                                    <br>
                                    Available at:
                                    @foreach($asset->services as $asset_service)
                                        <span>{{ $asset_service->name }}</span>@if(!$loop->last), @endif
                                    @endforeach

                                    <form method="POST" action="{{ route('test.asset.update', $asset->id) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="text" name="name" value="{{ $asset->name }}">
                                        <input type="text" name="description" value="{{ $asset->description }}">
                                        <select name="branch_ids[]" multiple>
                                            @foreach($business->branches as $branch)
                                                <option value="{{ $branch->id }}"
                                                        @if($asset->branches->contains($branch->id)) selected @endif>
                                                    {{ $branch->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <select name="service_ids[]" multiple>
                                            @foreach($business->services as $service)
                                                <option value="{{ $service->id }}"
                                                        @if($asset->services->contains($service->id)) selected @endif>
                                                    {{ $service->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <button type="submit">Update Asset</button>
                                    </form>
                                    <form method="POST" action="{{ route('test.asset.delete', $asset->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete service?')">Delete Asset</button>
                                    </form>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                    @endforeach
                </ul>

            </div>
            @endforeach
        </div>

        <!-- ================= SOFT DELETED ================= -->
        <div style="flex:1;">
            <h3>Deleted Businesses</h3>

            @foreach($deletedBusinesses as $business)
            <div style="border:1px solid #f99; padding:10px; margin-bottom:20px; background:#fff5f5;">
                <strong>Business:</strong> {{ $business->name }}<br>
                <strong>Description:</strong> {{ $business->description ?? '—' }}<br><br>

                <form method="POST" action="{{ route('test.business.restore', $business->id) }}">
                    @csrf
                    <button type="submit" style="color:white;background:green;padding:6px 10px;border:none;">
                        Restore
                    </button>
                </form>

                <p><strong>Branches:</strong></p>
                <ul>
                    @foreach($business->branches as $branch)
                    <li>{{ $branch->name }} ({{ $branch->type }})</li>
                    @endforeach
                </ul>

                <p><strong>Services:</strong></p>
                <ul>
                    @foreach($business->services as $service)
                    <li>
                        <strong>{{ $service->name }}</strong>
                        — {{ $service->duration_minutes }} min
                        — ${{ $service->price }}

                        <br>
                        Available at:
                        @foreach($service->branches as $branch)
                        <span>{{ $branch->name }} ({{ $branch->type }})</span>@if(!$loop->last), @endif
                        @endforeach
                    </li>
                    @endforeach
                </ul>
            </div>
            @endforeach
        </div>

    </div>
</body>

</html>
