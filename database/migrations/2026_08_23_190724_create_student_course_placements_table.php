<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_course_placements', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            /*
             * Öğrencinin yaptığı tercih.
             */
            $table->foreignId('student_course_selection_id')
                ->constrained('student_course_selections')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            /*
             * Öğrencinin seçtiği program / branş.
             */
            $table->foreignId('course_module_group_id')
                ->nullable()
                ->constrained('course_module_groups')
                ->nullOnDelete();

            /*
             * Okulun öğrenciyi yerleştirdiği gerçek modül.
             */
            $table->foreignId('course_module_id')
                ->nullable()
                ->constrained('course_modules')
                ->nullOnDelete();

            /*
             * Yerleştirilen haftalık ders saati.
             * Sınıf bilgisi burada yoktur.
             */
            $table->unsignedTinyInteger('weekly_hours');

            /*
             * 1 = Taslak
             * 2 = Yerleştirildi
             * 3 = Kesinleşti
             * 4 = İptal
             */
            $table->unsignedTinyInteger('status')
                ->default(1);

            $table->timestamp('placed_at')->nullable();

            $table->timestamp('confirmed_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            /*
             * Bir öğrencinin aynı tercih kaydı için
             * yalnızca bir yerleştirmesi olabilir.
             */
            $table->unique(
                'student_course_selection_id',
                'student_placement_selection_unique'
            );

            /*
             * Yerleştirme sorguları için.
             */
            $table->index(
                [
                    'academic_year_id',
                    'course_id',
                    'course_module_id',
                    'weekly_hours',
                    'status',
                ],
                'placement_year_course_module_hours_status_idx'
            );

            $table->index(
                [
                    'academic_year_id',
                    'course_module_group_id',
                    'course_module_id',
                    'weekly_hours',
                ],
                'placement_year_group_module_hours_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_placements');
    }
};