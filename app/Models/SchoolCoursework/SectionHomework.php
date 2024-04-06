<?php

namespace App\Models\SchoolCoursework;

use App\Models\HelperMethods;
use App\Models\SchoolClassStructure\ClassSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionHomework extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $table = 'section_homeworks';

    protected $fillable = [
        'section_id',
        'homework_id'
    ];

    public function section()
    {
        return $this->belongsTo(ClassSection::class, 'section_id', 'id');
    }

    public function homework()
    {
        return $this->belongsTo(Homework::class, 'homework_id', 'id');
    }

    public function new_homework() {
        return $this->belongsTo(Homework::class,'homework_id', 'id')
            ->whereDate('date', '>', date('Y-m-d'));
    } 

    public function finished_homework() {
        return $this->belongsTo(Homework::class,'homework_id', 'id')
            ->whereDate('date', '<', date('Y-m-d'));
    }
}