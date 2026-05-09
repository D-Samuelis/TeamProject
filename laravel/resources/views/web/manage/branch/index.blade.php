@extends('web.layouts.app')

@section('title', 'Bexora | My Branches')

@section('content')

<script>
    window.BE_DATA = {
        csrf: '{{ csrf_token() }}',
        branches: @json($branches),
        businesses: @json($businesses),
        meta: @json($meta),
        routes: {
            store: "{{ route('manage.branch.store') }}",
            show: "{{ route('manage.branch.show', ':id') }}",
            update: "{{ route('manage.branch.update', ':id') }}",
            branchDelete: "{{ route('manage.branch.delete', ':id') }}",
            restore: "{{ route('manage.branch.restore', ':id') }}"
        },
        toolbar: {
            showStatus: true,
            centerGroups: [
                {
                    groupId: 'manage',
                    actions: [
                        {
                            label: 'Create Branch',
                            icon: 'fa-plus',
                            modal: 'create-branch-modal'
                        }
                    ]
                }
            ],
            rightAction: {
                label: 'Ask Bexi',
                icon: 'fa-message',
                modal: 'xxx'
            }
        }
    };

    console.log('BE_DATA:', window.BE_DATA.branches);
</script>

<div class="business">
    <aside class="business__sidebar">
        @include('components.partials.dashboard_sidebar_info', ['active' => 'branches'])

        <section class="branch__filters">
            <h3 class="miniLists__subtitle">
                <i class="fa-solid fa-chevron-down"></i>
                <i class="fa-solid fa-filter"></i>
                Filters
            </h3>
            @include('web.manage.branch.partials.filter-sidebar')
        </section>
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
        <main class="business__main">
            <header class="business__header-wrapper business__header-wrapper--simple">
                <div class="business__header-corner"></div>

                <div class="business__header-info">
                    <h2 class="business-header__title">My Branches</h2>

                    <div class="business-info">
                        <div class="stat-item stat-item--all">
                            <i class="fa-solid fa-location-dot"></i>
                            <div id="countAll">{{ $branches->count() }}</div> Total
                        </div>
                        <div class="stat-item stat-item--published">
                            <i class="fa-solid fa-circle-check"></i>
                            <div id="countActive">{{ $branches->where('is_active', true)->count() }}</div> Active
                        </div>
                        <div class="stat-item stat-item--deleted">
                            <i class="fa-solid fa-trash"></i>
                            <div id="countDeleted">{{ $branches->whereNotNull('deleted_at')->count() }}</div> Archived
                        </div>
                    </div>
                </div>

                <div class="business__header-right">
                    <div class="business__header-right-section_2">
                        <div class="list-view__search-wrapper">
                            <div class="search-container">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="branchSearchInput" placeholder="Search branches...">
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="business__body-wrapper">
                <div id="branchTableContainer" class="list-view__body-wrapper">
                    {{-- Renderované cez JavaScript podobne ako Asset listView.js --}}
                </div>
            </div>

            <div id="paginationContainer" class="pagination"></div>
        </main>
        @include('components.ui.toolbar')
    </div>
</div>

@vite('resources/js/pages/branches/entry.js')

@endsection

<div id="tpl-status-filters" style="display: none;">
    @include('components.statuses_branch')
</div>
