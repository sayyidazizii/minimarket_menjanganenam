<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferenceCompany extends Model
{
    // use HasFactory;
    protected $table        = 'preference_company';
    protected $primaryKey   = 'company_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
