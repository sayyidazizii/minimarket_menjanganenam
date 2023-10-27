<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    // use HasFactory;
    protected $table = 'expenditure';
    protected $primaryKey = 'expenditure_id';
    protected $guarded = [
        'created_at',
        'updated_at'
    ];
}
