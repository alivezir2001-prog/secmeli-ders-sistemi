<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentCourseHistory extends Model
{
    protected $fillable = [
        'student_id',
        'academic_year_id',
        'course_id',
        'course_grade_option_id',
        'grade',
        'weekly_hours',
        'status',
        'notes',
    ];

    protected $casts = [
        'grade' => 'integer',
        'weekly_hours' => 'integer',
        'status' => 'integer',
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

    public static function timesTaken(
        int $studentId,
        int $courseId
        ): int {
        return static::query()
        ->where('student_id', $studentId)
        ->where('course_id', $courseId)
        ->whereIn('status', [1, 2])
        ->count();
    }

    public static function canTakeCourse(
        int $studentId,
        Course $course
        ): bool {
        $timesTaken = static::timesTaken(
        $studentId,
        $course->id
        );

        return $timesTaken < $course->max_selections;
    }
}