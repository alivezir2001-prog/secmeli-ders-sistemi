<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Eski unique index ve foreign key kaldırılır.
         */
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropUnique(
                'academic_year_module_hour_offering_unique'
            );

            $table->dropForeign([
                'course_module_hour_option_id',
            ]);

            $table->dropForeign([
                'course_module_id',
            ]);
        });

        /*
         * Yeni yapı:
         *
         * course_module_id
         * weekly_hours
         *
         * Sınıf artık offering içinde tutulmaz.
         */
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->unsignedTinyInteger('weekly_hours')
                ->after('course_module_id');

            $table->dropColumn(
                'course_module_hour_option_id'
            );

            $table->unique(
                [
                    'academic_year_id',
                    'course_module_id',
                    'weekly_hours',
                ],
                'academic_year_module_weekly_hours_unique'
            );
        });

        /*
         * course_module_id artık zorunlu.
         */
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->unsignedBigInteger('course_module_id')
                ->nullable(false)
                ->change();

            $table->foreign('course_module_id')
                ->references('id')
                ->on('course_modules')
                ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('course_offerings', function (Blueprint $table) {
            $table->dropUnique(
                'academic_year_module_weekly_hours_unique'
            );

            $table->dropForeign([
                'course_module_id',
            ]);

            $table->dropColumn(
                'weekly_hours'
            );

            $table->foreignId('course_module_hour_option_id')
                ->nullable()
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

            $table->foreign('course_module_id')
                ->references('id')
                ->on('course_modules')
                ->restrictOnDelete();
        });
    }
};