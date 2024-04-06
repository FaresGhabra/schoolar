<?php

namespace App\Models;

use App\Models\SchoolAccounts\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherAbsence extends Model
{
    use HasFactory;

    protected $table = 'teacher_absence';

    protected $fillable = [
        'teacher_id',
        'reasonable',
        'note'
    ];

    public function teacher() : BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }
}
