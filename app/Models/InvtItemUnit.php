<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemUnit extends Model
{
    // use HasFactory;
    protected $table        = 'invt_item_unit';
    protected $primaryKey   = 'item_unit_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
