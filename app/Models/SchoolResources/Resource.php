<?php

namespace App\Models\SchoolResources;

use App\Models\HelperMethods;
use App\Models\SchoolClassStructure\SchoolClass;
use App\Models\SchoolCurriculum\Subject;
use App\Models\SchoolAccounts\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resource extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $fillable = [
        'user_id',
        'subject_id',
        'file',
        'url',
        'description'
    ];

    protected $searchable = [
        'file',
        'url',
        'description'
    ];

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
    public function school_class() {
        return $this->hasOneThrough(SchoolClass::class, Subject::class, 'id', 'id', 'subject_id', 'class_id');
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
