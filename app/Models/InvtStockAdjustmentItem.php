<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtStockAdjustmentItem extends Model
{
    // use HasFactory;
    protected $table        = 'invt_stock_adjustment_item';
    protected $primaryKey   = 'stock_adjustment_item_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
