@extends('layouts.app')

@section('title', 'Dashboard - APARA System')

@section('content')
<div class="dashboard-hero mb-4">
    <div>
        <h3 class="dashboard-title mb-2">Application Monitoring Overview</h3>
        <p class="dashboard-subtitle mb-0">Track applications, review workflow progress, and manage new submissions from one clear workspace.</p>
    </div>
</div>

<!-- ================= STATUS SUMMARY ================= -->
<div class="status-grid mb-4">
    <div class="status-box pending"><h6>Pending</h6><h3 id="pendingCount">{{ $dashboardCounts['pending'] ?? 0 }}</h3><p>Awaiting first review</p></div>
    <div class="status-box accepted"><h6>Accepted</h6><h3 id="acceptedCount">{{ $dashboardCounts['accepted'] ?? 0 }}</h3><p>Ready for processing</p></div>
    <div class="status-box approved"><h6>Approved</h6><h3 id="approvedCount">{{ $dashboardCounts['approved'] ?? 0 }}</h3><p>Cleared successfully</p></div>
    <div class="status-box rejected"><h6>Rejected</h6><h3 id="rejectedCount">{{ $dashboardCounts['rejected'] ?? 0 }}</h3><p>Need follow-up action</p></div>
    <div class="status-box payment"><h6>Payment Pending</h6><h3 id="paymentPendingCount">0</h3><p>Waiting for settlement</p></div>
    <div class="status-box completed"><h6>Completed Payments</h6><h3 id="completedCount">0</h3><p>Finance completed</p></div>
    <div class="status-box finalized"><h6>Finalized</h6><h3 id="finalizedCount">0</h3><p>Archived and closed</p></div>
</div>

