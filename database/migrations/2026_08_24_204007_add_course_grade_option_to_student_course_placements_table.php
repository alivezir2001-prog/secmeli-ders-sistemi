<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_placements', function (Blueprint $table) {
            $table->foreignId('course_grade_option_id')
                ->nullable()
                ->after('course_module_id')
                ->constrained('course_grade_options');
        });
    }

    public function down(): void
    {
        Schema::table('student_course_placements', function (Blueprint $table) {
            $table->dropForeign([
                'course_grade_option_id',
            ]);

            $table->dropColumn(
                'course_grade_option_id'
            );
        });
    }
};