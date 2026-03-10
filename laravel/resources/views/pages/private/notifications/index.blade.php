<div class="page-container">
    <div class="card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2>Notification History</h2>
            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                @csrf
                <button class="btn-secondary">Mark All as Read</button>
            </form>
        </div>

        <div class="notification-history">
            @foreach ($notifications as $notification)
                <div class="history-item {{ $notification->read() ? 'is-read' : 'is-unread' }}"
                    style="padding:15px; border-bottom:1px solid #eee; {{ $notification->unread() ? 'background:#f0f7ff;' : '' }}">

                    <div style="display:flex; justify-content:space-between; align-items: center;">
                        <div>
                            <p style="margin:0; font-weight:{{ $notification->unread() ? 'bold' : 'normal' }}">
                                {{ $notification->data['message'] }}
                            </p>
                            <small class="text-gray">{{ $notification->created_at->format('M d, Y H:i') }}</small>
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px;">
                            @if ($notification->unread())
                                <span style="color:#2563eb; font-size:12px;">● New</span>

                                <form action="{{ route('notifications.markRead', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        style="background:#e5e7eb; border:none; padding:4px 8px; border-radius:4px; font-size:11px; cursor:pointer;">
                                        Mark as read
                                    </button>
                                </form>
                            @else
                                <span style="color:#9ca3af; font-size:12px;">Read</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="pagination-links" style="margin-top:20px;">
            {{ $notifications->links() }}
        </div>
    </div>
</div>
