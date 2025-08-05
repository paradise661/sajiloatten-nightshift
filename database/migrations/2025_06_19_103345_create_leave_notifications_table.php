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
        Schema::create('leave_notifications', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('leave_id')->nullable();
            $table->bigInteger('notified_user_id')->nullable();
            $table->string('status')->nullable();
            $table->boolean('is_seen')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_notifications');
    }
};
