<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreSupplier extends Model
{
    // use HasFactory;
    protected $table = 'core_supplier';
    protected $primaryKey = 'supplier_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
