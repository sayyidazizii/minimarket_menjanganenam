<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreMember;
use App\Models\InvtItem;
use App\Models\InvtItemCategory;
use App\Models\InvtItemUnit;
use App\Models\SalesInvoice;
use App\Models\SalesCustomer;
use App\Models\SalesInvoiceItem;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class SalesInvoiceReportController extends Controller
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
        if(!$sales_payment_method = Session::get('sales_payment_method')){
            $sales_payment_method = 0;
        } else {
            $sales_payment_method = Session::get('sales_payment_method');
        }
        if ($sales_payment_method == 0) {
            $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('company_id', Auth::user()->company_id)
            ->where('data_state',0)
            ->get();
        } else {
            $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('company_id', Auth::user()->company_id)
            ->where('sales_payment_method', $sales_payment_method)
            ->where('data_state',0)
            ->get();
        }
        $sales_payment_method_list = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];
        // dd($sales_payment_method);
        return view('content.SalesInvoiceReport.ListSalesInvoiceReport', compact('data','start_date','end_date','sales_payment_method','sales_payment_method_list'));
    }

    public function filterSalesInvoiceReport(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;
        $sales_payment_method   = $request->sales_payment_method;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);
        Session::put('sales_payment_method', $sales_payment_method);

        return redirect('/sales-invoice-report');
    }

    public function filterResetSalesInvoiceReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('sales_payment_method');

        return redirect('/sales-invoice-report');
    }

    public function getItemName($item_id)
    {
        $data = InvtItem::where('item_id',$item_id)->first();

        return $data['item_name'];
    }

    public function getItemUnitName($item_unit_id)
    {
        $data = InvtItemUnit::where('item_unit_id', $item_unit_id)->first();

        return $data['item_unit_name'];
    }

    public function getCategoryName($item_category_id)
    {
        $data = InvtItemCategory::where('item_category_id', $item_category_id)->first();

        return $data['item_category_name'];
    }

    public function getCustomerName($member_id)
    {
        $data = CoreMember::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->where('member_id', $member_id)
        ->first();

        return $data['member_name'];
    }

    public function getPaymentName($payment_method)
    {
        $sales_payment_method_list = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];

        return $sales_payment_method_list[$payment_method];
    }

    public function printSalesInvoiceReport()
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
        if(!$sales_payment_method = Session::get('sales_payment_method')){
            $sales_payment_method = 0;
        } else {
            $sales_payment_method = Session::get('sales_payment_method');
        }
        if ($sales_payment_method == 0) {
            $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('company_id', Auth::user()->company_id)
            ->where('data_state',0)
            ->get();
        } else {
            $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('company_id', Auth::user()->company_id)
            ->where('sales_payment_method', $sales_payment_method)
            ->where('data_state',0)
            ->get();
        }
        $sales_payment_method_list = [
            0 => '',
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];

        $pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf::setHeaderCallback(function($pdf){
            $pdf->SetFont('helvetica', '', 8);
            $header = "
            <div></div>
                <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
                    <tr>
                        <td rowspan=\"3\" width=\"6%\"><img src=\"".asset('resources/assets/img/logo_kopkar.png')."\" width=\"35\"></td>
                        <td rowspan=\"2\" width=\"70%\"><div style=\"text-align: left; font-weight: bold; font-size: 10pt;\">Koperasi Menjangan Enam</div></td>
                        <td width=\"10%\"><div style=\"text-align: left;\">Halaman</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".$pdf->getAliasNumPage()." / ".$pdf->getAliasNbPages()."</div></td>
                    </tr>  
                    <tr>
                        <td width=\"10%\"><div style=\"text-align: left;\">Dicetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".ucfirst(Auth::user()->name)."</div></td>
                    </tr>
                    <tr>
                        <td width=\"70%\">Kota Semarang</td>
                        <td width=\"10%\"><div style=\"text-align: left;\">Tgl. Cetak</div></td>
                        <td width=\"2%\"><div style=\"text-align: center;\">:</div></td>
                        <td width=\"12%\"><div style=\"text-align: left;\">".date('d-m-Y H:i')."</div></td>
                    </tr>
                </table>
                <hr>
            ";

            $pdf->writeHTML($header, true, false, false, false, '');
        });
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(10, 20, 10, 10); // put space of 10 on top

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN PENJUALAN ".$sales_payment_method_list[$sales_payment_method]."</div></td>
            </tr>
            <tr>
                <td><div style=\"text-align: center; font-size:12px\">PERIODE : ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date))."</div></td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($tbl, true, false, false, false, '');
        
        $no = 1;
        $tblStock1 = "
        <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" width=\"100%\">
            <tr>
                <th style=\"text-align:center; width: 5%;  font-weight: bold\">No</th>
                <th style=\"text-align:center; width: 12%;  font-weight: bold\">Anggota</th>
                <th style=\"text-align:center; width: 8%;  font-weight: bold\">Metode</th>
                <th style=\"text-align:center; width: 10%;  font-weight: bold\">Tanggal</th>
                <th style=\"text-align:center; width: 15%;  font-weight: bold\">No. Penjulan</th>
                <th style=\"text-align:center; width: 12%;  font-weight: bold\">Jumlah Barang</th>
                <th style=\"text-align:center; width: 10%;  font-weight: bold\">Subtotal</th>
                <th style=\"text-align:center; width: 8%;  font-weight: bold\">Diskon (%)</th>
                <th style=\"text-align:center; width: 10%;  font-weight: bold\">Total Diskon</th>
                <th style=\"text-align:center; width: 10%;  font-weight: bold\">Total</th>
            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        $subtotal_item = 0;
        $subtotal_amount = 0;
        $discount_amount_total = 0;
        $total_amount = 0;
        $subtotal_item1 = 0;
        $total_amount1 = 0;
        $total_transaksi1 = 0;
        $subtotal_item2 = 0;
        $total_amount2 = 0;
        $total_transaksi2 = 0;
        $subtotal_item3 = 0;
        $total_amount3 = 0;
        $total_transaksi3 = 0;
        $subtotal_item4 = 0;
        $total_amount4 = 0;
        $total_transaksi4 = 0;
        $subtotal_item5 = 0;
        $total_amount5 = 0;
        $total_transaksi5 = 0;
        
        foreach ($data as $key => $val) {
            $tblStock2 .="
            <tr nobr=\"true\">
                <td style=\"text-align:center\">". $no++ .".</td>
                <td>". $this->getCustomerName($val['customer_id']) ."</td>
                <td>". $this->getPaymentName($val['sales_payment_method']) ."</td>
                <td>". date('d-m-Y', strtotime($val['sales_invoice_date'])) ."</td>
                <td>". $val['sales_invoice_no'] ."</td>
                <td style=\"text-align:right;\">". $val['subtotal_item'] ."</td>
                <td style=\"text-align: right\">". number_format($val['subtotal_amount'],2,'.',',') ."</td>
                <td style=\"text-align: right\">". $val['discount_percentage_total'] ."</td>
                <td style=\"text-align: right\">". number_format($val['discount_amount_total'],2,'.',',') ."</td>
                <td style=\"text-align: right\">". number_format($val['total_amount'],2,'.',',') ."</td>
            </tr> 
                
            ";
            $subtotal_item += $val['subtotal_item'];
            $subtotal_amount += $val['subtotal_amount'];
            $discount_amount_total += $val['discount_amount_total'];
            $total_amount += $val['total_amount'];

            if ($val['sales_payment_method'] == 1) {
                $subtotal_item1 += $val['subtotal_item'];
                $total_amount1 += $val['total_amount'];
                $total_transaksi1 += 1;
            } else if ($val['sales_payment_method'] == 2) {
                $subtotal_item2 += $val['subtotal_item'];
                $total_amount2 += $val['total_amount'];
                $total_transaksi2 += 1;
            } else if ($val['sales_payment_method'] == 3) {
                $subtotal_item3 += $val['subtotal_item'];
                $total_amount3 += $val['total_amount'];
                $total_transaksi3 += 1;    
            } else if ($val['sales_payment_method'] == 4) {
                $subtotal_item4 += $val['subtotal_item'];
                $total_amount4 += $val['total_amount'];
                $total_transaksi4 += 1;    
            } else if ($val['sales_payment_method'] == 5) {
                $subtotal_item5 += $val['subtotal_item'];
                $total_amount5 += $val['total_amount'];
                $total_transaksi5 += 1;    
            }
        }
        
        $tblStock3 = " 
        <tr nobr=\"true\">
            <td colspan=\"5\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align:right;\"><div style=\"font-weight: bold\">". $subtotal_item ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($subtotal_amount,2,'.',',') ."</div></td>
            <td colspan=\"2\" style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($discount_amount_total,2,'.',',') ."</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($total_amount,2,'.',',') ."</div></td>
        </tr>
        </table>";

        $tblStock4 = "
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr nobr=\"true\">
                <th style=\"text-align:center; width: 25%;  font-weight: bold\">Metode Pembayaran</th>
                <th style=\"text-align:center; width: 25%;  font-weight: bold\">Jumlah Transaksi</th>
                <th style=\"text-align:center; width: 25%;  font-weight: bold\">Jumlah Barang</th>
                <th style=\"text-align:center; width: 25%;  font-weight: bold\">Total</th>
            </tr>
            <tr nobr=\"true\">
                <td><div style=\"text-align: left;\">Tunai</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $total_transaksi1 ."</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $subtotal_item1 ."</div></td>
                <td style=\"text-align: right\"><div style=\"\">". number_format($total_amount1,2,'.',',') ."</div></td>
            </tr>
            <tr nobr=\"true\">
                <td><div style=\"text-align: left;\">Piutang</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $total_transaksi2 ."</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $subtotal_item2 ."</div></td>
                <td style=\"text-align: right\"><div style=\"\">". number_format($total_amount2,2,'.',',') ."</div></td>
            </tr>
            <tr nobr=\"true\">
                <td><div style=\"text-align: left;\">Gopay</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $total_transaksi3 ."</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $subtotal_item3 ."</div></td>
                <td style=\"text-align: right\"><div style=\"\">". number_format($total_amount3,2,'.',',') ."</div></td>
            </tr>
            <tr nobr=\"true\">
                <td><div style=\"text-align: left;\">Ovo</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $total_transaksi4 ."</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $subtotal_item4 ."</div></td>
                <td style=\"text-align: right\"><div style=\"\">". number_format($total_amount4,2,'.',',') ."</div></td>
            </tr>
            <tr nobr=\"true\">
                <td><div style=\"text-align: left;\">Shoppepay</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $total_transaksi5 ."</div></td>
                <td style=\"text-align:right;\"><div style=\"\">". $subtotal_item5 ."</div></td>
                <td style=\"text-align: right\"><div style=\"\">". number_format($total_amount5,2,'.',',') ."</div></td>
            </tr>
        </table>
        ";

        if ($sales_payment_method == 0) {
            $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3.$tblStock4, true, false, false, false, '');
        } else {
            $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');
        }

        $filename = 'Laporan_Penjualan_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesInvoiceReport()
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
        if(!$sales_payment_method = Session::get('sales_payment_method')){
            $sales_payment_method = 0;
        } else {
            $sales_payment_method = Session::get('sales_payment_method');
        }
        if ($sales_payment_method == 0) {
            $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('company_id', Auth::user()->company_id)
            ->where('data_state',0)
            ->get();
        } else {
            $data = SalesInvoice::where('sales_invoice_date','>=',$start_date)
            ->where('sales_invoice_date','<=',$end_date)
            ->where('company_id', Auth::user()->company_id)
            ->where('sales_payment_method', $sales_payment_method)
            ->where('data_state',0)
            ->get();
        }
        $sales_payment_method_list = [
            0 => '',
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Laporan Penjualan")
                                        ->setSubject("")
                                        ->setDescription("Laporan Penjualan")
                                        ->setKeywords("Laporan, Penjualan")
                                        ->setCategory("Laporan Penjualan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:K1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:K3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:K3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:K3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Penjualan ".$sales_payment_method_list[$sales_payment_method]." Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Anggota");
            $sheet->setCellValue('D3',"Metode");
            $sheet->setCellValue('E3',"Tanggal");
            $sheet->setCellValue('F3',"No. Invoice");
            $sheet->setCellValue('G3',"Jumlah Barang");
            $sheet->setCellValue('H3',"Subtotal");
            $sheet->setCellValue('I3',"Diskon (%)");
            $sheet->setCellValue('J3',"Total Diskon");
            $sheet->setCellValue('K3',"Total");
            
            $j=4;
            $no=0;
            $subtotal_item = 0;
            $subtotal_amount = 0;
            $discount_amount_total = 0;
            $total_amount = 0;
            $subtotal_item1 = 0;
            $total_amount1 = 0;
            $total_transaksi1 = 0;
            $subtotal_item2 = 0;
            $total_amount2 = 0;
            $total_transaksi2 = 0;
            $subtotal_item3 = 0;
            $total_amount3 = 0;
            $total_transaksi3 = 0;
            $subtotal_item4 = 0;
            $total_amount4 = 0;
            $total_transaksi4 = 0;
            $subtotal_item5 = 0;
            $total_amount5 = 0;
            $total_transaksi5 = 0;
            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Laporan Penjualan");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':K'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getNumberFormat()->setFormatCode('0.00');
                    $spreadsheet->getActiveSheet()->getStyle('J'.$j.':K'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    $spreadsheet->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $this->getCustomerName($val['customer_id']));
                        $sheet->setCellValue('D'.$j, $this->getPaymentName($val['sales_payment_method']));
                        $sheet->setCellValue('E'.$j, date('d-m-Y', strtotime($val['sales_invoice_date'])));
                        $sheet->setCellValue('F'.$j, $val['sales_invoice_no']);
                        $sheet->setCellValue('G'.$j, $val['subtotal_item']);
                        $sheet->setCellValue('H'.$j, $val['subtotal_amount']);
                        $sheet->setCellValue('I'.$j, $val['discount_percentage_total']);
                        $sheet->setCellValue('J'.$j, $val['discount_amount_total']);
                        $sheet->setCellValue('K'.$j, $val['total_amount']);
                }
                $subtotal_item += $val['subtotal_item'];
                $subtotal_amount += $val['subtotal_amount'];
                $discount_amount_total += $val['discount_amount_total'];
                $total_amount += $val['total_amount'];
                $j++;

                if ($val['sales_payment_method'] == 1) {
                    $subtotal_item1 += $val['subtotal_item'];
                    $total_amount1 += $val['total_amount'];
                    $total_transaksi1 += 1;
                } else if ($val['sales_payment_method'] == 2) {
                    $subtotal_item2 += $val['subtotal_item'];
                    $total_amount2 += $val['total_amount'];
                    $total_transaksi2 += 1;
                } else if ($val['sales_payment_method'] == 3) {
                    $subtotal_item3 += $val['subtotal_item'];
                    $total_amount3 += $val['total_amount'];
                    $total_transaksi3 += 1;    
                } else if ($val['sales_payment_method'] == 4) {
                    $subtotal_item4 += $val['subtotal_item'];
                    $total_amount4 += $val['total_amount'];
                    $total_transaksi4 += 1;    
                } else if ($val['sales_payment_method'] == 5) {
                    $subtotal_item5 += $val['subtotal_item'];
                    $total_amount5 += $val['total_amount'];
                    $total_transaksi5 += 1;    
                }
                
            }
            $spreadsheet->getActiveSheet()->getStyle('H'.$j.':K'.$j)->getNumberFormat()->setFormatCode('0.00');

            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':K'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':F'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':K'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('I'.$j.':J'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->mergeCells('I'.$j.':J'.$j);
            $spreadsheet->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('K'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('G'.$j, $subtotal_item);
            $sheet->setCellValue('H'.$j, $subtotal_amount);
            $sheet->setCellValue('I'.$j, $discount_amount_total);
            $sheet->setCellValue('K'.$j, $total_amount);

            if ($sales_payment_method == 0) {
                $j += 4;
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getFont()->setBold(true);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $sheet->setCellValue('C'.$j, 'Metode Pembayaran');
                $sheet->setCellValue('D'.$j, 'Jumlah Transaksi');
                $sheet->setCellValue('E'.$j, 'Jumlah Barang');
                $sheet->setCellValue('F'.$j, 'Total');

                $j++;
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('C'.$j, 'Tunai');
                $sheet->setCellValue('D'.$j, $total_transaksi1);
                $sheet->setCellValue('E'.$j, $subtotal_item1);
                $sheet->setCellValue('F'.$j, $total_amount1);

                $j++;
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('C'.$j, 'Piutang');
                $sheet->setCellValue('D'.$j, $total_transaksi2);
                $sheet->setCellValue('E'.$j, $subtotal_item2);
                $sheet->setCellValue('F'.$j, $total_amount2);

                $j++;
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('C'.$j, 'Gopay');
                $sheet->setCellValue('D'.$j, $total_transaksi3);
                $sheet->setCellValue('E'.$j, $subtotal_item3);
                $sheet->setCellValue('F'.$j, $total_amount3);

                $j++;
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('C'.$j, 'Ovo');
                $sheet->setCellValue('D'.$j, $total_transaksi4);
                $sheet->setCellValue('E'.$j, $subtotal_item4);
                $sheet->setCellValue('F'.$j, $total_amount4);

                $j++;
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
                $spreadsheet->getActiveSheet()->getStyle('C'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $sheet->setCellValue('C'.$j, 'Shopeepay');
                $sheet->setCellValue('D'.$j, $total_transaksi5);
                $sheet->setCellValue('E'.$j, $subtotal_item5);
                $sheet->setCellValue('F'.$j, $total_amount5);
            }

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':K'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Penjualan_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }
}
