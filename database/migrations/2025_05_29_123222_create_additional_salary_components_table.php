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
        Schema::create('additional_salary_components', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('user_id')->nullable();
            $table->string('month')->nullable();
            $table->string('month_bs')->nullable();
            $table->string('title')->nullable(); // e.g., "Dashain Bonus"
            $table->decimal('amount', 10, 2)->nullable();
            $table->enum('type', ['earning', 'deduction'])->default('earning');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_salary_components');
    }
};
