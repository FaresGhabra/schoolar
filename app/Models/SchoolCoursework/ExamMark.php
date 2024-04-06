<?php

namespace App\Models\SchoolCoursework;

use App\Models\SchoolAccounts\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    use HasFactory;
    protected $fillable = ['student_id', 'mark', 'note', 'exam_id'];

    public function exam() {
        return $this->belongsTo(Exam::class, 'exam_id', 'id');
    }

    public function student() {
        return $this->belongsTo(Student::class, 'student_id','id');
    }
}