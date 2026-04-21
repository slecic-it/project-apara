@php
    $employeeName = $employee['name'] ?? 'SLECIC User';
    $currentRoute = request()->route()?->getName();
    $pageLabel = collect(explode('.', str_replace('-', ' ', (string) $currentRoute)))
        ->filter()
        ->map(fn ($part) => ucwords($part))
        ->implode(' / ');
    $headerNotifications = $headerNotifications ?? [
        'count' => 0,
        'hasItems' => false,
        'items' => [],
        'title' => 'Notifications',
        'subtitle' => 'No marketing notifications right now.',
    ];
    $notificationPageUrl = \Illuminate\Support\Facades\Route::has('notifications')
        ? route('notifications')
        : '#';
@endphp

<header class="app-header">
    <div class="app-header-left">
        <div class="app-header-logo-wrap">
            <img src="{{ asset('images/logo.png') }}" alt="SLECIC logo" class="app-header-logo">
        </div>
        <div class="app-header-text">
            <div class="app-header-eyebrow">Internal Workspace</div>
            <div class="app-header-title">{{ $pageLabel !== '' ? $pageLabel : 'Dashboard' }}</div>
        </div>
    </div>

    <div class="app-header-right">
        <div class="app-header-clock" id="headerDateTime">--</div>

        <div class="app-notification-wrap">
            <a href="{{ $notificationPageUrl }}" class="app-notification-btn" aria-label="Notifications">
                <i class="bi bi-bell"></i>
                @if(($headerNotifications['count'] ?? 0) > 0)
                    <span class="app-notification-count">{{ min((int) $headerNotifications['count'], 9) }}</span>
                    <span class="app-notification-dot"></span>
                @endif
            </a>
        </div>

        <div class="app-header-user">
            <span class="app-header-user-avatar">{{ strtoupper(substr($employeeName, 0, 1)) }}</span>
            <div class="app-header-user-meta">
                <strong>{{ $employeeName }}</strong>
                <span>System User</span>
            </div>
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const headerDateTime = document.getElementById('headerDateTime');

    function updateHeaderDateTime() {
        if (!headerDateTime) {
            return;
        }

        const now = new Date();
        headerDateTime.textContent = now.toLocaleString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    updateHeaderDateTime();
    setInterval(updateHeaderDateTime, 60000);
});
</script>
