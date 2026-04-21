<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<aside class="sidebar" id="appSidebar">
    <div class="sidebar-shell">
        <div class="sidebar-menu-button-wrap">
            <button class="sidebar-menu-button" id="sidebarMenuToggle" type="button" aria-label="Toggle menu">
                <i class="bi bi-list-ul"></i>
                <span>Menu</span>
            </button>
        </div>

        <nav class="sidebar-nav">
            @foreach($navSections as $section)
                <div class="sidebar-section">
                    @foreach($section['items'] as $item)
                        @if($item['type'] === 'link')
                            <a
                                class="sidebar-link {{ $isBankUser ? 'bank-sidebar-button' : '' }} {{ !empty($item['logout']) || in_array($item['route'], ['profile', 'bank.profile', 'dashboard', 'bank.dashboard'], true) ? 'sidebar-secondary-button' : '' }} {{ $item['active'] ? 'active' : '' }}"
                                href="{{ $item['url'] ?? $routeUrl($item['route']) }}"
                            >
                                @if(!empty($item['icon']))
                                    <span class="sidebar-link-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                                @endif
                                <span class="sidebar-link-label">{{ $item['label'] }}</span>
                            </a>
                        @else
                            @php $hasChildren = !empty($item['children']); @endphp
                            @php $isSecondaryGroup = in_array($item['route'] ?? null, ['all-applications', 'bank.all-applications'], true); @endphp
                            @if(!empty($item['route']))
                                <div class="sidebar-group-head {{ $isBankUser ? 'bank-sidebar-button' : '' }} {{ $isSecondaryGroup ? 'sidebar-secondary-group' : '' }} {{ $item['active'] ? 'active' : '' }}">
                                    <a
                                        class="sidebar-link sidebar-group-link {{ $isBankUser ? 'bank-sidebar-button' : '' }} {{ $isSecondaryGroup ? 'sidebar-secondary-button' : '' }} {{ $item['active'] ? 'active' : '' }}"
                                        href="{{ $item['url'] ?? $routeUrl($item['route']) }}"
                                    >
                                        <span class="sidebar-link-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                                        <span class="sidebar-link-label">{{ $item['label'] }}</span>
                                    </a>
                                    @if($hasChildren)
                                        <button
                                            class="sidebar-group-toggle sidebar-group-toggle-button {{ $isBankUser ? 'bank-sidebar-button' : '' }} {{ $isSecondaryGroup ? 'sidebar-secondary-button' : '' }} {{ $item['active'] ? 'active' : '' }}"
                                            type="button"
                                            data-target="{{ $item['id'] }}"
                                            aria-expanded="{{ $item['active'] ? 'true' : 'false' }}"
                                        >
                                            <span class="sidebar-link-caret"><i class="bi bi-chevron-down"></i></span>
                                        </button>
                                    @endif
                                </div>
                            @else
                                <button
                                    class="sidebar-link sidebar-group-toggle {{ $isBankUser ? 'bank-sidebar-button' : '' }} {{ $item['active'] ? 'active' : '' }}"
                                    type="button"
                                    data-target="{{ $item['id'] }}"
                                    aria-expanded="{{ $item['active'] ? 'true' : 'false' }}"
                                >
                                    <span class="sidebar-link-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                                    <span class="sidebar-link-label">{{ $item['label'] }}</span>
                                    <span class="sidebar-link-caret"><i class="bi bi-chevron-down"></i></span>
                                </button>
                            @endif

                            @if($hasChildren)
                                <div class="sidebar-submenu {{ $item['active'] ? 'open' : '' }}" id="{{ $item['id'] }}">
                                    @foreach($item['children'] as $child)
                                        <a class="sidebar-sublink {{ $child['active'] ? 'active' : '' }}" href="{{ $child['url'] ?? $routeUrl($child['route']) }}">
                                            <span class="sidebar-sublink-dot"></span>
                                            <span>{{ $child['label'] }}</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    @endforeach
                </div>
            @endforeach
        </nav>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('appSidebar');
    const menuToggleButton = document.getElementById('sidebarMenuToggle');
    const backdrop = document.getElementById('sidebarBackdrop');
    const groupToggles = document.querySelectorAll('.sidebar-group-toggle');

    function setSidebarOpen(isOpen) {
        if (!sidebar || !backdrop) {
            return;
        }

        sidebar.classList.toggle('show', isOpen);
        backdrop.classList.toggle('show', isOpen);
    }

    function toggleSidebarMenu() {
        if (!sidebar) {
            return;
        }

        if (window.innerWidth <= 991) {
            setSidebarOpen(!sidebar.classList.contains('show'));
            return;
        }

        sidebar.classList.toggle('sidebar-collapsed');
    }

    groupToggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            const targetId = toggle.getAttribute('data-target');
            const menu = document.getElementById(targetId);

            if (!menu) {
                return;
            }

            const willOpen = !menu.classList.contains('open');
            menu.classList.toggle('open', willOpen);
            toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
        });
    });

    if (menuToggleButton) {
        menuToggleButton.addEventListener('click', function () {
            toggleSidebarMenu();
        });
    }

    if (backdrop) {
        backdrop.addEventListener('click', function () {
            setSidebarOpen(false);
        });
    }

    window.addEventListener('resize', function () {
        if (window.innerWidth > 991) {
            setSidebarOpen(false);
        }
    });
});
</script>
