<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    /*
     * course_grade_option_id ve foreign key'i önceki,
     * kısmen başarılı migration çalışmasında zaten oluştu.
     */

    /*
     * Önce yeni unique index'i oluşturuyoruz.
     * Böylece academic_year_id foreign key'i için gerekli
     * index kesintiye uğramıyor.
     */
    Schema::table('course_offerings', function (Blueprint $table) {
        $table->unique(
            [
                'academic_year_id',
                'course_id',
                'course_grade_option_id',
            ],
            'academic_year_course_grade_option_offering_unique'
        );
    });

    /*
     * Yeni index artık foreign key tarafından kullanılabileceği
     * için eski index'i kaldırabiliriz.
     */
    Schema::table('course_offerings', function (Blueprint $table) {
        $table->dropUnique(
            'academic_year_course_offering_unique'
        );
    });
}

    public function down(): void
{
    Schema::table('course_offerings', function (Blueprint $table) {
        /*
         * Eski index'i önce geri oluştur.
         */
        $table->unique(
            [
                'academic_year_id',
                'course_id',
            ],
            'academic_year_course_offering_unique'
        );

        /*
         * Sonra yeni index'i kaldır.
         */
        $table->dropUnique(
            'academic_year_course_grade_option_offering_unique'
        );
    });
}
};