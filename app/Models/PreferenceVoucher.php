<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferenceVoucher extends Model
{
    // use HasFactory;
    protected $table        = 'preference_voucher';
    protected $primaryKey   = 'voucher_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
