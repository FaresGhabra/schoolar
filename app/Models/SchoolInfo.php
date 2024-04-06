<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolInfo extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'beginning_time',
        'address',
        'description',
        'photos',
        'logo',
    ];

    public function getPhotosAttribute($value)
    {
        return json_decode($value);
    }
}
