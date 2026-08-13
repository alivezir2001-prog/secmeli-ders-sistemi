<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\User;

class Student extends Model
{
    protected $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'national_id',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function studentYears(): HasMany
    {
        return $this->hasMany(StudentYear::class);
    }

    public function courseHistories(): HasMany
    {
        return $this->hasMany(StudentCourseHistory::class);
    }

    public function courseSelections(): HasMany
    {
    return $this->hasMany(StudentCourseSelection::class);
    }

    public function user()
    {
    return $this->hasOne(User::class);
    }

}