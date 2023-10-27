<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctProfitLossReport extends Model
{
    // use HasFactory;
    protected $table        = 'acct_profit_loss_report';
    protected $primaryKey   = 'profit_loss_report_id';
    protected $guarded = [
        'last_update'
    ];
}
