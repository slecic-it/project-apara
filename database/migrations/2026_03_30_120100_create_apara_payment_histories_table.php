<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apara_payment_histories', function (Blueprint $table) {
            $table->id();
            $table->string('payment_ref')->unique();
            $table->date('payment_date');
            $table->string('payment_type');
            $table->unsignedInteger('record_count')->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('status', ['paid', 'pending', 'failed'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apara_payment_histories');
    }
};
