<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    // use HasFactory;
    protected $table = 'purchase_invoice_item';
    protected $primaryKey = 'purchase_invoice_item_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
