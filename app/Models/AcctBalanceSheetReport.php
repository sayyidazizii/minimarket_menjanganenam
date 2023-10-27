<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctBalanceSheetReport extends Model
{
    // use HasFactory;
    protected $table        = 'acct_balance_sheet_report';
    protected $primaryKey   = 'balance_sheet_report_id';
    protected $guarded = [
        'last_update',
    ];
}
