<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseOffering extends Model
{
    protected $fillable = [
        'academic_year_id',
        'course_id',
        'course_module_id',
        'weekly_hours',
        'minimum_students',
        'maximum_students',
        'allow_multiple_classes',
        'maximum_classes',
        'active',
    ];

    protected $casts = [
        'weekly_hours' => 'integer',
        'minimum_students' => 'integer',
        'maximum_students' => 'integer',
        'allow_multiple_classes' => 'boolean',
        'maximum_classes' => 'integer',
        'active' => 'boolean',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(
            CourseModule::class,
            'course_module_id'
        );
    }

    public function minimumMet(int $studentCount): bool
    {
        return $studentCount >= $this->minimum_students;
    }

    public function maximumReached(int $studentCount): bool
    {
        return $this->maximum_students !== null
            && $studentCount >= $this->maximum_students;
    }
}