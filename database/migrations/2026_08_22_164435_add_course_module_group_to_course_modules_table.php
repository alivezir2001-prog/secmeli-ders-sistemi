<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->foreignId('course_module_group_id')
                ->nullable()
                ->after('course_id')
                ->constrained('course_module_groups')
                ->nullOnDelete();

            $table->index(
                ['course_module_group_id', 'active'],
                'course_module_group_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::table('course_modules', function (Blueprint $table) {
            $table->dropIndex(
                'course_module_group_active_idx'
            );

            $table->dropForeign(
                ['course_module_group_id']
            );

            $table->dropColumn('course_module_group_id');
        });
    }
};