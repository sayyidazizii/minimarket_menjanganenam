<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctSupplierBalance extends Model
{
    // use HasFactory;
    protected $table = 'acct_supplier_balance';
    protected $primaryKey = 'supplier_balance_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
