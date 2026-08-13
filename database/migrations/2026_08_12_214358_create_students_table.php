<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();

            $table->string('student_number', 30)->nullable();
            $table->string('first_name');
            $table->string('last_name');

            $table->string('national_id', 11)->nullable();

            $table->boolean('active')->default(true);

            $table->timestamps();

            $table->index('student_number');
            $table->index('national_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};