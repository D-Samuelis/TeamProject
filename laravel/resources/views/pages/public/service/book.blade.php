<div>{{ $service->name }}</div>

@foreach($service->assets as $asset)
    <div>
        <span>{{ $asset->name }}</span>
        <button><a href="{{ route('asset.book', [$service->id, $asset->id]) }}">Book</a></button>
    </div>
@endforeach
