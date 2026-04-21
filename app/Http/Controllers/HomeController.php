<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    private function resolveContextView(string $defaultView): string
    {
        $routeName = request()->route()?->getName() ?? '';
        $bankView = 'bank.' . $defaultView;

        if (str_starts_with($routeName, 'bank.') && view()->exists($bankView)) {
            return $bankView;
        }

        return $defaultView;
    }

    public function index()
    {
        return redirect()->route('login.form');
    }
    public function dashboard()
    {
        $employee = session('employee');
        $hasBanksTable = Schema::hasTable('banks');
        $hasBranchesTable = Schema::hasTable('branches');
        $hasBankEmployeesTable = Schema::hasTable('bank_employees');
        $hasApplicationsTable = Schema::hasTable('applications');
        $hasCountriesTable = Schema::hasTable('countries');

        $bankDirectory = $hasBanksTable ? DB::table('banks')->get() : collect();
        $applications = collect();

        if ($hasApplicationsTable) {
            $applicationQuery = DB::table('applications');

            if ($hasCountriesTable) {
                $applicationQuery->leftJoin('countries', 'applications.country_id', '=', 'countries.id');
            }

            $applicationSelects = [
                'applications.id',
                'applications.proposal_no',
                'applications.full_name',
                'applications.nic',
                'applications.passport_no',
                'applications.employment_type',
                'applications.recruitment_agency_name',
                'applications.status',
                'applications.created_at',
                DB::raw($hasCountriesTable ? 'countries.name as country_name' : "'' as country_name"),
            ];

            $hasSubmittingBankJoin = false;

            if (Schema::hasColumn('applications', 'entered_by') && $hasBankEmployeesTable && $hasBranchesTable) {
                $applicationQuery
                    ->leftJoin('bank_employees as submitting_bank_employees', 'applications.entered_by', '=', 'submitting_bank_employees.id')
                    ->leftJoin('branches as submitting_branches', 'submitting_bank_employees.branch_id', '=', 'submitting_branches.id');

                if ($hasBanksTable) {
                    $applicationQuery->leftJoin('banks as submitting_banks', 'submitting_branches.bank_id', '=', 'submitting_banks.id');
                    $hasSubmittingBankJoin = true;
                }
            }

            if (Schema::hasColumn('applications', 'for_branch_bank_id') && $hasBranchesTable && $hasBanksTable) {
                $applicationQuery
                    ->leftJoin('branches', 'applications.for_branch_bank_id', '=', 'branches.id')
                    ->leftJoin('banks', 'branches.bank_id', '=', 'banks.id');

                $applicationSelects[] = $hasSubmittingBankJoin
                    ? DB::raw("COALESCE(applications.selected_bank_name, banks.bank_name, banks.name, submitting_banks.name, '') as bank_name")
                    : DB::raw("COALESCE(applications.selected_bank_name, banks.bank_name, banks.name, '') as bank_name");
            } elseif (Schema::hasColumn('applications', 'for_branch_bank_id') && $hasBanksTable) {
                $applicationQuery->leftJoin('banks', 'applications.for_branch_bank_id', '=', 'banks.id');

                $applicationSelects[] = $hasSubmittingBankJoin
                    ? DB::raw("COALESCE(applications.selected_bank_name, banks.bank_name, banks.name, banks.branch_name, banks.branch, submitting_banks.name, '') as bank_name")
                    : DB::raw("COALESCE(applications.selected_bank_name, banks.bank_name, banks.name, banks.branch_name, banks.branch, '') as bank_name");
            } elseif (Schema::hasColumn('applications', 'selected_bank_name')) {
                $applicationSelects[] = $hasSubmittingBankJoin
                    ? DB::raw("COALESCE(applications.selected_bank_name, submitting_banks.name, '') as bank_name")
                    : DB::raw("COALESCE(applications.selected_bank_name, '') as bank_name");
            } else {
                $applicationSelects[] = $hasSubmittingBankJoin
                    ? DB::raw("COALESCE(submitting_banks.name, '') as bank_name")
                    : DB::raw("'' as bank_name");
            }

            $applications = $applicationQuery
                ->select($applicationSelects)
                ->orderByDesc('applications.created_at')
                ->orderByDesc('applications.id')
                ->limit(12)
                ->get();
        }

        $applicationBankCounts = $applications
            ->groupBy(function ($application) {
                return trim((string) ($application->bank_name ?? ''));
            })
            ->filter(fn ($group, $bankName) => $bankName !== '')
            ->map(function ($group, $bankName) {
                return [
                    'name' => $bankName,
                    'count' => $group->count(),
                    'icon' => 'bi-bank2',
                    'logo_url' => null,
                ];
            });

        $banks = $bankDirectory
            ->map(function ($bank) {
                $bankName = trim((string) ($bank->bank_name ?? $bank->name ?? ''));

                return [
                    'name' => $bankName,
                    'logo_url' => $this->resolveBankLogoUrl($bank),
                ];
            })
            ->filter(fn ($bank) => $bank['name'] !== '')
            ->unique('name')
            ->values();

        $bankSummaries = $banks
            ->map(function ($bank) use ($applicationBankCounts) {
                $summary = $applicationBankCounts->get($bank['name']);

                return [
                    'name' => $bank['name'],
                    'count' => $summary['count'] ?? 0,
                    'icon' => 'bi-bank2',
                    'logo_url' => $bank['logo_url'],
                ];
            })
            ->sortBy(function ($bank) {
                return strtolower($bank['name']);
            })
            ->values();

        $dashboardCounts = $hasApplicationsTable
            ? [
                'pending' => DB::table('applications')->where('status', 'pending')->count(),
                'accepted' => DB::table('applications')->where('status', 'accepted')->count(),
                'approved' => DB::table('applications')->where('status', 'approved')->count(),
                'rejected' => DB::table('applications')->where('status', 'rejected')->count(),
            ]
            : [
                'pending' => 0,
                'accepted' => 0,
                'approved' => 0,
                'rejected' => 0,
            ];

        $workflowDurations = [
            ['stage' => 'Submission', 'hours' => 4, 'owner' => 'Applicant / Bank', 'detail' => 'Average time to prepare and submit the application package.'],
            ['stage' => 'Inside Bank', 'hours' => 18, 'owner' => 'Bank', 'detail' => 'Internal bank review, verification, and branch approval handling.'],
            ['stage' => 'Operations', 'hours' => 12, 'owner' => 'Operations', 'detail' => 'Operational checks and document coordination within APARA.'],
            ['stage' => 'Marketing', 'hours' => 7, 'owner' => 'Marketing', 'detail' => 'Market-side validation, channel follow-up, and customer coordination.'],
            ['stage' => 'Finance', 'hours' => 10, 'owner' => 'Finance', 'detail' => 'Fee confirmation, payment processing, and finance clearance.'],
            ['stage' => 'Completion', 'hours' => 3, 'owner' => 'System Closeout', 'detail' => 'Final release, archive updates, and case closure.'],
        ];

        $workflowSummary = [
            'totalHours' => collect($workflowDurations)->sum('hours'),
            'bankHours' => collect($workflowDurations)->firstWhere('stage', 'Inside Bank')['hours'] ?? 0,
            'operationsHours' => collect($workflowDurations)->firstWhere('stage', 'Operations')['hours'] ?? 0,
            'marketingHours' => collect($workflowDurations)->firstWhere('stage', 'Marketing')['hours'] ?? 0,
            'financeHours' => collect($workflowDurations)->firstWhere('stage', 'Finance')['hours'] ?? 0,
        ];

        return view('slecic.dashboard', compact('employee', 'dashboardCounts', 'workflowDurations', 'workflowSummary', 'applications', 'bankSummaries'));
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

    public function profile()
    {
        $employee = session('employee');
        $user = Auth::user();
        $employeeName = data_get($employee, 'name') ?: 'System User';
        $initials = collect(preg_split('/\s+/', trim($employeeName)) ?: [])
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('');

        $designation = 'System User';
        $department = '-';

        if (data_get($employee, 'designation')) {
            $designation = data_get($employee, 'designation');
        } elseif (data_get($employee, 'designation_id')) {
            $designation = DB::table('designations')
                ->where('id', data_get($employee, 'designation_id'))
                ->value('title') ?? $designation;
        } elseif (data_get($employee, 'role')) {
            $designation = data_get($employee, 'role');
        }

        if (data_get($employee, 'department')) {
            $department = data_get($employee, 'department');
        } elseif (data_get($employee, 'department_id')) {
            $department = DB::table('departments')
                ->where('id', data_get($employee, 'department_id'))
                ->value('name') ?? $department;
        } elseif (data_get($employee, 'branch_id')) {
            $department = 'Branch #' . data_get($employee, 'branch_id');
        }

        $statusValue = data_get($employee, 'status', data_get($user, 'status', 'Active'));
        $statusLabel = match ((string) $statusValue) {
            '1', 'true', 'True' => 'Active',
            '0', 'false', 'False' => 'Inactive',
            default => ucfirst((string) $statusValue),
        };

        $avatarSvg = sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" width="240" height="240" viewBox="0 0 240 240"><defs><linearGradient id="g" x1="0%%" y1="0%%" x2="100%%" y2="100%%"><stop offset="0%%" stop-color="#dbeafe"/><stop offset="100%%" stop-color="#bfdbfe"/></linearGradient></defs><rect width="240" height="240" rx="48" fill="url(#g)"/><circle cx="120" cy="90" r="38" fill="#93c5fd"/><path d="M52 196c12-34 41-52 68-52s56 18 68 52" fill="#60a5fa"/><text x="120" y="222" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="30" font-weight="700" fill="#1e3a5f">%s</text></svg>',
            $initials ?: 'U'
        );

        $profile = [
            'name' => $employeeName,
            'email' => data_get($user, 'email', 'user@slecic.local'),
            'mobile_no' => data_get($user, 'mobile_no', '-'),
            'employee_no' => data_get($employee, 'emp_no', data_get($employee, 'id', 'N/A')),
            'designation' => $designation,
            'department' => $department,
            'status' => $statusLabel,
            'profile_photo' => 'data:image/svg+xml;utf8,' . rawurlencode($avatarSvg),
            'member_since' => optional(data_get($user, 'created_at'))->format('Y-m-d') ?? '-',
        ];

        return view($this->resolveContextView('profile'), compact('employee', 'profile'));
    }
  
}