<!-- ================= APPLICATION FLOW ================= -->
<div class="flow-card mb-4">
    @php
        $durationStages = collect($workflowDurations ?? []);
        $maxHours = max(1, (int) $durationStages->max('hours'));
        $chartLeft = 52;
        $chartTop = 26;
        $chartWidth = 620;
        $chartHeight = 210;
        $stepX = $durationStages->count() > 1 ? $chartWidth / ($durationStages->count() - 1) : 0;

        $durationPoints = $durationStages->values()->map(function ($item, $index) use ($chartLeft, $chartTop, $chartWidth, $chartHeight, $maxHours, $stepX) {
            $x = $chartLeft + ($index * $stepX);
            $y = $chartTop + $chartHeight - (($item['hours'] / $maxHours) * $chartHeight);
            return round($x, 2) . ',' . round($y, 2);
        })->implode(' ');

        $gridLines = collect(range(0, 5))->map(function ($line) use ($chartLeft, $chartTop, $chartWidth, $chartHeight, $maxHours) {
            $y = $chartTop + (($chartHeight / 5) * $line);
            $label = round($maxHours - (($maxHours / 5) * $line));
            return ['y' => round($y, 2), 'label' => $label];
        });
    @endphp

    <div class="section-header">
        <div>
            <h5 class="mb-1">Process Duration Timeline</h5>
            <p class="text-muted mb-0" id="processTimelineSubtitle">Track how long applications stay in submission, inside the bank, and across operations, marketing, and finance.</p>
        </div>
    </div>
    <div class="process-duration-layout">
        <div class="process-duration-chart-card">
            <div class="process-duration-chart-header">
                <span class="process-duration-tag" id="processTimelineTag">Average Hours by Stage</span>
                <strong id="processTimelineTotal">{{ $workflowSummary['totalHours'] ?? 0 }} hrs total cycle time</strong>
            </div>

            <div class="process-duration-chart-wrap">
                <svg class="process-duration-chart" viewBox="0 0 720 320" preserveAspectRatio="none" aria-label="Process duration line chart">
                    @foreach($gridLines as $gridLine)
                        <line class="process-duration-grid" x1="{{ $chartLeft }}" y1="{{ $gridLine['y'] }}" x2="{{ $chartLeft + $chartWidth }}" y2="{{ $gridLine['y'] }}"></line>
                        <text class="process-duration-y-label" x="16" y="{{ $gridLine['y'] + 4 }}">{{ $gridLine['label'] }}h</text>
                    @endforeach

                    <polyline class="process-duration-line" id="processDurationLine" points="{{ $durationPoints }}"></polyline>

                    @foreach($durationStages->values() as $index => $item)
                        @php
                            $x = $chartLeft + ($index * $stepX);
                            $y = $chartTop + $chartHeight - (($item['hours'] / $maxHours) * $chartHeight);
                        @endphp
                        <circle class="process-duration-point" id="processPoint{{ $index }}" cx="{{ round($x, 2) }}" cy="{{ round($y, 2) }}" r="6"></circle>
                        <text class="process-duration-point-label" id="processPointLabel{{ $index }}" x="{{ round($x, 2) }}" y="{{ round($y - 12, 2) }}">{{ $item['hours'] }}h</text>
                    @endforeach
                </svg>

                <div class="process-duration-axis">
                    @foreach($durationStages as $index => $item)
                        <div class="process-duration-axis-stage" data-stage-index="{{ $index }}">
                            <strong>{{ $item['stage'] }}</strong>
                            <span>{{ $item['owner'] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="process-duration-summary">
            <div class="process-duration-stat">
                <small>Total Duration</small>
                <strong id="processSummaryTotal">{{ $workflowSummary['totalHours'] ?? 0 }} hrs</strong>
            </div>
            <div class="process-duration-stat">
                <small>Inside Bank</small>
                <strong id="processSummaryBank">{{ $workflowSummary['bankHours'] ?? 0 }} hrs</strong>
            </div>
            <div class="process-duration-stat">
                <small>Operations</small>
                <strong id="processSummaryOperations">{{ $workflowSummary['operationsHours'] ?? 0 }} hrs</strong>
            </div>
            <div class="process-duration-stat">
                <small>Marketing</small>
                <strong id="processSummaryMarketing">{{ $workflowSummary['marketingHours'] ?? 0 }} hrs</strong>
            </div>
            <div class="process-duration-stat">
                <small>Finance</small>
                <strong id="processSummaryFinance">{{ $workflowSummary['financeHours'] ?? 0 }} hrs</strong>
            </div>

            <div class="process-duration-stage-list">
                @foreach($durationStages as $index => $item)
                    <div class="process-duration-stage-item" data-stage-index="{{ $index }}">
                        <div>
                            <strong>{{ $item['stage'] }}</strong>
                            <span>{{ $item['detail'] }}</span>
                        </div>
                        <b>{{ $item['hours'] }}h</b>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ================= APPLICATION TABLE ================= -->
<div class="table-card p-3" id="applicationListSection">
    <div class="section-header mb-3">
        <div>
            <h5 class="mb-1">Bank Wise Applications</h5>
        </div>
        <button type="button" class="bank-filter-button active" data-bank-filter="all" onclick="filterApplicationsByBank('all', this)">
            <i class="bi bi-grid me-2"></i>All Banks
        </button>
    </div>

    <div class="bank-filter-list mb-4">
        @foreach ($bankSummaries as $bank)
            <button
                type="button"
                class="bank-filter-button"
                data-bank-filter="{{ strtolower($bank['name']) }}"
                onclick="filterApplicationsByBank(@js($bank['name']), this)"
            >
                <span class="bank-filter-icon">
                    @if (!empty($bank['logo_url']))
                        <img src="{{ $bank['logo_url'] }}" alt="{{ $bank['name'] }}" class="bank-filter-logo">
                    @else
                        <i class="bi {{ $bank['icon'] }}"></i>
                    @endif
                </span>
                <span class="bank-filter-copy">
                    <strong>{{ $bank['name'] }}</strong>
                    <small>{{ $bank['count'] }} application{{ $bank['count'] === 1 ? '' : 's' }}</small>
                </span>
            </button>
        @endforeach
    </div>

    <div class="section-header mb-3">
        <div>
            <h5 class="mb-1">Applications List</h5>
            <p class="text-muted mb-0" id="applicationFilterLabel">Review the latest applications and take action directly from the table.</p>
        </div>
        <span class="table-count-pill">Total: <span id="totalAppCount">{{ $applications->count() }}</span> applications</span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover mt-2 application-overview-table">
            <thead class="table-light">
                <tr>
                    <th>App No</th>
                    <th>Proposal</th>
                    <th>Bank Name</th>
                    <th>Full Name</th>
                    <th>ID/Passport</th>
                    <th>Country</th>
                    <th>Employment Type</th>
                    <th>Guarantee Amount</th>
                    <th>Pre Departure</th>
                    <th>Recruitment Agency</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="applicationTable">
                @forelse ($applications as $application)
                    @php
                        $status = ucfirst(str_replace('_', ' ', strtolower($application->status ?? 'Pending')));
                        $statusBadgeClass = match (strtolower($application->status ?? 'pending')) {
                            'accepted' => 'bg-info',
                            'approved' => 'bg-success',
                            'rejected' => 'bg-danger',
                            'completed', 'finalized' => 'bg-primary',
                            'payment_pending' => 'bg-secondary',
                            default => 'bg-warning',
                        };
                    @endphp
                    <tr
                        data-app-id="APP-{{ str_pad((string) $application->id, 4, '0', STR_PAD_LEFT) }}"
                        data-status="{{ $status }}"
                        data-bank-name="{{ strtolower($application->bank_name ?: 'Unassigned') }}"
                    >
                        <td><button type="button" class="application-number-link" onclick="viewApplication(this)" title="View application location">APP-{{ str_pad((string) $application->id, 4, '0', STR_PAD_LEFT) }}</button></td>
                        <td>{{ $application->proposal_no ?? '-' }}</td>
                        <td>{{ $application->bank_name ?: '-' }}</td>
                        <td>{{ $application->full_name ?? '-' }}</td>
                        <td>{{ $application->nic ?: ($application->passport_no ?: '-') }}</td>
                        <td>{{ $application->country_name ?? '-' }}</td>
                        <td>{{ $application->employment_type ?? '-' }}</td>
                        <td>-</td>
                        <td>-</td>
                        <td>{{ $application->recruitment_agency_name ?? '-' }}</td>
                        <td><span class="badge {{ $statusBadgeClass }}">{{ $status }}</span></td>
                        <td class="action-buttons">
                            <i class="bi bi-eye me-2" onclick="viewApplication(this)" style="cursor:pointer;" title="View"></i>
                            <i class="bi bi-pencil-square me-2" onclick="openEditModal(this)" style="cursor:pointer;" title="Edit"></i>
                            <i class="bi bi-credit-card me-2" onclick="processPayment(this)" style="cursor:pointer;" title="Payment"></i>
                            <i class="bi bi-trash3" onclick="deleteApplication(this)" style="cursor:pointer; color:#dc3545;" title="Delete"></i>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted py-4">No applications found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ================= NEW APPLICATION MODAL ================= -->
<div id="newAppModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1050; overflow-y:auto; align-items:flex-start; justify-content:center;">
    <div style="background:white; border-radius:16px; width:90%; max-width:900px; margin:30px auto; padding:30px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h3 style="margin:0;">New Application Form</h3>
            <button id="closeModalBtn" style="background:none; border:none; font-size:28px; cursor:pointer;">&times;</button>
        </div>
        
        <form id="newAppFullForm" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label fw-bold">Select Branch<span style="color: red">*</span></label>
                <select name="for_branch_bank_id" id="forBranchBankId" class="form-control bg-light" required>
                    <option value="">-- Select Branch --</option>
                    <option value="1">Colombo Main Branch</option>
                    <option value="2">Kandy Branch</option>
                    <option value="3">Galle Branch</option>
                    <option value="4">Negombo Branch</option>
                </select>
            </div>

            <hr class="hr-custom">
            <h5>Personal Details</h5>
            <hr class="hr-custom">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Customer Full Name<span style="color: red">*</span></label>
                    <input type="text" name="inputCustomerName" id="inputCustomerName" class="form-control bg-light" placeholder="Enter Customer Full Name" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Customer Name with Initials<span style="color: red">*</span></label>
                    <input type="text" name="inputNameWithInitials" id="inputNameWithInitials" class="form-control bg-light" placeholder="Enter Customer Name with Initials" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Customer Address<span style="color: red">*</span></label>
                    <input type="text" name="inputCustomerAddress" id="inputCustomerAddress" class="form-control bg-light" placeholder="Enter Customer Address" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Province<span style="color: red">*</span></label>
                    <select name="inputProvince" id="inputProvince" class="form-control bg-light" required>
                        <option value="">-- Select Province --</option>
                        <option value="1">Western Province</option>
                        <option value="2">Central Province</option>
                        <option value="3">Southern Province</option>
                        <option value="4">Northern Province</option>
                        <option value="5">Eastern Province</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">District<span style="color: red">*</span></label>
                    <select name="inputDistrict" id="inputDistrict" class="form-control bg-light" required>
                        <option value="">-- Select District --</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Divisional Secretariat Office<span style="color: red">*</span></label>
                    <input type="text" name="inputDivisonalSecOffice" id="inputDivisonalSecOffice" class="form-control bg-light" placeholder="Enter Divisional Secretariat Office" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Passport No<span style="color: red">*</span></label>
                    <input type="text" name="inputPassportNo" id="inputPassportNo" class="form-control bg-light" placeholder="Ex: A1111111" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">ID No<span style="color: red">*</span></label>
                    <input type="text" name="inputIDNo" id="inputIDNo" class="form-control bg-light" placeholder="Enter ID No" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Country of Employment<span style="color: red">*</span></label>
                    <select name="inputEmploymentCountry" id="inputEmploymentCountry" class="form-control bg-light" required>
                        <option value="">-- Select Country --</option>
                        <option value="1">Saudi Arabia</option>
                        <option value="2">United Arab Emirates</option>
                        <option value="3">Qatar</option>
                        <option value="4">Kuwait</option>
                        <option value="5">Oman</option>
                        <option value="6">Malaysia</option>
                        <option value="7">Singapore</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Type of Employment<span style="color: red">*</span></label>
                    <input type="text" name="inputTypeOfEmployment" id="inputTypeOfEmployment" class="form-control bg-light" placeholder="Enter Type of Employment" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Credit Guarantee Amount (Rs.)<span style="color: red">*</span></label>
                    <input type="number" name="inputGuaranteeAmount" id="inputGuaranteeAmount" class="form-control bg-light" placeholder="Enter Credit Guarantee Amount" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Pre Departure Loan Amount (Rs.)<span style="color: red">*</span></label>
                    <input type="number" name="inputPreDepartureAmount" id="inputPreDepartureAmount" class="form-control bg-light" placeholder="Enter Pre Departure Loan Amount" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Recruitment Agency Name<span style="color: red">*</span></label>
                    <input type="text" name="inputRecruitmentAgency" id="inputRecruitmentAgency" class="form-control bg-light" placeholder="Enter Recruitment Agency Name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Labour Licence No<span style="color: red">*</span></label>
                    <input type="text" name="inputLabourLicenceNo" id="inputLabourLicenceNo" class="form-control bg-light" placeholder="Enter Labour Licence No" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Recruitment Agency Address<span style="color: red">*</span></label>
                    <input type="text" name="inputRecruitmentAgencyAddress" id="inputRecruitmentAgencyAddress" class="form-control bg-light" placeholder="Enter Recruitment Agency Address" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Guarantee Required Date<span style="color: red">*</span></label>
                    <input type="date" name="inputGuaranteeRequiredDate" id="inputGuaranteeRequiredDate" class="form-control bg-light" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Guarantee Required Period<span style="color: red">*</span></label>
                    <select name="inputGuaranteeRequiredPeriod" id="inputGuaranteeRequiredPeriod" class="form-control bg-light" required>
                        <option value="" disabled selected>-- Select Period --</option>
                        <option value="1">1 Year</option>
                        <option value="2">2 Years</option>
                        <option value="3">3 Years</option>
                        <option value="4">4 Years</option>
                        <option value="5">5 Years</option>
                    </select>
                </div>
            </div>

            <hr class="hr-custom mt-4">
            <h5>Guarantor Details</h5>
            <hr class="hr-custom">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Name of Guarantor 1<span style="color: red">*</span></label>
                    <input type="text" name="inputGuarantor1Name" id="inputGuarantor1Name" class="form-control bg-light" placeholder="Enter Name of Guarantor 1" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address of Guarantor 1<span style="color: red">*</span></label>
                    <input type="text" name="inputGuarantor1Address" id="inputGuarantor1Address" class="form-control bg-light" placeholder="Enter Address of Guarantor 1" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Name of Guarantor 2<span style="color: red">*</span></label>
                    <input type="text" name="inputGuarantor2Name" id="inputGuarantor2Name" class="form-control bg-light" placeholder="Enter Name of Guarantor 2" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Address of Guarantor 2<span style="color: red">*</span></label>
                    <input type="text" name="inputGuarantor2Address" id="inputGuarantor2Address" class="form-control bg-light" placeholder="Enter Address of Guarantor 2" required>
                </div>
                <div class="col-12">
                    <label class="form-label">Name of Guarantor 3 (Optional)</label>
                    <input type="text" name="inputGuarantor3Name" id="inputGuarantor3Name" class="form-control bg-light" placeholder="Enter Name of Guarantor 3">
                </div>
                <div class="col-12">
                    <label class="form-label">Address of Guarantor 3 (Optional)</label>
                    <input type="text" name="inputGuarantor3Address" id="inputGuarantor3Address" class="form-control bg-light" placeholder="Enter Address of Guarantor 3">
                </div>
            </div>

            <hr class="hr-custom mt-4">
            <h5>Documents Upload</h5>
            <hr class="hr-custom">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Passport of the Applicant<span style="color: red">*</span></label>
                    <input type="file" name="passportImage" id="passportImage" class="form-control bg-light" accept="application/pdf">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Quotation from Travel Agent</label>
                    <input type="file" name="travelAgentQuotation" id="travelAgentQuotation" class="form-control bg-light" accept="application/pdf">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Permanent Residence Letter</label>
                    <input type="file" name="permanentResidenceLetter" id="permanentResidenceLetter" class="form-control bg-light" accept="application/pdf">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Letter from Recruiting Agent</label>
                    <input type="file" name="recruitingAgentLetter" id="recruitingAgentLetter" class="form-control bg-light" accept="application/pdf">
                </div>
                <div class="col-md-6">
                    <label class="form-label">SLBFE Letter<span style="color: red">*</span></label>
                    <input type="file" name="SLBFELetter" id="SLBFELetter" class="form-control bg-light" accept="application/pdf">
                </div>
            </div>

            <hr class="hr-custom mt-4">
            <h5>Bank Details</h5>
            <hr class="hr-custom">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Applicant Bank Address<span style="color: red">*</span></label>
                    <input type="text" name="inputApplicantBankAddress" id="inputApplicantBankAddress" class="form-control bg-light" placeholder="Enter Applicant Bank Address" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tel No<span style="color: red">*</span></label>
                    <input type="text" name="telNo" id="telNo" class="form-control bg-light" placeholder="Enter Tel No" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Name of Signatory<span style="color: red">*</span></label>
                    <input type="text" name="signatoryName" id="signatoryName" class="form-control bg-light" placeholder="Enter Signatory Name" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Capacity of Signatory<span style="color: red">*</span></label>
                    <input type="text" name="signatoryCapacity" id="signatoryCapacity" class="form-control bg-light" placeholder="Enter Capacity" required>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="button" id="cancelFullModalBtn" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Application</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= EDIT APPLICATION MODAL ================= -->
<div id="editAppModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1050; align-items:center; justify-content:center;">
    <div style="background:white; border-radius:16px; width:90%; max-width:700px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h5 style="margin:0;">Edit Application</h5>
            <button id="closeEditModalBtn" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <form id="editAppForm">
            <input type="hidden" id="editAppId" value="">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Application Number</label>
                    <input type="text" id="editAppNo" class="form-control" readonly style="background-color:#e9ecef;">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Proposal Number</label>
                    <input type="text" id="editProposal" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Full Name</label>
                    <input type="text" id="editName" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">ID/Passport Number</label>
                    <input type="text" id="editId" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Country of Employment</label>
                    <input type="text" id="editCountry" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Employment Type</label>
                    <input type="text" id="editEmploymentType" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Guarantee Amount (Rs.)</label>
                    <input type="text" id="editAmount" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Pre Departure Amount (Rs.)</label>
                    <input type="text" id="editPreDeparture" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Recruitment Agency</label>
                    <input type="text" id="editAgency" class="form-control" required>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-bold">Status</label>
                    <select id="editStatus" class="form-select">
                        <option value="Pending">Pending</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" id="cancelEditModalBtn" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Application</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= VIEW APPLICATION MODAL ================= -->
<div id="viewAppModal" style="display:none; position:fixed; inset:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1200; overflow-y:auto; padding:18px 14px; align-items:flex-start; justify-content:center;">
    <div style="background:white; border-radius:16px; width:min(840px, 100%); max-height:calc(100vh - 36px); margin:0 auto; padding:20px; box-shadow:0 10px 25px rgba(0,0,0,0.2); overflow-y:auto;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h5 style="margin:0;">Application Details</h5>
            <button id="closeViewModalBtn" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
        </div>
        <div class="row application-detail-grid">
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Application Number</label>
                    <p id="viewAppNo" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Proposal Number</label>
                    <p id="viewProposal" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Bank Name</label>
                    <p id="viewBank" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Full Name</label>
                    <p id="viewName" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">ID/Passport Number</label>
                    <p id="viewId" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Country of Employment</label>
                    <p id="viewCountry" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Employment Type</label>
                    <p id="viewEmploymentType" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Guarantee Amount</label>
                    <p id="viewAmount" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Pre Departure Amount</label>
                    <p id="viewPreDeparture" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Recruitment Agency</label>
                    <p id="viewAgency" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="application-detail-item">
                    <label class="form-label fw-bold text-muted">Status</label>
                    <p id="viewStatus" class="mb-0 fs-6">-</p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="application-location-panel">
                    <div class="application-location-head">
                        <span class="application-location-kicker">Current Location</span>
                        <strong id="viewLocation">-</strong>
                    </div>
                    <div class="application-location-meta">
                        <div>
                            <small>Owning Department</small>
                            <strong id="viewDepartment">-</strong>
                        </div>
                        <div>
                            <small>Process Stage</small>
                            <strong id="viewStage">-</strong>
                        </div>
                    </div>
                    <p id="viewLocationNote" class="application-location-note mb-0">-</p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="application-location-chart-panel">
                    <div class="application-location-chart-head">
                        <span class="application-location-kicker">Location Line Chart</span>
                        <strong id="viewJourneyTitle">Application Journey</strong>
                    </div>
                    <div id="applicationLocationLineChart" class="application-location-line-chart" aria-live="polite"></div>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end">
            <button id="closeViewModalFooterBtn" class="btn btn-secondary">Close</button>
        </div>
    </div>
</div>

<script>
const allApplicationsUrl = @json(route('all-applications'));
const applicationStorageKey = "slecic_all_applications";

// Country name mapping
const countryNames = {
    '1': 'Saudi Arabia',
    '2': 'United Arab Emirates',
    '3': 'Qatar',
    '4': 'Kuwait',
    '5': 'Oman',
    '6': 'Malaysia',
    '7': 'Singapore'
};

// Province-District dynamic loading
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('inputProvince');
    const districtSelect = document.getElementById('inputDistrict');

    if(provinceSelect) {
        provinceSelect.addEventListener('change', function() {
            const provinceId = this.value;
            districtSelect.innerHTML = '<option value="">-- Select District --</option>';
            if(provinceId) {
                const districts = {
                    '1': [{id:1, name:'Colombo'}, {id:2, name:'Gampaha'}, {id:3, name:'Kalutara'}],
                    '2': [{id:4, name:'Kandy'}, {id:5, name:'Matale'}, {id:6, name:'Nuwara Eliya'}],
                    '3': [{id:7, name:'Galle'}, {id:8, name:'Matara'}, {id:9, name:'Hambantota'}],
                    '4': [{id:10, name:'Jaffna'}, {id:11, name:'Kilinochchi'}, {id:12, name:'Mannar'}],
                    '5': [{id:13, name:'Batticaloa'}, {id:14, name:'Ampara'}, {id:15, name:'Trincomalee'}]
                };
                const provinceDistricts = districts[provinceId] || [];
                provinceDistricts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.id;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            }
        });
    }
});

