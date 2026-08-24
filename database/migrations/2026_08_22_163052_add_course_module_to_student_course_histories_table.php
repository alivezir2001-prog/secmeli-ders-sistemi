<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_histories', function (Blueprint $table) {
            $table->foreignId('course_module_id')
                ->nullable()
                ->after('course_id')
                ->constrained('course_modules')
                ->nullOnDelete();

            $table->index(
                ['student_id', 'course_id', 'course_module_id'],
                'history_student_course_module_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_course_histories', function (Blueprint $table) {
            $table->dropIndex(
                'history_student_course_module_idx'
            );

            $table->dropForeign(
                ['course_module_id']
            );

            $table->dropColumn('course_module_id');
        });
    }
};