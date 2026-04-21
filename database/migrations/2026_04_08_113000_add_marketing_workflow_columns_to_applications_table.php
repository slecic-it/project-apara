<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table) {
            if (!Schema::hasColumn('applications', 'marketing_stage')) {
                $table->string('marketing_stage')->nullable()->after('status');
            }

            if (!Schema::hasColumn('applications', 'marketing_first_approved_by')) {
                $table->string('marketing_first_approved_by')->nullable()->after('marketing_stage');
            }

            if (!Schema::hasColumn('applications', 'marketing_first_approved_at')) {
                $table->timestamp('marketing_first_approved_at')->nullable()->after('marketing_first_approved_by');
            }

            if (!Schema::hasColumn('applications', 'marketing_second_approved_by')) {
                $table->string('marketing_second_approved_by')->nullable()->after('marketing_first_approved_at');
            }

            if (!Schema::hasColumn('applications', 'marketing_second_approved_at')) {
                $table->timestamp('marketing_second_approved_at')->nullable()->after('marketing_second_approved_by');
            }

            if (!Schema::hasColumn('applications', 'marketing_ack_letter')) {
                $table->text('marketing_ack_letter')->nullable()->after('marketing_second_approved_at');
            }

            if (!Schema::hasColumn('applications', 'marketing_comment')) {
                $table->text('marketing_comment')->nullable()->after('marketing_ack_letter');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('applications')) {
            return;
        }

        Schema::table('applications', function (Blueprint $table) {
            $columns = [
                'marketing_stage',
                'marketing_first_approved_by',
                'marketing_first_approved_at',
                'marketing_second_approved_by',
                'marketing_second_approved_at',
                'marketing_ack_letter',
                'marketing_comment',
            ];

            $existingColumns = array_values(array_filter($columns, fn ($column) => Schema::hasColumn('applications', $column)));

            if ($existingColumns !== []) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
