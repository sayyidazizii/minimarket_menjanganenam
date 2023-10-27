<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemRack extends Model
{
    // use HasFactory;
    protected $table        = 'invt_item_rack';
    protected $primaryKey   = 'item_rack_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];  
}
