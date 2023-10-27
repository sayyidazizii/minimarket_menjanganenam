<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SIIRemoveLog extends Model
{
    // use HasFactory;
    protected $table  = 'sii_remove_log';
    protected $primaryKey = 'sii_remove_log_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
