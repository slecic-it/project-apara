@php
    $currentRoute = request()->route()?->getName();
    $routeExists = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
    $routeUrl = fn ($name) => $routeExists($name) ? route($name) : '#';
    $isActive = fn (...$names) => in_array($currentRoute, $names, true);
    $navSections = [
        [
            'label' => 'Operations',
            'items' => [
                [
                    'type' => 'link',
                    'label' => 'Overview',
                    'icon' => 'bi-grid-1x2',
                    'route' => 'dashboard',
                    'active' => $isActive('dashboard'),
                ],
            ],
        ],
        [
            'label' => 'Insights',
            'items' => [
                [
                    'type' => 'group',
                    'id' => 'appMenu',
                    'label' => 'Applications',
                    'icon' => 'bi-folder2-open',
                    'active' => $isActive('pending', 'accepted', 'approved', 'rejected', 'all-applications'),
                    'children' => [
                        ['label' => 'Pending', 'route' => 'pending', 'active' => $isActive('pending')],
                        ['label' => 'Accepted', 'route' => 'accepted', 'active' => $isActive('accepted')],
                        ['label' => 'Approved', 'route' => 'approved', 'active' => $isActive('approved')],
                        ['label' => 'Rejected', 'route' => 'rejected', 'active' => $isActive('rejected')],
                        ['label' => 'All Applications', 'route' => 'all-applications', 'active' => $isActive('all-applications')],
                    ],
                ],
                [
                    'type' => 'group',
                    'id' => 'manageMenu',
                    'label' => 'Management',
                    'icon' => 'bi-briefcase',
                    'active' => $isActive('banks', 'employeelist', 'reports', 'settings'),
                    'children' => [
                        ['label' => 'Banks', 'route' => 'banks', 'active' => $isActive('banks')],
                        ['label' => 'Employee List', 'route' => 'employeelist', 'active' => $isActive('employeelist')],
                        ['label' => 'Reports Dashboard', 'route' => 'reports', 'active' => $isActive('reports')],
                        ['label' => 'Settings', 'route' => 'settings', 'active' => $isActive('settings')],
                    ],
                ],
                [
                    'type' => 'group',
                    'id' => 'financeMenu',
                    'label' => 'Finance',
                    'icon' => 'bi-cash-stack',
                    'active' => $isActive('invoice', 'fee_due', 'receipt', 'history', 'payments'),
                    'children' => [
                        ['label' => 'Invoice', 'route' => 'invoice', 'active' => $isActive('invoice')],
                        ['label' => 'Fee Due', 'route' => 'fee_due', 'active' => $isActive('fee_due')],
                        ['label' => 'Receipt', 'route' => 'receipt', 'active' => $isActive('receipt')],
                        ['label' => 'History', 'route' => 'history', 'active' => $isActive('history')],
                        ['label' => 'Payments', 'route' => 'payments', 'active' => $isActive('payments')],
                    ],
                ],
            ],
        ],
        [
            'label' => 'Help',
            'items' => [
                [
                    'type' => 'group',
                    'id' => 'reportsMenu',
                    'label' => 'Reports & Analytics',
                    'icon' => 'bi-bar-chart-line',
                    'active' => $isActive('monthly-report', 'yearly-report', 'performance-report', 'audit-trail'),
                    'children' => [
                        ['label' => 'Monthly Report', 'route' => 'monthly-report', 'active' => $isActive('monthly-report')],
                        ['label' => 'Yearly Report', 'route' => 'yearly-report', 'active' => $isActive('yearly-report')],
                        ['label' => 'Performance Report', 'route' => 'performance-report', 'active' => $isActive('performance-report')],
                        ['label' => 'Audit Trail', 'route' => 'audit-trail', 'active' => $isActive('audit-trail')],
                    ],
                ],
            ],
        ],
        [
            'label' => null,
            'items' => [
                [
                    'type' => 'group',
                    'id' => 'supportMenu',
                    'label' => 'Support',
                    'icon' => 'bi-life-preserver',
                    'active' => $isActive('tickets', 'faq', 'contact'),
                    'children' => [
                        ['label' => 'Support Tickets', 'route' => 'tickets', 'active' => $isActive('tickets')],
                        ['label' => 'FAQ', 'route' => 'faq', 'active' => $isActive('faq')],
                        ['label' => 'Contact Support', 'route' => 'contact', 'active' => $isActive('contact')],
                    ],
                ],
                [
                    'type' => 'link',
                    'label' => 'Profile',
                    'icon' => 'bi-person-circle',
                    'route' => 'profile',
                    'active' => $isActive('profile'),
                ],
                [
                    'type' => 'link',
                    'label' => 'Logout',
                    'icon' => 'bi-box-arrow-right',
                    'route' => 'logout',
                    'active' => false,
                    'logout' => true,
                ],
            ],
        ],
    ];
@endphp

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
                                class="sidebar-link {{ $item['active'] ? 'active' : '' }}"
                                href="{{ $routeUrl($item['route']) }}"
                            >
                                @if(!empty($item['icon']))
                                    <span class="sidebar-link-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                                @endif
                                <span class="sidebar-link-label">{{ $item['label'] }}</span>
                            </a>
                        @else
                            <button
                                class="sidebar-link sidebar-group-toggle {{ $item['active'] ? 'active' : '' }}"
                                type="button"
                                data-target="{{ $item['id'] }}"
                                aria-expanded="{{ $item['active'] ? 'true' : 'false' }}"
                            >
                                <span class="sidebar-link-icon"><i class="bi {{ $item['icon'] }}"></i></span>
                                <span class="sidebar-link-label">{{ $item['label'] }}</span>
                                <span class="sidebar-link-caret"><i class="bi bi-chevron-down"></i></span>
                            </button>

                            <div class="sidebar-submenu {{ $item['active'] ? 'open' : '' }}" id="{{ $item['id'] }}">
                                @foreach($item['children'] as $child)
                                    <a class="sidebar-sublink {{ $child['active'] ? 'active' : '' }}" href="{{ $routeUrl($child['route']) }}">
                                        <span class="sidebar-sublink-dot"></span>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
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
