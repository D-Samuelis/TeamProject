@extends('web.layouts.app')

@section('title', 'Bexora | Select Asset')

@section('content')
<div class="public-service-book">
    <aside class="public-service-book__sidebar">
        <div class="public-service-book__sidebar-top">
            <a
                href="{{ route('business.book', ['businessId' => $service->business_id, 'branch_id' => request('branch_id')]) }}"
                class="public-service-book__back-link"
            >
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Business</span>
            </a>
        </div>

        <section class="public-service-book__sidebar-section">
            <div class="public-service-book__steps">
                <div class="public-service-book__step public-service-book__step--active">
                    <span class="public-service-book__step-number">1</span>
                    <div class="public-service-book__step-text">
                        <strong>Choose Asset</strong>
                        <span>Select who or what you want to book</span>
                    </div>
                </div>

                <div class="public-service-book__step-dots">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>

                <div class="public-service-book__step">
                    <span class="public-service-book__step-number">2</span>
                    <div class="public-service-book__step-text">
                        <strong>Choose Time</strong>
                        <span>Pick an available date and time</span>
                    </div>
                </div>
            </div>
        </section>
    </aside>

    <main class="public-service-book__main">
        <header class="public-service-book__service-header">
            <div class="public-service-book__service-header-content">
                <p class="public-service-book__step-label-top">Step 1 of 2</p>
                <h1 class="public-service-book__service-title">{{ $service->name }}</h1>

                @if($service->description)
                    <p class="public-service-book__service-description">
                        {{ $service->description }}
                    </p>
                @endif

                <div class="public-service-book__service-meta">
                    @if($service->duration_minutes)
                        <span class="public-service-book__meta-badge">
                            <i class="fa-regular fa-clock"></i>
                            {{ $service->duration_minutes }} min
                        </span>
                    @endif

                    @if(!is_null($service->price))
                        <span class="public-service-book__meta-badge">
                            <i class="fa-solid fa-tag"></i>
                            €{{ number_format((float) $service->price, 2) }}
                        </span>
                    @endif
                </div>
            </div>
        </header>

        <section class="public-service-book__asset-panel">
            <header class="public-service-book__asset-panel-header">
                <div>
                    <h2 class="public-service-book__asset-panel-title">Available Assets</h2>
                    <p class="public-service-book__asset-panel-subtitle">
                        Choose one asset to continue to the next step.
                    </p>
                </div>
            </header>

            @if($service->assets->isNotEmpty())
                <div class="public-service-book__asset-grid">
                    @foreach($service->assets as $asset)
                        <article class="public-service-book__asset-card">
                            <div class="public-service-book__asset-card-body">
                                <div class="public-service-book__asset-card-top">
                                    <div class="public-service-book__asset-icon">
                                        <i class="fa-solid fa-cube"></i>
                                    </div>

                                    <div class="public-service-book__asset-text">
                                        <h3 class="public-service-book__asset-title">
                                            {{ $asset->name }}
                                        </h3>

                                        @if($asset->description)
                                            <p class="public-service-book__asset-description">
                                                {{ $asset->description }}
                                            </p>
                                        @else
                                            <p class="public-service-book__asset-description public-service-book__asset-description--muted">
                                                This asset is available for this service.
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="public-service-book__asset-card-footer">
                                    <a
                                        href="{{ route('asset.book', ['serviceId' => $service->id, 'assetId' => $asset->id, 'branch_id' => request('branch_id')]) }}"
                                        class="public-service-book__select-btn"
                                    >
                                        <span>Select Asset</span>
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="public-service-book__empty-box">
                    <i class="fa-regular fa-face-frown"></i>
                    <h3>No assets available</h3>
                    <p>There are currently no assets assigned to this service.</p>
                </div>
            @endif
        </section>
    </main>
</div>

