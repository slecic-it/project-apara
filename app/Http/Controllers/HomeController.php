<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        return redirect()->route('login.form');
    }
    public function dashboard()
    {
        $employee = session('employee');
        $dashboardCounts = [
            'pending' => DB::table('applications')->where('status', 'pending')->count(),
            'accepted' => DB::table('applications')->where('status', 'accepted')->count(),
            'approved' => DB::table('applications')->where('status', 'approved')->count(),
            'rejected' => DB::table('applications')->where('status', 'rejected')->count(),
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

        return view('slecic.dashboard', compact('employee', 'dashboardCounts', 'workflowDurations', 'workflowSummary'));
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

        return view('profile', compact('employee', 'profile'));
    }
  
}
