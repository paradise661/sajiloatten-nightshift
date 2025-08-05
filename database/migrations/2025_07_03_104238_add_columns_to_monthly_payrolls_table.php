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
            $table->decimal('overtime_amount', 10, 2)->default(0)->after('overtime');
            $table->decimal('undertime', 10, 2)->default(0)->after('overtime_amount');
            $table->decimal('undertime_amount', 10, 2)->default(0)->after('undertime');
            $table->longText('workingtime_details')->nullable()->after('undertime_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_payrolls', function (Blueprint $table) {
             $table->dropColumn('overtime_amount');
             $table->dropColumn('undertime');
             $table->dropColumn('undertime_amount');
             $table->dropColumn('workingtime_details');
        });
    }
};
