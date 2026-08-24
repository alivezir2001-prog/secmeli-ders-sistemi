<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_placements', function (Blueprint $table) {
            $table->foreignId('student_course_group_id')
                ->nullable()
                ->after('student_course_selection_id')
                ->constrained('student_course_groups')
                ->nullOnDelete();

            $table->index(
                [
                    'academic_year_id',
                    'student_course_group_id',
                    'status',
                ],
                'placement_year_group_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_course_placements', function (Blueprint $table) {
            $table->dropIndex(
                'placement_year_group_status_idx'
            );

            $table->dropForeign([
                'student_course_group_id',
            ]);

            $table->dropColumn(
                'student_course_group_id'
            );
        });
    }
};