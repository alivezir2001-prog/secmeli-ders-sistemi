<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_grade_options', function (Blueprint $table) {
            $table->foreignId('course_id')
                ->after('id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('grade')
                ->after('course_id');

            $table->unsignedTinyInteger('weekly_hours')
                ->after('grade');

            $table->unique(
                ['course_id', 'grade', 'weekly_hours'],
                'course_grade_hours_unique'
            );

            $table->index(['grade', 'course_id']);
        });
    }

    public function down(): void
    {
        Schema::table('course_grade_options', function (Blueprint $table) {
            $table->dropForeign(['course_id']);

            $table->dropUnique('course_grade_hours_unique');

            $table->dropIndex(['grade', 'course_id']);

            $table->dropColumn([
                'course_id',
                'grade',
                'weekly_hours',
            ]);
        });
    }
};