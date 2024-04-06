<?php

namespace App\Models\SchoolSchedules;

use App\Models\HelperMethods;
use App\Models\SchoolAccounts\Teacher;
use App\Models\SchoolClassStructure\ClassSection;
use App\Models\SchoolCurriculum\Subject;
use App\Models\SchoolInfo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Session extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;
    protected $appends = ['start_time', 'end_time'];

    protected $fillable = [
        'teacher_id',
        'section_id',
        'subject_id',
        'day',
        'session'
    ];

    public function teacher() {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id');
    }
    
    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }

    public function section() {
        return $this->belongsTo(ClassSection::class, 'section_id', 'id');
    }

    public function getStartTimeAttribute() {
        // dd($this->subject->school_class);
        $si = SchoolInfo::latest()->first();
        $b = (int)$this->subject->school_class->break_after;
        $bd = (int)$this->subject->school_class->break_duration;
        $s = (int)$this->session;
        $t = (int)$this->subject->school_class->duration_per_session;
        $time = (int)($s/$b) * $bd + $s * $t + (int)$si->beginning_time;
        return $time;
    }

    public function getEndTimeAttribute() {
        return $this->start_time + $this->subject->school_class->duration_per_session;
    }
}
