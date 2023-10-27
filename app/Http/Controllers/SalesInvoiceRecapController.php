<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Illuminate\Support\Facades\Auth;
use App\Models\SalesInvoice;

class SalesInvoiceRecapController extends Controller
{
    public function index()
    {
        if(!Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $sales_payment_method_list = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];
        return view('content.SalesInvoiceRecap.ListSalesInvoiceRecap',compact('start_date', 'end_date','sales_payment_method_list'));
    }

    public function filterSalesRecap(Request $request)
    {
        $start_date = $request->start_date;
        $end_date   = $request->end_date;

        Session::put('start_date',$start_date);
        Session::put('end_date', $end_date);

        return redirect('/sales-invoice-recap');
    }

    public function resetFilterSalesRecap(Request $request)
    {
        Session::forget('start_date');
        Session::forget('end_date');

        return redirect('/sales-invoice-recap');
    }

    public function getAmount($date, $method)
    {   
        $data = SalesInvoice::where('data_state',0)
        ->where('sales_invoice_date',$date)
        ->where('company_id', Auth::user()->company_id)
        ->where('sales_payment_method', $method)
        ->get();

        $amount = 0;
        foreach ($data as $key => $val) {
            $amount += $val['total_amount'];
        }

        return $amount;
    }

    public function getAmountTotal($key)
    {   
        if(!Session::get('start_date')){
            $start_date = date('Y-m-d');
        } else {
            $start_date = Session::get('start_date');
        }
        if(!Session::get('end_date')){
            $end_date = date('Y-m-d');
        } else {
            $end_date = Session::get('end_date');
        }
        $data = SalesInvoice::where('data_state',0)
        ->where('sales_invoice_date','>=',$start_date)
        ->where('sales_invoice_date','<=',$end_date)
        ->where('company_id', Auth::user()->company_id)
        ->where('sales_payment_method', $key)
        ->get();

        $amount = 0;
        foreach ($data as $key => $val) {
            $amount += $val['total_amount'];
        }

        return $amount;
    }

