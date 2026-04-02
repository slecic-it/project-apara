<?php
namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use Countable;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ApplicationController extends Controller {
    public function create(){
        $countries = Country::orderBy('name')->get();
        $provinces = Province::orderBy('name')->get();
        $banks = DB::table('banks')->get();
        // $districts = District::orderBy('name')->get();
        return view('application', compact('countries', 'provinces', 'banks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'selected_bank_name' => 'required|string|max:255',
            'for_branch_bank_id' => 'required',
            'ho_entered_by' => 'required|string|max:255',
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
        $enteredBy = data_get($employee, 'name') ?: $request->input('ho_entered_by');
        $status = in_array('pending', $this->availableApplicationStatuses(), true) ? 'pending' : null;

        $columnValueMap = [
            'proposal_no' => $proposalNo,
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
            ->route('bank.dashboard')
            ->with('success', "Application {$proposalNo} saved successfully.");
    }

    public function getDistricts($provinceId){
        $districts = District::where('province_id', $provinceId)
                                    ->orderBy('name')
                                    ->get();

        return response()->json($districts);
    }

    public function show($id)
    {
        $application = DB::table('applications')
            ->leftJoin('countries', 'applications.country_id', '=', 'countries.id')
            ->leftJoin('districts', 'applications.district_id', '=', 'districts.id')
            ->select(
                'applications.*',
                'countries.name as country_name',
                'districts.name as district_name'
            )
            ->where('applications.id', $id)
            ->first();

        abort_if(!$application, 404);

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

        $applicationView = [
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
            'status_label' => ucwords(str_replace('_', ' ', $status)),
            'status_class' => $statusClass,
        ];

        return view('application-show', compact('applicationView'));
    }

     public function pending()
    {
        $applications = DB::table('applications')
                        ->where('status','pending')
                        ->get();

        return view('pending', compact('applications'));
    }

    public function accepted()
{
    $applications = DB::table('applications')
                    ->where('status','accepted')
                    ->get();

    return view('accepted', compact('applications'));
}

public function approved()
{
    $applications = DB::table('applications')
                    ->where('status','approved')
                    ->get();

    return view('approved', compact('applications'));
}

public function rejected()
{
    $applications = DB::table('applications')
                    ->where('status','rejected')
                    ->get();

    return view('rejected', compact('applications'));
}

public function banks()
{
    $banks = DB::table('banks')->get();

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
        'email' => 'nullable|email|max:255',
        'tel' => 'nullable|string|max:100',
    ]);

    $table = 'banks';
    $payload = [];
    $tableColumns = Schema::getColumnListing($table);

    $bankName = $request->input('bank_name');
    $bankCode = $request->input('bank_code');

    $columnValueMap = [
        'bank_name' => $bankName,
        'name' => $bankName,
        'bank_code' => $bankCode,
        'code' => $bankCode,
        'branch_code' => $bankCode,
        'branch_name' => $request->input('branch_name'),
        'branch' => $request->input('branch_name'),
        'branch_grade' => $request->input('branch_grade'),
        'grade' => $request->input('branch_grade'),
        'province' => $request->input('province'),
        'email' => $request->input('email'),
        'branch_email' => $request->input('email'),
        'tel' => $request->input('tel'),
        'tel_no' => $request->input('tel'),
        'telephone_no' => $request->input('tel'),
        'telephone' => $request->input('tel'),
        'contact' => $request->input('tel'),
        'contact_no' => $request->input('tel'),
        'phone' => $request->input('tel'),
        'status' => 1,
    ];

    foreach ($columnValueMap as $column => $value) {
        if (in_array($column, $tableColumns, true)) {
            $payload[$column] = $value;
        }
    }

    if (in_array('name', $tableColumns, true) && empty($payload['name'])) {
        $payload['name'] = $bankName;
    }

    if (in_array('code', $tableColumns, true) && empty($payload['code'])) {
        $payload['code'] = $bankCode;
    }

    if (in_array('status', $tableColumns, true) && !array_key_exists('status', $payload)) {
        $payload['status'] = 1;
    }

    if (in_array('created_at', $tableColumns, true)) {
        $payload['created_at'] = now();
    }

    if (in_array('updated_at', $tableColumns, true)) {
        $payload['updated_at'] = now();
    }

    if (empty($payload)) {
        return redirect()
            ->route('banks')
            ->withErrors(['bank' => 'Bank table columns could not be matched for saving.']);
    }

    try {
        DB::table($table)->insert($payload);
    } catch (\Throwable $exception) {
        return redirect()
            ->route('banks')
            ->withErrors(['bank' => 'Bank could not be saved to the database. Please check the bank table structure.'])
            ->withInput();
    }

    return redirect()->route('banks')->with('success', 'Bank added successfully.');
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
    $pending = DB::table('applications')->where('status','pending')->count();
    $accepted = DB::table('applications')->where('status','accepted')->count();
    $approved = DB::table('applications')->where('status','approved')->count();
    $rejected = DB::table('applications')->where('status','rejected')->count();

    return view('reports', compact('pending', 'accepted','approved','rejected'));
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
    $applications = DB::table('applications')
        ->orderByDesc('created_at')
        ->orderByDesc('id')
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

    return view('bank.bankdashboard', compact('applications', 'statusCounts'));
}

public function allApplications()
{
    return view('all-applications');
}

public function monthlyReport()
{
    ['summary' => $summary, 'recentInvoices' => $recentInvoices] = $this->monthlyReportData();

    return view('analytics.monthly-report', compact('summary', 'recentInvoices'));
}

public function monthlyReportPdf()
{
    ['summary' => $summary, 'recentInvoices' => $recentInvoices] = $this->monthlyReportData();

    $pdfContent = $this->buildSimplePdf($this->monthlyReportPdfLines($summary, $recentInvoices));
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

    return view('analytics.yearly-report', compact('summary', 'months'));
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
            'hint' => $applicationCounts['accepted'] . ' accepted applications in queue',
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

    return view('analytics.performance-report', compact('metrics', 'teams', 'applicationCounts'));
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

    return view('analytics.audit-trail', compact('invoiceActivities', 'paymentActivities'));
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

    return view('support.tickets', compact('ticketSummary', 'tickets'));
}

public function faq()
{
    $faqs = collect([
        ['question' => 'How do I move an application from pending to accepted?', 'answer' => 'Open the application from the dashboard or application list, review the details, and update the status once validation is complete.'],
        ['question' => 'Where can I view paid invoices?', 'answer' => 'Use the Finance section in the sidebar and open the Invoice page to see paid records.'],
        ['question' => 'Why is a payment missing from history?', 'answer' => 'Check the selected date range and status filters first. If it still does not appear, raise a support ticket with the payment reference.'],
        ['question' => 'How do I contact support for urgent issues?', 'answer' => 'Use the Contact Support page and mark the issue as high priority, then call the help desk extension listed there.'],
    ]);

    return view('support.faq', compact('faqs'));
}

public function contact()
{
    $contacts = collect([
        ['team' => 'Help Desk', 'email' => 'helpdesk@apara.local', 'phone' => '+94 11 555 0101', 'hours' => '8:30 AM - 5:00 PM'],
        ['team' => 'Finance Support', 'email' => 'finance-support@apara.local', 'phone' => '+94 11 555 0102', 'hours' => '8:30 AM - 5:00 PM'],
        ['team' => 'Technical Support', 'email' => 'tech-support@apara.local', 'phone' => '+94 11 555 0103', 'hours' => '24/7 monitoring'],
    ]);

    return view('support.contact', compact('contacts'));
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

private function monthlyReportPdfLines(array $summary, $recentInvoices): array
{
    $lines = [
        'APARA Monthly Report Summary',
        'Period: ' . $summary['periodLabel'],
        'Generated: ' . Carbon::now()->format('Y-m-d H:i'),
        '',
        'Key Figures',
        'Applications in Pipeline: ' . $summary['applications_total'],
        'Invoices Created: ' . $summary['invoices_created'],
        'Invoices Paid: ' . $summary['invoices_paid'],
        'Payments Collected: LKR ' . number_format((float) $summary['payments_total'], 2),
        'Premium Total: LKR ' . number_format((float) $summary['premium_total'], 2),
        'Payment Count: ' . $summary['payments_count'],
        '',
        'Application Status Mix',
    ];

    foreach ($summary['applications'] as $label => $value) {
        $lines[] = ucfirst($label) . ': ' . $value;
    }

    $lines[] = '';
    $lines[] = 'Recent Invoice Activity';

    if ($recentInvoices->isEmpty()) {
        $lines[] = 'No invoice activity recorded for this month.';
    } else {
        foreach ($recentInvoices as $invoice) {
            $lines[] = sprintf(
                '%s | %s | %s | LKR %s',
                $invoice->invoice_no ?? '-',
                $invoice->customer_name ?? '-',
                ucfirst((string) ($invoice->status ?? 'unknown')),
                number_format((float) ($invoice->premium_amount ?? 0), 2)
            );
        }
    }

    return $lines;
}

private function buildSimplePdf(array $lines): string
{
    $objects = [];
    $lineHeight = 16;
    $fontSize = 12;
    $startY = 800;
    $contentLines = ['BT', '/F1 ' . $fontSize . ' Tf'];

    foreach (array_values($lines) as $index => $line) {
        $y = $startY - ($index * $lineHeight);

        if ($y < 40) {
            break;
        }

        $contentLines[] = sprintf('1 0 0 1 48 %d Tm (%s) Tj', $y, $this->escapePdfText($line));
    }

    $contentLines[] = 'ET';
    $stream = implode("\n", $contentLines);

    $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
    $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
    $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources << /Font << /F1 4 0 R >> >> /Contents 5 0 R >>';
    $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
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

private function escapePdfText(string $value): string
{
    $sanitized = preg_replace('/[^\x20-\x7E]/', ' ', $value) ?? '';

    return str_replace(
        ['\\', '(', ')'],
        ['\\\\', '\\(', '\\)'],
        $sanitized
    );
}
}
