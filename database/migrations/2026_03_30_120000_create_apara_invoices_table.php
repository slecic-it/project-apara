<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apara_invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('application_id')->nullable();
            $table->string('application_no')->nullable();
            $table->string('proposal_no')->nullable();
            $table->string('invoice_no')->unique();
            $table->string('customer_name');
            $table->string('id_no');
            $table->string('passport_no')->nullable();
            $table->string('particulars')->nullable();
            $table->decimal('premium_amount', 12, 2)->default(0);
            $table->text('comments')->nullable();
            $table->enum('approval_status', ['approved', 'pending', 'rejected'])->default('pending');
            $table->enum('status', ['due', 'paid', 'cancelled'])->default('due');
            $table->enum('guarantee_status', ['active', 'pending', 'inactive'])->default('pending');
            $table->timestamp('invoiced_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apara_invoices');
    }
};
