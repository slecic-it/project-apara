@extends('layouts.app')

@section('title', 'Bank Dashboard - APARA System')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div class="page-header-content">
            <h3 class="page-heading">Bank Dashboard</h3>
            <p class="page-subtitle">Real-time APARA system monitoring for bank-side application activity.</p>
        </div>
    </div>

    <div class="status-grid mb-4">
        <div class="status-box pending"><h6>Pending</h6><h3 id="pendingCount">{{ $statusCounts['pending'] ?? 0 }}</h3><p>Awaiting first review</p></div>
        <div class="status-box accepted"><h6>Accepted</h6><h3 id="acceptedCount">{{ $statusCounts['accepted'] ?? 0 }}</h3><p>Ready for processing</p></div>
        <div class="status-box approved"><h6>Approved</h6><h3 id="approvedCount">{{ $statusCounts['approved'] ?? 0 }}</h3><p>Cleared successfully</p></div>
        <div class="status-box rejected"><h6>Rejected</h6><h3 id="rejectedCount">{{ $statusCounts['rejected'] ?? 0 }}</h3><p>Need follow-up action</p></div>
        <div class="status-box payment"><h6>Payment Pending</h6><h3 id="paymentPendingCount">{{ $statusCounts['payment_pending'] ?? 0 }}</h3><p>Waiting for settlement</p></div>
        <div class="status-box completed"><h6>Completed Payments</h6><h3 id="completedCount">{{ $statusCounts['completed'] ?? 0 }}</h3><p>Finance completed</p></div>
        <div class="status-box finalized"><h6>Finalized</h6><h3 id="finalizedCount">{{ $statusCounts['finalized'] ?? 0 }}</h3><p>Archived and closed</p></div>
    </div>

    <div class="table-card p-3">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="section-header mb-3">
            <div>
                <h5 class="mb-1">Applications List</h5>
                <p class="text-muted mb-0">Review the latest bank-side applications and take action directly from the table.</p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="table-count-pill">Total: <span id="totalAppCount">{{ $applications->count() }}</span> application{{ $applications->count() === 1 ? '' : 's' }}</span>
                <a href="{{ route('application.create') }}" class="btn btn-primary dashboard-cta">
                    <i class="bi bi-plus-lg me-2"></i>New Application
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mt-2 application-overview-table">
                <thead class="table-light">
                    <tr>
                        <th>App No</th>
                        <th>Proposal</th>
                        <th>Name</th>
                        <th>ID</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="applicationTable">
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
                                <a href="{{ route('application.show', $application->id) }}" class="application-number-link">
                                    APP-{{ str_pad((string) $application->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td>{{ $application->proposal_no ?? '-' }}</td>
                            <td>{{ $application->full_name ?? '-' }}</td>
                            <td>{{ $application->nic ?? '-' }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ ucwords(str_replace('_', ' ', $status)) }}</span></td>
                            <td class="action-buttons text-muted">
                                <a href="{{ route('application.show', $application->id) }}" class="text-decoration-none text-muted" title="View">
                                    <i class="bi bi-eye me-2"></i>
                                </a>
                                <i class="bi bi-pencil-square me-2" title="Edit"></i>
                                <i class="bi bi-credit-card" title="Payment"></i>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No applications have been saved yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
