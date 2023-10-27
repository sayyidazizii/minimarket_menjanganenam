<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JournalVoucherItem extends Model
{
    // use HasFactory;
    protected $table        = 'acct_journal_voucher_item';
    protected $primaryKey   = 'journal_voucher_item_id';
    protected $guarded = [
        'updated_at',
        'created_at'
    ];
}
