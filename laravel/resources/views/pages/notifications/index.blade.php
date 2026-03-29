@extends('layouts.app')

@section('title', 'Bexora | Notifications')

@section('content')
<div class="notifications-history-page">
    <div class="notifications-history">
        <div class="notifications-history__header">
            <h2 class="notifications-history__title">Notification </h2>

            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="notifications-history__button notifications-history__button--secondary">
                    Mark All as Read
                </button>
            </form>
        </div>

        <div class="notifications-history__list">
            @forelse ($notifications as $notification)
                <div
                    class="notifications-history__item notifications-history__item--clickable {{ $notification->read_at ? 'notifications-history__item--read' : 'notifications-history__item--unread' }}"
                    data-message="{{ $notification->data['message'] ?? 'Notification' }}"
                    data-date="{{ $notification->created_at->format('M d, Y H:i') }}"
                    data-status="{{ $notification->read_at ? 'Read' : 'New' }}"
                    data-notification-id="{{ $notification->id }}"
                    data-is-unread="{{ $notification->read_at ? '0' : '1' }}"
                >
                    @if (!$notification->read_at)
                        <span class="notifications-history__badge">New</span>
                    @endif

                    <div class="notifications-history__item-main">
                        <div class="notifications-history__item-text">
                            <p class="notifications-history__message {{ !$notification->read_at ? 'notifications-history__message--unread' : '' }}">
                                {{ $notification->data['message'] ?? 'Notification' }}
                            </p>

                            <small class="notifications-history__date">
                                {{ $notification->created_at->format('M d, Y H:i') }}
                            </small>
                        </div>

                        <div class="notifications-history__item-actions">
                            @if (!$notification->read_at)
                                <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="notifications-history__button">
                                        Mark as read
                                    </button>
                                </form>
                            @else
                                <span class="notifications-history__status notifications-history__status--read">Read</span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="notifications-history__empty">
                    You have no notifications.
                </div>
            @endforelse
        </div>

@if ($notifications->hasPages())
    <div class="notifications-history__pagination">
        @if ($notifications->onFirstPage())
            <span class="notifications-history__pagination-button notifications-history__pagination-button--disabled">
                &larr;
            </span>
        @else
            <a
                href="{{ $notifications->previousPageUrl() }}"
                class="notifications-history__pagination-button"
                aria-label="Previous page"
            >
                &larr;
            </a>
        @endif

        <span class="notifications-history__pagination-info">
            Page {{ $notifications->currentPage() }} of {{ $notifications->lastPage() }}
        </span>

        @if ($notifications->hasMorePages())
            <a
                href="{{ $notifications->nextPageUrl() }}"
                class="notifications-history__pagination-button"
                aria-label="Next page"
            >
                &rarr;
            </a>
        @else
            <span class="notifications-history__pagination-button notifications-history__pagination-button--disabled">
                &rarr;
            </span>
        @endif
    </div>
@endif
<!-- 
<p>
    Count on this page: {{ $notifications->count() }}<br>
    Total: {{ $notifications->total() }}<br>
    Last page: {{ $notifications->lastPage() }}
</p>

-->

    <div class="modal hidden" id="notificationDetailModal">
        <div class="modal__overlay modal-close-trigger"></div>

        <div class="modal__content">
            <div class="modal__header">
                <h3 class="modal-header__title">Notification Detail</h3>
                <button type="button" class="modal-close-trigger notifications-history__icon-button">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <div class="modal__body">
              

                <div class="notifications-history-modal__group">
                    <span class="notifications-history-modal__label">Date</span>
                    <p id="notificationModalDate" class="notifications-history-modal__value"></p>
                </div>

                <div class="notifications-history-modal__group">
                    <span class="notifications-history-modal__label">Message</span>
                    <p id="notificationModalMessage" class="notifications-history-modal__value notifications-history-modal__value--message"></p>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
@vite('resources/js/pages/notifications/entry.js')