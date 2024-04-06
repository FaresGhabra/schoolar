<?php

namespace App\Models\SchoolCurriculum;

use App\Models\HelperMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $searchable = [
        'name',
        'number',
        'unit'
    ];

    protected $fillable = [
        'subject_id',
        'name',
        'number',
        'unit'
    ];

    public function subject() {
        return $this->belongsTo(Subject::class, 'subject_id', 'id');
    }
}
