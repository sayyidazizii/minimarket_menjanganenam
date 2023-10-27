<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CoreSupplier;
use App\Models\InvtWarehouse;
use App\Models\PurchaseReturn;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PurchaseReturnReportController extends Controller
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
        if(!$warehouse_id = Session::get('warehouse_id')){
            $warehouse_id = '';
        } else {
            $warehouse_id = Session::get('warehouse_id');
        }
        $warehouse = InvtWarehouse::where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get()
        ->pluck('warehouse_name','warehouse_id');
        if ($warehouse_id == '') {
            $data = PurchaseReturn::join('purchase_return_item','purchase_return.purchase_return_id','=','purchase_return_item.purchase_return_id')
            ->where('purchase_return.purchase_return_date','>=',$start_date)
            ->where('purchase_return.purchase_return_date','<=',$end_date)
            ->where('purchase_return.company_id', Auth::user()->company_id)
            ->where('purchase_return.data_state',0)
            ->get();
        } else {
            $data = PurchaseReturn::join('purchase_return_item','purchase_return.purchase_return_id','=','purchase_return_item.purchase_return_id')
            ->where('purchase_return.warehouse_id', $warehouse_id)
            ->where('purchase_return.purchase_return_date','>=',$start_date)
            ->where('purchase_return.purchase_return_date','<=',$end_date)
            ->where('purchase_return.company_id', Auth::user()->company_id)
            ->where('purchase_return.data_state',0)
            ->get();
        }
        
        return view('content.PurchaseReturnReport.ListPurchaseReturnReport', compact('warehouse', 'data', 'start_date', 'end_date','warehouse_id'));
    }

    public function filterPurchaseReturnReport(Request $request)
    {
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $warehouse_id   = $request->warehouse_id;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('warehouse_id', $warehouse_id);

        return redirect('/purchase-return-report');
    }

    public function getWarehouseName($warehouse_id)
    {
        $data   = InvtWarehouse::where('warehouse_id', $warehouse_id)->first();

        return $data['warehouse_name'];
    }

    public function filterResetPurchaseReturnReport()
    {
        Session::forget('start_date');
        Session::forget('end_date');
        Session::forget('warehouse_id');
        return redirect('/purchase-return-report');
    }

    public function getSupplierName($supplier_id)
    {
        $data = CoreSupplier::where('supplier_id', $supplier_id)
        ->first();

        return $data['supplier_name'];
    }

    public function printPurchaseReturnReport()
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
        if(!$warehouse_id = Session::get('warehouse_id')){
            $warehouse_id = '';
        } else {
            $warehouse_id = Session::get('warehouse_id');
        }
        if ($warehouse_id == '') {
            $data = PurchaseReturn::join('purchase_return_item','purchase_return.purchase_return_id','=','purchase_return_item.purchase_return_id')
            ->where('purchase_return.purchase_return_date','>=',$start_date)
            ->where('purchase_return.purchase_return_date','<=',$end_date)
            ->where('purchase_return.company_id', Auth::user()->company_id)
            ->where('purchase_return.data_state',0)
            ->get();
        } else {
            $data = PurchaseReturn::join('purchase_return_item','purchase_return.purchase_return_id','=','purchase_return_item.purchase_return_id')
            ->where('purchase_return.warehouse_id', $warehouse_id)
            ->where('purchase_return.purchase_return_date','>=',$start_date)
            ->where('purchase_return.purchase_return_date','<=',$end_date)
            ->where('purchase_return.company_id', Auth::user()->company_id)
            ->where('purchase_return.data_state',0)
            ->get();
        }

        $pdf = new TCPDF('P', PDF_UNIT, 'F4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(20, 10, 20, 10); // put space of 10 on top

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">LAPORAN RETUR PEMBELIAN</div></td>
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
                <td width=\"4%\" ><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"24%\" ><div style=\"text-align: center; font-weight: bold\">Nama Pemasok</div></td>
                <td width=\"24%\" ><div style=\"text-align: center; font-weight: bold\">Nama Gudang</div></td>
                <td width=\"24%\" ><div style=\"text-align: center; font-weight: bold\">Tanggal Retur Pembelian</div></td>
                <td width=\"24%\" ><div style=\"text-align: center; font-weight: bold\">Jumlah Total</div></td>
            </tr>
        
             ";

        $no = 1;
        $total_amount = 0;
        $tblStock2 =" ";
        foreach ($data as $key => $val) {
            $tblStock2 .="
                <tr>			
                    <td style=\"text-align:center\">$no.</td>
                    <td style=\"text-align:left\">".$this->getSupplierName($val['supplier_id'])."</td>
                    <td style=\"text-align:left\">".$this->getWarehouseName($val['warehouse_id'])."</td>
                    <td style=\"text-align:left\">".date('d-m-Y', strtotime($val['purchase_return_date']))."</td>
                    <td style=\"text-align:right\">".number_format($val['purchase_item_subtotal'],2,'.',',')."</td>
                </tr>
                
            ";
            $no++;
            $total_amount += $val['purchase_item_subtotal'];
        }
        $tblStock3 = " 
        <tr>
            <td colspan=\"4\"><div style=\"text-align: center;  font-weight: bold\">TOTAL</div></td>
            <td style=\"text-align: right\"><div style=\"font-weight: bold\">". number_format($total_amount,2,'.',',') ."</div></td>
        </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td style=\"text-align:right\">".Auth::user()->name.", ".date('d-m-Y H:i')."</td>
            </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Laporan_Pembelian_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportPurchaseReturnReport()
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
        if(!$warehouse_id = Session::get('warehouse_id')){
            $warehouse_id = '';
        } else {
            $warehouse_id = Session::get('warehouse_id');
        }
        if ($warehouse_id == '') {
            $data = PurchaseReturn::join('purchase_return_item','purchase_return.purchase_return_id','=','purchase_return_item.purchase_return_id')
            ->where('purchase_return.purchase_return_date','>=',$start_date)
            ->where('purchase_return.purchase_return_date','<=',$end_date)
            ->where('purchase_return.company_id', Auth::user()->company_id)
            ->where('purchase_return.data_state',0)
            ->get();
        } else {
            $data = PurchaseReturn::join('purchase_return_item','purchase_return.purchase_return_id','=','purchase_return_item.purchase_return_id')
            ->where('purchase_return.warehouse_id', $warehouse_id)
            ->where('purchase_return.purchase_return_date','>=',$start_date)
            ->where('purchase_return.purchase_return_date','<=',$end_date)
            ->where('purchase_return.company_id', Auth::user()->company_id)
            ->where('purchase_return.data_state',0)
            ->get();
        }

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("IBS CJDW")
                                        ->setLastModifiedBy("IBS CJDW")
                                        ->setTitle("Purchase Return Report")
                                        ->setSubject("")
                                        ->setDescription("Purchase Return Report")
                                        ->setKeywords("Purchase, Return, Report")
                                        ->setCategory("Purchase Return Report");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(23);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    
            $spreadsheet->getActiveSheet()->mergeCells("B1:F1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:F3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Retur Pembelian Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Pemasok");
            $sheet->setCellValue('D3',"Nama Gudang");
            $sheet->setCellValue('E3',"Tanggal Retur Pembelian");
            $sheet->setCellValue('F3',"Jumlah Total");
            
            $j=4;
            $no=0;
            $total_amount = 0;

            foreach($data as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Jurnal Umum");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);




                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $this->getSupplierName($val['supplier_id']));
                        $sheet->setCellValue('D'.$j, $this->getWarehouseName($val['warehouse_id']));
                        $sheet->setCellValue('E'.$j, date('d-m-Y', strtotime($val['purchase_return_date'])));
                        $sheet->setCellValue('F'.$j, $val['purchase_item_subtotal']);
                }
                $j++;
                $total_amount += $val['purchase_item_subtotal'];
        
            }
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':E'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('F'.$j, $total_amount);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':F'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Laporan_Retur_Pembelian_'.$start_date.'_s.d._'.$end_date.'.xls';
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
