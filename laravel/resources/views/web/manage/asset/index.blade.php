@extends('web.layouts.app')

@section('title', 'Bexora | Assets')

@section('content')

<script>
    window.BE_DATA = {
        csrf: '{{ csrf_token() }}',
        assets: @json($assets),
        allBranches: @json($branches),
        allServices: @json($services),
        routes: {
            store: "{{ route('manage.asset.store') }}",
            show: "{{ route('manage.asset.show', ':id') }}",
            update: "{{ route('manage.asset.update', ':id') }}",
            deleteAsset: "{{ route('manage.asset.delete', ':id') }}",
            restoreAsset: "{{ route('manage.asset.restore', ':id') }}"
        },
        csrf: "{{ csrf_token() }}",
        toolbar: {
            showStatus: true,
            centerActions: [
                {
                    label: 'Create Asset',
                    icon: 'fa-plus',
                    modal: 'create-asset-modal',
                    class: ''
                }
            ],
            rightAction: {
                label: 'Ask Bexi',
                icon: 'fa-message',
                modal: 'xxx'
            }
        }
    };
</script>

<div class="business">
    <aside class="business__sidebar">
        
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
        <main class="business__main">
            <header class="business__header-wrapper business__header-wrapper--simple">
                <div class="business__header-corner"></div>

                <div class="business__header-info">
                    <h2 class="business-header__title">My Assets</h2>

                    <div class="business-info">
                        <div class="stat-item stat-item--all">
                            <i class="fa-solid fa-layer-group"></i>
                            <div id="countAll">{{ $assets->count() }}</div> Total assets
                        </div>
                        <div class="stat-item stat-item--published">
                            <i class="fa-solid fa-circle-check"></i>
                            <div id="countPublished">{{ $assets->count() }}</div> Published
                        </div>
                        <div class="stat-item stat-item--hidden">
                            <i class="fa-solid fa-eye-slash"></i>
                            <div id="countHidden">{{ $assets->count() }}</div> Hidden
                        </div>
                        <div class="stat-item stat-item--deleted">
                            <i class="fa-solid fa-trash"></i>
                            <div id="countDeleted">{{ $assets->count() }}</div> Archived
                        </div>
                    </div>
                </div>

                <div class="business__header-right">
                    <div class="business__header-right-section_1"> </div>
                    <div class="business__header-right-section_2">
                        <div class="list-view__search-wrapper">
                            <div class="search-container">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="assetSearchInput" placeholder="Search assets...">
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="business__body-wrapper">
                <div id="assetTableContainer" class="list-view__body-wrapper">
                </div>
            </div>
        </main>
        @include('components.ui.toolbar')
    </div>
</div>

@vite('resources/js/pages/assets/entry.js')

@endsection

<div id="tpl-status-filters" style="display: none;">
    @include('components.statuses_asset')
</div>
