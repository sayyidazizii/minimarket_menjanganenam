<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreBank extends Model
{
    // use HasFactory;
    protected $table        = 'core_bank';
    protected $primaryKey   = 'bank_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