// Date & Time
function updateDateTime() {
    const now = new Date();
    const datetimeElement = document.getElementById("datetime");
    const dashboardDateLabel = document.getElementById("dashboardDateLabel");
    if(datetimeElement) {
        datetimeElement.innerText = now.toLocaleString();
    }
    if(dashboardDateLabel) {
        dashboardDateLabel.innerText = now.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }
}
setInterval(updateDateTime, 1000);
updateDateTime();

// Flow click
document.querySelectorAll(".flow-step").forEach(step => {
    step.onclick = () => {
        document.querySelectorAll(".flow-actions").forEach(a => a.classList.remove("show"));
        document.getElementById(step.dataset.step + "Actions").classList.add("show");
    };
});

// Update status counts
function updateStatusCounts() {
    let rows = document.querySelectorAll("#applicationTable tr");
    let visibleRows = Array.from(rows).filter(row => row.style.display !== "none" && row.querySelectorAll("td").length > 1);
    document.getElementById("totalAppCount").innerText = visibleRows.length;
}

function filterApplicationsByBank(bankName, button) {
    const normalizedBank = (bankName || "all").toString().toLowerCase();
    const rows = document.querySelectorAll("#applicationTable tr");
    const label = document.getElementById("applicationFilterLabel");

    rows.forEach(row => {
        if (row.querySelectorAll("td").length <= 1) {
            return;
        }

        const rowBank = (row.dataset.bankName || "unassigned").toLowerCase();
        row.style.display = normalizedBank === "all" || rowBank === normalizedBank ? "" : "none";
    });

    document.querySelectorAll(".bank-filter-button").forEach(item => item.classList.remove("active"));

    if (button && button.classList.contains("bank-filter-button")) {
        button.classList.add("active");
    }

    if (label) {
        label.innerText = normalizedBank === "all"
            ? "Review the latest applications and take action directly from the table."
            : `Displaying applications submitted under ${bankName}.`;
    }

    updateStatusCounts();
}

