<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentYear extends Model
{
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'grade',
        'section',
        'active',
    ];

    protected $casts = [
        'grade' => 'integer',
        'active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}