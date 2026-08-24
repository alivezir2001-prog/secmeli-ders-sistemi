<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseModule extends Model
{
    protected $fillable = [
        'course_id',
        'module_number',
        'name',
        'weekly_hours',
        'active',
        'notes',
    ];

    protected $casts = [
        'module_number' => 'integer',
        'weekly_hours' => 'integer',
        'active' => 'boolean',
    ];

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

    public function hourOptions(): HasMany
    {
    return $this->hasMany(
        CourseModuleHourOption::class,
        'course_module_id'
    )->orderBy('weekly_hours');
    }

}