<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentCourseGroup extends Model
{
    protected $fillable = [
        'academic_year_id',
        'course_id',
        'course_module_group_id',
        'course_module_id',
        'weekly_hours',
        'group_number',
        'minimum_students',
        'maximum_students',
        'status',
        'auto_created',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'weekly_hours' => 'integer',
        'group_number' => 'integer',
        'minimum_students' => 'integer',
        'maximum_students' => 'integer',
        'status' => 'integer',
        'auto_created' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
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

    public function placements(): HasMany
    {
        return $this->hasMany(
            StudentCoursePlacement::class,
            'student_course_group_id'
        );
    }

    public function confirmed(): bool
    {
        return $this->status === 3;
    }

    public function active(): bool
    {
        return in_array($this->status, [1, 2], true);
    }
}