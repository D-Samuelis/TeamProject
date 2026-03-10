@foreach($services as $service)
    <div>
        <a href="{{ route('service.show', $service->id) }}">{{ $service->name }}</a>
    </div>
@endforeach
