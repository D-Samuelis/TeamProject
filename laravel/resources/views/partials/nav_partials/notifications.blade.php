@auth
    <div class="notifications">
        <button class="notifications__trigger" id="notificationsMenu" type="button" aria-label="Open notifications">
            <i class="lni lni-alarm notifications__icon"></i>

            @if (auth()->user()->unreadNotifications->count() > 0)
                <span class="notifications__badge" id="notificationCount">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            @endif
        </button>

        <div class="notifications-menu hidden" id="notificationsMenuContent">
            <h3 class="notifications-menu__title">Notifications</h3>

            <div class="notifications-menu__content">
                <div class="notifications-menu__list" id="notificationList">
                    @forelse(auth()->user()->unreadNotifications as $notification)
                        <div
                            class="notifications-menu__item notifications-menu__item--unread"
                            id="notification-{{ $notification->id }}"
                            data-notification-id="{{ $notification->id }}"
                            data-url="{{ route('notifications.index', ['open' => $notification->id]) }}"
                        >
                            <div class="notifications-menu__item-body notifications-menu__item-body--clickable">
                                <p class="notifications-menu__message">
                                    {{ $notification->data['message'] ?? 'New notification' }}
                                </p>

                                <small class="notifications-menu__time">
                                    {{ $notification->created_at->diffForHumans() }}
                                </small>
                            </div>

                            <button
                                class="notifications-menu__dismiss"
                                type="button"
                                data-notification-id="{{ $notification->id }}"
                                aria-label="Mark as read"
                            >
                                &times;
                            </button>
                        </div>
                    @empty
                        <p class="notifications-menu__empty">You have no new notifications.</p>
                    @endforelse
                </div>
            </div>

            <div class="notifications-menu__footer">
                <a href="{{ route('notifications.index') }}" class="notifications-menu__link">
                    View All Notifications
                </a>
            </div>
        </div>
    </div>
@endauth