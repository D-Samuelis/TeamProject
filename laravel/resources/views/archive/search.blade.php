<div id="search-app" style="display: flex; gap: 20px; font-family: sans-serif; padding: 20px;">

    {{-- Sidebar Filters --}}
    <form action="{{ url()->current() }}" method="GET" style="width: 280px; border-right: 1px solid #eee; padding-right: 20px;">
        {{-- Remembers the active tab when filters are applied --}}
        <input type="hidden" name="target" id="active-target" value="{{ $dto->target }}">

        <h3>Filters</h3>
        
        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; font-size: 0.9em;">Keyword</label>
            <input type="text" name="q" value="{{ $dto->query }}" placeholder="Search name or info..." style="width:100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 15px;">
            <label style="font-weight: bold; font-size: 0.9em;">City</label>
            <input type="text" name="city" value="{{ $dto->city }}" placeholder="e.g. Trnava" style="width:100%; padding: 8px; box-sizing: border-box;">
        </div>

        <div style="margin-bottom: 20px;">
            <label style="font-weight: bold; font-size: 0.9em;">Location Types</label>
            <div style="margin-top: 5px;">
                @foreach (['branch' => 'At Shop', 'online' => 'Online', 'client_address' => 'At Home'] as $val => $label)
                    <label style="display: block; cursor: pointer; margin-bottom: 5px; font-size: 0.9em;">
                        <input type="checkbox" name="location_types[]" value="{{ $val }}"
                            {{ in_array($val, $dto->locationTypes) ? 'checked' : '' }}> {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        <button type="submit" style="width:100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">
            Apply Filters
        </button>
        
        <a href="{{ url()->current() }}" style="display: block; text-align: center; margin-top: 15px; color: #888; font-size: 0.85em; text-decoration: none;">✕ Reset All Filters</a>
    </form>

    {{-- Main Content Area --}}
    <div style="flex: 1;">
        
        {{-- Tab Navigation --}}
        <div style="margin-bottom: 20px; display: flex; gap: 10px;">
            @foreach(['business' => 'Shops', 'branch' => 'Locations', 'service' => 'Services'] as $key => $label)
                @php 
                    $tabData = ${Str::plural($key)};
                    /** * If it's the active tab, it's a Paginator (use total()).
                     * If not, it's a raw integer from the count() method.
                     */
                    $count = is_numeric($tabData) ? $tabData : $tabData->total();
                    
                    // URL preserves search filters but resets page to 1 for the new tab
                    $tabUrl = request()->fullUrlWithQuery(['target' => $key, 'page' => 1]);
                @endphp
                
                <a href="{{ $tabUrl }}" 
                   class="tab-btn {{ $dto->target === $key ? 'active' : '' }}" 
                   id="btn-{{ $key }}"
                   style="text-decoration: none; color: inherit;">
                    {{ $label }} <span style="opacity: 0.6; font-size: 0.9em;">({{ $count }})</span>
                </a>
            @endforeach
        </div>

        {{-- Businesses List --}}
        <div class="tab-content" id="content-business" style="{{ $dto->target === 'business' ? '' : 'display: none;' }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                @forelse($businesses instanceof \Illuminate\Contracts\Pagination\Paginator ? $businesses : [] as $shop)
                    <div class="result-card">
                        <strong style="font-size: 1.1em; display: block; margin-bottom: 5px;">{{ $shop->name }}</strong>
                        <p style="font-size: 0.85em; color: #666; margin: 0;">{{ Str::limit($shop->description, 90) }}</p>
                    </div>
                @empty
                    @if($dto->target === 'business')
                        <p style="color: #999;">No shops found matching your search.</p>
                    @endif
                @endforelse
            </div>
        </div>

        {{-- Branches List --}}
        <div class="tab-content" id="content-branch" style="{{ $dto->target === 'branch' ? '' : 'display: none;' }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                @forelse($branches instanceof \Illuminate\Contracts\Pagination\Paginator ? $branches : [] as $branch)
                    <div class="result-card">
                        <strong style="display: block;">{{ $branch->name }}</strong>
                        <span style="font-size: 0.85em; color: #007bff;">{{ $branch->city }}</span>
                        <p style="font-size: 0.85em; color: #888; margin-top: 5px;">{{ $branch->address }}</p>
                    </div>
                @empty
                    @if($dto->target === 'branch')
                        <p style="color: #999;">No locations found matching your search.</p>
                    @endif
                @endforelse
            </div>
        </div>

        {{-- Services List --}}
        <div class="tab-content" id="content-service" style="{{ $dto->target === 'service' ? '' : 'display: none;' }}">
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                @forelse($services instanceof \Illuminate\Contracts\Pagination\Paginator ? $services : [] as $service)
                    <div class="result-card">
                        <strong style="display: block;">{{ $service->name }}</strong>
                        <div style="margin: 8px 0;">
                            <span style="background: #e7f3ff; color: #007bff; padding: 2px 8px; border-radius: 4px; font-weight: bold;">
                                {{ number_format($service->price, 2) }} €
                            </span>
                        </div>
                        <small style="color: #999;">{{ $service->duration_minutes }} minutes</small>
                    </div>
                @empty
                    @if($dto->target === 'service')
                        <p style="color: #999;">No services found matching your search.</p>
                    @endif
                @endforelse
            </div>
        </div>

        {{-- Pagination Links --}}
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            {{ $results->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<style>
    .tab-btn { 
        padding: 10px 20px; 
        border: 1px solid #ccc; 
        background: #fff; 
        border-radius: 4px; 
        transition: all 0.2s ease; 
        font-size: 0.95em;
    }
    .tab-btn.active { 
        background: #333; 
        color: #fff !important; 
        border-color: #333; 
        font-weight: bold; 
    }
    .tab-btn:hover:not(.active) { 
        background: #f8f9fa; 
        border-color: #bbb;
    }
    .result-card {
        border: 1px solid #e0e0e0; 
        padding: 15px; 
        border-radius: 8px; 
        background: #fff;
        transition: transform 0.1s;
    }
    .result-card:hover {
        border-color: #007bff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    nav[role="navigation"] {
        display: flex;
        justify-content: center;
    }
</style>

<script>
    /**
     * Minimal JS to ensure the filter form knows which tab is active
     * for future filter submissions.
     */
    document.addEventListener('DOMContentLoaded', () => {
        const activeTarget = '{{ $dto->target }}';
        document.getElementById('active-target').value = activeTarget;
    });
</script>