<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoreMemberKopkar extends Model
{
    // use HasFactory;
    protected $connection = 'mysql2';
    protected $table = 'core_member';
    protected $primaryKey = 'member_id';
    protected $guarded = [
        'last_update',
    ];
    const CREATED_AT = "created_on";
    const UPDATED_AT = "last_update";
}
