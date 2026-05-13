@extends('web.layouts.app')

@section('title', 'Bexora | Dashboard')

@section('content')

    <script>
        window.BE_DATA = {
            csrf: '{{ csrf_token() }}',
            businesses:    @json($businesses),
            allBranches:   @json($branches),
            allServices:   @json($services),
            allAssets:     @json($assets),
            businessStats: @json($businessStats),
            branchStats:   @json($branchStats),
            serviceStats:  @json($serviceStats),
            assetStats:    @json($assetStats),
            coverage:      {{ $coverage }},
            toolbar: {
                showStatus: false,
                centerGroups: [],
                rightAction: { label: 'Ask Bexi', icon: 'fa-message' }
            }
        };
    </script>

    <div class="business">
        <aside class="business__sidebar">

            @include('components.partials.dashboard_sidebar_info', ['active' => 'dashboard'])

        </aside>

        <div class="display-column">
            <x-ui.breadcrumbs />

            <main class="business__main">

                <header class="business__header-wrapper">
                    <div class="business__header-corner">
                        <div class="view-switcher">
                            <button class="view-switcher__btn active">
                                <i class="fa-solid fa-chart-line"></i> Analytics
                            </button>
                        </div>
                    </div>
                    <div class="business__header-info">
                        <div class="business__header-info-text">
                            <h2 class="business-header__title">Business Overview</h2>
                        </div>
                    </div>
                    <div class="business__header-right"></div>
                </header>

                <div class="business__body-wrapper">
                    @if($businesses->isEmpty())
                        {{-- ── Empty State ────────────────── --}}
                        <div class="empty-dashboard-state">
                            <div class="empty-dashboard-state__content">
                                <div class="empty-dashboard-state__icon">
                                    <i class="fa-solid fa-chart-pie"></i>
                                </div>
                                <h3>No results found.</h3>
                                <p>You currently do not have any businesses associated with your account. You can create a new business at any time.</p>
                                <a href="{{ route('manage.business.index') }}" class="empty-dashboard-state__btn">
                                    <i class="fa-solid fa-plus"></i> Create Your First Business
                                </a>
                            </div>
                        </div>
                    @else
                        {{-- ── Business selector ───────────────────────────────── --}}
                        <div class="biz-selector">
                            <div class="biz-selector__left">
                                <div class="biz-selector__dropdown-wrap">
                                    <i class="fa-solid fa-briefcase biz-selector__icon"></i>
                                    <select id="bizSelect" class="biz-selector__select">
                                        <option value="all">All Businesses</option>
                                        @foreach ($businesses as $biz)
                                            <option value="{{ $biz->id }}">{{ $biz->name }}</option>
                                        @endforeach
                                    </select>
                                    <i class="fa-solid fa-chevron-down biz-selector__chevron"></i>
                                </div>
                            </div>

                            <div class="biz-selector__info" id="bizInfo">
                                <div class="biz-selector__info-item">
                                    <i class="fa-solid fa-layer-group"></i>
                                    <span id="bizInfoBranches">{{ $branches->count() }} branches</span>
                                </div>
                                <div class="biz-selector__info-item">
                                    <i class="fa-solid fa-bell-concierge"></i>
                                    <span id="bizInfoServices">{{ $services->count() }} services</span>
                                </div>
                                <div class="biz-selector__info-item">
                                    <i class="fa-solid fa-cube"></i>
                                    <span id="bizInfoAssets">{{ $assets->count() }} assets</span>
                                </div>
                                <div class="biz-selector__info-item" id="bizInfoStatusWrap">
                                    <i class="fa-solid fa-circle-dot"></i>
                                    <span id="bizInfoStatus"></span>
                                </div>
                            </div>
                        </div>

                        {{-- ── Businesses ───────────────────────────────────────── --}}
                        <div class="stat-group" id="group-businesses">
                            <h4 class="stat-group__label">
                                <i class="fa-solid fa-briefcase"></i> Businesses
                            </h4>
                            <div class="stat-group__cards">
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--blue"><i class="fa-solid fa-briefcase"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Total</span>
                                        <strong class="stat-card__value" id="s-biz-total">{{ $businessStats['total'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-globe"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Published</span>
                                        <strong class="stat-card__value" id="s-biz-published">{{ $businessStats['published'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--amber"><i class="fa-solid fa-eye-slash"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Hidden</span>
                                        <strong class="stat-card__value" id="s-biz-hidden">{{ $businessStats['hidden'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--red"><i class="fa-solid fa-box-archive"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Archived</span>
                                        <strong class="stat-card__value" id="s-biz-archived">{{ $businessStats['archived'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Branches ─────────────────────────────────────────── --}}
                        <div class="stat-group">
                            <h4 class="stat-group__label">
                                <i class="fa-solid fa-code-branch"></i> Branches
                            </h4>
                            <div class="stat-group__cards">
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--blue"><i class="fa-solid fa-code-branch"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Total</span>
                                        <strong class="stat-card__value" id="s-br-total">{{ $branchStats['total'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-circle-check"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Active</span>
                                        <strong class="stat-card__value" id="s-br-active">{{ $branchStats['active'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--amber"><i class="fa-solid fa-circle-pause"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Inactive</span>
                                        <strong class="stat-card__value" id="s-br-inactive">{{ $branchStats['inactive'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--red"><i class="fa-solid fa-box-archive"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Archived</span>
                                        <strong class="stat-card__value" id="s-br-archived">{{ $branchStats['archived'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Services ─────────────────────────────────────────── --}}
                        <div class="stat-group">
                            <h4 class="stat-group__label">
                                <i class="fa-solid fa-bell-concierge"></i> Services
                            </h4>
                            <div class="stat-group__cards">
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--blue"><i class="fa-solid fa-bell-concierge"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Total</span>
                                        <strong class="stat-card__value" id="s-svc-total">{{ $serviceStats['total'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-circle-check"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Active</span>
                                        <strong class="stat-card__value" id="s-svc-active">{{ $serviceStats['active'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--amber"><i class="fa-solid fa-circle-pause"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Inactive</span>
                                        <strong class="stat-card__value" id="s-svc-inactive">{{ $serviceStats['inactive'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--red"><i class="fa-solid fa-box-archive"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Archived</span>
                                        <strong class="stat-card__value" id="s-svc-archived">{{ $serviceStats['archived'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Assets ───────────────────────────────────────────── --}}
                        <div class="stat-group">
                            <h4 class="stat-group__label">
                                <i class="fa-solid fa-cube"></i> Assets
                            </h4>
                            <div class="stat-group__cards">
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--blue"><i class="fa-solid fa-cube"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Total</span>
                                        <strong class="stat-card__value" id="s-ast-total">{{ $assetStats['total'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--green"><i class="fa-solid fa-circle-check"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Active</span>
                                        <strong class="stat-card__value" id="s-ast-active">{{ $assetStats['active'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--amber"><i class="fa-solid fa-circle-pause"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Inactive</span>
                                        <strong class="stat-card__value" id="s-ast-inactive">{{ $assetStats['inactive'] }}</strong>
                                    </div>
                                </div>
                                <div class="stat-card">
                                    <div class="stat-card__icon stat-card__icon--red"><i class="fa-solid fa-box-archive"></i></div>
                                    <div class="stat-card__body">
                                        <span class="stat-card__label">Archived</span>
                                        <strong class="stat-card__value" id="s-ast-archived">{{ $assetStats['archived'] }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Charts ───────────────────────────────────────────── --}}
                        <section class="dashboard-charts">
                            <div class="dashboard-chart-card">
                                <h3 class="dashboard-chart-card__title">Business Status</h3>
                                <div id="chart-business-status"></div>
                            </div>
                            <div class="dashboard-chart-card">
                                <h3 class="dashboard-chart-card__title">Branch Status</h3>
                                <div id="chart-branch-status"></div>
                            </div>
                            <div class="dashboard-chart-card">
                                <h3 class="dashboard-chart-card__title">Service Status</h3>
                                <div id="chart-service-status"></div>
                            </div>
                            <div class="dashboard-chart-card">
                                <h3 class="dashboard-chart-card__title">Asset Status</h3>
                                <div id="chart-asset-status"></div>
                            </div>
                            <div class="dashboard-chart-card dashboard-chart-card--wide">
                                <h3 class="dashboard-chart-card__title">Branches & Services per Business</h3>
                                <div id="chart-per-business"></div>
                            </div>
                            <div class="dashboard-chart-card dashboard-chart-card--wide">
                                <h3 class="dashboard-chart-card__title">Service Link Coverage by Branch</h3>
                                <div id="chart-service-coverage"></div>
                            </div>
                        </section>
                    @endif
                </div>
            </main>

            @include('components.ui.toolbar')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @vite('resources/js/pages/dashboard/entry.js')

@endsection