// Generate next App No
function getNextAppNo() {
    let rows = document.querySelectorAll("#applicationTable tr");
    let maxNum = 0;
    rows.forEach(row => {
        let appNoCell = row.cells[0];
        if(appNoCell) {
            let text = appNoCell.innerText;
            let match = text.match(/APP-(\d+)/);
            if(match) {
                let num = parseInt(match[1], 10);
                if(num > maxNum) maxNum = num;
            }
        }
    });
    let nextNum = maxNum + 1;
    return "APP-" + nextNum.toString().padStart(4, '0');
}

function getStatusBadgeClass(status) {
    if(status === "Pending") return "bg-warning";
    else if(status === "Accepted") return "bg-info";
    else if(status === "Approved") return "bg-success";
    else if(status === "Rejected") return "bg-danger";
    return "bg-secondary";
}

function formatAmount(amount) {
    if(!amount) return "0";
    return parseFloat(amount).toLocaleString('en-IN');
}

function getStoredApplications() {
    try {
        return JSON.parse(localStorage.getItem(applicationStorageKey) || "[]");
    } catch (error) {
        return [];
    }
}

function saveStoredApplications(applications) {
    localStorage.setItem(applicationStorageKey, JSON.stringify(applications));
}

// Add application to table
function addApplicationToTable(proposal, fullName, idNumber, countryId, employmentType, guaranteeAmount, preDepartureAmount, agencyName, status) {
    let tableBody = document.getElementById("applicationTable");
    let newAppNo = getNextAppNo();
    let statusBadgeClass = getStatusBadgeClass(status);
    let countryName = countryNames[countryId] || countryId;
    let formattedGuarantee = formatAmount(guaranteeAmount);
    let formattedPreDeparture = formatAmount(preDepartureAmount);
    
    let newRow = document.createElement("tr");
    newRow.setAttribute("data-app-id", newAppNo);
    newRow.innerHTML = `
        <td><button type="button" class="application-number-link" onclick="viewApplication(this)" title="View application location">${escapeHtml(newAppNo)}</button></td>
        <td>${escapeHtml(proposal)}</td>
        <td>-</td>
        <td>${escapeHtml(fullName)}</td>
        <td>${escapeHtml(idNumber)}</td>
        <td>${escapeHtml(countryName)}</td>
        <td>${escapeHtml(employmentType)}</td>
        <td>${formattedGuarantee}</td>
        <td>${formattedPreDeparture}</td>
        <td>${escapeHtml(agencyName)}</td>
        <td><span class="badge ${statusBadgeClass}">${status}</span></td>
        <td class="action-buttons">
            <i class="bi bi-eye me-2" onclick="viewApplication(this)" style="cursor:pointer;" title="View"></i>
            <i class="bi bi-pencil-square me-2" onclick="openEditModal(this)" style="cursor:pointer;" title="Edit"></i>
            <i class="bi bi-credit-card me-2" onclick="processPayment(this)" style="cursor:pointer;" title="Payment"></i>
            <i class="bi bi-trash3" onclick="deleteApplication(this)" style="cursor:pointer; color:#dc3545;" title="Delete"></i>
        </td>
    `;
    tableBody.appendChild(newRow);
    updateStatusCounts();
    scrollToApplicationList(newRow);
    showNotification("Application added successfully!", "success");
}

