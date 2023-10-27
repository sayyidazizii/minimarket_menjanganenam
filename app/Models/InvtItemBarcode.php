<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemBarcode extends Model
{
    protected $table        = 'invt_item_barcode';
    protected $primaryKey   = 'item_barcode_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
