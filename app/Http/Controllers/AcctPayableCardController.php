<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctSupplierBalance;
use App\Models\CoreSupplier;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AcctPayableCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data_supplier = CoreSupplier::select('supplier_name', 'supplier_id')
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        return view('content.AcctPayableCard.ListAcctPayableCard', compact('start_date','end_date','data_supplier'));
    }

    public function filterPayableCard(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('payable-card');
    }

    public function resetFilterPayableCard()
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('payable-card');
    }

    public function getOpeningBalance($supplier_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data = AcctSupplierBalance::select('opening_balance')
        ->where('data_state',0)
        ->where('supplier_id', $supplier_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('supplier_balance_date', '>=', $start_date)
        ->where('supplier_balance_date', '<=', $end_date)
        // ->orderBy('supplier_balance_id', 'DESC')
        ->first();

        if (!empty($data)) {
            return $data['opening_balance'];
        } else {
            return 0;
        }
    }
    
    public function getPayableAmount($supplier_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data = AcctSupplierBalance::select('payable_amount')
        ->where('data_state',0)
        ->where('supplier_id', $supplier_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('supplier_balance_date', '>=', $start_date)
        ->where('supplier_balance_date', '<=', $end_date)
        ->get();

        $payable_amount = 0; 
        foreach ($data as $key => $val) {
            $payable_amount += $val['payable_amount'];
        }

        return $payable_amount;
    }

    public function getPaymentAmount($supplier_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data = AcctSupplierBalance::select('payment_amount')
        ->where('data_state',0)
        ->where('supplier_id', $supplier_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('supplier_balance_date', '>=', $start_date)
        ->where('supplier_balance_date', '<=', $end_date)
        ->get();

        $payment_amount = 0; 
        foreach ($data as $key => $val) {
            $payment_amount += $val['payment_amount'];
        }

        return $payment_amount;
    }

    public function getLastBalance($supplier_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data = AcctSupplierBalance::select('last_balance')
        ->where('data_state',0)
        ->where('supplier_id', $supplier_id)
        ->where('company_id', Auth::user()->company_id)
        ->where('supplier_balance_date', '>=', $start_date)
        ->where('supplier_balance_date', '<=', $end_date)
        ->orderBy('supplier_balance_id', 'DESC')
        ->first();

        return $data['last_balance'];
    }

    public function printPayableCard($supplier_id)
    {
        if(!$start_date = Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!$end_date = Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }

        $data_supplier = CoreSupplier::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('supplier_id', $supplier_id)
        ->first();

        $data = AcctSupplierBalance::where('supplier_id', $supplier_id)
        ->where('data_state',0)
        ->where('supplier_balance_date', '>=', $start_date)
        ->where('supplier_balance_date', '<=', $end_date)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 10, 10, 10); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::SetFont('helvetica', 'B', 20);

        $pdf::AddPage();

        $pdf::SetFont('helvetica', '', 8);

        $tbl = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">KARTU HUTANG PEMASOK</div></td>
            </tr>
            <tr>
                <td></td>
            </tr>
            <tr>
                <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Pemasok</div></td>
                <td width=\"5%\"><div style=\"text-align: center; font-size:12px\">:</div></td>
                <td width=\"30%\"><div style=\"text-align: left; font-size:12px\">".$data_supplier['supplier_name']."</div></td>
            </tr>
            <tr>
                <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Periode</div></td>
                <td width=\"5%\"><div style=\"text-align: center; font-size:12px\">:</div></td>
                <td width=\"30%\"><div style=\"text-align: left; font-size:12px\">".date('d-m-Y', strtotime($start_date))." s/d ".date('d-m-Y', strtotime($end_date))."</div></td>
            </tr>
            <tr>
                <td width=\"10%\"><div style=\"text-align: left; font-size:12px\">Saldo Awal</div></td>
                <td width=\"5%\"><div style=\"text-align: center; font-size:12px\">:</div></td>
                <td width=\"30%\"><div style=\"text-align: left; font-size:12px\">".number_format($this->getOpeningBalance($supplier_id),2,'.',',')."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <td width=\"5%\" ><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"13%\" ><div style=\"text-align: center; font-weight: bold\">No. Transaksi</div></td>
                <td width=\"13%\" ><div style=\"text-align: center; font-weight: bold\">Tanggal</div></td>
                <td width=\"30%\" ><div style=\"text-align: center; font-weight: bold\">Keterangan</div></td>
                <td width=\"13%\" ><div style=\"text-align: center; font-weight: bold\">Pembelian</div></td>
                <td width=\"13%\" ><div style=\"text-align: center; font-weight: bold\">Pembayaran</div></td>
                <td width=\"13%\" ><div style=\"text-align: center; font-weight: bold\">Saldo</div></td>
            </tr>
        
             ";

        $no = 1;
        $totalPayable = 0;
        $totalPayment = 0;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$val['transaction_no']."</td>
                    <td style=\"text-align:left\">".date('d-m-Y', strtotime($val['supplier_balance_date']))."</td>
                    <td style=\"text-align:left\">".$val['supplier_balance_remark']." : ".$data_supplier['supplier_name']."</td>
                    <td style=\"text-align:right\">".number_format($val['payable_amount'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['payment_amount'],2,'.',',')."</td>
                    <td style=\"text-align:right\">".number_format($val['last_balance'],2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
            $totalPayable += $val['payable_amount'];
            $totalPayment += $val['payment_amount'];
        }
        $tblStock3 = " 

            <tr>
                <td colspan=\"4\" style=\"text-align:center ; font-weight: bold\">Jumlah Mutasi</td>
                <td style=\"text-align:right; font-weight: bold\">".number_format($totalPayable,2,'.',',')."</td>
                <td style=\"text-align:right; font-weight: bold\">".number_format($totalPayment,2,'.',',')."</td>
                <td></td>
            </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Kartu_Hutang_Pemasok_'.$data_supplier['supplier_name'].'_'.$start_date.'_s/d_'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

}
