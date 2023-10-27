<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItemPackge extends Model
{
    protected $table        = 'invt_item_packge';
    protected $primaryKey   = 'item_packge_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
