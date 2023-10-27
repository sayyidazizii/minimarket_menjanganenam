<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemStock extends Model
{
    // use HasFactory;
    protected $table        = 'invt_item_stock';
    protected $primaryKey   = 'item_stock_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}