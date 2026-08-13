<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'course_category_id',
        'name',
        'grade_level',
        'weekly_hours',
        'max_selections',
        'is_modular',
        'offered',
        'active',
        'notes',
    ];

    protected $casts = [
        'is_modular' => 'boolean',
        'offered' => 'boolean',
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(CourseCategory::class, 'course_category_id');
    }

    public function gradeOptions(): HasMany
    {
        return $this->hasMany(CourseGradeOption::class);
    }

    public function histories(): HasMany
    {
    return $this->hasMany(StudentCourseHistory::class);
    }
}