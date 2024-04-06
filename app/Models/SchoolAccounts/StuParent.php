<?php

namespace App\Models\SchoolAccounts;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

// can't use parent as class name (reseved)
class StuParent extends Model
{
    use HasFactory;

    protected $table = 'parents';


    protected $fillable = [
        'user_id',
        'active',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'parent_id', 'id');
    }
}
