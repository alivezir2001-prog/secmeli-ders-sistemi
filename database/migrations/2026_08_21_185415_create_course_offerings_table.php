<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_offerings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            $table->foreignId('course_id')
                ->constrained('courses')
                ->restrictOnDelete();

            /*
             * Sistem kuralı:
             * Bir dersin açılabilmesi için en az 10 öğrenci
             * tarafından tercih edilmiş olması gerekir.
             *
             * Bu değer yönetici tarafından değiştirilemez.
             */
            $table->unsignedSmallInteger('minimum_students')
            ->default(10);

            /*
             * Okulun fiziksel/öğretmen kapasitesine göre
             * yönetici tarafından belirlenir.
             */
            $table->unsignedSmallInteger('maximum_students')
            ->nullable();

            /*
             * Maksimum öğrenci sayısı aşıldığında aynı dersin
             * ikinci/üçüncü grubunun açılmasına izin verilsin mi?
             */
            $table->boolean('allow_multiple_classes')
                ->default(false);

            /*
             * Çoklu sınıfa izin veriliyorsa açılabilecek
             * maksimum grup/sınıf sayısı.
             */
            $table->unsignedTinyInteger('maximum_classes')
                ->default(1);

            $table->boolean('active')
                ->default(true);

            $table->timestamps();

            /*
             * Aynı ders aynı eğitim yılında yalnızca bir
             * course_offering kaydına sahip olabilir.
             */
            $table->unique(
                ['academic_year_id', 'course_id'],
                'academic_year_course_offering_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('course_offerings');
    }
};