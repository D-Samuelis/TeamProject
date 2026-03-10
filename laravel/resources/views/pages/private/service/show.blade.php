<div class="service-header">
    {{ $service->name }}
</div>

<form action="{{ route('service.update', $service->id) }}" method="POST">
    @csrf @method('PUT')
    <input type="hidden" name="business_id" value="{{ $service->business->id }}">

    <input type="text" name="name" value="{{ $service->name }}">

    <div class="service-grid">
        <div>
            <label>Price</label>
            $<input type="number" name="price" value="{{ $service->price }}">
        </div>
        <div>
            <label>Duration</label>
            <input type="number" name="duration_minutes"
                   value="{{ $service->duration_minutes }}"> min
        </div>
    </div>

    <button>Update Service</button>

    <div class="service-availability">
        <p
            style="font-size:10px; font-weight:bold; color:#9ca3af; text-transform:uppercase; margin-bottom:8px;">
            Available At:</p>
        <div class="flex-wrap">
            @foreach ($service->business->branches as $branch)
                @php
                    $checked = $service->branches->contains($branch->id);
                @endphp

                <div
                    class="flex items-center justify-between mb-2 p-2 border rounded-lg hover:bg-gray-50">
                    <label
                        class="flex items-center space-x-2 cursor-pointer {{ $checked ? 'text-blue-600 font-semibold' : '' }}">
                        <input type="checkbox" name="branch_ids[]"
                               value="{{ $branch->id }}" {{ $checked ? 'checked' : '' }}
                               onchange="this.form.submit()"
                               class="form-checkbox h-4 w-4 text-blue-600">
                        <span>{{ $branch->name }}</span>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</form>

<div>
    <span>
        assets
    </span>

    <div>
        @foreach ($service->assets as $asset)
            <a href="{{ route('asset.show', [$service->business->id, $service->id, $asset]) }}">{{ $asset->name }}</a>
        @endforeach
    </div>
</div>
