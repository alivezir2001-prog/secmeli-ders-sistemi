<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * grade alanı bu migration'ın ilk denemesinde
         * zaten başarıyla oluşturuldu.
         *
         * Mevcut foreign key, course_module_id alanını içeren
         * eski unique index'e ihtiyaç duyduğu için önce FK'yi
         * kaldırıyoruz.
         */
        Schema::table('course_module_hour_options', function (Blueprint $table) {
            $table->dropForeign([
                'course_module_id',
            ]);

            $table->dropUnique(
                'course_module_hour_unique'
            );
        });

        /*
         * Yeni benzersizlik:
         *
         * Aynı modül + aynı sınıf + aynı saat
         * yalnızca bir kez tanımlanabilir.
         */
        Schema::table('course_module_hour_options', function (Blueprint $table) {
            $table->unique(
                [
                    'course_module_id',
                    'grade',
                    'weekly_hours',
                ],
                'course_module_grade_hour_unique'
            );

            $table->index(
                [
                    'course_module_id',
                    'grade',
                    'active',
                ],
                'course_module_grade_active_idx'
            );
        });

        /*
         * Foreign key'i tekrar oluşturuyoruz.
         */
        Schema::table('course_module_hour_options', function (Blueprint $table) {
            $table->foreign('course_module_id')
                ->references('id')
                ->on('course_modules')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('course_module_hour_options', function (Blueprint $table) {
            $table->dropForeign([
                'course_module_id',
            ]);

            $table->dropIndex(
                'course_module_grade_active_idx'
            );

            $table->dropUnique(
                'course_module_grade_hour_unique'
            );
        });

        Schema::table('course_module_hour_options', function (Blueprint $table) {
            $table->unique(
                [
                    'course_module_id',
                    'weekly_hours',
                ],
                'course_module_hour_unique'
            );
        });

        Schema::table('course_module_hour_options', function (Blueprint $table) {
            $table->foreign('course_module_id')
                ->references('id')
                ->on('course_modules')
                ->cascadeOnDelete();
        });

        /*
         * grade alanını geri kaldırıyoruz.
         */
        Schema::table('course_module_hour_options', function (Blueprint $table) {
            $table->dropColumn('grade');
        });
    }
};