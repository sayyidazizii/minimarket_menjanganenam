<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcctAccountSetting extends Model
{
    // use HasFactory;
    protected $table        = 'acct_account_setting';
    protected $primaryKey   = 'account_setting_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
