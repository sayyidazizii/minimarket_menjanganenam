<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\CoreSection;
use App\Models\CoreService;
use App\Models\CoreServiceStatus;
use App\Models\TransServiceDisposition;
use App\Models\TransServiceDispositionParameter;
use App\Models\TransServiceDispositionTerm;
use App\Models\TransServiceLog;
use App\Models\TransServiceRequisition;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Elibyy\TCPDF\Facades\TCPDF;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class DashboardReviewController extends PublicController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        Session::forget('service_requisition_id');

        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }else{
            $end_date = Session::get('end_date');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }

        $section_id = Session::get('section_id');
        $service_id = Session::get('service_id');

        $transservicerequisition = TransServiceRequisition::where('data_state','=',0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date);
        if($section_id||$section_id!=null||$section_id!=''){
            $transservicerequisition   = $transservicerequisition->where('section_id', $section_id);
        }
        if($service_id||$service_id!=null||$service_id!=''){
            $transservicerequisition   = $transservicerequisition->where('service_id', $service_id);
        }
        $transservicerequisition   = $transservicerequisition->get();

        $coresection = CoreSection::where('data_state', 0)
        ->pluck('section_name', 'section_id');

        $coreservice = CoreService::where('data_state', 0)
        ->pluck('service_name', 'service_id');

        return view('content/DashboardReview/ListTransServiceRequisition',compact('transservicerequisition', 'start_date', 'end_date', 'coresection', 'coreservice', 'section_id', 'service_id'));
    }    

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $section_id     = $request->section_id;
        $service_id     = $request->service_id;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('section_id', $section_id);
        Session::put('service_id', $service_id);

        return redirect('/dashboard-review');
    }

    public function tracking()
    {
        $service_requisition_id = Session::get('service_requisition_id');

        $transservicerequisition = TransServiceRequisition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        $servicerequisitionno = TransServiceRequisition::where('data_state', 0)
        ->pluck('service_requisition_no','service_requisition_id');

        $servicelog = TransServiceLog::where('service_requisition_no', $transservicerequisition['service_requisition_no'])
        ->get();

        return view('content/DashboardReview/TrackingTransServiceRequisition',compact('transservicerequisition', 'service_requisition_id', 'servicerequisitionno', 'servicelog'));
    }

    public function search(Request $request)
    {
        Session::put('service_requisition_id', $request->service_requisition_id);

        return redirect('/dashboard-review/tracking');
    }

    public function addReset()
    {
        Session::forget('sess_servicetoken');
        Session::forget('data_coresection');

        return redirect('/section/add');
    }

    public function getServiceName($service_id){
        $service = CoreService::where('data_state', 0)
        ->where('service_id', $service_id)
        ->first();

        return $service['service_name'];
    }

    public function getServiceStatus($service_status_id){
        $service_status = CoreServiceStatus::where('data_state', 0)
        ->where('service_status_id', $service_status_id)
        ->first();

        return $service_status['service_status_name'];
    }

    public function getUserName($user_id){
        $user = User::where('data_state', 0)
        ->where('user_id', $user_id)
        ->first();

        return $user['name'];
    }

    public function countData($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->count();

        return $transservicedisposition;
    }

    public function getServiceDispositionDate($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        return $transservicedisposition['created_at'];
    }

    public function getServiceDispositionApprovedDate($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        return $transservicedisposition['approved_at'];
    }

    public function getServiceDispositionReviewDate($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        return $transservicedisposition['review_at'];
    }

    public function getServiceDisposition($service_requisition_id){
        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        return $transservicedisposition;
    }

    public function getSectionName1($section_id){
        $section = CoreSection::where('data_state', 0)
        ->where('section_id', $section_id)
        ->first();

        return $section['section_name'];
    }

    public function getSectionName($service_requisition_id){
        $servicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first();

        if($servicedisposition){
            $section = CoreSection::where('data_state', 0)
            ->where('section_id', $servicedisposition['section_id'])
            ->first();
        }else{
            $section = CoreSection::where('data_state', 0)
            ->where('section_id', 1)
            ->first();
        }
        
        return $section['section_name'];
    }
    
    public function export($service_disposition_id){
        $transservicedisposition = TransServiceDisposition::findOrFail($service_disposition_id);

        $coreservice = CoreService::where('service_id', $transservicedisposition['service_id'])
        ->where('data_state', 0)
        ->first();

        $transservicedispositionparameter = TransServiceDispositionParameter::select('trans_service_disposition_parameter.*', 'core_service_parameter.service_parameter_description')
        ->where('service_disposition_id', $transservicedisposition['service_disposition_id'])
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_disposition_parameter.service_parameter_id')
        ->where('trans_service_disposition_parameter.data_state', 0)
        ->get();

        $transservicedispositionterm = TransServiceDispositionTerm::select('trans_service_disposition_term.*', 'core_service_term.service_term_description')
        ->where('service_disposition_id', $transservicedisposition['service_disposition_id'])
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_disposition_term.service_term_id')
        ->where('trans_service_disposition_term.data_state', 0)
        ->get();

        $username = User::select('name')->where('user_id','=',Auth::id())->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.PrintService.export',$username['name'],'Export');


        // create new PDF document
        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(6, 6, 6, 6); // put space of 10 on top

        // set image scale factor
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf::SetFont('helvetica', 'B', 20);

        // add a page
        $pdf::AddPage();

        /*$pdf::Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

        $pdf::SetFont('helvetica', '', 10);
        // set image scale factor

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // -----------------------------------------------------------------------------

        

        $export = "
        <div style=\"text-align:center;\">
            <a style=\"color: green; text-decoration: none; font-size: 25px\">BAZNAS</a>
            <br/>
            <a style=\"color: black; text-decoration: none;\">Badan Amil Zakat Nasional</a>
            <br/>
            <a style=\"color: green; text-decoration: none; font-size: 15px\">KABUPATEN SRAGEN</a>
        </div>
        <hr style=\"width:100%; text-align:left !important; \"></hr>
        <br></br>
        <div style=\"text-align:center;\">
            <a style=\"color: black; text-decoration: none; font-size: 15px\">FORMULIR LAYANAN</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\">".Str::upper($coreservice['service_name'])."</a>
        </div>
        <br/>
        <br/>
        <br/>
        ";

        $export2 = "";
        $no = 1;
        foreach($transservicedispositionparameter as $key => $val){
            $export2 .= "
                <a style=\"color: black; text-decoration: none;\">".$no.". ".$val['service_parameter_description']." : </a>
                <a style=\"color: #343633; text-decoration: none;\">".$val['service_disposition_parameter_value']."</a>
                <br/>
                <br/>
                <br/>
            ";
            $no++;
        }
        $export3 ="
        <br pagebreak=\"true\"/>
        <div style=\"text-align:center;\">
            <a style=\"color: black; text-decoration: none; font-size: 15px\">SYARAT DAN KETENTUAN</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\">".Str::upper($coreservice['service_name'])."</a>
        </div>
        <br/>
        <br/>
        <br/>
        ";

        $export4 = "";
        $no = 1;
        foreach($transservicedispositionterm as $key => $val){
            $export4 .= "
                <a style=\"color: black; text-decoration: none;\">".$no.". ".$val['service_term_description']."</a>
            ";
            
            if($val['service_disposition_term_status']==1){
                $export4 .= "
                <a style=\"color: green; text-decoration: none;\">(v)</a>
                <br/>
                ";
            }else{
                $export4 .= "
                <a style=\"color: red; text-decoration: none;\">(x)</a>
                <br/>
                ";
            }
            $no++;
        }

        $pdf::writeHTML($export.$export2.$export3.$export4, true, false, false, false, '');

        ob_clean();
        // -----------------------------------------------------------------------------
        
        //Close and output PDF document
        $filename = 'Disposisi Bantuan_'.$transservicedisposition['service_disposition_id'].'.pdf';
        $pdf::Output($filename, 'I');

        //============================================================+
        // END OF FILE
        //============================================================+
    }
    public function exportRecap()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date     = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date       = date('Y-m-d');
        }else{
            $end_date       = Session::get('end_date');
        }

        if(!Session::get('section_id')){
            $section_id      = '';
        }else{
            $section_id      = Session::get('section_id');
        }

        if(!Session::get('service_id')){
            $service_id      = '';
        }else{
            $service_id      = Session::get('service_id');
        }

        $coreservice = CoreService::where('data_state', 0)->get();
        $coresection = CoreSection::where('data_state', 0)->get();
        
        $transservicedisposition = TransServiceDisposition::where('data_state','=',0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$end_date)
        ->where('approved_status', 1)
        ->where('review_status', 1);
        if($section_id||$section_id!=null||$section_id!=''){
            $transservicedisposition   = $transservicedisposition->where('section_id', $section_id);
        }
        if($service_id||$service_id!=null||$service_id!=''){
            $transservicedisposition   = $transservicedisposition->where('service_id', $service_id);
        }
        $transservicedisposition   = $transservicedisposition->get();
        
        $spreadsheet = new Spreadsheet();

        if(count($transservicedisposition)>=0){
            $spreadsheet->getProperties()->setCreator("SMArT BAZNAS SRAGEN")
                                         ->setLastModifiedBy("SMArT BAZNAS SRAGEN")
                                         ->setTitle("Rekap Layanan")
                                         ->setSubject("")
                                         ->setDescription("Rekap Layanan")
                                         ->setKeywords("Rekap Layanan")
                                         ->setCategory("Rekap Layanan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Rekap Layanan");
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(60);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(60);
    
            $spreadsheet->getActiveSheet()->mergeCells("B2:F2");
            $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B4:F4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B4:F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('H6'.':I6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('H6:I6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            
            $sheet->setCellValue('B2',"Rekap Layanan Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B4',"No");
            $sheet->setCellValue('C4',"Tanggal");
            $sheet->setCellValue('D4',"Nama Pemohon");
            $sheet->setCellValue('E4',"Nama Layanan");
            $sheet->setCellValue('F4',"Bagian");
            $sheet->setCellValue('H6',"Nama Layanan");
            $sheet->setCellValue('I6',"Jumlah");
            
            
            $j=5;
            $no=0;
            
            foreach($transservicedisposition as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Rekap Layanan");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                    // $spreadsheet->getActiveSheet()->getStyle('H'.$j.':I'.$j)->getNumberFormat()->setFormatCode('0.00');
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    // $spreadsheet->getActiveSheet()->getStyle('C'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    // $spreadsheet->getActiveSheet()->getStyle('D'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    // $spreadsheet->getActiveSheet()->getStyle('E'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    // $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    // $spreadsheet->getActiveSheet()->getStyle('G'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                    // $spreadsheet->getActiveSheet()->getStyle('H'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                    // $spreadsheet->getActiveSheet()->getStyle('I'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);


                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $val['created_at']);
                        $sheet->setCellValue('D'.$j, $val['service_requisition_name']);
                        $sheet->setCellValue('E'.$j, $this->getServiceName($val['service_id']));
                        $sheet->setCellValue('F'.$j, $this->getSectionName($val['section_id']));
                        
                        
                    
                }else{
                    continue;
                }
                $j++;
        
            }
            
            $i = 7;
            $total = 0;
            foreach($coreservice as $key=>$val){
                $servicedispositioncount = TransServiceDisposition::where('data_state', 0)
                ->where('service_id', $val['service_id'])
                ->count();

                if(is_numeric($key)){
                    $spreadsheet->getActiveSheet()->getStyle('H'.$i.':H'.$i)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $no++;
                    $sheet->setCellValue('H'.$i, $val['service_name']);
                    $sheet->setCellValue('I'.$i, $servicedispositioncount);
                        
                        
                    
                }else{
                    continue;
                }
                $i++;
                $total += $servicedispositioncount;
        
            }
            $spreadsheet->getActiveSheet()->getStyle('H'.($i).':I'.($i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H'.($i), "Total");
            $sheet->setCellValue('I'.($i), $total);
            
            $spreadsheet->getActiveSheet()->getStyle('H'.($i+3).':I'.($i+3))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('H'.($i+3).':I'.($i+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H'.($i+3),"Nama Bagian");
            $sheet->setCellValue('I'.($i+3),"Jumlah");
            $k = $i+4;
            foreach($coresection as $key=>$val){
                $sectiondispositioncount = TransServiceDisposition::where('data_state', 0)
                ->where('section_id', $val['section_id'])
                ->count();

                if(is_numeric($key)){
                    $spreadsheet->getActiveSheet()->getStyle('H'.$k.':I'.$k)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $no++;
                    $sheet->setCellValue('H'.$k, $val['section_name']);
                    $sheet->setCellValue('I'.$k, $sectiondispositioncount);
                        
                        
                    
                }else{
                    continue;
                }
                $k++;
        
            }
            $spreadsheet->getActiveSheet()->getStyle('H'.($k).':I'.($k))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H'.($k), "Total");
            $sheet->setCellValue('I'.($k), $total);
               
            ob_clean();
            $filename='Rekap_Layanan_'.$start_date.'_s.d._'.$end_date.'.xls';
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
