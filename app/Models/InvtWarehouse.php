<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvtWarehouse extends Model
{
    protected $table        = 'invt_warehouse';
    protected $primaryKey   = 'warehouse_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
