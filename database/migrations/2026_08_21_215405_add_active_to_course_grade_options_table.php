<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_grade_options', function (Blueprint $table) {
            $table->boolean('active')
                ->default(true)
                ->after('weekly_hours');

            $table->index(
                ['course_id', 'active'],
                'course_grade_options_course_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('course_grade_options', function (Blueprint $table) {
            $table->dropIndex(
                'course_grade_options_course_active_idx'
            );

            $table->dropColumn('active');
        });
    }
};