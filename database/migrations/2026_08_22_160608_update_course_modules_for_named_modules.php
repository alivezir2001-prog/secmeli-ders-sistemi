<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropUnique(
                'course_module_number_unique'
            );
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->unsignedTinyInteger('module_number')
                ->nullable()
                ->change();

            $table->unique(
                ['course_id', 'name'],
                'course_module_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropUnique(
                'course_module_name_unique'
            );
        });

        Schema::table('course_modules', function (Blueprint $table) {
            $table->unsignedTinyInteger('module_number')
                ->nullable(false)
                ->change();

            $table->unique(
                ['course_id', 'module_number'],
                'course_module_number_unique'
            );
        });
    }
};