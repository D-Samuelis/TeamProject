<div class="container">
    <a href="{{ route('search.index') }}">&larr; Back to Explore</a>

    <header class="business-header">
        <h1>{{ $business->name }} [{{ ucfirst($business->state->value) }}]</h1>
        <p>{{ $business->description }}</p>
    </header>

    <div class="content-layout">
        {{-- Branches Column --}}
        <section class="branches">
            <h2>Locations</h2>
            @foreach ($business->branches as $branch)
                <div class="item-card">
                    <strong>{{ $branch->name }}</strong>
                    <p>Type: {{ is_object($branch->type) ? $branch->type->value : $branch->type }}</p>
                    <p>Available: {{ $branch->is_active ? 'Yes' : 'No' }}</p>
                    <address>
                        @if ($branch->address_line_1)
                            {{ $branch->address_line_1 }}<br>
                        @endif
                        @if ($branch->address_line_2)
                            {{ $branch->address_line_2 }}<br>
                        @endif
                        {{ $branch->country }}, {{ $branch->city }}, {{ $branch->postal_code }}
                    </address>
                </div>
            @endforeach
        </section>

        {{-- Services Column --}}
        <section class="services">
            <h2>Services</h2>
            <table class="data-table">
                <thead>
                <tr>
                    <th>Service</th>
                    <th>Duration</th>
                    <th>Price</th>
                    <th>Location</th>
                    <th>Available</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($business->services as $service)
                    <tr>
                        <td>
                            <strong>{{ $service->name }}</strong><br>
                            <small>{{ $service->description }}</small>
                        </td>
                        <td>{{ $service->duration_minutes }} min</td>
                        <td>{{ number_format($service->price, 2) }} €</td>
                        <td>{{ is_object($service->location_type) ? ucfirst($service->location_type->value) : ucfirst($service->location_type) }}
                        </td>
                        <td>{{ $service->is_active ? 'Yes' : 'No' }}</td>
                        <td><button><a href="{{ route('service.book', $service->id) }}">Book</a></button></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </section>
    </div>
</div>

<style>
    .container {
        max-width: 1100px;
        margin: 0 auto;
        padding: 20px;
        font-family: sans-serif;
    }

    .business-header {
        margin: 20px 0;
        border-bottom: 2px solid #eee;
        padding-bottom: 20px;
    }

    .content-layout {
        display: flex;
        gap: 40px;
    }

    .branches {
        flex: 1;
    }

    .services {
        flex: 2;
    }

    .item-card {
        background: #fafafa;
        border: 1px solid #eee;
        padding: 15px;
        margin-bottom: 10px;
        border-radius: 4px;
        font-size: 0.9rem;
    }

    .item-card p {
        margin: 5px 0;
        color: #666;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
    }

    .data-table th,
    .data-table td {
        text-align: left;
        padding: 12px;
        border-bottom: 1px solid #eee;
        font-size: 0.9rem;
    }

    .data-table th {
        background: #f8f8f8;
        font-weight: bold;
    }

    .data-table small {
        color: #777;
    }

    button {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
    }

    button:hover {
        background: #0056b3;
    }
</style>
