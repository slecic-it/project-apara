<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class BankPortalNotificationBuilder
{
    public function __construct(
        private NotificationReadState $readState
    ) {
    }

    public function build(?int $limit = 6): array
    {
        $bankProfile = session('bank_profile');

        if (!is_array($bankProfile) || empty($bankProfile) || !Schema::hasTable('applications')) {
            return [
                'count' => 0,
                'hasItems' => false,
                'items' => [],
                'title' => 'Notifications',
                'subtitle' => 'No workflow notifications right now.',
            ];
        }

        $workflow = $this->resolveWorkflow($bankProfile);
        $applications = $this->applicationQuery($bankProfile)->get();

        $items = collect($applications)
            ->map(fn ($application) => $this->buildNotificationItem($application, $workflow))
            ->filter()
            ->values();

        $unreadItems = $items
            ->reject(fn (array $item) => $item['is_read'] ?? false)
            ->when($limit !== null, fn ($collection) => $collection->take($limit))
            ->values()
            ->all();

        $readItems = $items
            ->filter(fn (array $item) => $item['is_read'] ?? false)
            ->when($limit !== null, fn ($collection) => $collection->take($limit))
            ->values()
            ->all();

        return [
            'count' => count($unreadItems),
            'total_count' => $items->count(),
            'hasItems' => $items->isNotEmpty(),
            'items' => $unreadItems,
            'unread_items' => $unreadItems,
            'read_items' => $readItems,
            'title' => ($workflow['label'] ?? 'Bank Workflow') . ' Notifications',
            'subtitle' => $items->isNotEmpty()
                ? 'Next actions for the logged-in bank.'
                : 'No workflow notifications right now.',
        ];
    }

    private function applicationQuery(array $bankProfile)
    {
        $employee = session('employee');
        $bankActorIds = array_values(array_unique(array_filter([
            is_numeric(data_get($employee, 'id')) ? (int) data_get($employee, 'id') : null,
            is_numeric(data_get($employee, 'user_id')) ? (int) data_get($employee, 'user_id') : null,
        ], fn ($value) => $value !== null)));

        $query = DB::table('applications')->select([
            'id',
            'proposal_no',
            'full_name',
            'status',
            'created_at',
            'updated_at',
        ]);

        foreach (['head_office_approve', 'ho_approved_by', 'ho_approved_date_time', 'selected_bank_name', 'for_branch_bank_id', 'entered_by', 'marketing_stage', 'marketing_ack_letter', 'marketing_comment'] as $column) {
            if (Schema::hasColumn('applications', $column)) {
                $query->addSelect($column);
            }
        }

        $query->where(function ($scopedQuery) use ($bankProfile, $bankActorIds) {
            $hasScopedCondition = false;

            if (Schema::hasColumn('applications', 'for_branch_bank_id') && !empty($bankProfile['id'])) {
                $scopedQuery->where('for_branch_bank_id', $bankProfile['id']);
                $hasScopedCondition = true;
            }

            if (Schema::hasColumn('applications', 'selected_bank_name') && !empty($bankProfile['bank_name'])) {
                if ($hasScopedCondition) {
                    $scopedQuery->orWhere('selected_bank_name', $bankProfile['bank_name']);
                } else {
                    $scopedQuery->where('selected_bank_name', $bankProfile['bank_name']);
                    $hasScopedCondition = true;
                }
            }

            if (Schema::hasColumn('applications', 'entered_by') && $bankActorIds !== []) {
                if ($hasScopedCondition) {
                    $scopedQuery->orWhereIn('entered_by', $bankActorIds);
                } else {
                    $scopedQuery->whereIn('entered_by', $bankActorIds);
                    $hasScopedCondition = true;
                }
            }

            if (!$hasScopedCondition) {
                $scopedQuery->whereRaw('1 = 0');
            }
        });

        return $query
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    private function buildNotificationItem(object $application, array $workflow): ?array
    {
        $status = strtolower(trim((string) ($application->status ?? 'pending')));
        $proposalNo = trim((string) ($application->proposal_no ?? ''));
        $proposalLabel = $proposalNo !== '' ? $proposalNo : 'APP-' . str_pad((string) ($application->id ?? 0), 4, '0', STR_PAD_LEFT);
        $customerName = trim((string) ($application->full_name ?? 'Customer'));
        $headOfficeApproved = $this->isHeadOfficeApproved($application, $status);
        $route = Route::has('bank.application.show')
            ? route('bank.application.show', $application->id)
            : '#';

        $item = [
            'id' => (int) ($application->id ?? 0),
            'url' => $route,
            'proposal' => $proposalLabel,
            'customer' => $customerName,
            'time' => $this->formatRelativeTime($application->updated_at ?? $application->created_at ?? null),
            'is_read' => $this->readState->isRead('bank', (int) ($application->id ?? 0)),
        ];

        if ($status === 'rejected') {
            return array_merge($item, [
                'tone' => 'danger',
                'icon' => 'bi-arrow-counterclockwise',
                'stage' => 'Branch Follow-up',
                'title' => 'Returned for correction',
                'message' => "{$proposalLabel} for {$customerName} needs branch-side correction before it can continue.",
            ]);
        }

        if (strtolower(trim((string) ($application->marketing_stage ?? ''))) === 'hold') {
            return array_merge($item, [
                'tone' => 'danger',
                'icon' => 'bi-pause-circle',
                'stage' => 'Marketing Department',
                'title' => 'Application placed on hold',
                'message' => trim((string) ($application->marketing_comment ?? '')) !== ''
                    ? trim((string) $application->marketing_comment)
                    : "{$proposalLabel} for {$customerName} has been placed on hold by Marketing.",
            ]);
        }

        if (strtolower(trim((string) ($application->marketing_stage ?? ''))) === 'second_approved' && !empty($application->marketing_ack_letter)) {
            return array_merge($item, [
                'tone' => 'success',
                'icon' => 'bi-envelope-check',
                'stage' => 'Marketing Department',
                'title' => 'Second approval acknowledged',
                'message' => "{$proposalLabel} for {$customerName} has received the 2nd marketing approval and the acknowledgement letter is ready.",
            ]);
        }

        if (strtolower(trim((string) ($application->marketing_stage ?? ''))) === 'first_approved' && !empty($application->marketing_ack_letter)) {
            return array_merge($item, [
                'tone' => 'info',
                'icon' => 'bi-envelope-paper',
                'stage' => 'Marketing Department',
                'title' => 'First approval acknowledged',
                'message' => "{$proposalLabel} for {$customerName} has received the 1st marketing approval and an acknowledgement letter has been sent.",
            ]);
        }

        if (($workflow['type'] ?? 'decentralized') === 'centralized') {
            if (!$headOfficeApproved && in_array($status, ['pending', 'accepted'], true)) {
                return array_merge($item, [
                    'tone' => 'warning',
                    'icon' => 'bi-building-check',
                    'stage' => 'Head Office',
                    'title' => 'Head office approval required',
                    'message' => "{$proposalLabel} for {$customerName} should move to Head Office before Marketing.",
                ]);
            }

            if ($headOfficeApproved || in_array($status, ['approved', 'payment_pending', 'completed', 'finalized'], true)) {
                return array_merge($item, [
                    'tone' => 'info',
                    'icon' => 'bi-megaphone',
                    'stage' => 'Marketing Department',
                    'title' => 'Marketing follow-up ready',
                    'message' => "{$proposalLabel} for {$customerName} has cleared Head Office and is ready for Marketing.",
                ]);
            }
        }

        if (in_array($status, ['approved', 'payment_pending', 'completed', 'finalized'], true)) {
            return array_merge($item, [
                'tone' => 'success',
                'icon' => 'bi-check2-circle',
                'stage' => 'Marketing / Finance',
                'title' => 'Processing moved forward',
                'message' => "{$proposalLabel} for {$customerName} has already reached downstream processing.",
            ]);
        }

        return array_merge($item, [
            'tone' => 'info',
            'icon' => 'bi-send-check',
            'stage' => 'Marketing Department',
            'title' => 'Marketing review required',
            'message' => "{$proposalLabel} for {$customerName} should go directly to the Marketing Department.",
        ]);
    }

    private function isHeadOfficeApproved(object $application, string $status): bool
    {
        $value = strtolower(trim((string) ($application->head_office_approve ?? '')));

        if (in_array($value, ['1', 'yes', 'approved', 'true', 'done'], true)) {
            return true;
        }

        if (!empty($application->ho_approved_by) || !empty($application->ho_approved_date_time)) {
            return true;
        }

        return in_array($status, ['approved', 'payment_pending', 'completed', 'finalized'], true);
    }

    private function formatRelativeTime($value): string
    {
        if (empty($value)) {
            return 'Just now';
        }

        return Carbon::parse($value)->diffForHumans();
    }

    private function resolveWorkflow(array $bankProfile): array
    {
        $identifier = strtoupper(trim(implode(' ', array_filter([
            $bankProfile['bank_name'] ?? '',
            $bankProfile['bank_code'] ?? '',
            $bankProfile['branch_grade'] ?? '',
            $bankProfile['branch_name'] ?? '',
        ]))));

        if ($identifier !== '') {
            foreach (['CENTRAL', 'HEAD OFFICE', 'HEAD-OFFICE', 'HQ'] as $keyword) {
                if (str_contains($identifier, $keyword)) {
                    return [
                        'type' => 'centralized',
                        'label' => 'Centralized Bank',
                    ];
                }
            }

            foreach (['DE-CENTRAL', 'DECENTRAL', 'REGIONAL', 'BRANCH'] as $keyword) {
                if (str_contains($identifier, $keyword)) {
                    return [
                        'type' => 'decentralized',
                        'label' => 'Decentralized Bank',
                    ];
                }
            }

            foreach (['BOC', 'DFCC', 'NSB'] as $keyword) {
                if (str_contains($identifier, $keyword)) {
                    return [
                        'type' => 'centralized',
                        'label' => 'Centralized Bank',
                    ];
                }
            }
        }

        return [
            'type' => 'decentralized',
            'label' => 'Decentralized Bank',
        ];
    }
}
