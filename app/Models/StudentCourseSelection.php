<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourseSelection extends Model
{
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'course_id',
        'course_grade_option_id',
        'preference_order',
        'status',
        'submitted_at',
        'notes',
    ];

    protected $casts = [
        'preference_order' => 'integer',
        'status' => 'integer',
        'submitted_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function gradeOption(): BelongsTo
    {
        return $this->belongsTo(
            CourseGradeOption::class,
            'course_grade_option_id'
        );
    }
}