function scrollToApplicationList(newRow = null) {
    const applicationListSection = document.getElementById("applicationListSection");

    if(applicationListSection) {
        applicationListSection.scrollIntoView({
            behavior: "smooth",
            block: "start"
        });

        applicationListSection.classList.add("table-focus");
        setTimeout(() => {
            applicationListSection.classList.remove("table-focus");
        }, 1800);
    }

    if(newRow) {
        newRow.classList.add("new-row-highlight");
        setTimeout(() => {
            newRow.classList.remove("new-row-highlight");
        }, 2200);
    }
}

function updateApplication(row, appNo, proposal, fullName, idNumber, country, employmentType, guaranteeAmount, preDepartureAmount, agencyName, status) {
    row.cells[0].innerText = appNo;
    row.cells[1].innerText = proposal;
    row.cells[3].innerText = fullName;
    row.cells[4].innerText = idNumber;
    row.cells[5].innerText = country;
    row.cells[6].innerText = employmentType;
    row.cells[7].innerText = formatAmount(guaranteeAmount);
    row.cells[8].innerText = formatAmount(preDepartureAmount);
    row.cells[9].innerText = agencyName;
    let statusBadgeClass = getStatusBadgeClass(status);
    row.cells[10].innerHTML = `<span class="badge ${statusBadgeClass}">${status}</span>`;
    updateStatusCounts();
    showNotification("Application updated successfully!", "success");
}

