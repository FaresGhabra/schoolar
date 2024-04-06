<?php

namespace App\Models\SchoolCourses;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseInvoice extends Model
{
    use HasFactory;

    const DRAFT = 'draft';
    const PAID = 'paid';
    const FAILED = 'failed';
    const CANCELED = 'canceled';
    const COMPLETED = 'completed';

    // protected $hidden = [
    //     'admin_id',
    // ];

    protected $table = 'courses_invoices';
    protected $casts = [
        'payment_info' => 'json',
    ];

    protected $fillable= [
      'course_id',
      'user_id',
      'status',
      'payment_method',
      'amount',
        'payment_info'
    ];
    /**
     * Get the user that owns the CourseInvoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function isPaid()
    {
        return in_array($this->status, [self::COMPLETED, self::PAID]);
    }

    public function isCanceled()
    {
        return in_array($this->status, [self::FAILED, self::CANCELED]);
    }

}
