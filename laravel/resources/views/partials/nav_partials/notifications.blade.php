@auth
    <div class="notifications">
        <button class="notifications__trigger" id="notificationsMenu">
            <i class="lni lni-alarm notifications__icon"></i>
            @if (auth()->user()->unreadNotifications->count() > 0)
                <span class="notifications__badge" id="notificationCount">
                    {{ auth()->user()->unreadNotifications->count() }}
                </span>
            @endif
        </button>
    </div>

    <div class="notifications-menu hidden" id="notificationsMenuContent">
        <h3 class="notifications-menu__title">Notifications</h3>

        <div class="notifications-menu__content">
            <div class="notifications-menu__list" id="notificationList">
                @forelse(auth()->user()->unreadNotifications as $notification)
                    <div class="notification-item" id="notification-{{ $notification->id }}"
                        style="display:flex; justify-content:space-between; align-items:flex-start; padding:12px; border-bottom:1px solid #f3f4f6;">

                        <div class="notification-item__body">
                            <p style="margin:0; font-size:13px; color:#374151;">
                                {{ $notification->data['message'] ?? 'New Assignment' }}
                            </p>
                            <small
                                style="color:#9ca3af; font-size:11px;">{{ $notification->created_at->diffForHumans() }}</small>
                        </div>

                        <button onclick="dismissNotification('{{ $notification->id }}')"
                            style="background:none; border:none; color:#d1d5db; cursor:pointer; font-size:18px; line-height:1;">
                            &times;
                        </button>
                    </div>
                @empty
                    <p class="notifications-menu__empty">You have no new notifications.</p>
                @endforelse
            </div>
        </div>

        {{-- Link to the full history --}}
        <div style="padding: 12px; text-align: center; border-top: 1px solid #f3f4f6;">
            <a href="{{ route('notifications.index') }}"
                style="font-size: 13px; color: #2563eb; text-decoration: none; font-weight: 500;">
                View All Notifications
            </a>
        </div>
    </div>
@endauth

<script>
    function dismissNotification(id) {
        fetch(`/notifications/${id}/dismiss`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 1. Remove the notification item from the list
                    const element = document.getElementById(`notification-${id}`);
                    element.remove();

                    // 2. Update the badge count
                    const countBadge = document.getElementById('notificationCount');
                    if (countBadge) {
                        let currentCount = parseInt(countBadge.innerText);
                        if (currentCount > 1) {
                            countBadge.innerText = currentCount - 1;
                        } else {
                            countBadge.remove(); // Hide badge if zero
                            document.getElementById('notificationList').innerHTML =
                                '<p class="notifications-menu__empty">You have no new notifications.</p>';
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