    public function printSalesRecap()
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
        $data = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];

        $datediff = strtotime($end_date) - strtotime($start_date);
        $count_date = round($datediff / (60 * 60 * 24));

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
                <td><div style=\"text-align: center; font-size:14px; font-weight: bold\">REKAP PENJUALAN</div></td>
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
                <td width=\"9%\"><div style=\"text-align: center; font-weight: bold\">No</div></td>
                <td width=\"13%\"><div style=\"text-align: center; font-weight: bold\">Tanggal</div></td>
                <td width=\"13%\"><div style=\"text-align: center; font-weight: bold\">Tunai</div></td>
                <td width=\"13%\"><div style=\"text-align: center; font-weight: bold\">Piutang</div></td>
                <td width=\"13%\"><div style=\"text-align: center; font-weight: bold\">Gopay</div></td>
                <td width=\"13%\"><div style=\"text-align: center; font-weight: bold\">Ovo</div></td>
                <td width=\"13%\"><div style=\"text-align: center; font-weight: bold\">Shopeepay</div></td>
                <td width=\"13%\"><div style=\"text-align: center; font-weight: bold\">Total</div></td>
            </tr>
        
             ";

        $no = 1;
        $tblStock2 =" ";
        $total_tunai = 0;
        $total_piutang = 0;
        $total_gopay = 0;
        $total_ovo = 0;
        $total_shopee = 0;
        $total = 0;

        for ($i=0; $i <= $count_date ; $i++) { 
            $tblStock2 .= "
            <tr nobr=\"true\">
                <td width=\"9%\"><div style=\"text-align: center;\">".$no++.".</div></td>
                <td width=\"13%\"><div style=\"text-align: center;\">".date('d-m-Y', strtotime("+".$i." days", strtotime($start_date)))."</div></td>
                <td width=\"13%\"><div style=\"text-align: right;\">".number_format($this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1),2,'.',',')."</div></td>
                <td width=\"13%\"><div style=\"text-align: right;\">".number_format($this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2),2,'.',',')."</div></td>
                <td width=\"13%\"><div style=\"text-align: right;\">".number_format($this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3),2,'.',',')."</div></td>
                <td width=\"13%\"><div style=\"text-align: right;\">".number_format($this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4),2,'.',',')."</div></td>
                <td width=\"13%\"><div style=\"text-align: right;\">".number_format($this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5),2,'.',',')."</div></td>
                <td width=\"13%\"><div style=\"text-align: right;\">".number_format($this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5),2,'.',',')."</div></td>
            </tr>
            ";

            $total_tunai += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1);
            $total_piutang += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2);
            $total_gopay += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3);
            $total_ovo += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4);
            $total_shopee += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5);
            $total += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5);
        }

        $tblStock3 = " 
        <tr nobr=\"true\">
            <td width=\"22%\"><div style=\"text-align: center; font-weight: bold;\">TOTAL</div></td>
            <td width=\"13%\"><div style=\"text-align: right; font-weight: bold;\">".number_format($total_tunai,2,'.',',')."</div></td>
            <td width=\"13%\"><div style=\"text-align: right; font-weight: bold;\">".number_format($total_piutang,2,'.',',')."</div></td>
            <td width=\"13%\"><div style=\"text-align: right; font-weight: bold;\">".number_format($total_gopay,2,'.',',')."</div></td>
            <td width=\"13%\"><div style=\"text-align: right; font-weight: bold;\">".number_format($total_ovo,2,'.',',')."</div></td>
            <td width=\"13%\"><div style=\"text-align: right; font-weight: bold;\">".number_format($total_shopee,2,'.',',')."</div></td>
            <td width=\"13%\"><div style=\"text-align: right; font-weight: bold;\">".number_format($total,2,'.',',')."</div></td>
        </tr>
        </table>";

        $pdf::writeHTML($tblStock1.$tblStock2.$tblStock3, true, false, false, false, '');

        $filename = 'Rekap_Penjualan_'.$start_date.'s.d.'.$end_date.'.pdf';
        $pdf::Output($filename, 'I');
    }

    public function exportSalesRecap()
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
        $data = [
            1 => 'Tunai',
            2 => 'Piutang',
            3 => 'Gopay',
            4 => 'Ovo',
            5 => 'Shopeepay'
        ];

        $datediff = strtotime($end_date) - strtotime($start_date);
        $count_date = round($datediff / (60 * 60 * 24));

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("CST MOZAIQ POS")
                                        ->setLastModifiedBy("CST MOZAIQ POS")
                                        ->setTitle("Recap Penjualan")
                                        ->setSubject("")
                                        ->setDescription("Recap Penjualan")
                                        ->setKeywords("Recap, Penjualan")
                                        ->setCategory("Recap Penjualan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(40);

    
            $spreadsheet->getActiveSheet()->mergeCells("B1:I1");
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('B3:I3')->getFont()->setBold(true);

            $spreadsheet->getActiveSheet()->getStyle('B3:I3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:I3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Recap Penjualan Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Tanggal");
            $sheet->setCellValue('D3',"Tunai");
            $sheet->setCellValue('E3',"Piutang");
            $sheet->setCellValue('F3',"Gopay");
            $sheet->setCellValue('G3',"ovo");
            $sheet->setCellValue('H3',"Shopeepay");
            $sheet->setCellValue('I3',"Total");
            
            $j=4;
            $no=0;
            $total_tunai = 0;
            $total_piutang = 0;
            $total_gopay = 0;
            $total_ovo = 0;
            $total_shopee = 0;
            $total = 0;
            
            for ($i=0; $i <= $count_date ; $i++) { 
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Recap Penjualan");
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':I'.$j)->getNumberFormat()->setFormatCode('0.00');
        
                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
    
                $no++;
                $sheet->setCellValue('B'.$j, $no);
                $sheet->setCellValue('C'.$j, date('d-m-Y', strtotime("+".$i." days", strtotime($start_date))));
                $sheet->setCellValue('D'.$j, $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1));
                $sheet->setCellValue('E'.$j, $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2));
                $sheet->setCellValue('F'.$j, $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3));
                $sheet->setCellValue('G'.$j, $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4));
                $sheet->setCellValue('H'.$j, $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5));
                $sheet->setCellValue('I'.$j, $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5));

                $j++;
                $total_tunai += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1);
                $total_piutang += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2);
                $total_gopay += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3);
                $total_ovo += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4);
                $total_shopee += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5);
                $total += $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 1) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 2) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 3) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 4) + $this->getAmount(date('Y-m-d', strtotime("+".$i." days", strtotime($start_date))), 5);
            }

            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('D'.$j.':I'.$j)->getNumberFormat()->setFormatCode('0.00');
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':C'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j.':I'.$j)->getFont()->setBold(true);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, 'TOTAL');
            $sheet->setCellValue('D'.$j, $total_tunai);
            $sheet->setCellValue('E'.$j, $total_piutang);
            $sheet->setCellValue('F'.$j, $total_gopay);
            $sheet->setCellValue('G'.$j, $total_ovo);
            $sheet->setCellValue('H'.$j, $total_shopee);
            $sheet->setCellValue('I'.$j, $total);

            $j++;
            $spreadsheet->getActiveSheet()->mergeCells('B'.$j.':I'.$j);
            $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->setCellValue('B'.$j, Auth::user()->name.", ".date('d-m-Y H:i'));
            
            $filename='Recap_Penjualan_'.$start_date.'_s.d._'.$end_date.'.xls';
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
