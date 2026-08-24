<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Eski FK önce kaldırılmalı.
         * Çünkü eski unique index bu FK tarafından kullanılıyor.
         */
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropForeign([
                'course_grade_option_id',
            ]);

            $table->dropUnique(
                'academic_year_course_grade_option_offering_unique'
            );
        });

        /*
         * Yeni anahtar:
         * ders + modül + sınıf + saat seçeneği
         */
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->foreignId('course_module_hour_option_id')
                ->after('course_module_id')
                ->constrained('course_module_hour_options')
                ->restrictOnDelete();

            $table->unique(
                [
                    'academic_year_id',
                    'course_module_hour_option_id',
                ],
                'academic_year_module_hour_offering_unique'
            );
        });

        /*
         * Artık eski alanı kaldırıyoruz.
         */
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropColumn(
            'course_grade_option_id'
            );
        });
    }

    public function down(): void
    {
        /*
         * Rollback gerektiğinde eski yapı tekrar oluşturulur.
         * Ancak mevcut offering verisi varsa otomatik veri dönüşümü
         * yapılmaz.
         */
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropUnique(
                'academic_year_module_hour_offering_unique'
            );

            $table->dropForeign([
                'course_module_hour_option_id',
            ]);

            $table->dropColumn(
                'course_module_hour_option_id'
            );

            $table->foreignId('course_grade_option_id')
                ->nullable()
                ->after('course_module_id')
                ->constrained('course_grade_options')
                ->restrictOnDelete();

            $table->unique(
                [
                    'academic_year_id',
                    'course_id',
                    'course_grade_option_id',
                ],
                'academic_year_course_grade_option_offering_unique'
            );
        });
    }
};