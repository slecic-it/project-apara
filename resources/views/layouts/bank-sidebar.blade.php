@php
    $currentRoute = request()->route()?->getName();
    $routeExists = fn ($name) => \Illuminate\Support\Facades\Route::has($name);
    $routeUrl = fn ($name) => $routeExists($name) ? route($name) : '#';
    $isActive = fn (...$names) => in_array($currentRoute, $names, true);
    $navSections = [
        [
            'label' => 'Bank Workspace',
            'items' => [
                [
                    'type' => 'link',
                    'label' => 'Overview',
                    'icon' => 'bi-grid-1x2',
                    'route' => 'bank.dashboard',
                    'active' => $isActive('bank.dashboard'),
                ],
            ],
        ],
        [
            'label' => 'Applications',
            'items' => [
                [
                    'type' => 'group',
                    'id' => 'bankAppMenu',
                    'label' => 'All Applications',
                    'icon' => 'bi-folder2-open',
                    'route' => 'bank.all-applications',
                    'active' => $isActive('bank.pending', 'bank.accepted', 'bank.approved', 'bank.rejected', 'bank.all-applications'),
                    'children' => [],
                ],
                [
                    'type' => 'link',
                    'label' => 'New Application',
                    'icon' => 'bi-plus-square',
                    'route' => 'bank.application.create',
                    'active' => $isActive('bank.application.create'),
                ],
                [
                    'type' => 'link',
                    'label' => 'Notifications',
                    'icon' => 'bi-bell',
                    'route' => 'bank.notifications',
                    'active' => $isActive('bank.notifications'),
                ],
                [
                    'type' => 'link',
                    'label' => 'Blacklist',
                    'icon' => 'bi-ban',
                    'route' => 'bank.blacklist',
                    'active' => $isActive('bank.blacklist'),
                ],
            ],
        ],
        [
            'label' => 'Finance',
            'items' => [
                [
                    'type' => 'link',
                    'label' => 'Payment History',
                    'icon' => 'bi-clock-history',
                    'route' => 'bank.history',
                    'active' => $isActive('bank.history'),
                ],
                [
                    'type' => 'group',
                    'id' => 'bankReportsMenu',
                    'label' => 'Reports & Analytics',
                    'icon' => 'bi-bar-chart-line',
                    'active' => $isActive('bank.reports', 'bank.monthly-report', 'bank.yearly-report', 'bank.performance-report', 'bank.audit-trail'),
                    'children' => [
                        ['label' => 'Reports Dashboard', 'route' => 'bank.reports', 'active' => $isActive('bank.reports')],
                        ['label' => 'Monthly Report', 'route' => 'bank.monthly-report', 'active' => $isActive('bank.monthly-report')],
                        ['label' => 'Yearly Report', 'route' => 'bank.yearly-report', 'active' => $isActive('bank.yearly-report')],
                        ['label' => 'Performance Report', 'route' => 'bank.performance-report', 'active' => $isActive('bank.performance-report')],
                        ['label' => 'Audit Trail', 'route' => 'bank.audit-trail', 'active' => $isActive('bank.audit-trail')],
                    ],
                ],
            ],
        ],
        [
            'label' => 'Support',
            'items' => [
                [
                    'type' => 'group',
                    'id' => 'bankSupportMenu',
                    'label' => 'Support',
                    'icon' => 'bi-life-preserver',
                    'active' => $isActive('bank.tickets', 'bank.faq', 'bank.contact'),
                    'children' => [
                        ['label' => 'Support Tickets', 'route' => 'bank.tickets', 'active' => $isActive('bank.tickets')],
                        ['label' => 'FAQ', 'route' => 'bank.faq', 'active' => $isActive('bank.faq')],
                        ['label' => 'Contact Support', 'route' => 'bank.contact', 'active' => $isActive('bank.contact')],
                    ],
                ],
                [
                    'type' => 'link',
                    'label' => 'Profile',
                    'icon' => 'bi-person-circle',
                    'route' => 'bank.profile',
                    'active' => $isActive('bank.profile'),
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

@include('layouts.sidebar-shell', ['navSections' => $navSections, 'isBankUser' => true])
