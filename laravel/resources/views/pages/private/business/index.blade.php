@extends('layouts.app')

@section('title', 'Bexora | My Businesses')

@section('content')
<div class="business">
    
    {{-- SIDEBAR --}}
    <aside class="business__sidebar">
        <section class="business__group">
            <h3 class="business__subtitle" data-collapse-trigger="managementList">
                <i class="fa-solid fa-briefcase"></i>
                Management
            </h3>
            <div id="managementList" class="dropdown__mini-list">
                <a href="{{ route('business.index') }}" class="business__nav-link {{ request()->routeIs('business.index') ? 'is-active' : '' }}">
                    <i class="fa-solid fa-list"></i>
                    <span>All Businesses</span>
                </a>
                <button type="button" class="business__nav-link" data-modal-target="create-business-modal">
                    <i class="fa-solid fa-plus"></i>
                    <span>New Business</span>
                </button>
            </div>
        </section>
    </aside>

    {{-- MAIN CONTENT --}}
    <main class="business__main">
        <header class="business__header-wrapper">
            <div class="business__header-info">
                <h2 class="timeline-header__title">My Businesses</h2>
                <div class="timeline-info">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Active Units: <strong>{{ $activeBusinesses->count() }}</strong></span>
                </div>
            </div>

            <div class="business__search-wrapper">
                <div class="business__search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="businessSearchInput" placeholder="Search businesses...">
                </div>
            </div>
        </header>

        <div class="business__body-wrapper">
            @if (session('error'))
                <div class="alert alert--danger mb-4">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- ACTIVE SECTION --}}
            <h3 class="business__subtitle mb-3">Active Businesses</h3>
            <div class="business-grid" id="activeBusinessGrid">
                @forelse ($activeBusinesses as $business)
                    <article class="business-card">
                        <div class="business-card__header">
                            {{-- Pridaná trieda js-search-data --}}
                            <h4 class="business-card__title js-search-data">{{ $business->name }}</h4>
                            
                            {{-- Status IGNORUJEME (nemá triedu js-search-data) --}}
                            <span class="status-cell {{ $business->is_published ? 'filter-item--green' : 'filter-item--black' }}">
                                {{ $business->is_published ? 'Published' : 'Hidden' }}
                            </span>
                        </div>

                        {{-- Pridaná trieda js-search-data --}}
                        <p class="business-card__description js-search-data">
                            {{ $business->description ?? 'No description provided.' }}
                        </p>

                        <div class="business-card__footer">
                            <a href="{{ route('business.show', $business->id) }}" class="business-card__manage-btn">Manage</a>
                            
                            {{-- Visibility Toggle (Oko) --}}
                            @can('update', $business)
                                <form action="{{ route('business.update', $business->id) }}" method="POST" style="display: contents;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_published" value="{{ $business->is_published ? '0' : '1' }}">
                                    <button type="submit" class="button-icon {{ $business->is_published ? '' : 'button-icon--danger' }}" title="Toggle Visibility">
                                        <i class="fa-solid {{ $business->is_published ? 'fa-eye' : 'fa-eye-slash' }}"></i>
                                    </button>
                                </form>
                            @endcan

                            @can('delete', $business)
                                <form method="POST" action="{{ route('business.delete', $business->id) }}" onsubmit="return confirm('Archive this business?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="button-icon button-icon--danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </div>
                    </article>
                @empty
                    <div class="business__empty">
                        <p>No active businesses found.</p>
                    </div>
                @endforelse
            </div>

            {{-- ARCHIVED SECTION --}}
            @if ($deletedBusinesses->count())
                <h3 class="business__subtitle mt-5 mb-3">Archived Businesses</h3>
                <div class="business-grid">
                    @foreach ($deletedBusinesses as $business)
                        {{-- Celá karta má triedu .business-card, takže ju search.js uvidí --}}
                        <article class="business-card business-card--archived">
                            <div class="business-card__header">
                                {{-- Pridaná js-search-data pre vyhľadávanie názvu --}}
                                <h4 class="business-card__title js-search-data">{{ $business->name }}</h4>
                                <span class="status-cell filter-item--red">Archived</span>
                            </div>

                            {{-- Pridaný popis s js-search-data, aby bol grid konzistentný --}}
                            <p class="business-card__description js-search-data">
                                {{ Str::limit($business->description ?? 'No description provided.', 80) }}
                            </p>

                            <div class="business-card__footer">
                                <form method="POST" action="{{ route('business.restore', $business->id) }}" style="width: 100%">
                                    @csrf
                                    <button type="submit" class="business-card__restore-btn">
                                        <i class="fa-solid fa-rotate-left"></i> Restore Business
                                    </button>
                                </form>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </main>
</div>

{{-- MODAL --}}
<div id="create-business-modal" class="business-modal hidden">
    <div class="business-modal__overlay"></div>
    <div class="business-modal__content">
        <div class="business-modal__header mb-4">
            <h2 class="timeline-header__title">Create New Business</h2>
        </div>
        <form method="POST" action="{{ route('business.store') }}">
            @csrf
            <div class="business__search-container mb-3" style="width: 100%">
                <input type="text" name="name" placeholder="Business Name" required>
            </div>
            <div class="business__search-container mb-4" style="width: 100%; height: auto;">
                <textarea name="description" placeholder="Description (optional)" style="width: 100%; border: none; background: transparent; outline: none; padding: 5px; min-height: 80px;"></textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="business__nav-link is-active" style="border: none; cursor: pointer;">
                    Save Business
                </button>
                <button type="button" class="business__nav-link modal-close-trigger" style="border: none; cursor: pointer;">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

@vite('resources/js/pages/businesses/entry.js')

@endsection