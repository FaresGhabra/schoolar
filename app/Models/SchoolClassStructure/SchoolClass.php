<?php

namespace App\Models\SchoolClassStructure;

use App\Models\HelperMethods;
use App\Models\SchoolAccounts\Student;
use App\Models\SchoolCoursework\Exam;
use App\Models\SchoolCurriculum\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SchoolClass extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $table = 'classes';

    protected $searchable = [
        'class_name',
        'section_capacity',
        'number_of_sessions',
        'duration_per_session',
        'break_after',
        'break_duration'
    ];
    protected $fillable = [
        'class_name',
        'section_capacity',
        'number_of_sessions',
        'duration_per_session',
        'break_after',
        'break_duration'
    ];

    public function sections()
    {
        return $this->hasMany(ClassSection::class, 'class_id', 'id');
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class, 'class_id', 'id');
    }

    public function students()
    {
        return $this->hasManyThrough(Student::class, ClassSection::class, 'class_id', 'section_id', 'id', 'id');
    }

    public function exams()
    {
        return $this->hasManyThrough(Exam::class, Subject::class, 'class_id', 'subject_id', 'id', 'id');
    }

    public function new_exams()
    {
        return $this->hasManyThrough(Exam::class, Subject::class, 'class_id', 'subject_id', 'id', 'id')->whereDate('date', '>=', now());
    }

    public function old_exams() {
        return $this->hasManyThrough(Exam::class, Subject::class, 'class_id', 'subject_id', 'id', 'id')->whereDate('date', '<', now());
        
    }
}