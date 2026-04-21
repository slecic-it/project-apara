@extends('layouts.app')

@section('title', 'Bank Dashboard - APARA System')

@section('content')
@php
    $bankBrand = session('bank_brand');
@endphp
<div class="page-shell">
    <section class="bank-dashboard-hero">
        <div class="bank-dashboard-hero-copy">
            <span class="dashboard-eyebrow">{{ $bankBrand['display_name'] ?? 'Bank Operations Workspace' }}</span>
            <h3 class="dashboard-title mb-2">Keep branch submissions moving without losing sight of application status.</h3>
            <p class="dashboard-subtitle mb-0">Monitor incoming APARA applications, spot stalled work early, and jump straight into the next bank-side action.</p>
        </div>

        <div class="bank-dashboard-hero-actions">
            @foreach ($summaryCards as $card)
                <div class="bank-hero-stat">
                    <div class="bank-hero-stat-icon">
                        <i class="bi {{ $card['icon'] }}"></i>
                    </div>
                    <div>
                        <span>{{ $card['label'] }}</span>
                        <strong>{{ $card['value'] }}</strong>
                        <small>{{ $card['hint'] }}</small>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @if (!empty($bankProfile))
        <section class="card data-card bank-dashboard-panel mb-4">
            <div class="table-title-row">
                <div>
                    <h5>Registered Bank Details</h5>
                    <span class="table-meta">Details for the bank account currently signed in.</span>
                </div>
            </div>

            <div class="profile-detail-grid">
                <div class="profile-detail-item">
                    <small>Bank Name</small>
                    <strong>{{ $bankProfile['bank_name'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Bank Code</small>
                    <strong>{{ $bankProfile['bank_code'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Branch Name</small>
                    <strong>{{ $bankProfile['branch_name'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Branch Grade</small>
                    <strong>{{ $bankProfile['branch_grade'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Province</small>
                    <strong>{{ $bankProfile['province'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Login Email</small>
                    <strong>{{ $bankProfile['email'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Telephone</small>
                    <strong>{{ $bankProfile['tel'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Bank Record ID</small>
                    <strong>{{ $bankProfile['id'] ?? '-' }}</strong>
                </div>
            </div>
        </section>
    @endif

    <div class="status-grid mb-4">
        <div class="status-box pending">
            <h6>Pending</h6>
            <h3>{{ $statusCounts['pending'] ?? 0 }}</h3>
            <p>Awaiting first review</p>
        </div>
        <div class="status-box accepted">
            <h6>Accepted</h6>
            <h3>{{ $statusCounts['accepted'] ?? 0 }}</h3>
            <p>Validated and moved ahead</p>
        </div>
        <div class="status-box approved">
            <h6>Approved</h6>
            <h3>{{ $statusCounts['approved'] ?? 0 }}</h3>
            <p>Ready for finance coordination</p>
        </div>
        <div class="status-box rejected">
            <h6>Rejected</h6>
            <h3>{{ $statusCounts['rejected'] ?? 0 }}</h3>
            <p>Returned for correction</p>
        </div>
        <div class="status-box payment">
            <h6>Payment Pending</h6>
            <h3>{{ $statusCounts['payment_pending'] ?? 0 }}</h3>
            <p>Waiting for settlement</p>
        </div>
        <div class="status-box completed">
            <h6>Completed</h6>
            <h3>{{ $statusCounts['completed'] ?? 0 }}</h3>
            <p>Processed successfully</p>
        </div>
        <div class="status-box finalized">
            <h6>Finalized</h6>
            <h3>{{ $statusCounts['finalized'] ?? 0 }}</h3>
            <p>Archived and closed</p>
        </div>
    </div>

    <div class="bank-dashboard-grid mb-4">
        <section class="card data-card bank-dashboard-panel">
            <div class="table-title-row">
                <div>
                    <h5>Bank Workflow Focus</h5>
                    <span class="table-meta">A simple stage view for what the branch and bank team should watch next.</span>
                </div>
            </div>

            <div class="bank-workflow-list">
                @foreach ($workflowSteps as $step)
                    <article class="bank-workflow-item bank-workflow-{{ $step['state'] }}">
                        <div class="bank-workflow-count">{{ $step['count'] }}</div>
                        <div>
                            <strong>{{ $step['title'] }}</strong>
                            <p>{{ $step['description'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="card data-card bank-dashboard-panel">
            <div class="table-title-row">
                <div>
                    <h5>Application Status</h5>
                    <span class="table-meta">Quick signals to help the branch team prioritize effort.</span>
                </div>
            </div>

            <div class="bank-review-grid">
                @foreach ($reviewBuckets as $bucket)
                    <div class="bank-review-card">
                        <small>{{ $bucket['label'] }}</small>
                        <strong>{{ $bucket['value'] }}</strong>
                        <span>{{ $bucket['hint'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    </div>

    <div class="bank-dashboard-grid bank-dashboard-grid-tight mb-4">
        <section class="card data-card bank-dashboard-panel">
            <div class="table-title-row">
                <div>
                    <h5>Top Employment Destinations</h5>
                    <span class="table-meta">Where current bank-side applications are headed most often.</span>
                </div>
            </div>

            <div class="bank-destination-list">
                @forelse ($destinationSummary as $destination)
                    <div class="bank-destination-item">
                        <div class="bank-destination-copy">
                            <strong>{{ $destination['country'] }}</strong>
                            <span>{{ $destination['count'] }} application{{ $destination['count'] === 1 ? '' : 's' }}</span>
                        </div>
                        <div class="bank-destination-progress">
                            <div style="width: {{ $destination['share'] }}%"></div>
                        </div>
                        <b>{{ $destination['share'] }}%</b>
                    </div>
                @empty
                    <p class="text-muted mb-0">No destination data is available yet.</p>
                @endforelse
            </div>
        </section>

        <section class="card data-card bank-dashboard-panel">
            <div class="table-title-row">
                <div>
                    <h5>Recent Activity</h5>
                    <span class="table-meta">The latest submitted or updated applications visible to the bank side.</span>
                </div>
            </div>

            <div class="bank-activity-list">
                @forelse ($recentActivity as $item)
                    <a href="{{ route('bank.application.show', $item['id']) }}" class="bank-activity-item">
                        <div class="bank-activity-main">
                            <strong>{{ $item['app_no'] }}</strong>
                            <span>{{ $item['full_name'] }} • {{ $item['country_name'] }}</span>
                        </div>
                        <div class="bank-activity-meta">
                            <span class="badge
                                @if ($item['status'] === 'approved') bg-success
                                @elseif ($item['status'] === 'accepted') bg-info
                                @elseif ($item['status'] === 'rejected') bg-danger
                                @elseif (in_array($item['status'], ['completed', 'finalized'])) bg-primary
                                @elseif ($item['status'] === 'payment_pending') bg-secondary
                                @else bg-warning text-dark
                                @endif">
                                {{ $item['status_label'] }}
                            </span>
                            <small>{{ $item['created_at'] }}</small>
                        </div>
                    </a>
                @empty
                    <p class="text-muted mb-0">No recent activity to show yet.</p>
                @endforelse
            </div>
        </section>
    </div>

    <section class="table-card p-3">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="section-header mb-3">
            <div>
                <h5 class="mb-1">Applications List</h5>
                <p class="text-muted mb-0">Open any application to review customer details, branch submission data, and current processing status.</p>
            </div>
            <span class="table-count-pill">Total: {{ $applications->count() }} application{{ $applications->count() === 1 ? '' : 's' }}</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mt-2 application-overview-table">
                <thead class="table-light">
                    <tr>
                        <th>App No</th>
                        <th>Proposal</th>
                        <th>Customer</th>
                        <th>ID</th>
                        <th>Destination</th>
                        <th>Employment</th>
                        <th>Status</th>
                        <th>Submitted</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($applications as $application)
                        @php
                            $status = strtolower($application->status ?? 'pending');
                            $statusClass = match ($status) {
                                'accepted' => 'bg-info',
                                'approved' => 'bg-success',
                                'rejected' => 'bg-danger',
                                'completed', 'finalized' => 'bg-primary',
                                'payment_pending' => 'bg-secondary',
                                default => 'bg-warning text-dark',
                            };
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('bank.application.show', $application->id) }}" class="application-number-link">
                                    APP-{{ str_pad((string) $application->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td>{{ $application->proposal_no ?? '-' }}</td>
                            <td>{{ $application->full_name ?? '-' }}</td>
                            <td>{{ $application->nic ?? ($application->passport_no ?? '-') }}</td>
                            <td>{{ $application->country_name ?? '-' }}</td>
                            <td>{{ $application->employment_type ?? '-' }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span></td>
                            <td>{{ !empty($application->created_at) ? \Illuminate\Support\Carbon::parse($application->created_at)->format('Y-m-d') : '-' }}</td>
                            <td class="action-buttons">
                                <a href="{{ route('bank.application.show', $application->id) }}" class="text-decoration-none text-muted" title="View application">
                                    <i class="bi bi-eye me-2"></i>
                                </a>
                                <a href="{{ route('bank.application.create') }}" class="text-decoration-none text-muted" title="Create new application">
                                    <i class="bi bi-plus-square"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No applications have been submitted yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
