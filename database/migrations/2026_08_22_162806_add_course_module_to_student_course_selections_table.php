<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_selections', function (Blueprint $table) {
            $table->foreignId('course_module_id')
                ->nullable()
                ->after('course_id')
                ->constrained('course_modules')
                ->nullOnDelete();

            $table->index(
                ['academic_year_id', 'course_module_id', 'status'],
                'selection_year_module_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_course_selections', function (Blueprint $table) {
            $table->dropIndex(
                'selection_year_module_status_idx'
            );

            $table->dropForeign(
                ['course_module_id']
            );

            $table->dropColumn('course_module_id');
        });
    }
};