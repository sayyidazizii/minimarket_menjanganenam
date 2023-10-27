<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtItem extends Model
{
    protected $table = 'invt_item';
    protected $primaryKey = 'item_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