function deleteApplication(element) {
    if(confirm("Are you sure you want to delete this application? This action cannot be undone.")) {
        let row = element.closest("tr");
        let appNo = row.cells[0].innerText;
        row.remove();
        updateStatusCounts();
        showNotification(`Application ${appNo} has been deleted.`, "warning");
    }
}

const processTimelineBaseStages = [
    { stage: "Submission", owner: "Applicant / Bank", hours: 4, detail: "Average time to prepare and submit the application package." },
    { stage: "Inside Bank", owner: "Bank", hours: 18, detail: "Internal bank review, verification, and branch approval handling." },
    { stage: "Operations", owner: "Operations", hours: 12, detail: "Operational checks and document coordination within APARA." },
    { stage: "Marketing", owner: "Marketing", hours: 7, detail: "Market-side validation, channel follow-up, and customer coordination." },
    { stage: "Finance", owner: "Finance", hours: 10, detail: "Fee confirmation, payment processing, and finance clearance." },
    { stage: "Completion", owner: "System Closeout", hours: 3, detail: "Final release, archive updates, and case closure." }
];

const processTimelineProfiles = {
    pending: { currentStageIndex: 1, completedUntil: 1, note: "Selected application is still at the bank review stage." },
    accepted: { currentStageIndex: 2, completedUntil: 2, note: "Selected application has moved to operations after bank acceptance." },
    approved: { currentStageIndex: 4, completedUntil: 4, note: "Selected application has cleared approval and is now in finance processing." },
    rejected: { currentStageIndex: 1, completedUntil: 1, note: "Selected application returned for follow-up before continuing the workflow." },
    payment_pending: { currentStageIndex: 4, completedUntil: 4, note: "Selected application is waiting for finance-side settlement." },
    completed: { currentStageIndex: 5, completedUntil: 5, note: "Selected application has completed payment processing." },
    finalized: { currentStageIndex: 5, completedUntil: 5, note: "Selected application is fully finalized and archived." },
    default: { currentStageIndex: 0, completedUntil: 0, note: "Selected application is active in the workflow." }
};

function updateProcessDurationTimeline(appNo, status) {
    const normalizedStatus = (status || "").toLowerCase().replace(/\s+/g, "_");
    const profile = processTimelineProfiles[normalizedStatus] || processTimelineProfiles.default;
    const stages = processTimelineBaseStages.map((stage, index) => ({
        ...stage,
        displayHours: index <= profile.completedUntil ? stage.hours : 0
    }));

    const maxHours = Math.max(...processTimelineBaseStages.map(stage => stage.hours), 1);
    const chartLeft = 52;
    const chartTop = 26;
    const chartWidth = 620;
    const chartHeight = 210;
    const stepX = stages.length > 1 ? chartWidth / (stages.length - 1) : 0;
    const points = stages.map((stage, index) => {
        const x = chartLeft + (index * stepX);
        const y = chartTop + chartHeight - ((stage.displayHours / maxHours) * chartHeight);
        return { x, y, hours: stage.displayHours };
    });

    const line = document.getElementById("processDurationLine");
    if (line) {
        line.setAttribute("points", points.map(point => `${point.x.toFixed(2)},${point.y.toFixed(2)}`).join(" "));
    }

    points.forEach((point, index) => {
        const circle = document.getElementById(`processPoint${index}`);
        const label = document.getElementById(`processPointLabel${index}`);

        if (circle) {
            circle.setAttribute("cx", point.x.toFixed(2));
            circle.setAttribute("cy", point.y.toFixed(2));
            circle.style.fill = index === profile.currentStageIndex ? "#2f5d8a" : (index <= profile.completedUntil ? "#22c55e" : "#cbd5e1");
        }

        if (label) {
            label.setAttribute("x", point.x.toFixed(2));
            label.setAttribute("y", (point.y - 12).toFixed(2));
            label.textContent = `${point.hours}h`;
        }
    });

    const totalHours = stages.reduce((sum, stage) => sum + stage.displayHours, 0);
    const setText = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value;
        }
    };

    setText("processTimelineTag", `${appNo} Timeline`);
    setText("processTimelineTotal", `${totalHours} hrs current workflow time`);
    setText("processTimelineSubtitle", profile.note);
    setText("processSummaryTotal", `${totalHours} hrs`);
    setText("processSummaryBank", `${stages[1]?.displayHours ?? 0} hrs`);
    setText("processSummaryOperations", `${stages[2]?.displayHours ?? 0} hrs`);
    setText("processSummaryMarketing", `${stages[3]?.displayHours ?? 0} hrs`);
    setText("processSummaryFinance", `${stages[4]?.displayHours ?? 0} hrs`);

    document.querySelectorAll(".process-duration-stage-item, .process-duration-axis-stage").forEach(item => {
        const index = Number(item.getAttribute("data-stage-index"));
        item.classList.remove("is-current", "is-complete");

        if (index < profile.currentStageIndex) {
            item.classList.add("is-complete");
        } else if (index === profile.currentStageIndex) {
            item.classList.add("is-current");
        }
    });
}

