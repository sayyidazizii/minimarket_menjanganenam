<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePaymentItem extends Model
{
    // use HasFactory;
    protected $table = 'purchase_payment_item';
    protected $primaryKey = 'payment_item_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
