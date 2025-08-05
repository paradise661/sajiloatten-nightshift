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
        Schema::create('payroll_payments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('monthly_payroll_id')->nullable();
            $table->bigInteger('user_id')->nullable(); // Employee ID

            $table->decimal('amount', 10, 2)->nullable();
            $table->date('payment_date')->nullable();
            $table->date('payment_date_bs')->nullable();
            $table->string('payment_method')->nullable(); // cash, bank, online
            $table->text('remarks')->nullable();
            $table->bigInteger('bank_detail_id')->nullable();
            $table->bigInteger('paid_by')->nullable(); // User who generated/paid the payroll
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_payments');
    }
};