function viewApplication(element) {
    let row = element.closest("tr");
    const status = row.cells[10].innerText.trim();
    const locationDetails = getApplicationLocationDetails(status);
    const appNo = row.cells[0].innerText.trim();

    updateProcessDurationTimeline(appNo, status);

    document.getElementById("viewAppNo").innerText = appNo;
    document.getElementById("viewProposal").innerText = row.cells[1].innerText;
    document.getElementById("viewBank").innerText = row.cells[2].innerText;
    document.getElementById("viewName").innerText = row.cells[3].innerText;
    document.getElementById("viewId").innerText = row.cells[4].innerText;
    document.getElementById("viewCountry").innerText = row.cells[5].innerText;
    document.getElementById("viewEmploymentType").innerText = row.cells[6].innerText;
    document.getElementById("viewAmount").innerText = row.cells[7].innerText;
    document.getElementById("viewPreDeparture").innerText = row.cells[8].innerText;
    document.getElementById("viewAgency").innerText = row.cells[9].innerText;
    document.getElementById("viewStatus").innerText = status;
    document.getElementById("viewLocation").innerText = locationDetails.location;
    document.getElementById("viewDepartment").innerText = locationDetails.department;
    document.getElementById("viewStage").innerText = locationDetails.stage;
    document.getElementById("viewLocationNote").innerText = locationDetails.note;
    document.getElementById("viewJourneyTitle").innerText = `${row.cells[0].innerText} Journey`;
    renderApplicationLocationChart(locationDetails);
    document.getElementById("viewAppModal").style.display = "flex";
}

function getApplicationLocationDetails(status) {
    const normalizedStatus = (status || "").toLowerCase();

    if(normalizedStatus === "pending") {
        return {
            location: "Inside Bank",
            department: "Bank Operations",
            stage: "Submission Review",
            note: "The application is currently with the bank for initial verification and branch-level checking.",
            currentStep: 2
        };
    }

    if(normalizedStatus === "accepted") {
        return {
            location: "Operations Desk",
            department: "Operations",
            stage: "Operational Validation",
            note: "The application has moved beyond the bank and is being processed by the operations team.",
            currentStep: 3
        };
    }

    if(normalizedStatus === "approved") {
        return {
            location: "Finance Clearance",
            department: "Finance",
            stage: "Payment / Guarantee Processing",
            note: "The application is approved and is currently in the finance stage for final settlement and release.",
            currentStep: 5
        };
    }

    if(normalizedStatus === "rejected") {
        return {
            location: "Returned for Follow-up",
            department: "Bank / Operations",
            stage: "Correction Required",
            note: "The application needs updates or clarification before it can continue through the workflow.",
            currentStep: 2
        };
    }

    return {
        location: "Workflow Queue",
        department: "System",
        stage: "Under Review",
        note: "The application is active in the system and waiting for the next assigned process step.",
        currentStep: 1
    };
}

function renderApplicationLocationChart(locationDetails) {
    const chart = document.getElementById("applicationLocationLineChart");
    if(!chart) {
        return;
    }

    const steps = [
        { label: "Submission", owner: "Customer" },
        { label: "Inside Bank", owner: "Bank" },
        { label: "Operations", owner: "Operations" },
        { label: "Marketing", owner: "Marketing" },
        { label: "Finance", owner: "Finance" },
        { label: "Completed", owner: "System" }
    ];

    const currentStep = Number(locationDetails.currentStep || 1);

    chart.innerHTML = steps.map((step, index) => {
        const stepNumber = index + 1;
        let stateClass = "";

        if(stepNumber < currentStep) {
            stateClass = "is-complete";
        } else if(stepNumber === currentStep) {
            stateClass = "is-current";
        }

        return `
            <div class="application-line-step ${stateClass}">
                <div class="application-line-marker">${stepNumber}</div>
                <div class="application-line-content">
                    <strong>${escapeHtml(step.label)}</strong>
                    <span>${escapeHtml(step.owner)}</span>
                </div>
            </div>
        `;
    }).join("");
}

function processPayment(element) {
    let row = element.closest("tr");
    showNotification(`Processing payment for ${row.cells[3].innerText} (${row.cells[0].innerText})...`, "info");
}

function openEditModal(element) {
    let row = element.closest("tr");
    document.getElementById("editAppId").value = row.cells[0].innerText;
    document.getElementById("editAppNo").value = row.cells[0].innerText;
    document.getElementById("editProposal").value = row.cells[1].innerText;
    document.getElementById("editName").value = row.cells[3].innerText;
    document.getElementById("editId").value = row.cells[4].innerText;
    document.getElementById("editCountry").value = row.cells[5].innerText;
    document.getElementById("editEmploymentType").value = row.cells[6].innerText;
    document.getElementById("editAmount").value = row.cells[7].innerText.replace(/,/g, '');
    document.getElementById("editPreDeparture").value = row.cells[8].innerText.replace(/,/g, '');
    document.getElementById("editAgency").value = row.cells[9].innerText;
    document.getElementById("editStatus").value = row.cells[10].innerText.trim();
    document.getElementById("editAppModal").style.display = "flex";
}

function escapeHtml(str) {
    if(!str) return "";
    return str.replace(/[&<>]/g, function(m) {
        if(m === '&') return '&amp;';
        if(m === '<') return '&lt;';
        if(m === '>') return '&gt;';
        return m;
    });
}

