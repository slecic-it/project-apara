@php
    $employeeName = $employee['name'] ?? 'SLECIC User';
    $currentRoute = request()->route()?->getName();
    $pageLabel = collect(explode('.', str_replace('-', ' ', (string) $currentRoute)))
        ->filter()
        ->map(fn ($part) => ucwords($part))
        ->implode(' / ');
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

        <button type="button" class="app-notification-btn" aria-label="Notifications">
            <i class="bi bi-bell"></i>
            <span class="app-notification-dot"></span>
        </button>

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
