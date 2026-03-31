<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->timestamps();
        });

        Schema::create('years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();
            $table->unsignedInteger('year')->nullable();
            $table->timestamps();
        });

        Schema::create('score_formats', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        DB::table('score_formats')->insert([
            ['name' => '100 Point',        'slug' => 'POINT_100',          'created_at' => now(), 'updated_at' => now()],
            ['name' => '10 Point Decimal', 'slug' => 'POINT_10_DECIMAL',   'created_at' => now(), 'updated_at' => now()],
            ['name' => '10 Point',         'slug' => 'POINT_10',           'created_at' => now(), 'updated_at' => now()],
            ['name' => '5 Star',           'slug' => 'POINT_5',            'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('score_formats');
        Schema::dropIfExists('years');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('formats');
    }
};
