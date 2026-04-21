@extends('layouts.app')

@section('title', 'All Applications')

@section('content')
@php
    $isBankUser = !empty(session('bank_profile'));
    $showRoute = $isBankUser ? 'bank.application.show' : 'application.show';
@endphp
<div class="page-shell">
    <div class="page-header">
        <div class="page-header-content">
            <h3 class="page-heading">{{ $filterLabel ?? 'All Applications' }}</h3>
            <p class="page-subtitle">Application records shown here follow the currently logged-in workspace context.</p>
        </div>
    </div>

    <div class="table-card p-3">
        <div class="table-title-row">
            <div>
                <h5>Application Records</h5>
                <span class="table-meta">Use the quick status filters to narrow the visible applications.</span>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-primary btn-sm all-applications-filter active" data-status="all">All</button>
                <button type="button" class="btn btn-primary btn-sm all-applications-filter" data-status="pending">Pending</button>
                <button type="button" class="btn btn-primary btn-sm all-applications-filter" data-status="approved">Approved</button>
                <button type="button" class="btn btn-primary btn-sm all-applications-filter" data-status="accepted">Accepted</button>
                <button type="button" class="btn btn-primary btn-sm all-applications-filter" data-status="rejected">Rejected</button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mt-2 application-overview-table">
                <thead class="table-light">
                    <tr>
                        <th>App No</th>
                        <th>Proposal</th>
                        <th>Customer</th>
                        <th>ID/Passport</th>
                        <th>Country</th>
                        <th>Employment Type</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="allApplicationsTable">
                    @forelse($applications as $application)
                        @php $status = strtolower($application->status ?? 'pending'); @endphp
                        <tr data-status="{{ $status }}">
                            <td>
                                <a
                                    href="{{ route($showRoute, ['id' => $application->id, 'marketing' => $marketingFilter ?: null]) }}"
                                    class="application-number-link"
                                >
                                    APP-{{ str_pad((string) ($application->id ?? 0), 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td>
                                <a
                                    href="{{ route($showRoute, ['id' => $application->id, 'marketing' => $marketingFilter ?: null]) }}"
                                    class="application-number-link"
                                >
                                    {{ $application->proposal_no ?? '-' }}
                                </a>
                            </td>
                            <td>{{ $application->full_name ?? '-' }}</td>
                            <td>{{ $application->nic ?? ($application->passport_no ?? '-') }}</td>
                            <td>{{ $application->country_name ?? '-' }}</td>
                            <td>{{ $application->employment_type ?? '-' }}</td>
                            <td>
                                <span class="badge
                                    @if ($status === 'approved') bg-success
                                    @elseif ($status === 'accepted') bg-info
                                    @elseif ($status === 'rejected') bg-danger
                                    @else bg-warning text-dark
                                    @endif">
                                    {{  (str_replace('_', ' ', $status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No applications found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.all-applications-filter');
    const rows = document.querySelectorAll('#allApplicationsTable tr[data-status]');

    filterButtons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedStatus = (button.dataset.status || 'all').toLowerCase();

            filterButtons.forEach(function (item) {
                item.classList.remove('active');
            });

            button.classList.add('active');

            rows.forEach(function (row) {
                const rowStatus = (row.dataset.status || '').toLowerCase();
                row.style.display = selectedStatus === 'all' || rowStatus === selectedStatus ? '' : 'none';
            });
        });
    });
});
</script>
@endpush
