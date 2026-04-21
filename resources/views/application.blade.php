<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>New Application</title>
        <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
        <link rel="stylesheet" href="icons/font/bootstrap-icons.min.css"/>
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="shortcut icon" href="Images/logo.png" />        
    </head>
 
    <body>
        @php
            $bankProfile = session('bank_profile');
            $isBankUser = !empty($bankProfile);
            $selectedBank = $isBankUser ? collect($banks ?? [])->first() : null;
            $selectedBankName = $isBankUser ? ($selectedBank->bank_name ?? $selectedBank->name ?? ($bankProfile['bank_name'] ?? '')) : '';
            $selectedBranchId = $isBankUser ? ($selectedBank->branch_id ?? $selectedBank->id ?? ($bankProfile['id'] ?? '')) : '';
            $selectedBranchName = $isBankUser ? ($selectedBank->branch_name ?? ($bankProfile['branch_name'] ?? '')) : '';
            $defaultWorkflowType = $selectedBank->workflow_type ?? 'decentralized';
            $bankWorkflowMap = collect($banks ?? [])
                ->mapWithKeys(function ($bank) {
                    $bankName = trim((string) ($bank->bank_name ?? $bank->name ?? ''));

                    return $bankName !== ''
                        ? [$bankName => [
                            'type' => $bank->workflow_type ?? 'decentralized',
                            'label' => $bank->workflow_label ?? 'Decentralized Bank',
                            'description' => $bank->workflow_description ?? 'New applications move directly from the bank branch to the Marketing Department.',
                        ]]
                        : [];
                })
                ->all();
        @endphp
        @include('layouts.sidebar')
        @include('layouts.header')

        <div class="main with-sidebar">
        <div class="main-content" style="padding-bottom: 100px;">
            <div class="container">


            <form action="{{ $isBankUser ? route('bank.application.store') : route('application.store') }}" method="POST" target="_self" id="newApplication" enctype="multipart/form-data">
                @csrf
                <div class="container mt-5">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="heading-container">
                        <h3>New Application</h3>
                        <hr class="hr-custom">
                    </div>
                    <div class="row justify-content-left">
                        <div class="col-12 col-md-8 col-lg-10 p-4 rounded custom-shadow border-container ms-3">
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="selectedBankName" class="form-label">Select Bank<span style="color: red">*</span></label>
                                    @if($isBankUser)
                                        <input type="text" class="form-control mb-3 bg-light" value="{{ $selectedBankName }}" readonly>
                                        <input type="hidden" name="selected_bank_name" id="selectedBankName" value="{{ $selectedBankName }}">
                                    @else
                                        <select name="selected_bank_name" id="selectedBankName" class="form-control mb-3 bg-light" required>
                                            <option value="">-- Select Bank --</option>
                                            @foreach(collect($banks)->map(fn ($bank) => $bank->bank_name ?? $bank->name ?? null)->filter()->unique()->values() as $bankName)
                                                <option value="{{ $bankName }}">{{ $bankName }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="w-100">
                                    <div class="alert alert-info mb-3" id="bankWorkflowNotice">
                                        <strong id="bankWorkflowLabel">{{ $selectedBank->workflow_label ?? 'Decentralized Bank' }}</strong>
                                        <div id="bankWorkflowDescription">{{ $selectedBank->workflow_description ?? 'New applications move directly from the bank branch to the Marketing Department.' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="forBranchBankId" class="form-label">Select Branch (For Which Application Is Submitted)<span style="color: red">*</span></label>
                                    @if($isBankUser)
                                        <input type="text" class="form-control mb-3 bg-light" value="{{ $selectedBranchName }}" readonly>
                                        <input type="hidden" name="for_branch_bank_id" id="forBranchBankId" value="{{ $selectedBranchId }}">
                                    @else
                                        <select name="for_branch_bank_id" id="forBranchBankId" class="form-control mb-3 bg-light" required>
                                            <option value="">-- Select Bank First --</option>
                                            @foreach($banks as $bank)
                                                @php
                                                    $bankId = $bank->branch_id ?? $bank->id ?? '';
                                                    $branchName = $bank->branch_name ?? $bank->branch ?? $bank->name ?? $bank->bank_name ?? 'Bank Branch';
                                                    $bankName = $bank->bank_name ?? $bank->name ?? '';
                                                @endphp
                                                <option value="{{ $bankId }}" data-bank-name="{{ $bankName }}" style="display:none;">
                                                    {{ $branchName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>

                            <?php 
                            // if ($isCentralizedBank && $bankName == 'DFCC'): 
                            ?>
                                <div class="row g-4" id="headOfficeFieldGroup">
                                    <div class="w-100">
                                        <label for="hoEnteredBy" class="form-label">Entered By (Head Office)<span style="color: red" id="headOfficeRequiredMark">*</span></label>
                                        <select name="ho_entered_by" id="hoEnteredBy" class="form-control mb-3 bg-light" required>
                                            <option value="">Select Officer</option>
                                            <option value="Tharangani Peiris">Tharangani Peiris</option>
                                            <option value="Sithumini Rathnayaka">Sithumini Rathnayaka</option>
                                            <option value="Nadeesha Thirkawala">Nadeesha Thirkawala</option>
                                            <option value="Jayapalan Prabhash">Jayapalan Prabhash</option>
                                        </select>
                                        <small class="text-muted d-block" id="headOfficeHelpText">Required only for centralized banks before the application moves to Marketing.</small>
                                    </div>
                                </div>
                            <?php 
                        // endif; 
                        ?>

                            <br/>
                            <h5>Personal Details</h5>
                            <hr class="hr-custom">

                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputCustomerName" class="form-label">Customer Full Name<span style="color: red">*</span></label>
                                    <input type="text" name="inputCustomerName" class="form-control mb-3 bg-light" id="inputCustomerName" placeholder="Enter Customer Full Name" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputNameWithInitials" class="form-label">Customer Name with Initials<span style="color: red">*</span></label>
                                    <input type="text" name="inputNameWithInitials" class="form-control mb-3 bg-light" id="inputNameWithInitials" placeholder="Enter Customer Name with Initials" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputCustomerAddress" class="form-label">Customer Address<span style="color: red">*</span></label>
                                    <input type="text" name="inputCustomerAddress" class="form-control mb-3 bg-light" id="inputCustomerAddress" placeholder="Enter Customer Address" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-4">
                                    <div class="w-100">
                                        <label for="inputProvince" class="form-label">Province<span style="color: red">*</span></label>
                                        <select class="form-control mb-3 bg-light" name="inputProvince" id="inputProvince" required>
                                        <option value="">-- Select Province --</option>
                                        @foreach($provinces as $province)
                                            <option value="{{ $province->id }}">{{$province->name}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="w-100">
                                        <label for="inputDistrict" class="form-label">District<span style="color: red">*</span></label>
                                        <select class="form-control mb-3 bg-light" name="inputDistrict" id="inputDistrict" required>
                                            <option value="">-- Select District --</option>
                                            
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4">
                                    <div class="w-100">
                                        <label for="inputDivisonalSecOffice" class="form-label">Divisonal Secretariat Office<span style="color: red">*</span></label>
                                        <input type="text" name="inputDivisonalSecOffice" class="form-control mb-3 bg-light" id="inputDivisonalSecOffice" placeholder="Enter Divisonal Secretariat Office" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputPassportNo" class="form-label">Passport No<span style="color: red">*</span></label>
                                        <input type="text" name="inputPassportNo" class="form-control mb-3 bg-light" id="inputPassportNo" placeholder="Ex: A1111111" required> 
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputIDNo" class="form-label">ID No<span style="color: red">*</span></label>
                                        <input type="text" name="inputIDNo" class="form-control mb-3 bg-light" id="inputIDNo" placeholder="Enter ID No" required> 
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputEmploymentCountry" class="form-label">Country of Employment<span style="color: red">*</span></label>
                                        <select class="form-control mb-3 bg-light" name="inputEmploymentCountry" id="inputEmploymentCountry" required>
                                        <option value="">-- Select Country --</option>
                                        @foreach($countries as $country)
                                            <option value="{{$country->id}}">{{$country->name}}</option>
                                        @endforeach

                                            
                                        </select>
                                    
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputTypeOfEmployment" class="form-label">Type of Employment<span style="color: red">*</span></label>
                                        <input type="text" name="inputTypeOfEmployment" class="form-control mb-3 bg-light" id="inputTypeOfEmployment" placeholder="Enter Type of Employment" required> 
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputGuaranteeAmount" class="form-label">Total Amount for which Credit Guarantee is Required (Rs.)<span style="color: red">*</span></label>
                                        <input type="text" name="inputGuaranteeAmount" class="form-control mb-3 bg-light" id="inputGuaranteeAmount" placeholder="Enter Credit Guarantee Amount" required>
                                    </div>
                                </div>
                            </div>


                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputPreDepartureAmount" class="form-label">Pre Departure Loan Amount (Rs.)<span style="color: red">*</span></label>
                                        <input type="text" name="inputPreDepartureAmount" class="form-control mb-3 bg-light" id="inputPreDepartureAmount" placeholder="Enter Pre Departure Loan Amount" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputRecruitmentAgency" class="form-label">Recruitment Agency Name<span style="color: red">*</span></label>
                                    <input type="text" name="inputRecruitmentAgency" class="form-control mb-3 bg-light" id="inputRecruitmentAgency" placeholder="Enter Recruitment Agency Name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputRecruitmentAgencyAddress" class="form-label">Recruitment Agency Address<span style="color: red">*</span></label>
                                    <input type="text" name="inputRecruitmentAgencyAddress" class="form-control mb-3 bg-light" id="inputRecruitmentAgencyAddress" placeholder="Enter Recruitment Agency Address" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputLabourLicenceNo" class="form-label">Labour Licence No<span style="color: red">*</span></label>
                                        <input type="text" name="inputLabourLicenceNo" class="form-control mb-3 bg-light" id="inputLabourLicenceNo" placeholder="Enter Labour Licence No" required >
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputGuaranteeRequiredDate" class="form-label">Date from which the Guarantee is Required<span style="color: red">*</span></label>
                                        <input type="date" name="inputGuaranteeRequiredDate" 
                                        min="
                                        <?php 
                                        // echo $todayDate; 
                                        ?>
                                        " 
                                        class="form-control mb-3 bg-light" id="inputGuaranteeRequiredDate" placeholder="Enter Date from which the Guarantee is Required" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="inputGuaranteeRequiredPeriod" class="form-label">Guarantee Required Period<span style="color: red">*</span></label>
                                        <select class="form-control mb-3 bg-light" name="inputGuaranteeRequiredPeriod" id="inputGuaranteeRequiredPeriod" required>
                                            <option value="" disabled selected>-- Select Guarantee Required Period --</option>                                           
                                            <option value="1">1 Year</option>
                                            <option value="2">2 Years</option>
                                            <option value="3">3 Years</option>
                                            <option value="4">4 Years</option>
                                            <option value="5">5 Years</option>
                                    </select>                                          
                                    </div>
                                </div>                                
                            </div>

                            <br/>
                            <h5>Guarantor Details</h5>
                            <hr class="hr-custom">

                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputGuarantor1Name" class="form-label">Name of Guarantor 1<span style="color: red">*</span></label>
                                    <input type="text" name="inputGuarantor1Name" class="form-control mb-3 bg-light" id="inputGuarantor1Name" placeholder="Enter Name of Guarantor 1" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputGuarantor1Address" class="form-label">Address of Guarantor 1<span style="color: red">*</span></label>
                                    <input type="text" name="inputGuarantor1Address" class="form-control mb-3 bg-light" id="inputGuarantor1Address" placeholder="Enter Address of Guarantor 1" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputGuarantor2Name" class="form-label">Name of Guarantor 2<span style="color: red">*</span></label>
                                    <input type="text" name="inputGuarantor2Name" class="form-control mb-3 bg-light" id="inputGuarantor2Name" placeholder="Enter Name of Guarantor 2" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputGuarantor2Address" class="form-label">Address of Guarantor 2<span style="color: red">*</span></label>
                                    <input type="text" name="inputGuarantor2Address" class="form-control mb-3 bg-light" id="inputGuarantor2Address" placeholder="Enter Address of Guarantor 2" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputGuarantor3Name" class="form-label">Name of Guarantor 3 (Optional)</label>
                                    <input type="text" name="inputGuarantor3Name" class="form-control mb-3 bg-light" id="inputGuarantor3Name" placeholder="Enter Name of Guarantor 3">
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputGuarantor3Address" class="form-label">Address of Guarantor 3 (Optional)</label>
                                    <input type="text" name="inputGuarantor3Address" class="form-control mb-3 bg-light" id="inputGuarantor3Address" placeholder="Enter Address of Guarantor 3">
                                </div>
                            </div>

                            <br/>
                            <h5>Documents Upload</h5>
                            <hr class="hr-custom">


                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="passportImage" class="form-label">Passport of the Applicant<span style="color: red">*</span></label>
                                        <input type="file" class="form-control mb-3 bg-light" name="passportImage" id="passportImage" accept="application/pdf" required>
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="travelAgentQuotation" class="form-label">Quotation from the Travel Agent</label>
                                        <input type="file" class="form-control mb-3 bg-light" name="travelAgentQuotation" id="travelAgentQuotation" accept="application/pdf">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="permanentResidenceLetter" class="form-label">Permanent Residence Letter</label>
                                        <input type="file" class="form-control mb-3 bg-light" name="permanentResidenceLetter" id="permanentResidenceLetter" accept="application/pdf">
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="recruitingAgentLetter" class="form-label">Letter from the Recruiting Agent</label>
                                        <input type="file" class="form-control mb-3 bg-light" name="recruitingAgentLetter" id="recruitingAgentLetter" accept="application/pdf">
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="SLBFELetter" class="form-label">SLBFE Letter<span style="color: red">*</span></label>
                                        <input type="file" class="form-control mb-3 bg-light" name="SLBFELetter" id="SLBFELetter" accept="application/pdf" required>
                                    </div>
                                </div>
                            </div>
                            

                            <br/>
                            <h5>Bank Details</h5>
                            <hr class="hr-custom">


                            <div class="row g-4">
                                <div class="w-100">
                                    <label for="inputApplicantBankAddress" class="form-label">Address of the Applicant Bank<span style="color: red">*</span></label>
                                    <input type="text" name="inputApplicantBankAddress" class="form-control mb-3 bg-light" id="inputApplicantBankAddress" placeholder="Enter Applicant Bank Address" required>
                                </div>
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="telNo" class="form-label">Tel No<span style="color: red">*</span></label>
                                        <input type="text" class="form-control mb-3 bg-light" name="telNo" id="telNo" placeholder="Enter Tel No Ex: 0712222222" required>
                                        
                                    </div>
                                </div>
                                
                            </div>
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="signatoryName" class="form-label">Name of the Signatory<span style="color: red">*</span></label>
                                        <input type="text" class="form-control mb-3 bg-light" name="signatoryName" id="signatoryName" placeholder="Enter Signatory Name" required>
                                        
                                    </div>
                                </div>
                                <div class="col-12 col-md-6">
                                    <div class="w-100">
                                        <label for="signatoryCapacity" class="form-label">Capacity of the Signatory<span style="color: red">*</span></label>
                                        <input type="text" class="form-control mb-3 bg-light" name="signatoryCapacity" id="signatoryCapacity" placeholder="Enter Capacity of the Signatory" required>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                    </div>
                    
                </div>
                <div class="container mt-4">
                    <div class="text-start">
                        <button onclick='return confirm("Are you sure you want to submit this application?")' type="Submit" class="btn btn-common" id="addApplication" name="addApplication" value="Submit"><i class="bi bi-save icon-spacing"></i>Submit</button>
                        <a href="{{ !empty(session('bank_profile')) ? route('bank.dashboard') : route('dashboard') }}" class="btn btn-common" onclick="return confirm('Are you sure you want to cancel this application?')">
                            <i class="bi bi-x-circle icon-spacing"></i> Cancel
                        </a>
                    </div>

                    
                    
                </div>
            </form>


            @include('layouts.footer')
            
        </div>
        </div>                

        
<script>
document.addEventListener('DOMContentLoaded', function () {
    const bankSelect = document.getElementById('selectedBankName');
    const branchSelect = document.getElementById('forBranchBankId');
    const provinceSelect = document.getElementById('inputProvince');
    const districtSelect = document.getElementById('inputDistrict');
    const workflowNotice = document.getElementById('bankWorkflowNotice');
    const workflowLabel = document.getElementById('bankWorkflowLabel');
    const workflowDescription = document.getElementById('bankWorkflowDescription');
    const headOfficeFieldGroup = document.getElementById('headOfficeFieldGroup');
    const headOfficeSelect = document.getElementById('hoEnteredBy');
    const headOfficeRequiredMark = document.getElementById('headOfficeRequiredMark');
    const headOfficeHelpText = document.getElementById('headOfficeHelpText');
    const bankLocked = @json($isBankUser);
    const defaultWorkflowType = @json($defaultWorkflowType);
    const workflowMap = @json($bankWorkflowMap);

    function currentWorkflowForBank(bankName) {
        if (bankName && workflowMap[bankName]) {
            return workflowMap[bankName];
        }

        return {
            type: defaultWorkflowType || 'decentralized',
            label: 'Decentralized Bank',
            description: 'New applications move directly from the bank branch to the Marketing Department.'
        };
    }

    function applyWorkflowState() {
        const selectedBankName = bankSelect ? bankSelect.value : '';
        const workflow = currentWorkflowForBank(selectedBankName);
        const requiresHeadOffice = workflow.type === 'centralized';

        if (workflowLabel) {
            workflowLabel.textContent = workflow.label || 'Decentralized Bank';
        }

        if (workflowDescription) {
            workflowDescription.textContent = workflow.description || 'New applications move directly from the bank branch to the Marketing Department.';
        }

        if (workflowNotice) {
            workflowNotice.classList.toggle('alert-warning', requiresHeadOffice);
            workflowNotice.classList.toggle('alert-info', !requiresHeadOffice);
        }

        if (headOfficeFieldGroup) {
            headOfficeFieldGroup.style.display = requiresHeadOffice ? '' : 'none';
        }

        if (headOfficeSelect) {
            headOfficeSelect.required = requiresHeadOffice;
            headOfficeSelect.disabled = !requiresHeadOffice;

            if (!requiresHeadOffice) {
                headOfficeSelect.value = '';
            }
        }

        if (headOfficeRequiredMark) {
            headOfficeRequiredMark.style.display = requiresHeadOffice ? '' : 'none';
        }

        if (headOfficeHelpText) {
            headOfficeHelpText.textContent = requiresHeadOffice
                ? 'Required for centralized banks before the application moves to Marketing.'
                : 'For decentralized banks, the application goes directly to the Marketing Department.';
        }
    }

    function updateBranchOptions() {
        if (!bankSelect || !branchSelect) {
            return;
        }

        const selectedBank = bankSelect.value;
        const branchOptions = Array.from(branchSelect.querySelectorAll('option[data-bank-name]'));
        let visibleCount = 0;

        branchSelect.value = '';

        branchOptions.forEach(option => {
            const shouldShow = selectedBank !== '' && option.dataset.bankName === selectedBank;
            option.style.display = shouldShow ? '' : 'none';

            if (shouldShow) {
                visibleCount++;
            }
        });

        const placeholder = branchSelect.querySelector('option[value=""]');
        if (placeholder) {
            placeholder.textContent = selectedBank === ''
                ? '-- Select Bank First --'
                : (visibleCount > 0 ? '-- Select Branch --' : '-- No Branches Available --');
        }
    }

    if (bankSelect && !bankLocked) {
        bankSelect.addEventListener('change', function () {
            updateBranchOptions();
            applyWorkflowState();
        });
        updateBranchOptions();
    }

    applyWorkflowState();

    provinceSelect.addEventListener('change', function () {
        const provinceId = this.value;

        // Clear the districts dropdown
        districtSelect.innerHTML = '<option value="">-- Select District --</option>';

        if (provinceId) {
            // Fetch districts for the selected province
            fetch(`/get-districts/${provinceId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(district => {
                        const option = document.createElement('option');
                        option.value = district.id;
                        option.textContent = district.name;
                        districtSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching districts:', error));
        }
    });
});
</script>


        
        </div>
        </div>
    </body>
</html>
