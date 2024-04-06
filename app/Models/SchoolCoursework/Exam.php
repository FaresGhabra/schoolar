<?php

namespace App\Models\SchoolCoursework;

use App\Models\HelperMethods;
use App\Models\SchoolCurriculum\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $fillable = [
        'subject_id',
        'fullmark',
        'note',
        'date',
        'type'
    ];

    protected $searchable = [
        'fullmark',
        'note',
        'date',
        'type'
    ];

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function school_class() {
        return $this->subject->school_class();
    }

    public function students() {
        return $this->school_class->students();
    }
    
    public function marks() {
        return $this->hasMany(ExamMark::class, 'exam_id', 'id');
    }
}
