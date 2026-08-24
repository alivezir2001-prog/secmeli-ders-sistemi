<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('module_number');

            $table->string('name');

            $table->unsignedTinyInteger('weekly_hours')->nullable();

            $table->boolean('active')->default(true);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->unique(
                ['course_id', 'module_number'],
                'course_module_number_unique'
            );

            $table->index(
                ['course_id', 'active'],
                'course_module_active_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_modules');
    }
};