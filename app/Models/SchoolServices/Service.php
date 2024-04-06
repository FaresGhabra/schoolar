<?php

namespace App\Models\SchoolServices;

use App\Models\HelperMethods;
use App\Models\SchoolAccounts\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes, HelperMethods;

    protected $searchable = [
        'name',
        'description',
        'price'
    ];

    protected $fillable = [
        'name',
        'description',
        'photos',
        'price',
    ];

    function users()
    {
        return $this->belongsToMany(User::class, 'user_services', 'service_id', 'user_id');
    }

    public function getPhotosAttribute($value)
    {
        return json_decode($value);
    }
}