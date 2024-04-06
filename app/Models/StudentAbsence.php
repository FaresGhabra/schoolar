<?php

namespace App\Models;

use App\Models\SchoolAccounts\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentAbsence extends Model
{
    use HasFactory;

    protected $table = 'student_absence';

    protected $fillable = [
        'student_id',
        'reasonable',
        'note'
    ];

    public function student() : BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
