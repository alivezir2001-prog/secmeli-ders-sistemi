<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_years', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('grade');
            $table->string('section', 20)->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->unique(
                ['student_id', 'academic_year_id'],
                'student_year_unique'
            );

            $table->index(['academic_year_id', 'grade', 'section']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_years');
    }
};