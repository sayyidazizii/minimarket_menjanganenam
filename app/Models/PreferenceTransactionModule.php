<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreferenceTransactionModule extends Model
{
    // use HasFactory;
    protected $table        = 'preference_transaction_module';
    protected $primaryKey   = 'transaction_module_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
