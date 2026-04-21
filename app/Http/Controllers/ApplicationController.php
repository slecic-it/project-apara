<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use Countable;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Support\BankPortalNotificationBuilder;
use App\Support\NotificationReadState;
use App\Support\SlecicNotificationBuilder;

class ApplicationController extends Controller {
    private function resolveContextView(string $defaultView): string
    {
        $routeName = request()->route()?->getName() ?? '';
        $bankView = 'bank.' . $defaultView;

        if (str_starts_with($routeName, 'bank.') && view()->exists($bankView)) {
            return $bankView;
        }

        return $defaultView;
    }

    public function create(){
        $countries = Country::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $banks = Schema::hasTable('banks')
            ? $this->bankDirectoryQuery()->get()->map(function ($bank) {
                return $this->decorateBankDirectoryEntry($bank);
            })
            : collect();

        if ($this->isBankContext()) {
            $bankProfile = $this->currentBankProfile();
            $banks = $banks->filter(function ($bank) use ($bankProfile) {
                return (int) ($bank->branch_id ?? $bank->id ?? 0) === (int) ($bankProfile['id'] ?? 0);
            })->values();
        }
        // $districts = District::orderBy('name')->get();
        return view('application', compact('countries', 'provinces', 'banks'));
    }

    public function store(Request $request)
    {
        $selectedBank = $this->resolveApplicationBankForRequest($request);
        $bankWorkflow = $this->resolveBankWorkflowMetadata($selectedBank);
        $requiresHeadOffice = $bankWorkflow['type'] === 'centralized';

        $request->validate([
            'selected_bank_name' => 'required|string|max:255',
            'for_branch_bank_id' => 'required',
            'ho_entered_by' => ($requiresHeadOffice ? 'required' : 'nullable') . '|string|max:255',
            'inputCustomerName' => 'required|string|max:255',
            'inputNameWithInitials' => 'required|string|max:255',
            'inputCustomerAddress' => 'required|string|max:500',
            'inputProvince' => 'required|integer',
            'inputDistrict' => 'required|integer',
            'inputDivisonalSecOffice' => 'required|string|max:255',
            'inputPassportNo' => 'required|string|max:100',
            'inputIDNo' => 'required|string|max:100',
            'inputEmploymentCountry' => 'required|integer',
            'inputTypeOfEmployment' => 'required|string|max:255',
            'inputRecruitmentAgency' => 'required|string|max:255',
            'inputRecruitmentAgencyAddress' => 'required|string|max:500',
            'inputLabourLicenceNo' => 'required|string|max:100',
            'telNo' => 'required|string|max:50',
        ]);

        $table = 'applications';
        $tableColumns = Schema::getColumnListing($table);
        $proposalNo = $this->generateProposalNumber();
        $employee = session('employee');
        $enteredByLabel = data_get($employee, 'name') ?: $request->input('ho_entered_by');
        $enteredBy = $this->normalizeApplicationActorValue(
            $table,
            'entered_by',
            $enteredByLabel,
            data_get($employee, 'id') ?: data_get($employee, 'user_id')
        );
        $headOfficeEntry = $this->normalizeApplicationActorValue(
            $table,
            'ho_entered_by',
            $requiresHeadOffice ? $request->input('ho_entered_by') : 'Marketing Department',
            data_get($employee, 'id') ?: data_get($employee, 'user_id')
        );
        $status = in_array('pending', $this->availableApplicationStatuses(), true) ? 'pending' : null;

        $columnValueMap = [
            'proposal_no' => $proposalNo,
            'selected_bank_name' => $request->input('selected_bank_name'),
            'for_branch_bank_id' => $request->input('for_branch_bank_id'),
            'ho_entered_by' => $headOfficeEntry,
            'nic' => $request->input('inputIDNo'),
            'passport_no' => $request->input('inputPassportNo'),
            'full_name' => $request->input('inputCustomerName'),
            'name_with_initials' => $request->input('inputNameWithInitials'),
            'address' => $request->input('inputCustomerAddress'),
            'division_sec_office' => $request->input('inputDivisonalSecOffice'),
            'district_id' => $request->input('inputDistrict'),
            'tel_no' => $request->input('telNo'),
            'employment_type' => $request->input('inputTypeOfEmployment'),
            'country_id' => $request->input('inputEmploymentCountry'),
            'recruitment_agency_name' => $request->input('inputRecruitmentAgency'),
            'recruitment_agency_address' => $request->input('inputRecruitmentAgencyAddress'),
            'labour_license_no' => $request->input('inputLabourLicenceNo'),
            'entered_by' => $enteredBy,
            'emp_type' => $bankWorkflow['type'],
            'head_office_approve' => $requiresHeadOffice ? 'pending' : 'not_required',
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $payload = [];

        foreach ($columnValueMap as $column => $value) {
            if (in_array($column, $tableColumns, true) && $value !== null) {
                $payload[$column] = $value;
            }
        }

        DB::table($table)->insert($payload);

        return redirect()
            ->route($this->isBankContext() ? 'bank.dashboard' : 'dashboard')
            ->with('success', $requiresHeadOffice
                ? "Application {$proposalNo} saved successfully. Head Office has been notified before Marketing."
                : "Application {$proposalNo} saved successfully. Marketing Department has been notified directly.");
    }

    public function getDistricts($provinceId){
        $districts = District::where('province_id', $provinceId)
                                    ->orderBy('name')
                                    ->get();

        return response()->json($districts);
    }

    public function show($id)
    {
        $applicationQuery = DB::table('applications')
            ->leftJoin('countries', 'applications.country_id', '=', 'countries.id')
            ->leftJoin('districts', 'applications.district_id', '=', 'districts.id')
            ->select(
                'applications.*',
                'countries.name as country_name',
                'districts.name as district_name'
            );

        foreach (['selected_bank_name', 'marketing_stage', 'marketing_first_approved_by', 'marketing_first_approved_at', 'marketing_second_approved_by', 'marketing_second_approved_at', 'marketing_ack_letter', 'marketing_comment'] as $column) {
            if (Schema::hasColumn('applications', $column)) {
                $applicationQuery->addSelect("applications.{$column}");
            }
        }

        $application = $applicationQuery
            ->where('applications.id', $id)
            ->first();

        abort_if(!$application, 404);
        abort_if($this->applicationOutsideBankScope($application), 403);

        $isBankContext = $this->isBankContext();
        $status = strtolower($application->status ?? 'pending');
        $statusClass = match ($status) {
            'accepted' => 'bg-info',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'completed', 'finalized' => 'bg-primary',
            'payment_pending' => 'bg-secondary',
            default => 'bg-warning text-dark',
        };

        $formatDate = function ($value) {
            return !empty($value) ? Carbon::parse($value)->format('Y-m-d H:i') : '-';
        };

        $marketingStage = $this->resolveMarketingStage($application);
        $requestedMarketingFilter = trim((string) request('marketing', ''));
        $marketingNavFilter = $requestedMarketingFilter !== ''
            ? $requestedMarketingFilter
            : $this->marketingFilterForStage($marketingStage);

        $applicationView = [
            'id' => (int) ($application->id ?? 0),
            'app_no' => 'APP-' . str_pad((string) ($application->id ?? 0), 4, '0', STR_PAD_LEFT),
            'proposal_no' => $application->proposal_no ?? '-',
            'full_name' => $application->full_name ?? '-',
            'name_with_initials' => $application->name_with_initials ?? '-',
            'nic' => $application->nic ?? '-',
            'passport_no' => $application->passport_no ?? '-',
            'address' => $application->address ?? '-',
            'district_name' => $application->district_name ?? '-',
            'division_sec_office' => $application->division_sec_office ?? '-',
            'tel_no' => $application->tel_no ?? '-',
            'country_name' => $application->country_name ?? '-',
            'employment_type' => $application->employment_type ?? '-',
            'recruitment_agency_name' => $application->recruitment_agency_name ?? '-',
            'recruitment_agency_address' => $application->recruitment_agency_address ?? '-',
            'labour_license_no' => $application->labour_license_no ?? '-',
            'entered_by' => $application->entered_by ?? '-',
            'approved_by' => $application->approved_by ?? '-',
            'approved_at' => $formatDate($application->approved_at ?? null),
            'created_at' => $formatDate($application->created_at ?? null),
            'updated_at' => $formatDate($application->updated_at ?? null),
            'selected_bank_name' => $application->selected_bank_name ?? '-',
            'status_label' => ucwords(str_replace('_', ' ', $status)),
            'status_class' => $statusClass,
            'is_bank_context' => $isBankContext,
            'marketing' => [
                'stage' => $marketingStage,
                'nav_filter' => $marketingNavFilter,
                'stage_label' => $this->marketingStageLabel($marketingStage),
                'first_approved_by' => $application->marketing_first_approved_by ?? '-',
                'first_approved_at' => $formatDate($application->marketing_first_approved_at ?? null),
                'second_approved_by' => $application->marketing_second_approved_by ?? '-',
                'second_approved_at' => $formatDate($application->marketing_second_approved_at ?? null),
                'ack_letter' => trim((string) ($application->marketing_ack_letter ?? '')),
                'comment' => trim((string) ($application->marketing_comment ?? '')),
                'can_second_approve' => in_array($marketingStage, ['first_approved', 'second_approved'], true),
                'steps' => [
                    [
                        'title' => 'Receive Application',
                        'description' => 'Marketing receives the application from the bank or after Head Office clearance.',
                        'state' => in_array($marketingStage, ['pending_first_approval', 'first_approved', 'second_approved', 'hold'], true) ? 'complete' : 'current',
                    ],
                    [
                        'title' => 'Give 1st Approval',
                        'description' => 'Issue the 1st approval and send an acknowledgement letter back to the bank.',
                        'state' => $marketingStage === 'first_approved'
                            ? 'current'
                            : (in_array($marketingStage, ['second_approved'], true) ? 'complete' : 'pending'),
                    ],
                    [
                        'title' => 'Give 2nd Approval',
                        'description' => 'Issue the final marketing approval and send the updated acknowledgement letter to the bank.',
                        'state' => $marketingStage === 'second_approved' ? 'current' : 'pending',
                    ],
                    [
                        'title' => 'Hold and Comment',
                        'description' => 'Place the application on hold and send the marketing comment to the bank for follow-up.',
                        'state' => $marketingStage === 'hold' ? 'current' : 'pending',
                    ],
                ],
            ],
        ];

        return view('application-show', compact('applicationView'));
    }

    public function updateMarketingProcess(Request $request, int $id)
    {
        abort_if($this->isBankContext(), 403);

        $validated = $request->validate([
            'action' => 'required|in:first_approve,second_approve,hold',
            'note' => 'nullable|string|max:2000',
        ]);

        $application = DB::table('applications')->where('id', $id)->first();
        abort_if(!$application, 404);

        $action = $validated['action'];
        $note = trim((string) ($validated['note'] ?? ''));
        $now = now();
        $actor = $this->currentMarketingActorName();
        $currentStage = $this->resolveMarketingStage($application);

        if ($action === 'second_approve' && !in_array($currentStage, ['first_approved', 'second_approved'], true)) {
            return redirect()
                ->route('application.show', ['id' => $id, 'marketing' => $this->marketingFilterForStage($currentStage)])
                ->withErrors(['marketing' => 'First approval must be completed before the second approval can be given.']);
        }

        if ($action === 'hold' && $note === '') {
            return redirect()
                ->route('application.show', ['id' => $id, 'marketing' => $this->marketingFilterForStage($currentStage)])
                ->withErrors(['marketing' => 'Please enter the comment that should be sent to the bank when placing the application on hold.']);
        }

        $payload = [];
        $acknowledgement = '';

        if ($action === 'first_approve') {
            $payload['status'] = 'accepted';
            $payload['marketing_stage'] = 'first_approved';
            $payload['marketing_first_approved_by'] = $actor;
            $payload['marketing_first_approved_at'] = $now;
            $payload['marketing_comment'] = null;
            $acknowledgement = $note !== ''
                ? $note
                : "Acknowledgement letter: Marketing issued the 1st approval for proposal {$application->proposal_no} on {$now->format('Y-m-d H:i')} and informed the bank to continue with the next review step.";
        } elseif ($action === 'second_approve') {
            $payload['status'] = 'approved';
            $payload['marketing_stage'] = 'second_approved';
            $payload['marketing_second_approved_by'] = $actor;
            $payload['marketing_second_approved_at'] = $now;
            $payload['marketing_comment'] = null;
            $acknowledgement = $note !== ''
                ? $note
                : "Acknowledgement letter: Marketing issued the 2nd approval for proposal {$application->proposal_no} on {$now->format('Y-m-d H:i')} and confirmed to the bank that the application is fully cleared by Marketing.";
        } else {
            $payload['status'] = 'pending';
            $payload['marketing_stage'] = 'hold';
            $payload['marketing_comment'] = $note;
            $payload['marketing_ack_letter'] = null;
        }

        if ($acknowledgement !== '') {
            $payload['marketing_ack_letter'] = $acknowledgement;
        }

        $tableColumns = Schema::getColumnListing('applications');
        $safePayload = [];

        foreach ($payload as $column => $value) {
            if (in_array($column, $tableColumns, true)) {
                $safePayload[$column] = $value;
            }
        }

        if (in_array('updated_at', $tableColumns, true)) {
            $safePayload['updated_at'] = $now;
        }

        DB::table('applications')->where('id', $id)->update($safePayload);

        $message = match ($action) {
            'first_approve' => 'First approval recorded and the acknowledgement letter is now available to the bank.',
            'second_approve' => 'Second approval recorded and the updated acknowledgement letter is now available to the bank.',
            default => 'Application placed on hold and the marketing comment is now visible to the bank.',
        };

        return redirect()
            ->route('application.show', ['id' => $id, 'marketing' => $this->marketingFilterForStage((string) ($payload['marketing_stage'] ?? $currentStage))])
            ->with('success', $message);
    }

     public function pending()
    {
        $applications = DB::table('applications')
                        ->where('status','pending');

        $this->applyBankApplicationScope($applications);

        $applications = $applications
                        ->get();

        return view($this->resolveContextView('pending'), compact('applications'));
    }

    public function accepted()
{
    $applications = DB::table('applications')
                    ->where('status','accepted');

    $this->applyBankApplicationScope($applications);

    $applications = $applications
                    ->get();

    return view($this->resolveContextView('accepted'), compact('applications'));
}

public function approved()
{
    $applications = DB::table('applications')
                    ->where('status','approved');

    $this->applyBankApplicationScope($applications);

    $applications = $applications
                    ->get();

    return view($this->resolveContextView('approved'), compact('applications'));
}

public function rejected()
{
    $applications = DB::table('applications')
                    ->where('status','rejected');

    $this->applyBankApplicationScope($applications);

    $applications = $applications
                    ->get();

    return view($this->resolveContextView('rejected'), compact('applications'));
}

public function banks()
{
    $banks = Schema::hasTable('banks')
        ? $this->bankDirectoryQuery()
            ->get()
            ->map(function ($bank) {
                $bank->logo_url = $this->resolveBankLogoUrl($bank);

                return $bank;
            })
        : collect();

    return view('banks', compact('banks'));
}

public function storeBank(Request $request)
{
    $request->validate([
        'bank_name' => 'required|string|max:255',
        'branch_name' => 'nullable|string|max:255',
        'bank_code' => 'nullable|string|max:100',
        'branch_grade' => 'nullable|string|max:100',
        'province' => 'nullable|string|max:100',
        'email' => 'required|email|max:255',
        'password' => 'required|min:8',
        'tel' => 'nullable|string|max:100',
        'bank_logo' => 'nullable|image|max:2048',
    ]);

    $bankName = $request->input('bank_name');
    $bankCode = $request->input('bank_code');
    $table = 'banks';
    $payload = [];
    $tableColumns = Schema::getColumnListing($table);

    if ($request->hasFile('bank_logo')) {
        $logoFile = $request->file('bank_logo');
        $storedLogoPath = $this->storeBankLogoFile($logoFile, $bankName, $bankCode);
    } else {
        $storedLogoPath = null;
    }

    try {
        DB::transaction(function () use ($table, $tableColumns, $request, $bankName, $bankCode, $storedLogoPath, &$payload) {
            foreach ([
                'bank_name' => $bankName,
                'name' => $bankName,
                'bank_code' => $bankCode,
                'code' => $bankCode,
                'branch_name' => $request->input('branch_name'),
                'branch_grade' => $request->input('branch_grade'),
                'province' => $request->input('province'),
                'email' => $request->input('email'),
                'tel' => $request->input('tel'),
                'status' => 1,
            ] as $column => $value) {
                if (in_array($column, $tableColumns, true) && $value !== null) {
                    $payload[$column] = $value;
                }
            }

            if ($storedLogoPath !== null) {
                foreach (['logo_path', 'logo', 'image', 'icon', 'icon_path'] as $logoColumn) {
                    if (in_array($logoColumn, $tableColumns, true)) {
                        $payload[$logoColumn] = $storedLogoPath;
                        break;
                    }
                }
            }

            if (in_array('created_at', $tableColumns, true)) {
                $payload['created_at'] = now();
            }

            if (in_array('updated_at', $tableColumns, true)) {
                $payload['updated_at'] = now();
            }

            DB::table($table)->insert($payload);
            $bankId = (int) DB::getPdo()->lastInsertId();
            $branchId = $this->upsertBranchRecord(
                $bankId,
                $request->input('branch_name'),
                $request->input('email'),
                $request->input('tel'),
                $request->input('branch_grade'),
                $request->input('province')
            );

            $this->syncBankLoginAccount(
                $branchId,
                $bankName,
                $request->input('branch_name'),
                $bankCode,
                $request->input('email'),
                $request->input('password')
            );
        });
    } catch (\Throwable $exception) {
        return redirect()
            ->route('banks')
            ->withErrors(['bank' => $exception->getMessage() ?: 'Bank could not be saved to the database. Please check the bank table structure.'])
            ->withInput();
    }

    return redirect()->route('banks')->with('success', 'Bank added successfully with login credentials.');
}

public function updateBank(Request $request, int $id)
{
    $request->validate([
        'bank_name' => 'required|string|max:255',
        'branch_name' => 'nullable|string|max:255',
        'bank_code' => 'nullable|string|max:100',
        'branch_grade' => 'nullable|string|max:100',
        'province' => 'nullable|string|max:100',
        'email' => 'required|email|max:255',
        'password' => 'nullable|min:8',
        'tel' => 'nullable|string|max:100',
        'bank_logo' => 'nullable|image|max:2048',
    ]);

    $table = 'banks';
    $existingBank = DB::table($table)->where('id', $id)->first();

    if (!$existingBank) {
        return redirect()
            ->route('banks')
            ->withErrors(['bank' => 'Selected bank record could not be found.']);
    }

    $bankName = $request->input('bank_name');
    $bankCode = $request->input('bank_code');
    $tableColumns = Schema::getColumnListing($table);

    if ($request->hasFile('bank_logo')) {
        $logoFile = $request->file('bank_logo');
        $storedLogoPath = $this->storeBankLogoFile($logoFile, $bankName, $bankCode);
    } else {
        $storedLogoPath = null;
    }

    try {
        DB::transaction(function () use ($table, $id, $tableColumns, $request, $bankName, $bankCode, $storedLogoPath) {
            $payload = [];

            foreach ([
                'bank_name' => $bankName,
                'name' => $bankName,
                'bank_code' => $bankCode,
                'code' => $bankCode,
                'branch_name' => $request->input('branch_name'),
                'branch_grade' => $request->input('branch_grade'),
                'province' => $request->input('province'),
                'email' => $request->input('email'),
                'tel' => $request->input('tel'),
            ] as $column => $value) {
                if (in_array($column, $tableColumns, true) && $value !== null) {
                    $payload[$column] = $value;
                }
            }

            if ($storedLogoPath !== null) {
                foreach (['logo_path', 'logo', 'image', 'icon', 'icon_path'] as $logoColumn) {
                    if (in_array($logoColumn, $tableColumns, true)) {
                        $payload[$logoColumn] = $storedLogoPath;
                        break;
                    }
                }
            }

            if (in_array('updated_at', $tableColumns, true)) {
                $payload['updated_at'] = now();
            }

            DB::table($table)->where('id', $id)->update($payload);
            $branchId = $this->upsertBranchRecord(
                $id,
                $request->input('branch_name'),
                $request->input('email'),
                $request->input('tel'),
                $request->input('branch_grade'),
                $request->input('province')
            );

            $this->syncBankLoginAccount(
                $branchId,
                $bankName,
                $request->input('branch_name'),
                $bankCode,
                $request->input('email'),
                $request->input('password')
            );
        });
    } catch (\Throwable $exception) {
        return redirect()
            ->route('banks')
            ->withErrors(['bank' => $exception->getMessage() ?: 'Bank could not be updated. Please check the bank table structure.'])
            ->withInput();
    }

    return redirect()->route('banks')->with('success', 'Bank updated successfully.');
}

public function destroyBank(int $id)
{
    $bank = DB::table('banks')->where('id', $id)->first();

    if (!$bank) {
        return redirect()
            ->route('banks')
            ->withErrors(['bank' => 'Selected bank record could not be found.']);
    }

    try {
        DB::transaction(function () use ($id) {
            $employeeUserIds = collect();
            $branchIds = collect();

            if (Schema::hasTable('branches')) {
                $branchIds = DB::table('branches')
                    ->where('bank_id', $id)
                    ->pluck('id')
                    ->filter()
                    ->unique()
                    ->values();
            }

            if (Schema::hasTable('bank_employees')) {
                $employeeUserIds = DB::table('bank_employees')
                    ->when($branchIds->isNotEmpty(), fn ($query) => $query->whereIn('branch_id', $branchIds), fn ($query) => $query->where('branch_id', $id))
                    ->pluck('user_id')
                    ->filter()
                    ->unique()
                    ->values();

                DB::table('bank_employees')
                    ->when($branchIds->isNotEmpty(), fn ($query) => $query->whereIn('branch_id', $branchIds), fn ($query) => $query->where('branch_id', $id))
                    ->delete();
            }

            if (Schema::hasTable('branches')) {
                DB::table('branches')->where('bank_id', $id)->delete();
            }

            DB::table('banks')->where('id', $id)->delete();

            if (Schema::hasTable('users')) {
                foreach ($employeeUserIds as $userId) {
                    $isStillLinked = Schema::hasTable('bank_employees')
                        ? DB::table('bank_employees')->where('user_id', $userId)->exists()
                        : false;

                    if (!$isStillLinked) {
                        DB::table('users')
                            ->where('id', $userId)
                            ->where('type', 1)
                            ->delete();
                    }
                }
            }
        });
    } catch (\Throwable $exception) {
        return redirect()
            ->route('banks')
            ->withErrors(['bank' => $exception->getMessage() ?: 'Bank could not be deleted.']);
    }

    return redirect()->route('banks')->with('success', 'Bank deleted successfully.');
}

public function employeelist()
{
    $employeeTable = 'slecic_employees';
    $query = DB::table($employeeTable);
    $selects = ["{$employeeTable}.id"];

    if (Schema::hasColumn($employeeTable, 'emp_no')) {
        $selects[] = "{$employeeTable}.emp_no as employee_id";
    } else {
        $selects[] = "{$employeeTable}.id as employee_id";
    }

    if (Schema::hasColumn($employeeTable, 'name')) {
        $selects[] = "{$employeeTable}.name as employee_name";
    } else {
        $selects[] = DB::raw("'' as employee_name");
    }

    if (Schema::hasColumn($employeeTable, 'desig_id')) {
        $query->leftJoin('designations', "{$employeeTable}.desig_id", '=', 'designations.id');
        $selects[] = 'designations.title as designation';
    } elseif (Schema::hasColumn($employeeTable, 'designation')) {
        $selects[] = "{$employeeTable}.designation as designation";
    } else {
        $selects[] = DB::raw("'' as designation");
    }

    if (Schema::hasColumn($employeeTable, 'dep_id')) {
        $query->leftJoin('departments', "{$employeeTable}.dep_id", '=', 'departments.id');
        $selects[] = 'departments.name as department';
    } elseif (Schema::hasColumn($employeeTable, 'department')) {
        $selects[] = "{$employeeTable}.department as department";
    } else {
        $selects[] = DB::raw("'' as department");
    }

    $employeelist = $query
        ->select($selects)
        ->orderBy("{$employeeTable}.id")
        ->get();

    return view('employeelist', compact('employeelist'));
}

public function reports()
{
    if (!Schema::hasTable('applications')) {
        $pending = 0;
        $accepted = 0;
        $approved = 0;
        $rejected = 0;

        return view($this->resolveContextView('reports'), compact('pending', 'accepted', 'approved', 'rejected'));
    }

    $pendingQuery = DB::table('applications')->where('status','pending');
    $acceptedQuery = DB::table('applications')->where('status','accepted');
    $approvedQuery = DB::table('applications')->where('status','approved');
    $rejectedQuery = DB::table('applications')->where('status','rejected');

    $this->applyBankApplicationScope($pendingQuery);
    $this->applyBankApplicationScope($acceptedQuery);
    $this->applyBankApplicationScope($approvedQuery);
    $this->applyBankApplicationScope($rejectedQuery);

    $pending = $pendingQuery->count();
    $accepted = $acceptedQuery->count();
    $approved = $approvedQuery->count();
    $rejected = $rejectedQuery->count();

    return view($this->resolveContextView('reports'), compact('pending', 'accepted','approved','rejected'));
}

public function settings()
{
    $settingsSummary = [
        'environment' => config('app.env'),
        'timezone' => config('app.timezone'),
        'locale' => config('app.locale'),
        'notifications' => 'Enabled',
    ];

    return view('settings', compact('settingsSummary'));
}

public function bankDashboard()
{
    $bankProfile = session('bank_profile');
    $bankWorkflow = $this->resolveBankWorkflowMetadata($bankProfile);
    $applicationQuery = DB::table('applications')
        ->leftJoin('countries', 'applications.country_id', '=', 'countries.id')
        ->select('applications.*', 'countries.name as country_name');

    $this->applyBankApplicationScope($applicationQuery);

    $applications = $applicationQuery
        ->orderByDesc('applications.created_at')
        ->orderByDesc('applications.id')
        ->get();

    $statusCounts = [
        'pending' => $applications->where('status', 'pending')->count(),
        'accepted' => $applications->where('status', 'accepted')->count(),
        'approved' => $applications->where('status', 'approved')->count(),
        'rejected' => $applications->where('status', 'rejected')->count(),
        'payment_pending' => $applications->where('status', 'payment_pending')->count(),
        'completed' => $applications->where('status', 'completed')->count(),
        'finalized' => $applications->where('status', 'finalized')->count(),
    ];

    $now = Carbon::now();
    $totalApplications = max($applications->count(), 1);
    $activeApplications = collect($statusCounts)->only(['pending', 'accepted', 'payment_pending'])->sum();
    $approvedTotal = ($statusCounts['approved'] ?? 0) + ($statusCounts['completed'] ?? 0) + ($statusCounts['finalized'] ?? 0);
    $approvalRate = round(($approvedTotal / $totalApplications) * 100);

    $summaryCards = [
        [
            'label' => 'Total Applications',
            'value' => $applications->count(),
            'hint' => 'Bank-side cases currently visible in the portal',
            'icon' => 'bi-collection',
        ],
        [
            'label' => 'Submitted This Month',
            'value' => $applications->filter(function ($application) use ($now) {
                return !empty($application->created_at)
                    && Carbon::parse($application->created_at)->isSameMonth($now);
            })->count(),
            'hint' => 'Fresh submissions added during ' . $now->format('F'),
            'icon' => 'bi-calendar-check',
        ],
        [
            'label' => 'Active Applications',
            'value' => $activeApplications,
            'hint' => 'Still moving through review, processing, or payment',
            'icon' => 'bi-hourglass-split',
        ],
        [
            'label' => 'Approval Yield',
            'value' => $approvalRate . '%',
            'hint' => 'Approved, completed, and finalized applications',
            'icon' => 'bi-graph-up-arrow',
        ],
    ];

    $reviewBuckets = [
        [
            'label' => 'Fresh Today',
            'value' => $applications->filter(function ($application) use ($now) {
                return !empty($application->created_at)
                    && Carbon::parse($application->created_at)->isToday();
            })->count(),
            'hint' => 'New submissions created today',
        ],
        [
            'label' => 'Needs Review',
            'value' => $applications->where('status', 'pending')->count(),
            'hint' => 'Applications waiting for the first bank decision',
        ],
        [
            'label' => 'Awaiting Finance',
            'value' => $applications->whereIn('status', ['approved', 'payment_pending'])->count(),
            'hint' => 'Approved cases not yet fully settled',
        ],
        [
            'label' => 'Recently Updated',
            'value' => $applications->filter(function ($application) use ($now) {
                return !empty($application->updated_at)
                    && Carbon::parse($application->updated_at)->greaterThanOrEqualTo($now->copy()->subDays(3));
            })->count(),
            'hint' => 'Cases touched in the last 72 hours',
        ],
    ];

    $workflowSteps = [
        [
            'title' => 'Submission Intake',
            'count' => $statusCounts['pending'] ?? 0,
            'description' => $bankWorkflow['type'] === 'centralized'
                ? 'Applications waiting for branch validation before Head Office review.'
                : 'Applications waiting for branch validation before Marketing review.',
            'state' => ($statusCounts['pending'] ?? 0) > 0 ? 'current' : 'idle',
        ],
        [
            'title' => $bankWorkflow['type'] === 'centralized' ? 'Head Office Approval' : 'Marketing Handoff',
            'count' => $statusCounts['accepted'] ?? 0,
            'description' => $bankWorkflow['type'] === 'centralized'
                ? 'Centralized-bank applications that need Head Office approval before they move onward.'
                : 'Decentralized-bank applications that move straight to the Marketing Department.',
            'state' => ($statusCounts['accepted'] ?? 0) > 0 ? 'active' : 'idle',
        ],
        [
            'title' => $bankWorkflow['type'] === 'centralized' ? 'Marketing Department' : 'Approval & Clearance',
            'count' => $statusCounts['approved'] ?? 0,
            'description' => $bankWorkflow['type'] === 'centralized'
                ? 'Applications cleared by Head Office and now ready for Marketing follow-up.'
                : 'Applications cleared and ready for finance completion steps.',
            'state' => ($statusCounts['approved'] ?? 0) > 0 ? 'active' : 'idle',
        ],
        [
            'title' => 'Payments & Closeout',
            'count' => ($statusCounts['payment_pending'] ?? 0) + ($statusCounts['completed'] ?? 0) + ($statusCounts['finalized'] ?? 0),
            'description' => 'Payment follow-up, settlement, and finalized case closure.',
            'state' => (($statusCounts['payment_pending'] ?? 0) + ($statusCounts['completed'] ?? 0) + ($statusCounts['finalized'] ?? 0)) > 0 ? 'complete' : 'idle',
        ],
    ];

    $destinationSummary = $applications
        ->groupBy(function ($application) {
            return $application->country_name ?: 'Unassigned';
        })
        ->map(function ($group, $country) use ($applications) {
            $count = $group->count();

            return [
                'country' => $country,
                'count' => $count,
                'share' => $applications->count() > 0 ? round(($count / $applications->count()) * 100) : 0,
            ];
        })
        ->sortByDesc('count')
        ->take(5)
        ->values();

    $recentActivity = $applications
        ->take(6)
        ->map(function ($application) {
            $status = strtolower($application->status ?? 'pending');
            $statusLabel = ucwords(str_replace('_', ' ', $status));

            return [
                'id' => $application->id,
                'app_no' => 'APP-' . str_pad((string) ($application->id ?? 0), 4, '0', STR_PAD_LEFT),
                'proposal_no' => $application->proposal_no ?? '-',
                'full_name' => $application->full_name ?? '-',
                'country_name' => $application->country_name ?? '-',
                'status' => $status,
                'status_label' => $statusLabel,
                'created_at' => !empty($application->created_at) ? Carbon::parse($application->created_at)->diffForHumans() : '-',
            ];
        });

    return view('bank.bankdashboard', compact(
        'applications',
        'statusCounts',
        'summaryCards',
        'reviewBuckets',
        'workflowSteps',
        'destinationSummary',
        'recentActivity',
        'bankProfile',
        'bankWorkflow'
    ));
}

public function bankNotifications(BankPortalNotificationBuilder $notificationBuilder)
{
    $notifications = $notificationBuilder->build(null);

    return view('bank.notifications', compact('notifications'));
}

public function slecicNotifications(SlecicNotificationBuilder $notificationBuilder)
{
    $notifications = $notificationBuilder->build(null);

    return view('slecic.notifications', compact('notifications'));
}

public function markBankNotificationRead(int $id, NotificationReadState $readState)
{
    $readState->markRead('bank', $id);

    return redirect()->route('bank.notifications');
}

public function markBankNotificationUnread(int $id, NotificationReadState $readState)
{
    $readState->markUnread('bank', $id);

    return redirect()->route('bank.notifications');
}

public function markSlecicNotificationRead(int $id, NotificationReadState $readState)
{
    $readState->markRead('slecic', $id);

    return redirect()->route('notifications');
}

public function markSlecicNotificationUnread(int $id, NotificationReadState $readState)
{
    $readState->markUnread('slecic', $id);

    return redirect()->route('notifications');
}

    public function allApplications()
{
    $marketingFilter = trim((string) request('marketing', ''));
    $filterLabel = match ($marketingFilter) {
        'review' => 'Review Application',
        'first_approval' => '1st Approval + Letter',
        'second_approval' => '2nd Approval + Letter',
        'hold' => 'Hold + Comment to Bank',
        default => 'All Applications',
    };

    if (!Schema::hasTable('applications')) {
        $applications = collect();

        return view($this->resolveContextView('all-applications'), compact('applications', 'marketingFilter', 'filterLabel'));
    }

    $applications = DB::table('applications')
        ->leftJoin('countries', 'applications.country_id', '=', 'countries.id')
        ->select('applications.*', 'countries.name as country_name');

    $this->applyBankApplicationScope($applications);

    $applications = $applications
        ->orderByDesc('applications.created_at')
        ->orderByDesc('applications.id')
        ->get();

    if ($marketingFilter !== '') {
        $applications = $applications
            ->filter(function ($application) use ($marketingFilter) {
                return $this->marketingFilterForStage($this->resolveMarketingStage($application)) === $marketingFilter;
            })
            ->values();
    }

    return view($this->resolveContextView('all-applications'), compact('applications', 'marketingFilter', 'filterLabel'));
}

public function monthlyReport()
{
    ['summary' => $summary, 'recentInvoices' => $recentInvoices] = $this->monthlyReportData();

    $monthlyReportPdfRouteName = str_starts_with((string) request()->route()?->getName(), 'bank.')
        ? 'bank.monthly-report.pdf'
        : 'monthly-report.pdf';

    return view($this->resolveContextView('analytics.monthly-report'), compact('summary', 'recentInvoices', 'monthlyReportPdfRouteName'));
}

public function monthlyReportPdf()
{
    ['summary' => $summary, 'recentInvoices' => $recentInvoices] = $this->monthlyReportData();

    $pdfContent = $this->buildMonthlyReportPdf($summary, $recentInvoices);
    $fileName = 'monthly-report-' . Carbon::now()->format('Y-m') . '.pdf';

    return response($pdfContent, 200, [
        'Content-Type' => 'application/pdf',
        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        'Content-Length' => strlen($pdfContent),
    ]);
}

public function yearlyReport()
{
    $now = Carbon::now();
    $applicationCounts = $this->applicationStatusCounts();

    $invoiceMonths = DB::table('apara_invoices')
        ->selectRaw('MONTH(created_at) as month_number, COUNT(*) as invoice_count, SUM(premium_amount) as premium_total')
        ->whereYear('created_at', $now->year)
        ->groupByRaw('MONTH(created_at)')
        ->orderByRaw('MONTH(created_at)')
        ->get();

    $paymentMonths = DB::table('apara_payment_histories')
        ->selectRaw('MONTH(payment_date) as month_number, COUNT(*) as payment_count, SUM(total_amount) as payment_total')
        ->whereYear('payment_date', $now->year)
        ->groupByRaw('MONTH(payment_date)')
        ->orderByRaw('MONTH(payment_date)')
        ->get()
        ->keyBy('month_number');

    $months = collect(range(1, 12))->map(function ($month) use ($invoiceMonths, $paymentMonths) {
        $invoice = $invoiceMonths->firstWhere('month_number', $month);
        $payment = $paymentMonths->get($month);

        return [
            'label' => Carbon::create()->month($month)->format('M'),
            'invoice_count' => $invoice->invoice_count ?? 0,
            'premium_total' => $invoice->premium_total ?? 0,
            'payment_count' => $payment->payment_count ?? 0,
            'payment_total' => $payment->payment_total ?? 0,
        ];
    });

    $summary = [
        'year' => $now->year,
        'applications' => $applicationCounts,
        'applications_total' => array_sum($applicationCounts),
        'yearly_premium_total' => $months->sum('premium_total'),
        'yearly_payment_total' => $months->sum('payment_total'),
    ];

    return view($this->resolveContextView('analytics.yearly-report'), compact('summary', 'months'));
}

public function performanceReport()
{
    $applicationCounts = $this->applicationStatusCounts();
    $totalApplications = max(array_sum($applicationCounts), 1);
    $paidInvoices = DB::table('apara_invoices')->where('status', 'paid');
    $allInvoices = DB::table('apara_invoices');
    $payments = DB::table('apara_payment_histories');

    $metrics = [
        [
            'label' => 'Approval Rate',
            'value' => number_format(($applicationCounts['approved'] / $totalApplications) * 100, 1) . '%',
            'hint' => $applicationCounts['approved'] . ' approved out of ' . array_sum($applicationCounts) . ' applications',
        ],
        [
            'label' => 'Acceptance Rate',
            'value' => number_format(($applicationCounts['accepted'] / $totalApplications) * 100, 1) . '%',
            'hint' => $applicationCounts['accepted'] . ' accepted applications in progress',
        ],
        [
            'label' => 'Invoice Collection Rate',
            'value' => number_format(((clone $paidInvoices)->count() / max((clone $allInvoices)->count(), 1)) * 100, 1) . '%',
            'hint' => (clone $paidInvoices)->count() . ' paid invoices processed',
        ],
        [
            'label' => 'Payment Success Rate',
            'value' => number_format(((clone $payments)->where('status', 'paid')->count() / max((clone DB::table('apara_payment_histories'))->count(), 1)) * 100, 1) . '%',
            'hint' => (clone DB::table('apara_payment_histories'))->where('status', 'paid')->count() . ' successful payment batches',
        ],
    ];

    $teams = [
        ['team' => 'Application Desk', 'completed' => $applicationCounts['accepted'] + $applicationCounts['approved'], 'backlog' => $applicationCounts['pending']],
        ['team' => 'Approval Desk', 'completed' => $applicationCounts['approved'], 'backlog' => $applicationCounts['accepted']],
        ['team' => 'Finance Desk', 'completed' => (clone $paidInvoices)->count(), 'backlog' => (clone DB::table('apara_invoices'))->where('status', 'due')->count()],
        ['team' => 'Support Desk', 'completed' => (clone DB::table('apara_payment_histories'))->where('status', 'paid')->count(), 'backlog' => (clone DB::table('apara_payment_histories'))->where('status', 'pending')->count()],
    ];

    return view($this->resolveContextView('analytics.performance-report'), compact('metrics', 'teams', 'applicationCounts'));
}

public function auditTrail()
{
    $invoiceActivities = DB::table('apara_invoices')
        ->select('invoice_no as reference', 'customer_name as subject', 'status', 'approval_status', 'created_at')
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

    $paymentActivities = DB::table('apara_payment_histories')
        ->select('payment_ref as reference', 'payment_type as subject', 'status', 'payment_date')
        ->orderByDesc('payment_date')
        ->limit(10)
        ->get();

    return view($this->resolveContextView('analytics.audit-trail'), compact('invoiceActivities', 'paymentActivities'));
}

public function tickets()
{
    $ticketSummary = [
        'open' => 8,
        'in_progress' => 5,
        'resolved' => 21,
        'sla_risk' => 2,
    ];

    $tickets = collect([
        ['id' => 'SUP-1001', 'subject' => 'Unable to update invoice status', 'priority' => 'High', 'owner' => 'Finance Desk', 'status' => 'Open'],
        ['id' => 'SUP-1002', 'subject' => 'Dashboard counts not refreshing', 'priority' => 'Medium', 'owner' => 'MIS Team', 'status' => 'In Progress'],
        ['id' => 'SUP-1003', 'subject' => 'Need password reset for branch user', 'priority' => 'Low', 'owner' => 'Admin', 'status' => 'Resolved'],
    ]);

    return view($this->resolveContextView('support.tickets'), compact('ticketSummary', 'tickets'));
}

public function faq()
{
    $faqs = collect([
        ['question' => 'How do I move an application from pending to accepted?', 'answer' => 'Open the application from the dashboard or application list, review the details, and update the status once validation is complete.'],
        ['question' => 'Where can I view paid invoices?', 'answer' => 'Use the Finance section in the sidebar and open the Invoice page to see paid records.'],
        ['question' => 'Why is a payment missing from history?', 'answer' => 'Check the selected date range and status filters first. If it still does not appear, raise a support ticket with the payment reference.'],
        ['question' => 'How do I contact support for urgent issues?', 'answer' => 'Use the Contact Support page and mark the issue as high priority, then call the help desk extension listed there.'],
    ]);

    return view($this->resolveContextView('support.faq'), compact('faqs'));
}

public function contact()
{
    $contacts = collect([
        ['team' => 'Help Desk', 'email' => 'helpdesk@apara.local', 'phone' => '+94 11 555 0101', 'hours' => '8:30 AM - 5:00 PM'],
        ['team' => 'Finance Support', 'email' => 'finance-support@apara.local', 'phone' => '+94 11 555 0102', 'hours' => '8:30 AM - 5:00 PM'],
        ['team' => 'Technical Support', 'email' => 'tech-support@apara.local', 'phone' => '+94 11 555 0103', 'hours' => '24/7 monitoring'],
    ]);

    return view($this->resolveContextView('support.contact'), compact('contacts'));
}

public function blacklist()
{
    $blacklistEntries = collect([
        ['reference' => 'BL-1001', 'bank_name' => 'BOC', 'subject' => 'Duplicate guarantee request', 'status' => 'Restricted', 'updated_at' => Carbon::now()->subDays(2)->format('Y-m-d')],
        ['reference' => 'BL-1002', 'bank_name' => 'DFCC', 'subject' => 'Incomplete compliance documents', 'status' => 'Watchlist', 'updated_at' => Carbon::now()->subDays(5)->format('Y-m-d')],
        ['reference' => 'BL-1003', 'bank_name' => 'NSB', 'subject' => 'Manual review hold', 'status' => 'Restricted', 'updated_at' => Carbon::now()->subWeek()->format('Y-m-d')],
    ]);

    return view($this->resolveContextView('blacklist'), compact('blacklistEntries'));
}

private function applicationStatusCounts(): array
{
    return [
        'pending' => DB::table('applications')->where('status', 'pending')->count(),
        'accepted' => DB::table('applications')->where('status', 'accepted')->count(),
        'approved' => DB::table('applications')->where('status', 'approved')->count(),
        'rejected' => DB::table('applications')->where('status', 'rejected')->count(),
    ];
}

private function generateProposalNumber(): string
{
    $latestId = DB::table('applications')->max('id') ?? 0;

    return 'PR-' . str_pad((string) ($latestId + 1), 4, '0', STR_PAD_LEFT);
}

private function availableApplicationStatuses(): array
{
    try {
        return DB::table('applications')
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->pluck('status')
            ->filter()
            ->values()
            ->all();
    } catch (\Throwable $exception) {
        return [];
    }
}

private function resolveMarketingStage(object $application): string
{
    $storedStage = strtolower(trim((string) ($application->marketing_stage ?? '')));

    if ($storedStage !== '') {
        return $storedStage;
    }

    $status = strtolower(trim((string) ($application->status ?? 'pending')));

    return match ($status) {
        'approved', 'payment_pending', 'completed', 'finalized' => 'second_approved',
        'accepted' => 'first_approved',
        default => 'pending_first_approval',
    };
}

private function marketingStageLabel(string $stage): string
{
    return match ($stage) {
        'first_approved' => '1st Approval Given',
        'second_approved' => '2nd Approval Given',
        'hold' => 'On Hold',
        default => 'Pending 1st Approval',
    };
}

private function marketingFilterForStage(string $stage): string
{
    return match (strtolower(trim($stage))) {
        'first_approved' => 'first_approval',
        'second_approved' => 'second_approval',
        'hold' => 'hold',
        default => 'review',
    };
}

private function currentMarketingActorName(): string
{
    $employeeName = trim((string) data_get(session('employee'), 'name', ''));

    if ($employeeName !== '') {
        return $employeeName;
    }

    $user = auth()->user();

    return trim((string) data_get($user, 'name'))
        ?: trim((string) data_get($user, 'email'))
        ?: 'Marketing Department';
}

private function resolveBankLogoUrl(object $bank): ?string
{
    foreach (['logo_path', 'logo', 'image', 'icon', 'icon_path'] as $column) {
        $value = trim((string) data_get($bank, $column, ''));

        if ($value !== '') {
            return asset(ltrim($value, '/'));
        }
    }

    if ($storedLogoPath = $this->findStoredBankLogoPath($bank)) {
        return asset($storedLogoPath);
    }

    $bankName = strtoupper(trim((string) ($bank->bank_name ?? $bank->name ?? '')));
    $bankCode = strtoupper(trim((string) ($bank->bank_code ?? $bank->code ?? $bank->branch_code ?? '')));
    $bankIdentifier = trim($bankName . ' ' . $bankCode);

    if (str_contains($bankIdentifier, 'DFCC')) {
        return asset('images/dfcc-logo.jpg');
    }

    if (str_contains($bankIdentifier, 'LOLC')) {
        return asset('images/lolc-logo.jpg');
    }

    if (str_contains($bankIdentifier, 'BOC')) {
        return asset('images/boc.png');
    }

    return null;
}

private function decorateBankDirectoryEntry(object $bank): object
{
    $workflow = $this->resolveBankWorkflowMetadata($bank);
    $bank->workflow_type = $workflow['type'];
    $bank->workflow_label = $workflow['label'];
    $bank->workflow_description = $workflow['description'];
    $bank->requires_head_office = $workflow['type'] === 'centralized';

    return $bank;
}

private function resolveApplicationBankForRequest(Request $request): ?object
{
    $branchId = (int) $request->input('for_branch_bank_id');
    $bankName = trim((string) $request->input('selected_bank_name'));

    if (!Schema::hasTable('banks')) {
        return null;
    }

    $query = $this->bankDirectoryQuery();

    if ($branchId > 0 && Schema::hasTable('branches')) {
        return $query->where('branches.id', $branchId)->first();
    }

    if ($branchId > 0) {
        return $query->where('banks.id', $branchId)->first();
    }

    if ($bankName !== '') {
        return $query->where('banks.name', $bankName)->first();
    }

    return null;
}

private function normalizeApplicationActorValue(string $table, string $column, ?string $label, $numericFallback = null)
{
    if (!Schema::hasColumn($table, $column)) {
        return $label;
    }

    $columnType = Schema::getColumnType($table, $column);

    if (in_array($columnType, ['integer', 'int', 'bigint', 'smallint', 'tinyint'], true)) {
        return is_numeric($numericFallback) ? (int) $numericFallback : 0;
    }

    return $label;
}

private function resolveBankWorkflowMetadata($bank): array
{
    $identifier = strtoupper(trim(implode(' ', array_filter([
        data_get($bank, 'bank_name', data_get($bank, 'name', '')),
        data_get($bank, 'bank_code', data_get($bank, 'code', '')),
        data_get($bank, 'branch_grade', ''),
        data_get($bank, 'branch_name', ''),
    ]))));

    if ($identifier !== '') {
        foreach (['CENTRAL', 'HEAD OFFICE', 'HEAD-OFFICE', 'HQ'] as $keyword) {
            if (str_contains($identifier, $keyword)) {
                return [
                    'type' => 'centralized',
                    'label' => 'Centralized Bank',
                    'description' => 'New applications go to the bank Head Office first, then move to the Marketing Department after approval.',
                ];
            }
        }

        foreach (['DE-CENTRAL', 'DECENTRAL', 'REGIONAL', 'BRANCH'] as $keyword) {
            if (str_contains($identifier, $keyword)) {
                return [
                    'type' => 'decentralized',
                    'label' => 'Decentralized Bank',
                    'description' => 'New applications move directly from the bank branch to the Marketing Department.',
                ];
            }
        }

        foreach (['BOC', 'DFCC', 'NSB'] as $keyword) {
            if (str_contains($identifier, $keyword)) {
                return [
                    'type' => 'centralized',
                    'label' => 'Centralized Bank',
                    'description' => 'New applications go to the bank Head Office first, then move to the Marketing Department after approval.',
                ];
            }
        }
    }

    return [
        'type' => 'decentralized',
        'label' => 'Decentralized Bank',
        'description' => 'New applications move directly from the bank branch to the Marketing Department.',
    ];
}

private function bankDirectoryQuery()
{
    $hasBranchesTable = Schema::hasTable('branches');
    $hasProvinceJoin = $hasBranchesTable && Schema::hasTable('districts') && Schema::hasTable('provinces');
    $query = DB::table('banks');

    if ($hasBranchesTable) {
        $query->leftJoin('branches', 'branches.bank_id', '=', 'banks.id');
    }

    if ($hasProvinceJoin) {
        $query->leftJoin('districts', 'districts.id', '=', 'branches.district_id')
            ->leftJoin('provinces', 'provinces.id', '=', 'districts.province_id');
    }

    $query->select([
        DB::raw($hasBranchesTable ? 'COALESCE(branches.id, banks.id) as id' : 'banks.id as id'),
        DB::raw($hasBranchesTable ? 'branches.id as branch_id' : 'NULL as branch_id'),
        'banks.id as bank_id',
        'banks.name',
        'banks.code',
        'banks.status',
        'banks.created_at',
        'banks.updated_at',
        DB::raw($hasBranchesTable ? "COALESCE(branches.name, '') as branch_name" : "'' as branch_name"),
        DB::raw($hasBranchesTable ? "COALESCE(branches.grade, '') as branch_grade" : "'' as branch_grade"),
        DB::raw($hasBranchesTable ? "COALESCE(branches.email, '') as email" : "'' as email"),
        DB::raw($hasBranchesTable ? "COALESCE(branches.tel_no, '') as tel" : "'' as tel"),
        DB::raw($hasProvinceJoin ? "COALESCE(provinces.name, '') as province" : "'' as province"),
    ]);

    if (Schema::hasColumn('banks', 'name')) {
        $query->orderBy('banks.name');
    }

    if (Schema::hasTable('branches') && Schema::hasColumn('branches', 'name')) {
        $query->orderBy('branches.name');
    }

    return $query;
}

private function isBankContext(): bool
{
    return !empty($this->currentBankProfile());
}

private function currentBankProfile(): ?array
{
    $profile = session('bank_profile');

    return is_array($profile) && !empty($profile) ? $profile : null;
}

private function applyBankApplicationScope($query, string $tableAlias = 'applications'): void
{
    $bankProfile = $this->currentBankProfile();
    $bankActorIds = $this->currentBankActorIds();

    if (!$bankProfile && $bankActorIds === []) {
        return;
    }

    $query->where(function ($scopedQuery) use ($bankProfile, $bankActorIds, $tableAlias) {
        $hasScopedCondition = false;

        if ($bankProfile && Schema::hasColumn('applications', 'for_branch_bank_id') && !empty($bankProfile['id'])) {
            $scopedQuery->where("{$tableAlias}.for_branch_bank_id", $bankProfile['id']);
            $hasScopedCondition = true;
        }

        if ($bankProfile && Schema::hasColumn('applications', 'selected_bank_name') && !empty($bankProfile['bank_name'])) {
            if ($hasScopedCondition) {
                $scopedQuery->orWhere("{$tableAlias}.selected_bank_name", $bankProfile['bank_name']);
            } else {
                $scopedQuery->where("{$tableAlias}.selected_bank_name", $bankProfile['bank_name']);
                $hasScopedCondition = true;
            }
        }

        if (Schema::hasColumn('applications', 'entered_by') && $bankActorIds !== []) {
            if ($hasScopedCondition) {
                $scopedQuery->orWhereIn("{$tableAlias}.entered_by", $bankActorIds);
            } else {
                $scopedQuery->whereIn("{$tableAlias}.entered_by", $bankActorIds);
                $hasScopedCondition = true;
            }
        }

        if (!$hasScopedCondition) {
            $scopedQuery->whereRaw('1 = 0');
        }
    });
}

private function applicationOutsideBankScope(object $application): bool
{
    $bankProfile = $this->currentBankProfile();
    $bankActorIds = $this->currentBankActorIds();

    if (!$bankProfile && $bankActorIds === []) {
        return false;
    }

    $matchesBranch = $bankProfile && isset($application->for_branch_bank_id) && !empty($bankProfile['id'])
        ? (int) $application->for_branch_bank_id === (int) $bankProfile['id']
        : false;

    $matchesBank = $bankProfile && isset($application->selected_bank_name) && !empty($bankProfile['bank_name'])
        ? trim((string) $application->selected_bank_name) === trim((string) $bankProfile['bank_name'])
        : false;

    $matchesActor = isset($application->entered_by) && $bankActorIds !== []
        ? in_array((int) $application->entered_by, $bankActorIds, true)
        : false;

    if (isset($application->for_branch_bank_id) || isset($application->selected_bank_name) || isset($application->entered_by)) {
        return !$matchesBranch && !$matchesBank && !$matchesActor;
    }

    return true;
}

private function currentBankActorIds(): array
{
    $employee = session('employee');
    $candidateIds = array_filter([
        is_numeric(data_get($employee, 'id')) ? (int) data_get($employee, 'id') : null,
        is_numeric(data_get($employee, 'user_id')) ? (int) data_get($employee, 'user_id') : null,
    ], fn ($value) => $value !== null);

    return array_values(array_unique($candidateIds));
}

private function upsertBranchRecord(
    int $bankId,
    ?string $branchName,
    ?string $email,
    ?string $tel,
    ?string $grade,
    ?string $provinceName
): int
{
    if (!Schema::hasTable('branches')) {
        // Fall back to the bank id when the schema stores bank logins without a dedicated branches table.
        return $bankId;
    }

    $branch = DB::table('branches')->where('bank_id', $bankId)->first();
    $districtId = $this->resolveDistrictIdForProvinceName($provinceName);
    $branchColumns = Schema::getColumnListing('branches');
    $payload = [];

    foreach ([
        'bank_id' => $bankId,
        'name' => $branchName ?: 'Head Office',
        'email' => $email,
        'tel_no' => $tel,
        'grade' => $grade,
        'district_id' => $districtId,
    ] as $column => $value) {
        if (in_array($column, $branchColumns, true) && $value !== null) {
            $payload[$column] = $value;
        }
    }

    if (in_array('updated_at', $branchColumns, true)) {
        $payload['updated_at'] = now();
    }

    if ($branch) {
        DB::table('branches')->where('id', $branch->id)->update($payload);
        return (int) $branch->id;
    }

    if (in_array('created_at', $branchColumns, true)) {
        $payload['created_at'] = now();
    }

    DB::table('branches')->insert($payload);

    return (int) DB::getPdo()->lastInsertId();
}

private function resolveDistrictIdForProvinceName(?string $provinceName): int
{
    $normalizedProvince = strtolower(trim((string) $provinceName));
    $provinceId = null;

    if ($normalizedProvince !== '' && Schema::hasTable('provinces')) {
        $provinceId = DB::table('provinces')
            ->whereRaw('LOWER(name) = ?', [$normalizedProvince])
            ->value('id');

        if (!$provinceId) {
            $aliases = [
                'northern' => 'nothern',
                'north central' => 'north eastern',
            ];

            if (isset($aliases[$normalizedProvince])) {
                $provinceId = DB::table('provinces')
                    ->whereRaw('LOWER(name) = ?', [$aliases[$normalizedProvince]])
                    ->value('id');
            }
        }
    }

    $districtId = $provinceId
        ? DB::table('districts')->where('province_id', $provinceId)->orderBy('id')->value('id')
        : DB::table('districts')->orderBy('id')->value('id');

    if (!$districtId) {
        throw new \RuntimeException('No district record is available for branch creation.');
    }

    return (int) $districtId;
}

private function storeBankLogoFile($logoFile, ?string $bankName, ?string $bankCode): string
{
    $logoDirectory = public_path('images/bank-logos');

    if (!is_dir($logoDirectory)) {
        mkdir($logoDirectory, 0777, true);
    }

    $baseName = $this->bankLogoBaseName($bankName, $bankCode);

    foreach (glob($logoDirectory . DIRECTORY_SEPARATOR . $baseName . '.*') ?: [] as $existingFile) {
        if (is_file($existingFile)) {
            unlink($existingFile);
        }
    }

    $extension = strtolower($logoFile->getClientOriginalExtension() ?: 'jpg');
    $logoFileName = $baseName . '.' . $extension;
    $logoFile->move($logoDirectory, $logoFileName);

    return 'images/bank-logos/' . $logoFileName;
}

private function findStoredBankLogoPath(object $bank): ?string
{
    $logoDirectory = public_path('images/bank-logos');

    if (!is_dir($logoDirectory)) {
        return null;
    }

    $baseName = $this->bankLogoBaseName(
        (string) ($bank->bank_name ?? $bank->name ?? ''),
        (string) ($bank->bank_code ?? $bank->code ?? $bank->branch_code ?? '')
    );

    $matches = glob($logoDirectory . DIRECTORY_SEPARATOR . $baseName . '.*') ?: [];

    if ($matches === []) {
        return null;
    }

    return 'images/bank-logos/' . basename($matches[0]);
}

private function bankLogoBaseName(?string $bankName, ?string $bankCode): string
{
    $parts = array_filter([
        Str::slug((string) $bankName),
        Str::slug((string) $bankCode),
    ]);

    return $parts !== [] ? implode('-', $parts) : 'bank-logo';
}

private function syncBankLoginAccount(
    int $branchId,
    ?string $bankName,
    ?string $branchName,
    ?string $bankCode,
    string $email,
    ?string $password
): void
{
    if (!Schema::hasTable('users')) {
        throw new \RuntimeException('Users table is not available for bank login creation.');
    }

    if (!Schema::hasTable('bank_employees')) {
        throw new \RuntimeException('Bank employees table is not available for bank login creation.');
    }

    $employee = DB::table('bank_employees')->where('branch_id', $branchId)->first();
    $existingUser = User::where('email', $email)->first();
    $linkedUser = $employee?->user_id ? User::find($employee->user_id) : null;

    if ($existingUser && (!$linkedUser || $existingUser->id !== $linkedUser->id)) {
        throw new \RuntimeException('That email address is already being used by another user.');
    }

    $displayName = $branchName ?: $bankName ?: 'Bank User';
    $userColumns = Schema::getColumnListing('users');
    $user = $linkedUser ?? new User();

    foreach ([
        'name' => $displayName,
        'email' => $email,
        'type' => 1,
        'status' => 1,
        'first_login' => 'yes',
        'mobile_no' => 0,
    ] as $column => $value) {
        if (in_array($column, $userColumns, true)) {
            $user->{$column} = $value;
        }
    }

    if ($password !== null && in_array('password', $userColumns, true)) {
        $user->password = Hash::make($password);
    }

    $user->save();

    $employeeColumns = Schema::getColumnListing('bank_employees');
    $employeePayload = [];
    $roleValue = 0;
    $statusValue = 'active';

    if (in_array('role', $employeeColumns, true)) {
        $roleType = Schema::getColumnType('bank_employees', 'role');
        $roleValue = in_array($roleType, ['integer', 'int', 'bigint', 'smallint', 'tinyint'], true) ? 1 : 'bank_user';
    }

    if (in_array('status', $employeeColumns, true)) {
        $statusType = Schema::getColumnType('bank_employees', 'status');
        $statusValue = in_array($statusType, ['integer', 'int', 'bigint', 'smallint', 'tinyint', 'boolean'], true) ? 1 : 'active';
    }

    foreach ([
        'name' => $displayName,
        'user_id' => $user->id,
        'branch_id' => $branchId,
        'role' => $roleValue,
        'role_id' => 0,
        'status' => $statusValue,
    ] as $column => $value) {
        if (in_array($column, $employeeColumns, true)) {
            $employeePayload[$column] = $value;
        }
    }

    if (in_array('updated_at', $employeeColumns, true)) {
        $employeePayload['updated_at'] = now();
    }

    if ($employee) {
        DB::table('bank_employees')->where('id', $employee->id)->update($employeePayload);
        return;
    }

    if (in_array('created_at', $employeeColumns, true)) {
        $employeePayload['created_at'] = now();
    }

    DB::table('bank_employees')->insert($employeePayload);
}

private function monthlyReportData(): array
{
    $now = Carbon::now();
    $applicationCounts = $this->applicationStatusCounts();
    $monthlyInvoices = DB::table('apara_invoices')
        ->whereYear('created_at', $now->year)
        ->whereMonth('created_at', $now->month);

    $monthlyPayments = DB::table('apara_payment_histories')
        ->whereYear('payment_date', $now->year)
        ->whereMonth('payment_date', $now->month);

    return [
        'summary' => [
            'periodLabel' => $now->format('F Y'),
            'applications_total' => array_sum($applicationCounts),
            'applications' => $applicationCounts,
            'invoices_created' => (clone $monthlyInvoices)->count(),
            'invoices_paid' => (clone $monthlyInvoices)->where('status', 'paid')->count(),
            'premium_total' => (clone $monthlyInvoices)->sum('premium_amount'),
            'payments_total' => (clone $monthlyPayments)->sum('total_amount'),
            'payments_count' => (clone $monthlyPayments)->count(),
        ],
        'recentInvoices' => DB::table('apara_invoices')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get(),
    ];
}

private function buildMonthlyReportPdf(array $summary, $recentInvoices): string
{
    $commands = [];
    $pageWidth = 595;
    $pageHeight = 842;

    $commands[] = $this->pdfFillColor(0.97, 0.98, 1.00);
    $commands[] = sprintf('0 0 %d %d re f', $pageWidth, $pageHeight);

    $commands[] = $this->pdfFillColor(0.07, 0.18, 0.32);
    $commands[] = '36 742 523 72 re f';
    $commands[] = $this->pdfFillColor(1, 1, 1);
    $commands[] = $this->pdfText('F2', 24, 52, 786, 'APARA Monthly Report');
    $commands[] = $this->pdfText('F1', 11, 52, 766, 'Executive summary for ' . ($summary['periodLabel'] ?? 'Current Period'));
    $commands[] = $this->pdfText('F1', 10, 420, 786, 'Generated');
    $commands[] = $this->pdfText('F2', 11, 420, 770, Carbon::now()->format('Y-m-d H:i'));

    $cards = [
        ['Applications', (string) ($summary['applications_total'] ?? 0)],
        ['Invoices Created', (string) ($summary['invoices_created'] ?? 0)],
        ['Invoices Paid', (string) ($summary['invoices_paid'] ?? 0)],
        ['Payments Collected', 'LKR ' . number_format((float) ($summary['payments_total'] ?? 0), 2)],
    ];

    $cardX = 36;
    foreach ($cards as [$label, $value]) {
        $commands[] = $this->pdfFillColor(1, 1, 1);
        $commands[] = sprintf('%.2f 650 120 74 re f', $cardX);
        $commands[] = $this->pdfStrokeColor(0.84, 0.89, 0.95);
        $commands[] = sprintf('%.2f 650 120 74 re S', $cardX);
        $commands[] = $this->pdfFillColor(0.36, 0.51, 0.70);
        $commands[] = $this->pdfText('F1', 10, $cardX + 12, 704, strtoupper($label));
        $commands[] = $this->pdfFillColor(0.06, 0.14, 0.24);
        $commands[] = $this->pdfText('F2', 16, $cardX + 12, 678, $value);
        $cardX += 130;
    }

    $commands[] = $this->pdfFillColor(1, 1, 1);
    $commands[] = '36 490 252 136 re f';
    $commands[] = '307 490 252 136 re f';
    $commands[] = $this->pdfStrokeColor(0.84, 0.89, 0.95);
    $commands[] = '36 490 252 136 re S';
    $commands[] = '307 490 252 136 re S';

    $commands[] = $this->pdfFillColor(0.06, 0.14, 0.24);
    $commands[] = $this->pdfText('F2', 15, 52, 600, 'Financial Overview');
    $commands[] = $this->pdfText('F2', 15, 323, 600, 'Application Status Mix');

    $financialRows = [
        ['Premium Total', 'LKR ' . number_format((float) ($summary['premium_total'] ?? 0), 2)],
        ['Payment Count', (string) ($summary['payments_count'] ?? 0)],
        ['Period', (string) ($summary['periodLabel'] ?? '-')],
    ];

    $financialY = 574;
    foreach ($financialRows as [$label, $value]) {
        $commands[] = $this->pdfFillColor(0.38, 0.47, 0.57);
        $commands[] = $this->pdfText('F1', 11, 52, $financialY, $label);
        $commands[] = $this->pdfFillColor(0.06, 0.14, 0.24);
        $commands[] = $this->pdfText('F2', 12, 170, $financialY, $value);
        $financialY -= 30;
    }

    $statusRows = [
        ['Pending', (string) ($summary['applications']['pending'] ?? 0)],
        ['Accepted', (string) ($summary['applications']['accepted'] ?? 0)],
        ['Approved', (string) ($summary['applications']['approved'] ?? 0)],
        ['Rejected', (string) ($summary['applications']['rejected'] ?? 0)],
    ];

    $statusY = 574;
    foreach ($statusRows as [$label, $value]) {
        $commands[] = $this->pdfFillColor(0.38, 0.47, 0.57);
        $commands[] = $this->pdfText('F1', 11, 323, $statusY, $label);
        $commands[] = $this->pdfFillColor(0.06, 0.14, 0.24);
        $commands[] = $this->pdfText('F2', 12, 445, $statusY, $value);
        $statusY -= 26;
    }

    $commands[] = $this->pdfFillColor(1, 1, 1);
    $commands[] = '36 120 523 340 re f';
    $commands[] = $this->pdfStrokeColor(0.84, 0.89, 0.95);
    $commands[] = '36 120 523 340 re S';
    $commands[] = $this->pdfFillColor(0.06, 0.14, 0.24);
    $commands[] = $this->pdfText('F2', 15, 52, 432, 'Recent Invoice Activity');
    $commands[] = $this->pdfFillColor(0.94, 0.96, 0.99);
    $commands[] = '52 390 491 28 re f';

    $headers = [
        ['Invoice No', 60],
        ['Customer', 170],
        ['Status', 360],
        ['Amount', 455],
    ];

    foreach ($headers as [$text, $x]) {
        $commands[] = $this->pdfFillColor(0.15, 0.25, 0.37);
        $commands[] = $this->pdfText('F2', 10, $x, 401, $text);
    }

    $rowY = 370;
    $rows = $recentInvoices->take(8);

    if ($rows->isEmpty()) {
        $commands[] = $this->pdfFillColor(0.38, 0.47, 0.57);
        $commands[] = $this->pdfText('F1', 12, 60, $rowY, 'No invoice activity recorded for this month.');
    } else {
        foreach ($rows as $index => $invoice) {
            if ($rowY < 150) {
                break;
            }

            if ($index % 2 === 0) {
                $commands[] = $this->pdfFillColor(0.985, 0.99, 1);
                $commands[] = sprintf('52 %d 491 24 re f', $rowY - 10);
            }

            $commands[] = $this->pdfFillColor(0.19, 0.28, 0.39);
            $commands[] = $this->pdfText('F1', 10, 60, $rowY, (string) ($invoice->invoice_no ?? '-'));
            $commands[] = $this->pdfText('F1', 10, 170, $rowY, $this->truncatePdfText((string) ($invoice->customer_name ?? '-'), 28));
            $commands[] = $this->pdfText('F1', 10, 360, $rowY, ucfirst((string) ($invoice->status ?? 'unknown')));
            $commands[] = $this->pdfText('F1', 10, 455, $rowY, number_format((float) ($invoice->premium_amount ?? 0), 2));
            $rowY -= 30;
        }
    }

    $commands[] = $this->pdfFillColor(0.45, 0.53, 0.62);
    $commands[] = $this->pdfText('F1', 9, 36, 70, 'APARA automated report summary');

    $stream = implode("\n", $commands);
    $objects = [];
    $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
    $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
    $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R /F2 5 0 R >> >> /Contents 6 0 R >>';
    $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
    $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';
    $objects[] = '<< /Length ' . strlen($stream) . " >>\nstream\n" . $stream . "\nendstream";

    $pdf = "%PDF-1.4\n";
    $offsets = [];

    foreach ($objects as $index => $object) {
        $offsets[] = strlen($pdf);
        $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
    }

    $xrefOffset = strlen($pdf);
    $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
    $pdf .= "0000000000 65535 f \n";

    foreach ($offsets as $offset) {
        $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
    }

    $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
    $pdf .= "startxref\n" . $xrefOffset . "\n%%EOF";

    return $pdf;
}

private function pdfText(string $font, int $size, float $x, float $y, string $text): string
{
    return sprintf(
        "BT /%s %d Tf 1 0 0 1 %.2f %.2f Tm (%s) Tj ET",
        $font,
        $size,
        $x,
        $y,
        $this->escapePdfText($text)
    );
}

private function pdfFillColor(float $r, float $g, float $b): string
{
    return sprintf('%.3F %.3F %.3F rg', $r, $g, $b);
}

private function pdfStrokeColor(float $r, float $g, float $b): string
{
    return sprintf('%.3F %.3F %.3F RG', $r, $g, $b);
}

private function escapePdfText(string $value): string
{
    $sanitized = preg_replace('/[^\x20-\x7E]/', ' ', $value) ?? '';

    return str_replace(
        ['\\', '(', ')'],
        ['\\\\', '\\(', '\\)'],
        $sanitized
    );
}

private function truncatePdfText(string $value, int $limit): string
{
    return mb_strlen($value) > $limit
        ? rtrim(mb_substr($value, 0, $limit - 3)) . '...'
        : $value;
}
}
