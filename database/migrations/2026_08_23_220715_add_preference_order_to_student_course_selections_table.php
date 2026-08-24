<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_course_selections', function (Blueprint $table) {
            $table->unsignedTinyInteger('preference_order')
                ->nullable()
                ->after('weekly_hours');

            $table->index(
                [
                    'student_id',
                    'academic_year_id',
                    'preference_order',
                ],
                'student_year_preference_order_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('student_course_selections', function (Blueprint $table) {
            $table->dropIndex(
                'student_year_preference_order_idx'
            );

            $table->dropColumn('preference_order');
        });
    }
};