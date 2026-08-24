<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_module_groups', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->string('name');

            $table->boolean('active')
                ->default(true);

            $table->timestamps();

            $table->unique(
                ['course_id', 'name'],
                'course_module_group_name_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_module_groups');
    }
};