<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    // use HasFactory;
    protected $table = 'purchase_payment';
    protected $primaryKey = 'payment_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
