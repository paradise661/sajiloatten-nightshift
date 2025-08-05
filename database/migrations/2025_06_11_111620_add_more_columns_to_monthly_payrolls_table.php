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
        Schema::table('monthly_payrolls', function (Blueprint $table) {
            $table->integer('total_days_in_month')->after('total_expected_working_days')->nullable();
            $table->decimal('per_day_salary', 10, 2)->after('paid_by')->nullable();
            $table->decimal('absence_deduction', 10, 2)->after('per_day_salary')->nullable();
            $table->decimal('total_earnings', 10, 2)->after('absence_deduction')->nullable();
            $table->decimal('taxable_salary', 10, 2)->after('total_earnings')->nullable();
            $table->decimal('attendance_deduction', 10, 2)->after('taxable_salary')->nullable();
            $table->decimal('remaining_salary', 10, 2)->after('attendance_deduction')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_payrolls', function (Blueprint $table) {
            $table->dropColumn('total_days_in_month');
            $table->dropColumn('per_day_salary');
            $table->dropColumn('absence_deduction');
            $table->dropColumn('total_earnings');
            $table->dropColumn('taxable_salary');
            $table->dropColumn('attendance_deduction');
            $table->dropColumn('remaining_salary');
        });
    }
};
