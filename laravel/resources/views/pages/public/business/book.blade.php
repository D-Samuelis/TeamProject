@extends('layouts.app')

@section('title', 'Bexora | Business Details')

@section('content')
@php
    $selectedBranchId = request('branch_id');

    $preparedBranches = $business->branches->map(function ($branch) {
        $branchType = strtolower(is_object($branch->type) ? $branch->type->value : $branch->type);

        $branchTypeLabel = match ($branchType) {
            'hybrid' => 'Hybrid',
            'online' => 'Online',
            'physical' => 'Physical',
            default => ucfirst($branchType),
        };

        $branchAddress = implode(', ', array_filter([
            $branch->address_line_1,
            $branch->address_line_2,
            trim(($branch->postal_code ?? '') . ' ' . ($branch->city ?? '')),
            $branch->country,
        ]));

        $branchMapQuery = trim(
            ($branch->address_line_1 ?? '') . ' ' .
            ($branch->address_line_2 ?? '') . ' ' .
            ($branch->postal_code ?? '') . ' ' .
            ($branch->city ?? '') . ' ' .
            ($branch->country ?? '')
        );

        $hasMapData =
            $branch->city ||
            $branch->country ||
            $branch->address_line_1 ||
            $branch->address_line_2 ||
            $branch->postal_code;

        return [
            'id' => $branch->id,
            'name' => $branch->name,
            'type' => $branchType,
            'type_label' => $branchTypeLabel,
            'address' => $branchAddress,
            'map_query' => $branchMapQuery,
            'has_map_data' => $hasMapData,
            'services_count' => $branch->services->count(),
            'services' => $branch->services->map(function ($service) use ($branch, $branchAddress) {
                $locationType = strtolower($service->location_type);

                $locationLabel = match ($locationType) {
                    'hybrid' => 'Hybrid',
                    'online' => 'Online',
                    default => 'Physical',
                };

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'description' => $service->description,
                    'duration_minutes' => $service->duration_minutes,
                    'price' => $service->price,
                    'location_label' => $locationLabel,
                    'branch_name' => $branch->name,
                    'branch_address' => $branchAddress,
                    'book_url' => route('service.book', $service->id),
                ];
            })->values(),
        ];
    })->values();
@endphp

<div class="public-business-detail">
    <aside class="public-business-detail__sidebar">
        <div class="public-business-detail__sidebar-top">
            <a href="{{ route('search.index') }}" class="public-business-detail__back-link">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Explore</span>
            </a>
        </div>

        <section class="public-business-detail__sidebar-section">
            <h3 class="miniLists__subtitle">
                <i class="fa-solid fa-chevron-down"></i> Branches
            </h3>

            <div id="branchList" class="dropdown__mini-list public-business-detail__branch-nav">
                @foreach($preparedBranches as $branch)
                    @php
                        $isActiveBranch = ((string) $selectedBranchId === (string) $branch['id']) || (!$selectedBranchId && $loop->first);
                    @endphp

                    <button
                        type="button"
                        class="public-business-detail__branch-link {{ $isActiveBranch ? 'is-active' : '' }}"
                        data-branch-id="{{ $branch['id'] }}"
                    >
                        <i class="fa-solid fa-shop"></i>
                        <span>{{ $branch['name'] }}</span>
                    </button>
                @endforeach
            </div>
        </section>
    </aside>

    <main class="public-business-detail__main">
        <header class="public-business-detail__business-header">
            <div class="public-business-detail__business-header-content">
                <h1 class="public-business-detail__business-title">{{ $business->name }}</h1>

                @if($business->description)
                    <p class="public-business-detail__business-description">
                        {{ $business->description }}
                    </p>
                @endif
            </div>
        </header>

        @foreach($preparedBranches as $branch)
            @php
                $isActiveBranch = ((string) $selectedBranchId === (string) $branch['id']) || (!$selectedBranchId && $loop->first);
            @endphp

            <section
                class="public-business-detail__branch-panel {{ $isActiveBranch ? 'is-active' : '' }}"
                data-branch-panel="{{ $branch['id'] }}"
            >
                <header class="public-business-detail__branch-header">
                    <div class="public-business-detail__branch-header-left">
                        <h2 class="public-business-detail__branch-title">{{ $branch['name'] }}</h2>

                        <div class="public-business-detail__branch-meta">
                            @if ($branch['type'])
                                <span class="public-business-detail__branch-badge public-business-detail__branch-badge--{{ $branch['type'] }}">
                                    {{ $branch['type_label'] }}

                                    <span class="public-business-detail__badge-tooltip">
                                        <span>Physical - services at a physical location.</span>
                                        <span>Online - services only online.</span>
                                        <span>Hybrid - both online and in-person services.</span>
                                    </span>
                                </span>
                            @endif

                            @if ($branch['address'])
                                <div class="public-business-detail__branch-location-block">
                                    <p class="public-business-detail__branch-address">
                                        <i class="fa-solid fa-location-dot"></i>
                                        <span>{{ $branch['address'] }}</span>
                                    </p>

                                    @if ($branch['has_map_data'])
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($branch['map_query']) }}"
                                           target="_blank"
                                           class="public-business-detail__directions-link">
                                            <i class="fa-solid fa-map-location-dot"></i>
                                            <span>Get Directions</span>
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="public-business-detail__branch-header-right">
                        <div class="public-business-detail__branch-header-tools">
                            <div class="public-business-detail__branch-search">
                                <div class="list-view__search-wrapper">
                                    <div class="search-container">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <input
                                            type="text"
                                            id="publicBusinessDetailSearch-{{ $branch['id'] }}"
                                            placeholder="Search services..."
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <div class="public-business-detail__body-wrapper">
                    <div
                        id="publicBusinessDetailTable-{{ $branch['id'] }}"
                        class="list-view__body-wrapper public-business-detail__table-container"
                    ></div>
                </div>
            </section>
        @endforeach
    </main>
</div>

<script>
    window.PUBLIC_BUSINESS_DETAIL_DATA = {!! json_encode([
        'selectedBranchId' => $selectedBranchId,
        'branches' => $preparedBranches,
    ]) !!};
</script>

@vite('resources/js/pages/manualBooking/entry.js')
@endsection