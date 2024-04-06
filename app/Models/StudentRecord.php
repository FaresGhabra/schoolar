<?php

namespace App\Models;

use App\Models\SchoolClassStructure\ClassSection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'section_id',
        'status',
        'note',
        'expire_date',
    ];

    public function student() : BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section() : BelongsTo
    {
        return $this->belongsTo(ClassSection::class);
    }
}