function showNotification(message, type = "info") {
    let notification = document.createElement("div");
    notification.style.position = "fixed";
    notification.style.top = "20px";
    notification.style.right = "20px";
    notification.style.padding = "12px 20px";
    notification.style.borderRadius = "8px";
    notification.style.zIndex = "9999";
    notification.style.fontSize = "14px";
    notification.style.fontWeight = "500";
    notification.style.boxShadow = "0 4px 12px rgba(0,0,0,0.15)";
    
    if(type === "success") {
        notification.style.backgroundColor = "#d4edda";
        notification.style.color = "#155724";
        notification.style.borderLeft = "4px solid #28a745";
    } else if(type === "warning") {
        notification.style.backgroundColor = "#fff3cd";
        notification.style.color = "#856404";
        notification.style.borderLeft = "4px solid #ffc107";
    } else if(type === "error") {
        notification.style.backgroundColor = "#f8d7da";
        notification.style.color = "#721c24";
        notification.style.borderLeft = "4px solid #dc3545";
    } else {
        notification.style.backgroundColor = "#d1ecf1";
        notification.style.color = "#0c5460";
        notification.style.borderLeft = "4px solid #17a2b8";
    }
    
    notification.innerText = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.style.opacity = "0";
        notification.style.transition = "opacity 0.3s ease";
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Modal handling for New Application
const newModal = document.getElementById("newAppModal");
const newAppBtn = document.getElementById("newApplicationBtn");
const closeModalBtn = document.getElementById("closeModalBtn");
const cancelFullModalBtn = document.getElementById("cancelFullModalBtn");
const newAppFullForm = document.getElementById("newAppFullForm");

function openNewModal() {
    if(!newModal) return;
    newModal.style.display = "flex";
    document.body.style.overflow = "hidden";
}
function closeNewModal() {
    if(!newModal) return;
    newModal.style.display = "none";
    document.body.style.overflow = "auto";
    if(newAppFullForm) {
        newAppFullForm.reset();
    }
}

if(newAppBtn) {
    newAppBtn.addEventListener("click", openNewModal);
}
if(closeModalBtn) {
    closeModalBtn.addEventListener("click", closeNewModal);
}
if(cancelFullModalBtn) {
    cancelFullModalBtn.addEventListener("click", closeNewModal);
}

if(newAppFullForm) {
    newAppFullForm.addEventListener("submit", function(e) {
        e.preventDefault();
        
        let fullName = document.getElementById("inputCustomerName").value.trim();
        let idNumber = document.getElementById("inputIDNo").value.trim();
        let passportNo = document.getElementById("inputPassportNo").value.trim();
        let finalIdNumber = idNumber || passportNo;
        let countryId = document.getElementById("inputEmploymentCountry").value;
        let employmentType = document.getElementById("inputTypeOfEmployment").value.trim();
        let guaranteeAmount = document.getElementById("inputGuaranteeAmount").value;
        let preDepartureAmount = document.getElementById("inputPreDepartureAmount").value;
        let agencyName = document.getElementById("inputRecruitmentAgency").value.trim();
        let proposalNumber = "PR-" + Math.floor(Math.random() * 900 + 100);
        let status = "Pending";
        
        if(!fullName) {
            showNotification("Please fill Customer Full Name", "error");
            return;
        }
        if(!finalIdNumber) {
            showNotification("Please fill ID No or Passport No", "error");
            return;
        }
        if(!countryId) {
            showNotification("Please select Country of Employment", "error");
            return;
        }
        if(!employmentType) {
            showNotification("Please enter Type of Employment", "error");
            return;
        }
        if(!guaranteeAmount) {
            showNotification("Please enter Guarantee Amount", "error");
            return;
        }
        if(!preDepartureAmount) {
            showNotification("Please enter Pre Departure Amount", "error");
            return;
        }
        if(!agencyName) {
            showNotification("Please enter Recruitment Agency Name", "error");
            return;
        }
        
        let newAppNo = getNextAppNo();
        let countryName = countryNames[countryId] || countryId;
        let submittedApplications = getStoredApplications();

        submittedApplications.push({
            appNo: newAppNo,
            proposal: proposalNumber,
            fullName: fullName,
            idNumber: finalIdNumber,
            country: countryName,
            employmentType: employmentType,
            guaranteeAmount: formatAmount(guaranteeAmount),
            preDepartureAmount: formatAmount(preDepartureAmount),
            agencyName: agencyName,
            status: status
        });

        saveStoredApplications(submittedApplications);
        closeNewModal();
        window.location.href = allApplicationsUrl;
    });
}

// Edit Modal handlers
const editModal = document.getElementById("editAppModal");
const closeEditModalBtn = document.getElementById("closeEditModalBtn");
const cancelEditModalBtn = document.getElementById("cancelEditModalBtn");
const editAppForm = document.getElementById("editAppForm");

function closeEditModal() {
    editModal.style.display = "none";
    editAppForm.reset();
}

if(closeEditModalBtn) {
    closeEditModalBtn.addEventListener("click", closeEditModal);
}
if(cancelEditModalBtn) {
    cancelEditModalBtn.addEventListener("click", closeEditModal);
}

if(editAppForm) {
    editAppForm.addEventListener("submit", function(e) {
        e.preventDefault();
        let appNo = document.getElementById("editAppNo").value;
        let proposal = document.getElementById("editProposal").value.trim();
        let fullName = document.getElementById("editName").value.trim();
        let idNumber = document.getElementById("editId").value.trim();
        let country = document.getElementById("editCountry").value.trim();
        let employmentType = document.getElementById("editEmploymentType").value.trim();
        let amount = document.getElementById("editAmount").value.trim();
        let preDeparture = document.getElementById("editPreDeparture").value.trim();
        let agency = document.getElementById("editAgency").value.trim();
        let status = document.getElementById("editStatus").value;
        
        if(!proposal || !fullName || !idNumber || !country || !employmentType || !amount || !preDeparture || !agency) {
            showNotification("Please fill all fields", "error");
            return;
        }
        
        let rows = document.querySelectorAll("#applicationTable tr");
        let targetRow = null;
        for(let row of rows) {
            if(row.cells[0].innerText === appNo) {
                targetRow = row;
                break;
            }
        }
        
        if(targetRow) {
            updateApplication(targetRow, appNo, proposal, fullName, idNumber, country, employmentType, amount, preDeparture, agency, status);
            closeEditModal();
        } else {
            showNotification("Application not found!", "error");
        }
    });
}

// View Modal handlers
const viewModal = document.getElementById("viewAppModal");
const closeViewModalBtn = document.getElementById("closeViewModalBtn");
const closeViewModalFooterBtn = document.getElementById("closeViewModalFooterBtn");

function closeViewModal() {
    viewModal.style.display = "none";
}

if(closeViewModalBtn) {
    closeViewModalBtn.addEventListener("click", closeViewModal);
}
if(closeViewModalFooterBtn) {
    closeViewModalFooterBtn.addEventListener("click", closeViewModal);
}

// Close modals when clicking outside
window.addEventListener("click", function(event) {
    if (event.target === newModal) closeNewModal();
    if (event.target === editModal) closeEditModal();
    if (event.target === viewModal) closeViewModal();
});

// Set minimum date for guarantee required date
const today = new Date().toISOString().split('T')[0];
const guaranteeDateInput = document.getElementById("inputGuaranteeRequiredDate");
if(guaranteeDateInput) {
    guaranteeDateInput.min = today;
}

// Initialize counts
updateStatusCounts();
</script>

@endsection
