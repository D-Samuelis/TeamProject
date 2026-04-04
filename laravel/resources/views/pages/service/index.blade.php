{{-- resources/views/pages/private/service/index.blade.php --}}

@if (session('success'))
    <p style="color:green;">{{ session('success') }}</p>
@endif

<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
    <h1>Services</h1>
    <button onclick="openModal('createServiceModal')">+ New Service</button>
</div>

@forelse($services as $service)
    <div style="border:1px solid #ddd;border-radius:6px;padding:1rem;margin-bottom:0.75rem;display:flex;justify-content:space-between;align-items:center;">
        <div>
            <a href="{{ route('manage.service.show', $service->id) }}"
               style="font-weight:500;{{ $service->trashed() ? 'text-decoration:line-through;color:#991b1b;' : '' }}">
                {{ $service->name }}
            </a>
            <span style="font-size:12px;color:#888;margin-left:8px;">{{ $service->base_duration_minutes }}min</span>
            <span style="font-size:12px;color:#aaa;margin-left:4px;">· €{{ number_format($service->base_price, 2) }}</span>
        </div>
        <div style="display:flex;gap:6px;align-items:center;">
            <span style="font-size:12px;padding:2px 8px;border-radius:20px;
                         background:{{ $service->is_active ? '#d1fae5' : '#fee2e2' }};
                         color:{{ $service->is_active ? '#065f46' : '#991b1b' }};">
                {{ $service->is_active ? 'Active' : 'Inactive' }}
            </span>
            @if ($service->trashed())
                @can('restore', $service)
                    <form method="POST" action="{{ route('manage.service.restore', $service->id) }}">
                        @csrf @method('PATCH')
                        <button type="submit" style="color:#2563eb;background:none;border:none;cursor:pointer;font-size:13px;">Restore</button>
                    </form>
                @endcan
            @else
                @can('delete', $service)
                    <form method="POST" action="{{ route('manage.service.delete', $service->id) }}"
                          onsubmit="return confirm('Delete this service?')">
                        @csrf @method('DELETE')
                        <button type="submit" style="color:red;background:none;border:none;cursor:pointer;font-size:13px;">Delete</button>
                    </form>
                @endcan
            @endif
        </div>
    </div>
@empty
    <p style="color:#888;">No services yet.</p>
@endforelse

<div id="createServiceModal" class="modal-backdrop" style="display:none;">
    <div class="modal-box">
        <button class="modal-close" onclick="closeModal('createServiceModal')">&times;</button>
        <h2 style="margin-bottom:1.5rem;">New Service</h2>
        <form method="POST" action="{{ route('manage.service.store') }}">
            @csrf
            @include('pages.service.partials.service-form', [
                'prefix'              => 'create',
                'service'             => null,
                'businesses'          => $businesses,
                'branches'            => $branches,
                'showBranchAssignment' => true,
            ])
            <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:1.5rem;">
                <button type="button" onclick="closeModal('createServiceModal')" class="btn-secondary">Cancel</button>
                <button type="submit" class="btn-primary">Create Service</button>
            </div>
        </form>
    </div>
</div>

@include('pages.service.partials.modal-styles-scripts')

@if ($errors->any())
    <script>openModal('createServiceModal');</script>
@endif
