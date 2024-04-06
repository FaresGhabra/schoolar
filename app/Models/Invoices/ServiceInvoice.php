<?php

namespace App\Models\Invoices;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceInvoice extends Model
{
    use HasFactory;

    protected $table = 'services_invoices';

    protected $fillable = [
        'users_services_id',
        'amount',
        'paid_online',
    ];

    public function userInvoice()
    {
        return $this->belongsTo(UserService::class);
    }
}
