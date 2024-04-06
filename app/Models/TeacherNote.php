<?php

namespace App\Models;

use App\Models\SchoolAccounts\Student;
use App\Models\SchoolCurriculum\Lesson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\HelperMethods;

class TeacherNote extends Model
{
    use HasFactory, HelperMethods;

    protected $table = 'teacher_notes';

    protected $fillable = [
        'student_id',
        'teacher_id',
        'lesson_id',
        'note',
    ];

    protected $searchable = [
        'note',
        'created_at'
    ];

    public function student() : BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function teacher() : BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function lesson() : BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
