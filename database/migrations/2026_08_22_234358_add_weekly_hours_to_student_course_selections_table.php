<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_selections', function (Blueprint $table) {
            $table->unsignedTinyInteger('weekly_hours')
                ->nullable()
                ->after('course_module_id');

            $table->index(
                [
                    'academic_year_id',
                    'course_module_id',
                    'weekly_hours',
                    'status',
                ],
                'selection_year_module_hours_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_course_selections', function (Blueprint $table) {
            $table->dropIndex(
                'selection_year_module_hours_status_idx'
            );

            $table->dropColumn('weekly_hours');
        });
    }
};