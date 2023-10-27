<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CloseCashierLog extends Model
{
    // use HasFactory;
    protected $table = 'close_cashier_log';
    protected $primaryKey = 'cashier_log_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
