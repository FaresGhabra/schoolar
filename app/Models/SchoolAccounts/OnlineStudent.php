<?php

namespace App\Models\SchoolAccounts;

use App\Models\HelperMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineStudent extends Model
{
    use HasFactory, HelperMethods, SoftDeletes;

    protected $searchable = [
        'study_year'
    ];

    protected $fillable = [
        'birth_date',
        'user_id',
        'study_year',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}