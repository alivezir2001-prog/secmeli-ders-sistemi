<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Önce aynı modül + haftalık saat için
         * oluşmuş sınıf bazlı tekrar kayıtlarını temizle.
         *
         * Her kombinasyondan ilk kayıt tutulur.
         */
        $duplicates = DB::table('course_module_hour_options')
            ->select(
                'course_module_id',
                'weekly_hours',
                DB::raw('MIN(id) as keep_id')
            )
            ->groupBy(
                'course_module_id',
                'weekly_hours'
            )
            ->get();

        foreach ($duplicates as $row) {
            DB::table('course_module_hour_options')
                ->where('course_module_id', $row->course_module_id)
                ->where('weekly_hours', $row->weekly_hours)
                ->where('id', '!=', $row->keep_id)
                ->delete();
        }

        Schema::table(
            'course_module_hour_options',
            function (Blueprint $table) {
                $table->dropForeign([
                    'course_module_id',
                ]);

                $table->dropIndex(
                    'course_module_grade_active_idx'
                );

                $table->dropUnique(
                    'course_module_grade_hour_unique'
                );

                $table->dropColumn('grade');
            }
        );

        Schema::table(
            'course_module_hour_options',
            function (Blueprint $table) {
                $table->unique(
                    [
                        'course_module_id',
                        'weekly_hours',
                    ],
                    'course_module_hour_unique'
                );

                $table->foreign('course_module_id')
                    ->references('id')
                    ->on('course_modules')
                    ->cascadeOnDelete();
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'course_module_hour_options',
            function (Blueprint $table) {
                $table->dropForeign([
                    'course_module_id',
                ]);

                $table->dropUnique(
                    'course_module_hour_unique'
                );

                $table->unsignedTinyInteger('grade')
                    ->after('course_module_id');

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

                $table->foreign('course_module_id')
                    ->references('id')
                    ->on('course_modules')
                    ->cascadeOnDelete();
            }
        );
    }
};