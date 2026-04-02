@extends('layouts.app')

@section('title', 'Application Details - APARA System')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div class="page-header-content">
            <h3 class="page-heading">Application Details</h3>
            <p class="page-subtitle">This is the saved application record opened from the application number.</p>
        </div>
        <div class="page-header-actions mt-3 mt-md-0">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <div class="table-card p-4">
        <div class="section-header mb-3">
            <div>
                <h5 class="mb-1">{{ $applicationView['app_no'] }}</h5>
                <p class="text-muted mb-0">Proposal {{ $applicationView['proposal_no'] }}</p>
            </div>
            <div>
                <span class="badge {{ $applicationView['status_class'] }}">{{ $applicationView['status_label'] }}</span>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="border rounded p-3 h-100 bg-white">
                    <h6 class="mb-3">Applicant</h6>
                    <div class="mb-2"><strong>Full Name:</strong> {{ $applicationView['full_name'] }}</div>
                    <div class="mb-2"><strong>Name with Initials:</strong> {{ $applicationView['name_with_initials'] }}</div>
                    <div class="mb-2"><strong>NIC:</strong> {{ $applicationView['nic'] }}</div>
                    <div class="mb-2"><strong>Passport No:</strong> {{ $applicationView['passport_no'] }}</div>
                    <div class="mb-2"><strong>Address:</strong> {{ $applicationView['address'] }}</div>
                    <div class="mb-2"><strong>District:</strong> {{ $applicationView['district_name'] }}</div>
                    <div class="mb-2"><strong>Division Secretariat:</strong> {{ $applicationView['division_sec_office'] }}</div>
                    <div><strong>Telephone:</strong> {{ $applicationView['tel_no'] }}</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border rounded p-3 h-100 bg-white">
                    <h6 class="mb-3">Employment</h6>
                    <div class="mb-2"><strong>Country of Employment:</strong> {{ $applicationView['country_name'] }}</div>
                    <div class="mb-2"><strong>Employment Type:</strong> {{ $applicationView['employment_type'] }}</div>
                    <div class="mb-2"><strong>Recruitment Agency:</strong> {{ $applicationView['recruitment_agency_name'] }}</div>
                    <div class="mb-2"><strong>Agency Address:</strong> {{ $applicationView['recruitment_agency_address'] }}</div>
                    <div><strong>Labour Licence No:</strong> {{ $applicationView['labour_license_no'] }}</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border rounded p-3 h-100 bg-white">
                    <h6 class="mb-3">Workflow</h6>
                    <div class="mb-2"><strong>Status:</strong> {{ $applicationView['status_label'] }}</div>
                    <div class="mb-2"><strong>Entered By:</strong> {{ $applicationView['entered_by'] }}</div>
                    <div class="mb-2"><strong>Approved By:</strong> {{ $applicationView['approved_by'] }}</div>
                    <div class="mb-2"><strong>Approved At:</strong> {{ $applicationView['approved_at'] }}</div>
                    <div><strong>Created At:</strong> {{ $applicationView['created_at'] }}</div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="border rounded p-3 h-100 bg-white">
                    <h6 class="mb-3">System Reference</h6>
                    <div class="mb-2"><strong>Application No:</strong> {{ $applicationView['app_no'] }}</div>
                    <div class="mb-2"><strong>Proposal No:</strong> {{ $applicationView['proposal_no'] }}</div>
                    <div><strong>Last Updated:</strong> {{ $applicationView['updated_at'] }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
