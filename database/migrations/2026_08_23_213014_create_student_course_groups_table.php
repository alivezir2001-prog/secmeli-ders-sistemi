<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_course_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            $table->foreignId('course_module_group_id')
                ->nullable()
                ->constrained('course_module_groups')
                ->nullOnDelete();

            $table->foreignId('course_module_id')
                ->nullable()
                ->constrained('course_modules')
                ->nullOnDelete();

            $table->unsignedTinyInteger('weekly_hours');

            /*
             * Aynı modül + saat içinde:
             * Grup 1, Grup 2, ...
             */
            $table->unsignedTinyInteger('group_number');

            $table->unsignedSmallInteger('minimum_students')
                ->default(10);

            $table->unsignedSmallInteger('maximum_students')
                ->nullable();

            /*
             * 1 = Taslak
             * 2 = Aktif
             * 3 = Kesinleşti
             * 4 = Kapalı
             */
            $table->unsignedTinyInteger('status')
                ->default(1);

            $table->boolean('auto_created')
                ->default(true);

            $table->timestamp('confirmed_at')
                ->nullable();

            $table->text('notes')
                ->nullable();

            $table->timestamps();

            $table->unique(
                [
                    'academic_year_id',
                    'course_id',
                    'course_module_group_id',
                    'course_module_id',
                    'weekly_hours',
                    'group_number',
                ],
                'student_course_group_unique'
            );

            $table->index(
                [
                    'academic_year_id',
                    'course_id',
                    'course_module_id',
                    'weekly_hours',
                    'status',
                ],
                'student_group_year_course_module_hours_status_idx'
            );

            $table->index(
                [
                    'academic_year_id',
                    'course_module_group_id',
                    'course_module_id',
                    'weekly_hours',
                ],
                'student_group_year_program_module_hours_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_groups');
    }
};