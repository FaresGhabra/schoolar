<?php

namespace App\Models\SchoolAccounts;

use App\Models\HelperMethods;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolCoursework\Homework;
use App\Models\SchoolCurriculum\Subject;
use App\Models\SchoolSchedules\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory, HelperMethods;


    protected $appends = ['fullname'];
    protected $fillable = [
        'user_id',
    ];

    protected $cast = [
        'gender' => GenderEnum::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sections()
    {
        return $this->belongsToMany(ClassSection::class, 'teacher_sections_subjects');
    }


    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'teacher_sections_subjects');
    }

    public function getFullnameAttribute()
    {
        return $this->user->fullname;
    }

    public function getProgramAttribute()
    {
        $sessions = Session::where('teacher_id', $this->id)
            ->with('section')
            ->with('section.school_class')
            ->with('subject')
            ->orderBy('day')
            ->orderBy('session')
            ->get()
            ->toArray();
        $program = array_fill(0, 7, array_fill(0, 1, null));
        foreach ($sessions as $s) {
            $program[(int) $s['day'] - 1][(int) $s['session'] - 1] = [
                'subject' => $s['subject']['name'],
                'section' => $s['section']['number'],
                'class' => $s['section']['school_class']['class_name'],
                'from' => $s['start_time'],
                'to' => $s['end_time'],
                'id' => $s['id']
            ];
        }
        for ($i = 0; $i < 7; $i++) {
            if (!isset($program[$i]))
                $program[$i] = null;
        }
        return $program;
    }

    public function getDetailedProgramAttribute()
    {
        $sessions = Session::where('teacher_id', $this->id)
            ->with('section')
            ->with('section.school_class')
            ->with('subject')
            ->orderBy('day')
            ->orderBy('session')
            ->get()
            ->toArray();
        $program = array_fill(0, 7, array_fill(0, 20, new \stdClass()));
        foreach ($sessions as $s) {
            $program[(int) $s['day'] - 1][(int) $s['session'] - 1] = [
                'subject' => $s['subject']['name'],
                'section' => $s['section']['number'],
                'class' => $s['section']['school_class']['class_name'],
                'from' => $s['start_time'],
                'to' => $s['end_time'],
                'id' => $s['id']
            ];
        }
        for ($i = 0; $i < 7; $i++) {
            $j = 19;
            while ($j >= 0) {
                if ($program[$i][$j] == new \stdClass())
                    array_splice($program[$i], $j, 1);
                $j--;
            }
        }


        return $program;
    }


    public function homeworks()
    {
        return $this->hasMany(Homework::class, 'teacher_id', 'id');
    }

    public function new_homeworks()
    {
        return $this->hasMany(Homework::class, 'teacher_id', 'id')->whereDate('date', '>=', now());

    }
    
    public function old_homeworks()
    {
        return $this->hasMany(Homework::class, 'teacher_id', 'id')->whereDate('date', '<', now());
    }

}