<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseModuleHourOption extends Model
{
    protected $fillable = [
        'course_module_id',
        'grade',
        'weekly_hours',
        'active',
    ];

    protected $casts = [
        'grade' => 'integer',
        'weekly_hours' => 'integer',
        'active' => 'boolean',
    ];

    public function module(): BelongsTo
    {
        return $this->belongsTo(
            CourseModule::class,
            'course_module_id'
        );
    }
}