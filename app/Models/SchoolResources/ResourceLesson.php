<?php

namespace App\Models\SchoolResources;

use App\Models\HelperMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ResourceLesson extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;
}
