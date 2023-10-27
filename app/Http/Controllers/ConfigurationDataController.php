<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AcctCreditAccount;
use App\Models\CloseCashierLog;
use App\Models\CoreEmployee;
use App\Models\InvtItem;
use App\Models\InvtItemBarcode;
use App\Models\InvtItemCategory;
use App\Models\InvtItemPackge;
use App\Models\InvtItemRack;
use App\Models\InvtItemStock;
use App\Models\InvtItemUnit;
use App\Models\InvtWarehouse;
use App\Models\PreferenceCompany;
use App\Models\PreferenceVoucher;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SystemLoginLog;
use App\Models\SIIRemoveLog;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;



class ConfigurationDataController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('content.ConfigurationData.ConfigurationData');
    }

    public function checkDataConfiguration()
    {
        $item_stock = curl_init();
        curl_setopt($item_stock, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-stock');
        curl_setopt($item_stock, CURLOPT_RETURNTRANSFER, true);
        $response_item_stock = curl_exec($item_stock);
        $result_item_stock = json_decode($response_item_stock,TRUE);
        curl_close($item_stock);
        
        foreach ($result_item_stock as $key => $val) {
            $data_stock[$key] = InvtItemStock::where('company_id', Auth::user()->company_id)
            ->where('item_id', $val['item_id'])
            ->where('item_unit_id', $val['item_unit_id'])
            ->where('item_category_id', $val['item_category_id'])
            ->where('last_balance','!=',$val['last_balance'])
            ->first();
        }

        $data = array_slice($data_stock, 0, 1);
        return json_encode($data, true);

    }

    public function dwonloadConfigurationData() 
    {
        set_time_limit(3000);
        $item_category = curl_init();
        curl_setopt($item_category, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-category');
        curl_setopt($item_category, CURLOPT_RETURNTRANSFER, true);
        $response_item_category = curl_exec($item_category);
        $result_item_category = json_decode($response_item_category,TRUE);
        curl_close($item_category);
        
        InvtItemCategory::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_category_id')->delete();
        foreach ($result_item_category as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data1 = InvtItemCategory::create($val);
            }
        }
        
        $item_unit = curl_init();
        curl_setopt($item_unit, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-unit');
        curl_setopt($item_unit, CURLOPT_RETURNTRANSFER, true);
        $response_item_unit = curl_exec($item_unit);
        $result_item_unit = json_decode($response_item_unit,TRUE);
        curl_close($item_unit);
        
        InvtItemUnit::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_unit_id')->delete();
        foreach ($result_item_unit as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data2 = InvtItemUnit::create($val);
            }
        }

        $item_barcode = curl_init();
        curl_setopt($item_barcode, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-barcode');
        curl_setopt($item_barcode, CURLOPT_RETURNTRANSFER, true);
        $response_item_barcode = curl_exec($item_barcode);
        $result_item_barcode = json_decode($response_item_barcode,TRUE);
        curl_close($item_barcode);
        
        InvtItemBarcode::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_barcode_id')->delete();
        foreach ($result_item_barcode as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data3 = InvtItemBarcode::create($val);
            }
        }

        $item_packge = curl_init();
        curl_setopt($item_packge, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-packge');
        curl_setopt($item_packge, CURLOPT_RETURNTRANSFER, true);
        $response_item_packge = curl_exec($item_packge);
        $result_item_packge = json_decode($response_item_packge,TRUE);
        curl_close($item_packge);
        
        InvtItemPackge::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_packge_id')->delete();
        foreach ($result_item_packge as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data4 = InvtItemPackge::create($val);
            }
        }
        
        $item_warehouse = curl_init();
        curl_setopt($item_warehouse, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-warehouse');
        curl_setopt($item_warehouse, CURLOPT_RETURNTRANSFER, true);
        $response_item_warehouse = curl_exec($item_warehouse);
        $result_item_warehouse = json_decode($response_item_warehouse,TRUE);
        curl_close($item_warehouse);

        InvtWarehouse::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('warehouse_id')->delete();
        foreach ($result_item_warehouse as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data5 = InvtWarehouse::create($val);
            }
        }

        $item = curl_init();
        curl_setopt($item, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item');
        curl_setopt($item, CURLOPT_RETURNTRANSFER, true);
        $response_item = curl_exec($item);
        $result_item = json_decode($response_item,TRUE);
        curl_close($item);

        InvtItem::select(DB::statement('SET FOREIGN_KEY_CHECKS=0'))
        ->whereNotNull('item_id')->delete();
        foreach ($result_item as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data6 = InvtItem::create($val);
            }
        }

        $item_stock = curl_init();
        curl_setopt($item_stock, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-stock');
        curl_setopt($item_stock, CURLOPT_RETURNTRANSFER, true);
        $response_item_stock = curl_exec($item_stock);
        $result_item_stock = json_decode($response_item_stock,TRUE);
        curl_close($item_stock);
        
        InvtItemStock::whereNotNull('item_stock_id')->delete();
        foreach ($result_item_stock as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data7 = InvtItemStock::create($val);
            }
        }

        $item_rack = curl_init();
        curl_setopt($item_rack, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-item-rack');
        curl_setopt($item_rack, CURLOPT_RETURNTRANSFER, true);
        $response_item_rack = curl_exec($item_rack);
        $result_item_rack = json_decode($response_item_rack,TRUE);
        curl_close($item_rack);
        
        InvtItemRack::whereNotNull('item_rack_id')->delete();
        foreach ($result_item_rack as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data8 = InvtItemRack::create($val);
            }
        }

        $core_member = curl_init();
        curl_setopt($core_member, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-core-employee');
        curl_setopt($core_member, CURLOPT_RETURNTRANSFER, true);
        $response_core_member = curl_exec($core_member);
        $result_core_member = json_decode($response_core_member,TRUE);
        curl_close($core_member);
        
        // CoreEmployee::whereNotNull('employee_id')->delete();
        // foreach ((array)$result_core_member as $key => $val) {
        //     if ($val['company_id'] == Auth::user()->company_id) {
        //         $data9 = CoreEmployee::create($val);
        //     }
        // }

        $preference_voucher = curl_init();
        curl_setopt($preference_voucher, CURLOPT_URL,'http://127.0.0.1:8080/api/get-data-preference-voucher');
        curl_setopt($preference_voucher, CURLOPT_RETURNTRANSFER, true);
        $response_preference_voucher = curl_exec($preference_voucher);
        $result_preference_voucher = json_decode($response_preference_voucher,TRUE);
        curl_close($preference_voucher);
        
        PreferenceVoucher::whereNotNull('voucher_id')->delete();
        foreach ($result_preference_voucher as $key => $val) {
            if ($val['company_id'] == Auth::user()->company_id) {
                $data10 = PreferenceVoucher::create($val);
            }
        }

        if (($data1 == true) && ($data2 == true) && ($data3 == true) && ($data4 == true) && ($data5 == true) && ($data6 == true) && ($data7 == true) && ($data8 == true) && ($data10 == true)) {
            $msg = "Data Berhasil diunduh";
            return redirect('/configuration-data')->with('msg', $msg);
        } else {
            $msg = "Data Gagal diunduh";
            return redirect('/configuration-data')->with('msg', $msg);
        }
    }   

    public function uploadConfigurationData()
    {
        $sales = DB::table('sales_invoice')
        ->where('status_upload',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $salesItem = DB::table('sales_invoice_item')
        ->where('status_upload',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        //dd($salesItem);
        // $member = DB::table('core_member')
        // ->where('member_account_receivable_amount_temp', '!=', 0)
        // ->where('company_id', Auth::user()->company_id)
        // ->get();
        


        $closeCashier = DB::table('close_cashier_log')
        ->where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        //use DB if needed
        $loginLog = SystemLoginLog::
        where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $salesRemove = SIIRemoveLog::
        where('status_upload', 0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $accCredit = AcctCreditAccount::
         where('status_upload', 0)
        ->get();
        
        $response = Http::post(env('API_URL','http://127.0.0.1:8080').'/api/post-data', [
            'sales'         => json_decode($sales, true),
            'salesItem'     => json_decode($salesItem, true),
            // 'member'        => json_decode($member, true),
            'accCredit'     => json_decode($accCredit, true),
            'closeCashier'  => json_decode($closeCashier, true),
            'loginLog'      => json_decode($loginLog, true),
            'salesRemove'   => json_decode($salesRemove, true),
        ]);
    //    echo json_encode($response->body());exit;
    //     return $response->body();
        if ($response->body() == 'true') {
            $data=$response->object();
            session()->flash('error', $data);
            DB::beginTransaction();
            try {
                
                SalesInvoice::where('status_upload',0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);
                
                SalesInvoiceItem::where('status_upload',0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);

                // CoreMember::where('member_account_receivable_amount_temp', '!=', 0)
                // ->where('company_id', Auth::user()->company_id)
                // ->update([
                //     'member_account_receivable_amount_temp' => 0,
                // ]);

                CloseCashierLog::where('status_upload', 0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);

                SystemLoginLog::where('status_upload', 0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                ]);
                
                SIIRemoveLog::where('status_upload', 0)
                ->where('company_id', Auth::user()->company_id)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);

                DB::connection('mysql2')->table('acct_credits_account')
                ->where('status_upload',0)
                ->update([
                    'status_upload' => 1,
                    'updated_id' => Auth::id()
                ]);
                
                DB::commit();
                $msg = "Data Berhasil diupload";
                return redirect('configuration-data')->with('msg', $msg);
                
            } catch (\Throwable $th) {
                
                $data=$response->object();
                session()->flash('error', [$th,$data]);
                DB::rollback();
                $msg = "Data Gagal diupload";
                return redirect('configuration-data')->with('msg', $msg);
                
            }
        } else {
            $data=$response->object();
            session()->flash('error', $data);
            $msg = "Data Gagal diupload";
            return redirect('configuration-data')->with('msg', $msg);
        }

    }
    
    public function checkCloseCashierConfiguration()
    {
        $data = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDate('cashier_log_date', date('Y-m-d'))
        ->get();

        return count($data);
    }

    public function closeCashierConfiguration()
    {
        $sales_invoice = SalesInvoice::where('data_state',0)
        ->whereDate('sales_invoice_date', date('Y-m-d'))
        ->where('company_id', Auth::user()->company_id)
        ->get();
        $close_cashier = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDate('cashier_log_date', date('Y-m-d'))
        ->get();
        $first_cashier = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->whereDate('cashier_log_date', date('Y-m-d'))
        ->first();

        $total_cash_transaction         = 0;
        $amount_cash_transaction        = 0;
        $total_receivable_transaction   = 0;
        $amount_receivable_transaction  = 0;
        $total_cashless_transaction     = 0;
        $amount_cashless_transaction    = 0;
        $total_transaction              = 0;
        $total_amount                   = 0;

        foreach ($sales_invoice as $key => $val) {
            if ($val['sales_payment_method'] == 1) {
                $total_cash_transaction += 1;
                $amount_cash_transaction += $val['total_amount'];
            } else if ($val['sales_payment_method'] == 2) {
                $total_receivable_transaction += 1;
                $amount_receivable_transaction += $val['total_amount'];
            } else {
                $total_cashless_transaction += 1;
                $amount_cashless_transaction += $val['total_amount'];
            }

            $total_transaction += 1;
            $total_amount +=  $val['total_amount'];
        }

        if (count($close_cashier) == 1) {
            $data_close_cashier = array(
                'company_id' => Auth::user()->company_id,
                'cashier_log_date' => date('Y-m-d'),
                'shift_cashier' => 2,
                'total_cash_transaction' => $total_cash_transaction - $first_cashier['total_cash_transaction'],
                'amount_cash_transaction' =>  $amount_cash_transaction - $first_cashier['amount_cash_transaction'],
                'total_receivable_transaction' => $total_receivable_transaction - $first_cashier['total_receivable_transaction'],
                'amount_receivable_transaction' => $amount_receivable_transaction - $first_cashier['amount_receivable_transaction'],
                'total_cashless_transaction' => $total_cashless_transaction - $first_cashier['total_cashless_transaction'],
                'amount_cashless_transaction' => $amount_cashless_transaction - $first_cashier['amount_cashless_transaction'],
                'total_transaction' => $total_transaction - ($first_cashier['total_cash_transaction'] + $first_cashier['total_receivable_transaction'] + $first_cashier['total_cashless_transaction']),
                'total_amount' => $total_amount - ($first_cashier['amount_cash_transaction'] + $first_cashier['amount_receivable_transaction'] + $first_cashier['amount_cashless_transaction']),
                'created_id' => Auth::id(),
                'updated_id' => Auth::id()
            );
        } else if (count($close_cashier) == 0) {
            $data_close_cashier = array(
                'company_id' => Auth::user()->company_id,
                'cashier_log_date' => date('Y-m-d'),
                'shift_cashier' => 1,
                'total_cash_transaction' => $total_cash_transaction,
                'amount_cash_transaction' => $amount_cash_transaction,
                'total_receivable_transaction' => $total_receivable_transaction,
                'amount_receivable_transaction' => $amount_receivable_transaction,
                'total_cashless_transaction' => $total_cashless_transaction,
                'amount_cashless_transaction' => $amount_cashless_transaction,
                'total_transaction' => $total_transaction,
                'total_amount' => $total_amount,
                'created_id' => Auth::id(),
                'updated_id' => Auth::id()
            );
        }

        if (CloseCashierLog::create($data_close_cashier)) {
            $msg = "Tutup Kasir Berhasil";
            return redirect('/configuration-data')->with('msg',$msg);
        } else {
            $msg = "Tutup Kasir Gagal";
            return redirect('/configuration-data')->with('msg',$msg);
        }
    }

    public function printCloseCashierConfiguration()
    {
        $data = CloseCashierLog::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->orderBy('cashier_log_id', 'DESC')
        ->first();

        $data_company = PreferenceCompany::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->first();

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(1, 1, 1, 1); // put space of 10 on top

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        $pdf::AddPage('P', array(48, 3276));

        $pdf::SetFont('helvetica', '', 10);

        $tbl = " 
        <table style=\" font-size:9px; \" >
            <tr>
                <td style=\"text-align: center; font-size:12px; font-weight: bold\">".$data_company['company_name']."</td>
            </tr>
            <tr>
                <td style=\"text-align: center; font-size:9px;\">".$data_company['company_address']."</td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
            
        $tblStock1 = "
        <div>---------------------------------------</div>
        <table style=\" font-size:9px; \">
            <tr>
                <td width=\"25%\">TGL.</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td width=\"60%\">".date('d-m-Y')."  ".date('H:i')."</td>
            </tr>
            <tr>
                <td width=\"25%\">SHIFT</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td>".$data['shift_cashier']."</td>
            </tr>
            <tr>
                <td width=\"25%\">KASIR</td>
                <td width=\"10%\" style=\"text-align: center;\">:</td>
                <td width=\"60%\">".ucfirst(Auth::user()->name)."</td>
            </tr>
        </table>
        <div>---------------------------------------</div>
        ";

        $tblStock2 = "
        <table style=\" font-size:9px; \" width=\" 100% \">
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">SALDO AWAL</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">400.000</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">TOTAL</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['total_amount'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">PIUTANG</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_receivable_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_receivable_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">E-WALLET</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_cashless_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cashless_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 35% \" style=\"text-align: left;\">TUNAI</td>
                <td width=\" 25% \" style=\"text-align: right;\">(".$data['total_cash_transaction'].")</td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cash_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">DISETOR</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">".number_format($data['amount_cash_transaction'],0,',','.')."</td>
            </tr>
            <tr>
                <td width=\" 45% \" style=\"text-align: left;\">SALDO AKHIR</td>
                <td width=\" 15% \" style=\"text-align: right;\"></td>
                <td width=\" 7% \" style=\"text-align: right;\">:</td>
                <td width=\" 33% \" style=\"text-align: right;\">400.000</td>
            </tr>
        </table>
        <div>---------------------------------------</div>
        
        ";

        $pdf::writeHTML($tblStock1.$tblStock2, true, false, false, false, '');


        $filename = 'Tutup_Kasir.pdf';
        $pdf::Output($filename, 'I');
    }

    public function backupDataConfiguration()
    {
        exec('start /B C:\xampp\htdocs\minimarket_menjanganenam\backup_data.bat');

        $msg = "Data Berhasil dicadangkan";
        return redirect('/configuration-data')->with('msg', $msg);
    }
}
