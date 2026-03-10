@foreach($assets as $asset)
    <div>
        <a href="{{ route('asset.show', $asset->id) }}">{{ $asset->name }}</a>
    </div>
@endforeach

