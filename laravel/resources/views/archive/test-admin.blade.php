<!DOCTYPE html>
<html>
<head>
    <title>Test Admin Panel</title>
</head>
<body>

<h1>Test Admin Panel</h1>

{{-- ================= BUSINESS ================= --}}
<form method="POST" action="{{ route('test.business.store') }}">
    @csrf
    <h2>Create Business</h2>
    <input type="text" name="name" placeholder="Business Name" required>
    <input type="text" name="description" placeholder="Description">
    <button type="submit">Create Business</button>
</form>

{{-- ================= BRANCH ================= --}}
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
    <input type="text" name="address" placeholder="Address">

    <button type="submit">Create Branch</button>
</form>

{{-- ================= SERVICE ================= --}}
<form method="POST" action="{{ route('test.service.store') }}">
    @csrf
    <h2>Create Service</h2>

    <select name="business_id" required>
        <option value="">Select Business</option>
        @foreach($businesses as $business)
            <option value="{{ $business->id }}">{{ $business->name }}</option>
        @endforeach
    </select>

    <select name="branch_id" required>
        <option value="">Select Branch</option>
        @foreach($businesses as $business)
            @foreach($business->branches as $branch)
                <option value="{{ $branch->id }}">
                    {{ $business->name }} - {{ $branch->name }}
                </option>
            @endforeach
        @endforeach
    </select>

    <input type="text" name="name" placeholder="Service Name" required>
    <input type="text" name="description" placeholder="Description">

    <label>
        <input type="checkbox" name="is_online" value="1">
        Is Online
    </label>

    <button type="submit">Create Service</button>
</form>

{{-- ================= DISPLAY DATA ================= --}}
<h2>Database Overview</h2>

@foreach($businesses as $business)
    <div class="box">
        <strong>Business:</strong> {{ $business->name }}

        <p><strong>Branches:</strong></p>
        <ul>
            @foreach($business->branches as $branch)
                <li>{{ $branch->name }}</li>
            @endforeach
        </ul>

        <p><strong>Services:</strong></p>
        <ul>
            @foreach($business->services as $service)
                <li>
                    {{ $service->name }}
                    (Branch: {{ $service->branch->name ?? 'N/A' }})
                    @if($service->is_online)
                        — Online
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endforeach

</body>
</html>