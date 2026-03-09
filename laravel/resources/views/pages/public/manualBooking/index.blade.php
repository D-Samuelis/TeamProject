<section class="explore-container">
    <header class="explore-header">
        <h1>Explore Marketplace</h1>

        <nav class="target-tabs">
            @foreach (['business' => 'Shops', 'branch' => 'Locations', 'service' => 'Services'] as $key => $label)
                <a href="{{ route('manualBooking.index', array_merge(request()->query(), ['target' => $key])) }}"
                    class="tab-link {{ $filters->target === $key ? 'is-active' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </header>

    <div class="explore-content">
        <aside class="explore-sidebar">
            {{-- This include will now sit inside the styled sidebar --}}
            @include('pages.public.manualBooking.partials.filter-sidebar')
        </aside>

        <main class="explore-results">
            <div class="results-meta">
                Showing <strong>{{ $results->count() }}</strong>
                {{ strtolower($filters->target) }}{{ $results->count() !== 1 ? 's' : '' }}
            </div>

            @if ($results->isEmpty())
                <div class="no-results">
                    <p>We couldn't find any {{ $filters->target }}s matching your search.</p>
                    <a href="{{ route('manualBooking.index', ['target' => $filters->target]) }}">Clear all filters</a>
                </div>
            @else
                <div class="results-grid">
                    @foreach ($results as $item)
                        @include('pages.public.manualBooking.partials.cards.' . $filters->target, [
                            'item' => $item,
                        ])
                    @endforeach
                </div>
            @endif
        </main>
    </div>
</section>

<style>
    .explore-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
        font-family: sans-serif;
    }

    .explore-header {
        margin-bottom: 30px;
        border-bottom: 2px solid #eee;
    }

    .explore-header h1 {
        margin-bottom: 20px;
        color: #333;
    }

    /* Tabs Styling */
    .target-tabs {
        display: flex;
        gap: 20px;
        margin-bottom: -2px;
        /* Pull tabs down to sit on the border */
    }

    .tab-link {
        text-decoration: none;
        color: #666;
        padding: 10px 5px;
        font-weight: bold;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
    }

    .tab-link:hover {
        color: #007bff;
    }

    .tab-link.is-active {
        color: #007bff;
        border-bottom-color: #007bff;
    }

    /* Layout */
    .explore-content {
        display: flex;
        gap: 30px;
        align-items: flex-start;
    }

    .explore-sidebar {
        flex: 0 0 280px;
        background: #fafafa;
        padding: 20px;
        border-radius: 8px;
        border: 1px solid #eee;
    }

    .explore-results {
        flex: 1;
    }

    .results-meta {
        margin-bottom: 20px;
        color: #555;
        font-size: 0.95rem;
    }

    /* Grid for Cards */
    .results-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }

    /* General Card Styling (Expected for the partials) */
    .item-card {
        background: white;
        border: 1px solid #eee;
        padding: 20px;
        border-radius: 8px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .item-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        background: #fdfdfd;
        border: 2px dashed #eee;
        border-radius: 8px;
        color: #888;
    }

    /* Sidebar form adjustments */
    .filter-sidebar form>*+* {
        margin-top: 15px;
    }

    @media (max-width: 768px) {
        .explore-content {
            flex-direction: column;
        }

        .explore-sidebar {
            flex: none;
            width: 100%;
            box-sizing: border-box;
        }
    }
</style>
