<?php

namespace App\Models\SchoolCurriculum;

use App\Models\HelperMethods;
use App\Models\SchoolClassStructure\SchoolClass;
use App\Models\SchoolResources\Resource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $searchable = [
        'name',
    ];
    protected $fillable = [
        'class_id',
        'name',
    ];

    public function school_class()
    {
        return $this->belongsTo(SchoolClass::class, 'class_id', 'id');
    }

    public function lessons()
    {
        return $this->hasMany(Lesson::class, 'subject_id', 'id');
    }

    public function resources() {
        return $this->hasMany(Resource::class, 'subject_id', 'id');      
    }
}