<?php

namespace App\Exports;

use App\Models\AcctJournalVoucher;
use Maatwebsite\Excel\Concerns\FromCollection;

class LedgerExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AcctJournalVoucher::all();
    }
}
