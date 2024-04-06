<?php

namespace App\Models\SchoolCoursework;

use App\Models\HelperMethods;
use App\Models\SchoolAccounts\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomeworkMark extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $fillable = [
        'student_id',
        'homework_id',
        'note',
        'mark'
    ];

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }

}