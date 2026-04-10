@php
    $segments = array_values(array_filter(explode('/', request()->path())));
    
    if (empty($segments)) {
        return;
    }

    $isBookFlow = isset($segments[0]) && $segments[0] === 'book';
    $crumbs = [['label' => 'Domov', 'url' => '/']];

    if ($isBookFlow) {
        $ref = request('ref');
        $branchId = request('branch_id');
        $target   = request('target');

        if ($ref === 'search') {
            $crumbs[] = [
                'label' => 'Search',
                'url'   => route('search.index', array_filter(['target' => $target])),
            ];
        }

        if (isset($segments[2])) {
            $businessId = $segments[2];
            $crumbs[] = [
                'label' => isset($business) ? $business->name : 'Business',
                'url'   => route('book.business', array_filter(['businessId' => $businessId, 'ref' => $ref, 'branch_id' => $branchId, 'target' => $target])),
            ];
        }

        if (isset($segments[4])) {
            $serviceId = $segments[4];
            $crumbs[] = [
                'label' => isset($service) ? $service->name : 'Service',
                'url'   => route('book.service', array_filter(['businessId' => $segments[2], 'serviceId' => $serviceId, 'ref' => $ref, 'branch_id' => $branchId, 'target' => $target])),
            ];
        }

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

            // Základný label zo segmentu URL
            $label = str_replace('-', ' ', ucfirst($segment));

            // Ak je to ID a máme preň definovanú sekciu (Meno z DB)
            if (is_numeric($segment) && View::hasSection("breadcrumb-{$segment}")) {
                $rawLabel = View::getSection("breadcrumb-{$segment}");
                
                // 1. Dekódujeme HTML entity (opraví &amp; -> &)
                // 2. Odstránime prípadné HTML tagy
                // 3. Orežeme dĺžku, aby to nerozbilo šípky
                $label = Str::limit(strip_tags(html_entity_decode($rawLabel)), 30);
            }

            $crumbs[] = ['label' => $label, 'url' => $url];
        }
    }
@endphp

<nav aria-label="breadcrumb" class="breadcrumbs-container">
    <ul class="breadcrumbs">
        @foreach($crumbs as $crumb)
            @php $isLast = $loop->last; @endphp
            <li class="breadcrumbs__item {{ $isLast ? 'is-active' : '' }}">
                @if($isLast || !$crumb['url'])
                    <span class="breadcrumbs__link">{{ $crumb['label'] }}</span>
                @else
                    <a href="{{ $crumb['url'] }}" class="breadcrumbs__link">{{ $crumb['label'] }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</nav>