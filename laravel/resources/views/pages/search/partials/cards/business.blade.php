<div class="card business-card">
    <div class="card-body">
        <h3 class="card-title">
            <a href="{{ route('business.book', $item->id) }}">{{ $item->name }}</a>
        </h3>
        <p class="card-description">{{ Str::limit($item->description, 100) }}</p>

        <div class="card-footer">
            <span class="badge">{{ $item->services->count() }} Services</span>
            <span class="badge">{{ $item->branches->count() }} Locations</span>
            <a href="{{ route('business.book', $item->id) }}" class="btn-link">View Profile &rarr;</a>
        </div>
    </div>
</div>
