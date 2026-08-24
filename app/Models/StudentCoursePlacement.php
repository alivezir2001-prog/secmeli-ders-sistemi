<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCoursePlacement extends Model
{
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'student_course_selection_id',
        'course_id',
        'course_module_group_id',
        'course_module_id',
        'course_grade_option_id',
        'student_course_group_id',
        'weekly_hours',
        'status',
        'placed_at',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'weekly_hours' => 'integer',
        'status' => 'integer',
        'placed_at' => 'datetime',
        'course_grade_option_id' => 'integer',
        'confirmed_at' => 'datetime',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function selection(): BelongsTo
    {
        return $this->belongsTo(
            StudentCourseSelection::class,
            'student_course_selection_id'
        );
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function moduleGroup(): BelongsTo
    {
        return $this->belongsTo(
            CourseModuleGroup::class,
            'course_module_group_id'
        );
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(
            CourseModule::class,
            'course_module_id'
        );
    }

    public function group(): BelongsTo
    {
    return $this->belongsTo(
        StudentCourseGroup::class,
        'student_course_group_id'
    );
    }

    public function gradeOption(): BelongsTo
    {
    return $this->belongsTo(
        CourseGradeOption::class,
        'course_grade_option_id'
    );
    }
    
}