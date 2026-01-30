<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->integer('branch_code');
            $table->string('name');
            $table->string('email');
            $table->string('tel_no');
            $table->string('branch_grade');
            $table->unsignedBigInteger('bank_id');
            $table->unsignedBigInteger('dist_id');

            //Foreign Key
            $table->foreign('bank_id')->references('id')->on('banks')->onDelete('cascade');
            $table->foreign('dist_id')->references('id')->on('districts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
