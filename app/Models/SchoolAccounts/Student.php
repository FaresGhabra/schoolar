<?php

namespace App\Models\SchoolAccounts;

use App\Models\HelperMethods;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolCoursework\ExamMark;
use App\Models\SchoolCoursework\HomeworkMark;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Student extends Model
{
    use HasFactory, HelperMethods;

    protected $appends = ['fullname'];

    public $searchable = [
        'birth_date',
        'active'
    ];

    public $fillable = [
        'user_id',
        'parent_id',
        'birth_date',
        'active',
        'section_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(StuParent::class, 'parent_id');
    }

    public function section()
    {
        return $this->belongsTo(ClassSection::class, 'section_id', 'id');
    }

    public function homeworks()
    {
        return $this->sections()->marks;
    }

    public function getFullnameAttribute()
    {
        return $this->user->fullname;
    }

    public function exam_marks()
    {
        return $this->hasMany(ExamMark::class, 'student_id', 'id');
    }

    public function homework_marks()
    {
        return $this->hasMany(HomeworkMark::class, 'student_id', 'id');
    }
}