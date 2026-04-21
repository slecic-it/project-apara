@php
    $currentRoute = request()->route()?->getName();
    $currentMarketingFilter = trim((string) (request('marketing') ?? data_get($applicationView ?? null, 'marketing.nav_filter', '')));
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
                    'route' => 'all-applications',
                    'active' => $isActive('pending', 'accepted', 'approved', 'rejected', 'all-applications'),
                    'children' => [],
                ],
            ],
        ],
        [
            'label' => 'Marketing Department',
            'items' => [
                [
                    'type' => 'group',
                    'id' => 'marketingProcessMenu',
                    'label' => 'Marketing Process',
                    'icon' => 'bi-megaphone',
                    'route' => 'notifications',
                    'active' => $isActive('notifications', 'all-applications', 'application.show'),
                    'children' => [
                        ['label' => 'Received Applications', 'route' => 'notifications', 'active' => $isActive('notifications')],
                        ['label' => 'Review Application', 'route' => 'all-applications', 'url' => route('all-applications', ['marketing' => 'review']), 'active' => $isActive('all-applications', 'application.show') && $currentMarketingFilter === 'review'],
                        ['label' => '1st Approval + Letter', 'route' => 'all-applications', 'url' => route('all-applications', ['marketing' => 'first_approval']), 'active' => $isActive('all-applications', 'application.show') && $currentMarketingFilter === 'first_approval'],
                        ['label' => '2nd Approval + Letter', 'route' => 'all-applications', 'url' => route('all-applications', ['marketing' => 'second_approval']), 'active' => $isActive('all-applications', 'application.show') && $currentMarketingFilter === 'second_approval'],
                        ['label' => 'Hold + Comment to Bank', 'route' => 'all-applications', 'url' => route('all-applications', ['marketing' => 'hold']), 'active' => $isActive('all-applications', 'application.show') && $currentMarketingFilter === 'hold'],
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

@include('layouts.sidebar-shell', ['navSections' => $navSections, 'isBankUser' => false])
