<?php

namespace App\Models\SchoolAccounts;

use App\Models\HelperMethods;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prompt extends Model
{
    use HasFactory, HelperMethods;

    protected $fillable = [
        'user_id',
    ];

    protected $cast = [
        'gender' => GenderEnum::class
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
