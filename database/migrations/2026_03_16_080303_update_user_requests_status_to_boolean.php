<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add temporary boolean column
        Schema::table('user_requests', function (Blueprint $table) {
            $table->boolean('status_bool')->default(false)->after('status');
        });

        // 2. Migrate data
        DB::table('user_requests')->where('status', 'attended')->update(['status_bool' => true]);
        DB::table('user_requests')->where('status', 'pending')->update(['status_bool' => false]);

        // 3. Drop enum and rename bool
        Schema::table('user_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('user_requests', function (Blueprint $table) {
            $table->renameColumn('status_bool', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_requests', function (Blueprint $table) {
            $table->enum('status_old', ['pending', 'attended'])->default('pending')->after('status');
        });

        DB::table('user_requests')->where('status', true)->update(['status_old' => 'attended']);
        DB::table('user_requests')->where('status', false)->update(['status_old' => 'pending']);

        Schema::table('user_requests', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('user_requests', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};
