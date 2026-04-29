{{-- components/connections.blade.php --}}
<div class="dropdown__mini-list"> {{-- Zmenil som id="branchesList" na class --}}
    @if($asset->branch)
        @php
            $c = branchColor($asset->branch->id);
            $branchServices = $asset->services->filter(
                fn($s) => $s->branches->contains('id', $asset->branch->id)
            );
        @endphp

        <div class="service-item" style="background:{{ $c['bg'] }}; border-left:3px solid {{ $c['border'] }}; padding: 8px; margin-bottom: 4px;">
            <span style="color:{{ $c['text'] }}; font-weight: bold;">{{ $asset->branch->name }}</span>
        </div>

        @forelse($branchServices as $s)
            <div class="branch-item" style="padding: 4px 12px; display: flex; align-items: center; gap: 8px;">
                <i class="fa-solid fa-circle" style="color:{{ $c['dot'] }}; font-size: 6px;"></i>
                <span style="font-size: 13px;">{{ $s->name }}</span>
            </div>
        @empty
            <p style="font-size:12px; color:#bbb; padding-left:12px;">No services</p>
        @endforelse
    @else
        <p style="padding: 12px;">No branch assigned</p>
    @endif
</div>