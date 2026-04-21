<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('banks')) {
            return;
        }

        Schema::create('banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code', 100)->nullable();
            $table->string('branch_name')->nullable();
            $table->string('branch_grade', 50)->nullable();
            $table->string('province', 100)->nullable();
            $table->string('email')->nullable();
            $table->string('tel', 100)->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('banks')) {
            Schema::dropIfExists('banks');
        }
    }
};
