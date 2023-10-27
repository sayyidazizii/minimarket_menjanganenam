<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctProfitLossCombinedReport extends Model
{
    // use HasFactory;
    protected $table        = 'acct_profit_loss_combined_report';
    protected $primaryKey   = 'profit_loss_combined_report_id';
    protected $guarded = [
        'last_update'
    ];
}
