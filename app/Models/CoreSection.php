<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoreSection extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */

    protected $table        = 'core_section'; 
    protected $primaryKey   = 'section_id';
    
    protected $guarded = [
        'section_id',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
    ];

}
