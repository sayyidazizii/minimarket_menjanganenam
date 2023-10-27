<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePaymentTransfer extends Model
{
    use HasFactory;
    protected $table = 'purchase_payment_transfer';
    protected $primaryKey = 'payment_transfer_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
