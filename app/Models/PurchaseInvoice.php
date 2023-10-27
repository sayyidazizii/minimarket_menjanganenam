<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    // use HasFactory;
    protected $table = 'purchase_invoice';
    protected $primaryKey = 'purchase_invoice_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
