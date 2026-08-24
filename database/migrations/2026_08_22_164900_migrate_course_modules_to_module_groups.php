<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->unique(
                [
                    'course_module_group_id',
                    'module_number',
                ],
                'course_module_group_number_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropUnique(
                'course_module_group_number_unique'
            );
        });
    }
};