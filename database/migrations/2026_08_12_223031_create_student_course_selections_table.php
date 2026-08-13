<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_course_selections', function (Blueprint $table) {
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
                ->constrained('course_grade_options')
                ->restrictOnDelete();

            /*
             * Öğrencinin tercih sırası.
             * 1 = birinci tercih
             * 2 = ikinci tercih vb.
             */
            $table->unsignedTinyInteger('preference_order');

            /*
             * Tercihin durumu:
             * 1 = Taslak
             * 2 = Gönderildi
             * 3 = Kesinleşti
             * 4 = İptal
             */
            $table->unsignedTinyInteger('status')->default(1);

            $table->timestamp('submitted_at')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            /*
             * Aynı öğrenci aynı eğitim yılında
             * aynı dersi iki kere tercih edemez.
             */
            $table->unique(
                ['student_id', 'academic_year_id', 'course_id'],
                'student_year_course_selection_unique'
            );

            /*
             * Aynı öğrenci aynı eğitim yılında
             * aynı tercih sırasını iki farklı derse veremez.
             */
            $table->unique(
                ['student_id', 'academic_year_id', 'preference_order'],
                'student_year_preference_order_unique'
            );

            $table->index(
            ['academic_year_id', 'course_id', 'status'],
            'selection_year_course_status_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_selections');
    }
};