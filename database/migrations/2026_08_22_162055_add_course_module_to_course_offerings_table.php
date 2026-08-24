<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->foreignId('course_module_id')
                ->nullable()
                ->after('course_id')
                ->constrained('course_modules')
                ->restrictOnDelete();

            $table->index(
                ['academic_year_id', 'course_module_id'],
                'course_offering_year_module_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropIndex(
                'course_offering_year_module_idx'
            );

            $table->dropForeign(
                ['course_module_id']
            );

            $table->dropColumn(
                'course_module_id'
            );
        });
    }
};