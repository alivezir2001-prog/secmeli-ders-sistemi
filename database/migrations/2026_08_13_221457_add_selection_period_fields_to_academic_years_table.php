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
        Schema::table('academic_years', function (Blueprint $table) {
            $table->boolean('selection_enabled')
                ->default(false)
                ->after('active');

            $table->dateTime('selection_start_at')
                ->nullable()
                ->after('selection_enabled');

            $table->dateTime('selection_end_at')
                ->nullable()
                ->after('selection_start_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->dropColumn([
                'selection_enabled',
                'selection_start_at',
                'selection_end_at',
            ]);
        });
    }
};