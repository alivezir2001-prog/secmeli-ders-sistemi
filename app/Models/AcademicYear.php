<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
        'name',
        'active',
        'selection_enabled',
        'selection_start_at',
        'selection_end_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'selection_enabled' => 'boolean',
        'selection_start_at' => 'datetime',
        'selection_end_at' => 'datetime',
    ];

    public function studentYears(): HasMany
    {
        return $this->hasMany(StudentYear::class);
    }

    public function courseHistories(): HasMany
    {
        return $this->hasMany(StudentCourseHistory::class);
    }

    public function courseOfferings(): HasMany
    {
    return $this->hasMany(CourseOffering::class);
    }

    public function courseSelections(): HasMany
    {
        return $this->hasMany(StudentCourseSelection::class);
    }

    public function selectionsAreOpen(): bool
    {
    if (! $this->active || ! $this->selection_enabled) {
        return false;
    }

    $now = now();

    if ($this->selection_start_at && $now->lt($this->selection_start_at)) {
        return false;
    }

    if ($this->selection_end_at && $now->gt($this->selection_end_at)) {
        return false;
    }

    return true;
    }

}