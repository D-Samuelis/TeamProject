@foreach($branches as $branch)
    <div>
        <a href="{{ route('branch.show', $branch->id) }}">{{ $branch->name }}</a>
    </div>
@endforeach
