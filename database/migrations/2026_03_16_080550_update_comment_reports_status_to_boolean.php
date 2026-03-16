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
        Schema::table('comment_reports', function (Blueprint $table) {
            $table->boolean('status_bool')->default(false)->after('status');
        });

        // 2. Migrate data
        DB::table('comment_reports')->where('status', 'resolved')->update(['status_bool' => true]);
        DB::table('comment_reports')->where('status', 'dismissed')->update(['status_bool' => true]);
        DB::table('comment_reports')->where('status', 'pending')->update(['status_bool' => false]);

        // 3. Drop string status and rename bool
        Schema::table('comment_reports', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('comment_reports', function (Blueprint $table) {
            $table->renameColumn('status_bool', 'status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comment_reports', function (Blueprint $table) {
            $table->string('status_old')->default('pending')->after('status');
        });

        DB::table('comment_reports')->where('status', true)->update(['status_old' => 'resolved']);
        DB::table('comment_reports')->where('status', false)->update(['status_old' => 'pending']);

        Schema::table('comment_reports', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('comment_reports', function (Blueprint $table) {
            $table->renameColumn('status_old', 'status');
        });
    }
};