<style>
    .public-service-book {
        display: grid;
        grid-template-columns: 16.75rem 1fr;
        width: 100%;
        min-height: calc(100vh - 100px - 24px);
        border-top: 1px solid var(--color-border-light);
        border-bottom: 1px solid var(--color-border-light);
        overflow: hidden;
        background-color: var(--color-bg);
    }

    .public-service-book__sidebar {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        height: 100%;
        padding: 1rem;
        overflow-y: auto;
        border-right: 1px solid var(--color-border-light);
        background-color: var(--color-bg);
    }

    .public-service-book__sidebar-top {
        display: flex;
        align-items: center;
    }

    .public-service-book__back-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-text);
        text-decoration: none;
        font-size: 0.95rem;
        transition: color 0.2s ease;
    }

    .public-service-book__back-link:hover {
        color: var(--color-primary);
    }

    .public-service-book__sidebar-section {
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
    }

    .public-service-book__steps {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
    }

    .public-service-book__step {
        display: flex;
        gap: 0.75rem;
        align-items: flex-start;
        padding: 0.85rem;
        border: 1px solid var(--color-border-light);
        border-radius: 0.75rem;
        background-color: var(--color-bg);
        opacity: 0.75;
    }

    .public-service-book__step--active {
        opacity: 1;
        border-color: var(--color-primary);
        box-shadow: 0 4px 14px var(--color-box-shadow);
    }

    .public-service-book__step-number {
        width: 2rem;
        height: 2rem;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 0.95rem;
        font-weight: 700;
        border: 1px solid var(--color-border-light);
        background-color: var(--color-bg-complement);
        color: var(--color-text);
    }

    .public-service-book__step--active .public-service-book__step-number {
        background-color: var(--color-primary);
        border-color: var(--color-primary);
        color: var(--color-text-white);
    }

    .public-service-book__step-text {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        min-width: 0;
    }

    .public-service-book__step-text strong {
        font-size: 0.95rem;
        color: var(--color-text);
    }

    .public-service-book__step-text span {
        font-size: 0.85rem;
        color: var(--color-text-unimportant-dark);
        line-height: 1.45;
    }

    .public-service-book__step-dots {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.35rem;
        padding: 0.15rem 0;
    }

    .public-service-book__step-dots span {
        width: 0.32rem;
        height: 0.32rem;
        border-radius: 999px;
        background-color: var(--color-text-unimportant-light);
    }

    .public-service-book__main {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        min-width: 0;
        padding: 1rem;
        background-color: var(--color-bg);
    }

    .public-service-book__service-header {
        border: 1px solid var(--color-border-light);
        border-radius: 0.85rem;
        background-color: var(--color-bg-complement);
    }

    .public-service-book__service-header-content {
        padding: 1.25rem;
    }

    .public-service-book__step-label-top {
        margin: 0 0 0.45rem;
        font-size: 0.82rem;
        font-weight: 700;
        color: var(--color-primary);
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .public-service-book__service-title {
        margin: 0;
        font-size: clamp(1.65rem, 3vw, 2.2rem);
        line-height: 1.15;
        color: var(--color-text);
    }

    .public-service-book__service-description {
        margin: 0.75rem 0 0;
        max-width: 760px;
        color: var(--color-text-unimportant-dark);
        line-height: 1.6;
    }

    .public-service-book__service-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-top: 1rem;
    }

    .public-service-book__meta-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.55rem 0.8rem;
        border: 1px solid var(--color-border-light);
        border-radius: 999px;
        background-color: var(--color-bg);
        color: var(--color-text);
        font-size: 0.92rem;
    }

    .public-service-book__asset-panel {
        border: 1px solid var(--color-border-light);
        border-radius: 0.85rem;
        background-color: var(--color-bg-complement);
        padding: 1rem;
    }

    .public-service-book__asset-panel-header {
        margin-bottom: 1rem;
    }

    .public-service-book__asset-panel-title {
        margin: 0;
        color: var(--color-text);
        font-size: 1.15rem;
    }

    .public-service-book__asset-panel-subtitle {
        margin: 0.35rem 0 0;
        color: var(--color-text-unimportant-dark);
        font-size: 0.94rem;
    }

    .public-service-book__asset-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }

    .public-service-book__asset-card {
        border: 1px solid var(--color-border-light);
        border-radius: 0.8rem;
        background-color: var(--color-bg);
        transition: transform 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .public-service-book__asset-card:hover {
        transform: translateY(-2px);
        border-color: var(--color-primary);
        box-shadow: 0 8px 20px var(--color-box-shadow);
    }

    .public-service-book__asset-card-body {
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        gap: 1rem;
        padding: 1rem;
    }

    .public-service-book__asset-card-top {
        display: flex;
        gap: 0.85rem;
        align-items: flex-start;
    }

    .public-service-book__asset-icon {
        width: 2.7rem;
        height: 2.7rem;
        border-radius: 0.75rem;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--color-bg-complement);
        color: var(--color-primary);
        border: 1px solid var(--color-border-light);
        font-size: 1rem;
    }

    .public-service-book__asset-text {
        min-width: 0;
    }

    .public-service-book__asset-title {
        margin: 0;
        color: var(--color-text);
        font-size: 1.02rem;
        word-break: break-word;
    }

    .public-service-book__asset-description {
        margin: 0.45rem 0 0;
        color: var(--color-text-unimportant-dark);
        font-size: 0.92rem;
        line-height: 1.55;
    }

    .public-service-book__asset-description--muted {
        color: var(--color-text-unimportant);
    }

    .public-service-book__asset-card-footer {
        margin-top: auto;
    }

    .public-service-book__select-btn {
        width: 100%;
        min-height: 2.8rem;
        padding: 0.75rem 1rem;
        border-radius: 0.7rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        font-weight: 600;
        background-color: var(--color-primary);
        color: var(--color-text-white);
        transition: background-color 0.2s ease, transform 0.2s ease;
    }

    .public-service-book__select-btn:hover {
        background-color: var(--color-primary-hover);
        transform: translateY(-1px);
    }

    .public-service-book__empty-box {
        padding: 2.5rem 1.25rem;
        border: 1px dashed var(--color-border-light);
        border-radius: 0.85rem;
        text-align: center;
        background-color: var(--color-bg);
        color: var(--color-text-unimportant-dark);
    }

    .public-service-book__empty-box i {
        font-size: 2rem;
        margin-bottom: 0.8rem;
        color: var(--color-text-unimportant);
    }

    .public-service-book__empty-box h3 {
        margin: 0 0 0.5rem;
        color: var(--color-text);
    }

    .public-service-book__empty-box p {
        margin: 0;
    }

    @media (max-width: 992px) {
        .public-service-book {
            grid-template-columns: 1fr;
        }

        .public-service-book__sidebar {
            border-right: none;
            border-bottom: 1px solid var(--color-border-light);
        }
    }

    @media (max-width: 768px) {
        .public-service-book__main {
            padding: 0.75rem;
        }

        .public-service-book__service-header-content,
        .public-service-book__asset-panel {
            padding: 0.9rem;
        }

        .public-service-book__asset-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection