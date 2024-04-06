<?php

namespace App\Models\SchoolCoursework;

use App\Models\HelperMethods;
use App\Models\SchoolAccounts\Teacher;
use App\Models\SchoolAccounts\Student;
use App\Models\SchoolClassStructure\ClassSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Homework extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $table = 'homeworks';

    protected $fillable = [
        'teacher_id',
        'note',
        'title',
        'fullmark',
        'date'
    ];

    protected $searchable = [
        'note',
        'title',
        'fullmark',
        'date'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }

    public function sections()
    {
       return $this->belongsToMany(ClassSection::class, 'section_homeworks', 'homework_id', 'section_id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            Student::class, 
            SectionHomework::class,
            'homework_id', // Foreign key on Section
            'section_id', // Foreign key on Student
            'id', // Local key on Homework
            'section_id' // Local key on Section
        );
    }

    public function marks() {
        return $this->hasMany(HomeworkMark::class, 'homework_id', 'id');
    }
}