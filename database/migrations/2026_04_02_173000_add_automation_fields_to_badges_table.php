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
        Schema::table('badges', function (Blueprint $table) {
            $table->boolean('is_automatic')->default(false)->after('is_active');
            $table->string('requirement_type')->nullable()->after('is_automatic');
            $table->integer('requirement_value')->nullable()->after('requirement_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn(['is_automatic', 'requirement_type', 'requirement_value']);
        });
    }
};
