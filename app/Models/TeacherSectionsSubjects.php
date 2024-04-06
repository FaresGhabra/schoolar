<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherSectionsSubjects extends Model
{
    use HasFactory;

    protected $fillable = ['subject_id', 'section_id', 'teacher_id'];
}