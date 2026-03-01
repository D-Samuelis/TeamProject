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

    <!-- MULTIPLE BRANCHES -->
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

<!-- ================= DISPLAY DATA ================= -->
<h2>Database Overview</h2>

@foreach($businesses as $business)
    <div style="border:1px solid #ccc; padding:10px; margin-bottom:20px;">
        <strong>Business:</strong> {{ $business->name }}

        <p><strong>Branches:</strong></p>
        <ul>
            @foreach($business->branches as $branch)
                <li>
                    {{ $branch->name }} ({{ $branch->type }})
                </li>
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
                        <span>
                            {{ $branch->name }} ({{ $branch->type }})
                        </span>@if(!$loop->last), @endif
                    @endforeach
                </li>
            @endforeach
        </ul>
    </div>
@endforeach

</body>
</html>