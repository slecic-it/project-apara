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
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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
                    <div class="mb-2"><strong>Bank:</strong> {{ $applicationView['selected_bank_name'] }}</div>
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

            <div class="col-12">
                <div class="border rounded p-3 h-100 bg-white">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <h6 class="mb-1">Marketing Process</h6>
                            <p class="text-muted mb-0">Marketing receives the application, then either gives 1st and 2nd approvals with acknowledgement letters or places it on hold with a bank-facing comment.</p>
                        </div>
                        <span class="badge bg-dark">{{ $applicationView['marketing']['stage_label'] }}</span>
                    </div>

                    <div class="row g-3 mb-3">
                        @foreach ($applicationView['marketing']['steps'] as $step)
                            @php
                                $stepBorderClass = match ($step['state']) {
                                    'complete' => 'border-success',
                                    'current' => 'border-primary',
                                    default => 'border-light',
                                };
                                $stepBadgeClass = match ($step['state']) {
                                    'complete' => 'bg-success',
                                    'current' => 'bg-primary',
                                    default => 'bg-secondary',
                                };
                                $stepLabel = match ($step['state']) {
                                    'complete' => 'Completed',
                                    'current' => 'Current',
                                    default => 'Pending',
                                };
                            @endphp
                            <div class="col-md-3">
                                <div class="border rounded p-3 h-100 {{ $stepBorderClass }}">
                                    <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                                        <strong>{{ $step['title'] }}</strong>
                                        <span class="badge {{ $stepBadgeClass }}">{{ $stepLabel }}</span>
                                    </div>
                                    <div class="text-muted small">{{ $step['description'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <strong class="d-block mb-2">1st Approval</strong>
                                <div class="mb-1"><strong>Officer:</strong> {{ $applicationView['marketing']['first_approved_by'] }}</div>
                                <div><strong>Date:</strong> {{ $applicationView['marketing']['first_approved_at'] }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 h-100">
                                <strong class="d-block mb-2">2nd Approval</strong>
                                <div class="mb-1"><strong>Officer:</strong> {{ $applicationView['marketing']['second_approved_by'] }}</div>
                                <div><strong>Date:</strong> {{ $applicationView['marketing']['second_approved_at'] }}</div>
                            </div>
                        </div>
                    </div>

                    @if ($applicationView['marketing']['ack_letter'] !== '')
                        <div class="alert alert-success">
                            <strong>Acknowledgement Letter to Bank</strong>
                            <div class="mt-2">{{ $applicationView['marketing']['ack_letter'] }}</div>
                        </div>
                    @endif

                    @if ($applicationView['marketing']['comment'] !== '')
                        <div class="alert {{ $applicationView['marketing']['stage'] === 'hold' ? 'alert-warning' : 'alert-info' }} mb-0">
                            <strong>Marketing Comment to Bank</strong>
                            <div class="mt-2">{{ $applicationView['marketing']['comment'] }}</div>
                        </div>
                    @endif

                    @if (!$applicationView['is_bank_context'])
                        <div class="row g-3 mt-1">
                            <div class="col-lg-4">
                                <form action="{{ route('application.marketing.process', ['id' => $applicationView['id']]) }}" method="POST" class="border rounded p-3 h-100 bg-light">
                                    @csrf
                                    <h6 class="mb-2">1st Approval</h6>
                                    <p class="text-muted small mb-3">Approve the received application and send the first acknowledgement letter to the bank.</p>
                                    <div class="mb-3">
                                        <label for="marketingFirstApprovalNote" class="form-label">Acknowledgement Letter</label>
                                        <textarea name="note" id="marketingFirstApprovalNote" rows="5" class="form-control" placeholder="Enter the acknowledgement letter to be sent to the bank."></textarea>
                                    </div>
                                    <button type="submit" name="action" value="first_approve" class="btn btn-info text-white w-100">Give 1st Approval</button>
                                </form>
                            </div>

                            <div class="col-lg-4">
                                <form action="{{ route('application.marketing.process', ['id' => $applicationView['id']]) }}" method="POST" class="border rounded p-3 h-100 bg-light">
                                    @csrf
                                    <h6 class="mb-2">2nd Approval</h6>
                                    <p class="text-muted small mb-3">Finalize the marketing review and send the second acknowledgement letter to the bank.</p>
                                    <div class="mb-3">
                                        <label for="marketingSecondApprovalNote" class="form-label">Acknowledgement Letter</label>
                                        <textarea name="note" id="marketingSecondApprovalNote" rows="5" class="form-control" placeholder="Enter the final acknowledgement letter to be sent to the bank."></textarea>
                                    </div>
                                    <button type="submit" name="action" value="second_approve" class="btn btn-success w-100" @disabled(!$applicationView['marketing']['can_second_approve'])>Give 2nd Approval</button>
                                </form>
                            </div>

                            <div class="col-lg-4">
                                <form action="{{ route('application.marketing.process', ['id' => $applicationView['id']]) }}" method="POST" class="border rounded p-3 h-100 bg-light">
                                    @csrf
                                    <h6 class="mb-2">Hold Application</h6>
                                    <p class="text-muted small mb-3">Pause the application after receipt and send the marketing comment back to the bank.</p>
                                    <div class="mb-3">
                                        <label for="marketingHoldComment" class="form-label">Comment to Bank</label>
                                        <textarea name="note" id="marketingHoldComment" rows="5" class="form-control" placeholder="Enter the reason for hold and the instructions for the bank." required>{{ old('note', $applicationView['marketing']['stage'] === 'hold' ? $applicationView['marketing']['comment'] : '') }}</textarea>
                                    </div>
                                    <button type="submit" name="action" value="hold" class="btn btn-warning text-dark w-100">Hold and Comment to Bank</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
