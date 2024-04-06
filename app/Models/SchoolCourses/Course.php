<?php

namespace App\Models\SchoolCourses;

use App\Models\HelperMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use HasFactory, HelperMethods, SoftDeletes;

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'admin_id',
    ];

    protected $fillable = [
        'title',
        'description',
        'price',
        'tags',
        'subtitle',
        'author',
        'admin_id',
        'thumb',
        'folder'
    ];

    protected $searchable = [
        'title',
        'description',
        'price',
        'tags',
        'subtitle'
    ];

    protected $table = 'courses';


    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get all of the files for the Course
     */
    public function files()
    {
        return $this->hasMany(CourseFile::class, 'course_id', 'id');
    }

    /**
     * Get all of the videos for the Course
     */
    public function videos()
    {
        return $this->hasMany(CourseVideo::class, 'course_id', 'id');
    }
}