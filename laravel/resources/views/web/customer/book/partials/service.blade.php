<div class="public-service-book">
    <aside class="public-service-book__sidebar">
        <div class="public-service-book__sidebar-top">
            <a
                href="{{ route('book.business', ['businessId' => $businessId]) }}"
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

    <div class="display-column">
        <x-ui.breadcrumbs />
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
                                            href="{{ route('book.asset', ['businessId' => $businessId, 'serviceId' => $service->id, 'assetId' => $asset->id, 'ref' => request('ref'), 'branch_id'  => request('branch_id'), 'target' => request('target')]) }}"
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
</div>
