<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesCustomer extends Model
{
    // use HasFactory;
    protected $table        = 'sales_customer';
    protected $primaryKey   = 'customer_id';
    protected $guarded = [
        'updated_at',
        'created_at',
    ];
}
