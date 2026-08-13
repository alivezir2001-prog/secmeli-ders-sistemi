<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseGradeOption extends Model
{
    protected $fillable = [
        'course_id',
        'grade',
        'weekly_hours',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }
}