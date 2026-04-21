<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class SlecicNotificationBuilder
{
    public function __construct(
        private NotificationReadState $readState
    ) {
    }

    public function build(?int $limit = 6): array
    {
        if (!Schema::hasTable('applications')) {
            return $this->emptyPayload();
        }

        $query = DB::table('applications')->select([
            'id',
            'proposal_no',
            'full_name',
            'status',
            'created_at',
            'updated_at',
        ]);

        foreach (['selected_bank_name', 'head_office_approve', 'approved_by', 'approved_at', 'marketing_stage', 'marketing_comment'] as $column) {
            if (Schema::hasColumn('applications', $column)) {
                $query->addSelect($column);
            }
        }

        $items = $query
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get()
            ->map(fn ($application) => $this->mapNotification($application))
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
            'title' => 'Marketing Notifications',
            'subtitle' => $items->isNotEmpty()
                ? 'New bank-side submissions waiting for SLECIC marketing review.'
                : 'No marketing notifications right now.',
        ];
    }

    private function mapNotification(object $application): ?array
    {
        $status = strtolower(trim((string) ($application->status ?? 'pending')));
        $proposalLabel = trim((string) ($application->proposal_no ?? '')) ?: 'APP-' . str_pad((string) ($application->id ?? 0), 4, '0', STR_PAD_LEFT);
        $customerName = trim((string) ($application->full_name ?? 'Customer'));
        $bankName = trim((string) ($application->selected_bank_name ?? 'Bank'));
        $url = Route::has('application.show') ? route('application.show', $application->id) : '#';

        if (in_array($status, ['rejected', 'completed', 'finalized'], true)) {
            return null;
        }

        if ($this->requiresMarketingReview($application, $status)) {
            $marketingStage = strtolower(trim((string) ($application->marketing_stage ?? '')));
            $title = match ($marketingStage) {
                'hold' => 'Application is on hold in marketing',
                'first_approved' => 'Second approval pending',
                default => 'First approval required',
            };
            $message = match ($marketingStage) {
                'hold' => !empty($application->marketing_comment)
                    ? "{$proposalLabel} from {$bankName} is on hold. Latest comment: {$application->marketing_comment}"
                    : "{$proposalLabel} from {$bankName} is on hold and waiting for a marketing follow-up.",
                'first_approved' => "{$proposalLabel} from {$bankName} for {$customerName} has received the 1st approval and is waiting for the 2nd approval.",
                default => "{$proposalLabel} from {$bankName} for {$customerName} is ready for the 1st marketing approval.",
            };

            return [
                'id' => (int) ($application->id ?? 0),
                'url' => $url,
                'proposal' => $proposalLabel,
                'customer' => $customerName,
                'time' => $this->formatRelativeTime($application->updated_at ?? $application->created_at ?? null),
                'is_read' => $this->readState->isRead('slecic', (int) ($application->id ?? 0)),
                'tone' => 'warning',
                'icon' => 'bi-megaphone',
                'stage' => 'Marketing Department',
                'title' => $title,
                'message' => $message,
            ];
        }

        if (in_array($status, ['approved', 'payment_pending'], true)) {
            return [
                'id' => (int) ($application->id ?? 0),
                'url' => $url,
                'proposal' => $proposalLabel,
                'customer' => $customerName,
                'time' => $this->formatRelativeTime($application->updated_at ?? $application->created_at ?? null),
                'is_read' => $this->readState->isRead('slecic', (int) ($application->id ?? 0)),
                'tone' => 'info',
                'icon' => 'bi-check2-circle',
                'stage' => 'Operations / Finance',
                'title' => 'Application moved past marketing',
                'message' => "{$proposalLabel} from {$bankName} has already progressed beyond marketing review.",
            ];
        }

        return null;
    }

    private function requiresMarketingReview(object $application, string $status): bool
    {
        $headOfficeState = strtolower(trim((string) ($application->head_office_approve ?? '')));

        if (in_array($headOfficeState, ['pending', '0', 'no'], true)) {
            return false;
        }

        return in_array($status, ['pending', 'accepted', '1'], true);
    }

    private function formatRelativeTime($value): string
    {
        if (empty($value)) {
            return 'Just now';
        }

        return Carbon::parse($value)->diffForHumans();
    }

    private function emptyPayload(): array
    {
        return [
            'count' => 0,
            'hasItems' => false,
            'items' => [],
            'title' => 'Marketing Notifications',
            'subtitle' => 'No marketing notifications right now.',
        ];
    }
}
