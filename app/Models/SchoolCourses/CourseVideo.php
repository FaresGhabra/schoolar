<?php

namespace App\Models\SchoolCourses;

use App\Models\HelperMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseVideo extends Model
{

    protected $table = 'courses_videos';

    use HasFactory, HelperMethods, SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'description'
    ];
    
    /**
     * Get the course that owns the CourseFile
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    // public function getSourcesAttribute($value) {
    //     return json_decode($value);
    // }
}
