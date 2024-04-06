<?php

namespace App\Models\SchoolClassStructure;

use App\Models\HelperMethods;
use App\Models\SchoolAccounts\Student;
use App\Models\SchoolCoursework\Exam;
use App\Models\SchoolCoursework\ExamMark;
use App\Models\SchoolCoursework\Homework;
use App\Models\SchoolCoursework\SectionHomework;
use App\Models\SchoolSchedules\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassSection extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $fillable = [
        'class_id',
        'prompt_id',
        'number',
        'capacity',
        'student_count'
    ];

    public function school_class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'id');
    }

    public function homeworks()
    {
        return $this->belongsToMany(Homework::class, 'section_homeworks', 'section_id', 'homework_id')->with('teacher');
    }

    public function new_homeworks()
    {
        return $this->belongsToMany(Homework::class, 'section_homeworks', 'section_id', 'homework_id')->with('teacher')->whereDate('date', '>=', now());
    }

    public function old_homeworks()
    {
        return $this->belongsToMany(Homework::class, 'section_homeworks', 'section_id', 'homework_id')->with('teacher')->whereDate('date', '<', now());
    }

    public function exam_marks()
    {
        return $this->hasManyThrough(ExamMark::class, Student::class, 'section_id', 'student_id');
    }

    public function section_homework()
    {
        return $this->hasMany(SectionHomework::class);
    }

    public function getProgramAttribute()
    {
        $sessions = Session::where('section_id', $this->id)->with('teacher')->with('subject')->orderBy('day')->orderBy('session')->get()->toArray();
        $program = array_fill(0, 7, array_fill(0, (int) $this->number_of_sessions, null));
        foreach ($sessions as $s) {
            $program[(int) $s['day'] - 1][(int) $s['session'] - 1] = [
                'subject' => $s['subject']['name'],
                'subject_id' => $s['subject']['id'],
                'from' => $s['start_time'],
                'to' => $s['end_time'],
                'teacher' => $s['teacher']['fullname']
            ];
        }
        return $program;
    }

    public function getDetailedProgramAttribute()
    {
        $sessions = Session::where('section_id', $this->id)
            ->with('teacher')
            ->with('subject')
            ->with('subject.school_class')
            ->orderBy('day')
            ->orderBy('session')
            ->get()->toArray();
        $program = array_fill(0, 7, array_fill(0, (int) $this->number_of_sessions, null));
        foreach ($sessions as $s) {
            unset($s['teacher']['user']);
            $program[(int) $s['day'] - 1][(int) $s['session'] - 1] = [
                'subject' => $s['subject']['name'],
                'subject_id' => $s['subject']['id'],
                'from' => $s['start_time'],
                'to' => $s['end_time'],
                'teacher' => $s['teacher']['fullname'],

                'id' => $s['id']
            ];
        }
        return $program;
    }
}