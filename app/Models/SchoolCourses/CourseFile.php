<?php

namespace App\Models\SchoolCourses;

use App\Models\HelperMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseFile extends Model
{

    protected $table = 'courses_files';

    use HasFactory, HelperMethods, SoftDeletes;

    protected $fillable =[
        'note',
        'file',
        'course_id'
    ];

    /**
     * Get the course that owns the CourseFile
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }
}
