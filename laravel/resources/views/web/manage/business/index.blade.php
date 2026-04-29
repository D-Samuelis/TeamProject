@extends('web.layouts.app')

@section('title', 'Bexora | My Businesses')

@section('content')
<div class="business">
    <aside class="business__sidebar">

    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
        <main class="business__main">
            <header class="business__header-wrapper business__header-wrapper--simple">
                <div class="business__header-corner"></div>

                <div class="business__header-info">
                    <h2 class="business-header__title">My Businesses</h2>
                    
                    <div class="business-info">
                        <div class="stat-item stat-item--all">
                            <i class="fa-solid fa-layer-group"></i>
                            <div id="countAll">0</div> Total businesses
                        </div>
                        <div class="stat-item stat-item--published">
                            <i class="fa-solid fa-circle-check"></i>
                            <div id="countPublished">0</div> Published
                        </div>
                        <div class="stat-item stat-item--hidden">
                            <i class="fa-solid fa-eye-slash"></i>
                            <div id="countHidden">0</div> Hidden
                        </div>
                        <div class="stat-item stat-item--deleted">
                            <i class="fa-solid fa-trash"></i>
                            <div id="countDeleted">0</div> Archived
                        </div>
                    </div>
                </div>

                <div class="business__header-right">
                    <div class="business__header-right-section_1"> </div>
                    <div class="business__header-right-section_2">
                        <div class="list-view__search-wrapper">
                            <div class="search-container">
                                <i class="fa-solid fa-magnifying-glass"></i>
                                <input type="text" id="businessSearchInput" placeholder="Search businesses...">
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="business__body-wrapper">
                <div id="businessTableContainer" class="list-view__body-wrapper"></div>
            </div>
        </main>
        @include('components.ui.toolbar')
    </div>
</div>

<script>
    window.BE_DATA = {
        businesses: @json($activeBusinesses->merge($deletedBusinesses)),
        routes: {
            store: "{{ route('manage.business.store') }}",
            restore: "{{ route('manage.business.restore', ':id') }}",
            delete: "{{ route('manage.business.delete', ':id') }}",
            update: "{{ route('manage.business.update', ':id') }}",
            show: "{{ route('manage.business.show', ':id') }}"
        },
        csrf: "{{ csrf_token() }}",
        toolbar: {
            showStatus: false,
            centerGroups: [
                {
                    groupId: 'manage',
                    actions: [
                        {
                            label: 'Create Business',
                            icon: 'fa-plus',
                            modal: 'create-business-modal'
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
</script>

@vite('resources/js/pages/businesses/entry.js')
@endsection

<div id="tpl-business-filters" style="display: none;">
    @include('components.statuses_business')
</div>