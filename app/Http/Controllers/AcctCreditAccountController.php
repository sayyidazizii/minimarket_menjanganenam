<?php

namespace App\Http\Controllers;

use App\Models\AcctCreditAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AcctCreditAccountController extends Controller
{
    public function getRecord()
    {
       
        $data = DB::connection('mysql2')->table("acct_credits_account")->get();
        return response()->json($data);
    }
}
