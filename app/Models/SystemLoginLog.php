<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLoginLog extends Model
{
    // use HasFactory;
    protected $table  = 'system_login_log';
    protected $primaryKey = 'login_log_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
