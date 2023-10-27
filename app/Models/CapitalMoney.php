<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CapitalMoney extends Model
{
    // use HasFactory;
    protected $table = 'capital_money';
    protected $primaryKey = 'capital_money_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
