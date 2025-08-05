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
        Schema::create('monthly_payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('payroll_unique_id')->nullable();       // Unique ID
            $table->bigInteger('user_id')->nullable(); // Employee ID
            $table->string('month')->nullable();       // Payroll month (AD)
            $table->string('month_bs')->nullable();    // Payroll month (BS)

            // Attendance summary
            $table->integer('total_expected_working_days')->nullable();  // Total expected working days in the month
            $table->integer('present_days')->nullable();        // Total present days
            $table->integer('paid_leaves')->nullable();         // Paid leave days
            $table->integer('unpaid_leaves')->nullable();       // Unpaid leave days
            $table->integer('absent_days')->nullable();         // Absent days without leave
            $table->integer('public_holidays')->nullable();     // Total public days within month
            $table->integer('weekends')->nullable();         //  Total public days within month

            // Earnings and deductions
            $table->decimal('base_salary', 10, 2)->nullable();              // Basic salary
            $table->decimal('allowances', 10, 2)->nullable();               // Total allowances
            $table->decimal('overtime', 10, 2)->nullable();                 // Overtime earnings
            $table->decimal('additional_earnings', 10, 2)->nullable();      // Bonuses or other earnings
            $table->decimal('additional_deductions', 10, 2)->nullable();    // Fines or other deductions
            $table->decimal('gross_salary', 10, 2)->nullable();
            $table->decimal('tax_amount', 10, 2)->nullable();               // Tax amount deducted
            $table->decimal('total_deductions', 10, 2)->nullable();         // Total amount deducted
            $table->decimal('net_salary', 10, 2)->nullable();               // Final payable salary
            $table->decimal('paid_amount', 10, 2)->nullable();              // Amount actually paid

            $table->bigInteger('paid_by')->nullable();      // User who generated/paid the payroll
            $table->enum('status', ['pending', 'partial', 'paid'])->default('pending');
            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_payrolls');
    }
};
