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
        Schema::table('users', function (Blueprint $table) {
            $table->string('marital_status')->nullable()->after('gender');
            $table->string('pan_number')->nullable()->after('marital_status');
            $table->string('pan_photo')->nullable()->after('pan_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('marital_status');
            $table->dropColumn('pan_number');
            $table->dropColumn('pan_photo');
        });
    }
};
