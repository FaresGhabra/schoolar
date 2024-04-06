<?php

namespace App\Models;

use App\Models\SchoolAccounts\Student;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusSupervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'gender',
        'phone_number',
        'code',
        'chat_id',
        'bus_number',
    ];

    public function student() : HasMany
    {
        return $this->hasMany(Student::class);
    }
}
