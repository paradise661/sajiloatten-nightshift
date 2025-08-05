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
        Schema::create('salary_settings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->decimal('base_salary', 10, 2)->nullable();
            $table->decimal('allowance', 10, 2)->nullable();
            $table->decimal('overtime_rate', 8, 2)->nullable(); // Rate per overtime hour

            $table->boolean('is_epf_enrolled')->default(true);
            $table->boolean('is_cit_enrolled')->default(false);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_deduction_enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_settings');
    }
};
