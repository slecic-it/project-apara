@extends('layouts.app')

@section('title', 'All Applications')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div class="page-header-content">
            <h3 class="page-heading">All Applications</h3>
            <p class="page-subtitle">Every submitted application is collected here so the full list can be reviewed in one place.</p>
        </div>
    </div>

    <div class="table-card p-3">
        <div class="table-title-row">
            <h5>Application Records</h5>
            <span class="table-meta">Latest submissions from the dashboard will appear here automatically.</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mt-2 application-overview-table">
                <thead class="table-light">
                    <tr>
                        <th>App No</th>
                        <th>Proposal</th>
                        <th>Full Name</th>
                        <th>ID/Passport</th>
                        <th>Country</th>
                        <th>Employment Type</th>
                        <th>Guarantee Amount</th>
                        <th>Pre Departure</th>
                        <th>Recruitment Agency</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="allApplicationsTable"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const storageKey = 'slecic_all_applications';
    const tableBody = document.getElementById('allApplicationsTable');

    if (!tableBody) {
        return;
    } 

    const defaultApplications = [
        {
            appNo: 'APP-0012',
            proposal: 'PR-556',
            fullName: 'John Doe',
            idNumber: '902134567V',
            country: 'Saudi Arabia',
            employmentType: 'Skilled Worker',
            guaranteeAmount: '500,000',
            preDepartureAmount: '100,000',
            agencyName: 'Global Manpower',
            status: 'Pending'
        },
        {
            appNo: 'APP-0015',
            proposal: 'PR-892',
            fullName: 'Jane Smith',
            idNumber: '823456789V',
            country: 'UAE',
            employmentType: 'Driver',
            guaranteeAmount: '750,000',
            preDepartureAmount: '150,000',
            agencyName: 'Overseas Recruiters',
            status: 'Accepted'
        },
        {
            appNo: 'APP-0021',
            proposal: 'PR-124',
            fullName: 'Robert Johnson',
            idNumber: '765432198V',
            country: 'Qatar',
            employmentType: 'Engineer',
            guaranteeAmount: '1,200,000',
            preDepartureAmount: '250,000',
            agencyName: 'Tech Recruiters',
            status: 'Approved'
        }
    ];

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function statusBadgeClass(status) {
        switch ((status || '').toLowerCase()) {
            case 'accepted':
                return 'bg-info';
            case 'approved':
                return 'bg-success';
            case 'rejected':
                return 'bg-danger';
            default:
                return 'bg-warning';
        }
    }

    let storedApplications = [];

    try {
        storedApplications = JSON.parse(localStorage.getItem(storageKey) || '[]');
    } catch (error) {
        storedApplications = [];
    }

    const applications = [...defaultApplications, ...storedApplications];

    tableBody.innerHTML = applications.map(function (application) {
        return `
            <tr>
                <td>${escapeHtml(application.appNo)}</td>
                <td>${escapeHtml(application.proposal)}</td>
                <td>${escapeHtml(application.fullName)}</td>
                <td>${escapeHtml(application.idNumber)}</td>
                <td>${escapeHtml(application.country)}</td>
                <td>${escapeHtml(application.employmentType)}</td>
                <td>${escapeHtml(application.guaranteeAmount)}</td>
                <td>${escapeHtml(application.preDepartureAmount)}</td>
                <td>${escapeHtml(application.agencyName)}</td>
                <td><span class="badge ${statusBadgeClass(application.status)}">${escapeHtml(application.status)}</span></td>
            </tr>
        `;
    }).join('');
});
</script>
@endpush
