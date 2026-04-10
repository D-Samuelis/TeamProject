@php
    $segments = array_values(array_filter(explode('/', request()->path())));

    // Detekcia book flow: /book/business/{id}/service/{id}/asset/{id}
    $isBookFlow = isset($segments[0]) && $segments[0] === 'book';

    $crumbs = [['label' => 'Domov', 'url' => '/']];

    if ($isBookFlow) {
        $ref = request('ref');
        $branchId = request('branch_id');
        $target   = request('target');

        // Ak prišiel používateľ zo search, pridaj ho ako prvý crumb
        if ($ref === 'search') {
            $crumbs[] = [
                'label' => 'Search',
                'url'   => route('search.index', array_filter(['target' => $target])),
            ];
        }

        // /book/business/{businessId}
        if (isset($segments[2])) {
            $businessId = $segments[2];
            $crumbs[] = [
                'label' => isset($business) ? $business->name : 'Business',
                'url'   => route('book.business', array_filter(['businessId' => $businessId, 'ref' => $ref, 'branch_id' => $branchId, 'target' => $target])),
            ];
        }

        // /book/business/{businessId}/service/{serviceId}
        if (isset($segments[4])) {
            $serviceId = $segments[4];
            $crumbs[] = [
                'label' => isset($service) ? $service->name : 'Service',
                'url'   => route('book.service', array_filter(['businessId' => $segments[2], 'serviceId' => $serviceId, 'ref' => $ref, 'branch_id' => $branchId, 'target' => $target])),
            ];
        }

        // /book/business/{businessId}/service/{serviceId}/asset/{assetId}
        if (isset($segments[6])) {
            $crumbs[] = [
                'label' => isset($asset) ? $asset->name : 'Asset',
                'url'   => null,
            ];
        }
    } else {
        $blacklisted = ['manage', 'customer'];
        $url = '';
        foreach ($segments as $segment) {
            $url .= '/' . $segment;
            if (in_array(strtolower($segment), $blacklisted)) continue;
            $crumbs[] = ['label' => str_replace('-', ' ', ucfirst($segment)), 'url' => $url];
        }
    }
@endphp

<nav aria-label="breadcrumb" class="mb-4">
    <ol class="flex list-reset text-gray-600 text-sm">
        @foreach($crumbs as $crumb)
            @php $isLast = $loop->last; @endphp
            <li class="flex items-center">
                @if (!$loop->first)
                    <span class="mx-2 text-gray-400">/</span>
                @endif

                @if($isLast || !$crumb['url'])
                    <span class="font-bold text-gray-800">{{ $crumb['label'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}" class="text-blue-600 hover:text-blue-800">{{ $crumb['label'] }}</a>
                @endif
            </li>
        @endforeach
    </ol>
</nav>