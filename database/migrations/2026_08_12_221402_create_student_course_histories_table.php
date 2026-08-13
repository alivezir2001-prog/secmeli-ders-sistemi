<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_course_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            $table->foreignId('course_grade_option_id')
                ->nullable()
                ->constrained('course_grade_options')
                ->nullOnDelete();

            $table->unsignedTinyInteger('grade');

            $table->unsignedTinyInteger('weekly_hours');

            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('1=Alıyor, 2=Tamamladı, 3=İptal');

            $table->text('notes')->nullable();

            $table->timestamps();

            /*
             * Aynı öğrenci aynı eğitim yılında aynı dersi
             * iki kez geçmiş olarak kaydetmesin.
             */
            $table->unique(
                ['student_id', 'academic_year_id', 'course_id'],
                'student_year_course_unique'
            );

            $table->index([
                'student_id',
                'course_id',
            ]);

            $table->index([
                'academic_year_id',
                'course_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_histories');
    }
};