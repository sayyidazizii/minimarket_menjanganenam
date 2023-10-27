<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemUserGroup extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'system_user_group'; 
    protected $primaryKey   = 'user_group_id';
    
    protected $fillable = [
        'user_group_id',
        'user_group_level',
        'user_group_name',
        'company_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}
