<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->foreignId('course_category_id')
                ->nullable()
                ->after('id')
                ->constrained('course_categories')
                ->nullOnDelete();

            $table->string('name')->after('course_category_id');

            $table->unsignedTinyInteger('max_selections')
                ->default(1)
                ->comment('Dersin en fazla kaç kez alınabileceği');

            $table->boolean('is_modular')
                ->default(false);

            $table->boolean('offered')
                ->default(true)
                ->comment('Bu eğitim yılında okul tarafından açılabilir mi?');

            $table->boolean('active')
                ->default(true);

            $table->text('notes')->nullable();

            $table->index(['course_category_id', 'offered']);
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropForeign(['course_category_id']);
            $table->dropColumn([
                'course_category_id',
                'name',
                'max_selections',
                'is_modular',
                'offered',
                'active',
                'notes',
            ]);
        });
    }
};