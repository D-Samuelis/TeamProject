@extends('web.layouts.app')

@section('title', 'Bexora | My Services')

@section('content')
<div class="business"> {{-- Ponechávame triedu .business kvôli CSS konzistencii --}}
    <aside class="business__sidebar">
        <section class="business__filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Management</h3>
            <div id="managementList" class="dropdown__mini-list">
                <a href="{{ route('manage.service.index') }}" class="business__nav-link is-active">
                    <i class="fa-solid fa-list"></i><span>All Services</span>
                </a>
                <button type="button" class="business__nav-link" data-modal-target="create-service-modal">
                    <i class="fa-solid fa-plus"></i><span>New Service</span>
                </button>
            </div>
        </section>

        <section class="business__status-filters">
            <h3 class="miniLists__subtitle"><i class="fa-solid fa-chevron-down"></i> Status</h3>
            <div id="statusList" class="dropdown__mini-list">
                {{-- Sem JS renderuje filtre (Active, Inactive, Archived) --}}
            </div>
        </section>
    </aside>

    <div class="display-column">
        <x-ui.breadcrumbs />
        
        <main class="business__main">
            <header class="business__header-wrapper business__header-wrapper--simple">
                <div class="business__header-corner"></div>

                <div class="business__header-info">
                    <h2 class="business-header__title">My Services</h2>
                    
                    <div class="business-info">
                        <div class="stat-item stat-item--all">
                            <i class="fa-solid fa-layer-group"></i>
                            <div id="countAll">0</div> Total services
                        </div>
                        <div class="stat-item stat-item--published">
                            <i class="fa-solid fa-circle-check"></i>
                            <div id="countActive">0</div> Active
                        </div>
                        <div class="stat-item stat-item--hidden">
                            <i class="fa-solid fa-eye-slash"></i>
                            <div id="countInactive">0</div> Inactive
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
                                <input type="text" id="serviceSearchInput" placeholder="Search services...">
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <div class="business__body-wrapper">
                {{-- Kontajner, do ktorého JS vloží tabuľku/zoznam --}}
                <div id="serviceTableContainer" class="list-view__body-wrapper"></div>
            </div>
        </main>
    </div>
</div>

<script>
    window.BE_DATA = {
        // Zlúčime aktívne a vymazané služby (soft deletes)
        services: @json($services->merge($deletedServices ?? [])),
        routes: {
            store: "{{ route('manage.service.store') }}",
            restore: "{{ route('manage.service.restore', ':id') }}",
            delete: "{{ route('manage.service.delete', ':id') }}",
            update: "{{ route('manage.service.update', ':id') }}",
            show: "{{ route('manage.service.show', ':id') }}"
        },
        csrf: "{{ csrf_token() }}"
    };

    console.log(window.BE_DATA['services'][0]); // Debug: Skontrolujte načítanie dát do JS
</script>

@vite('resources/js/pages/services/entry.js')
@endsection