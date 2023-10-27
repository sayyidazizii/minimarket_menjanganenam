<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SystemLoginLog;
use App\Models\User;
use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class AttendanceReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        if(!Session::get('date')){
            $date     = date('Y-m-d');
        }else{
            $date = Session::get('date');
        }

        $user = User::select('name','full_name','user_id')
        ->where('user_id', '!=', 55)
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        // if(!Session::get('date')){
        //     $date     = date('Y-m-d');
        // }else{
        //     $date = Session::get('date');
        // }
        // $data = SystemLoginLog::select('*',DB::raw("SUBSTR(log_time,1,10) AS date"))->orderBy('log_time', 'ASC')
        // ->get();
        // $date = date('d', strtotime('2022-12-08'));
        // for ($i=0; $i < $date; $i++) { 
        //     $data[$i] = SystemLoginLog::where('user_id',55)
        //     ->orderBy('log_time', 'ASC')
        //     ->whereDay('log_time','>=',01)
        //     ->whereDay('log_time','<=',$i)
        //     ->first();
        // }
        // $array = array_filter($data);
        // dd($date);


        

        return view('content.AttendanceReport.ListAttendanceReport',compact('date','user'));
    }

    public function filterAttendanceReport(Request $request)
    {
        $date = $request->date;

        Session::put('date', $date);

        return redirect('/attendance-report');
    }

    public function resetFilterAttendanceReport()
    {
        Session::forget('date');

        return redirect('/attendance-report');
    }

    public function getRemark($user_id)
    {
        if(!Session::get('date')){
            $date     = date('Y-m-d');
        }else{
            $date = Session::get('date');
        }
        $data = SystemLoginLog::select('log_time')
        ->where('user_id',$user_id)
        ->where('log_status', 0)
        ->orderBy('log_time', 'ASC')
        ->whereDate('log_time',$date)
        ->first();

        if(empty($data)){
            return "<b>Belum Hadir</b>";
        }else{
            return "<b>".date('H:i',strtotime($data['log_time']))."</b>";

        }
    }

    public function getAbsensi($user_id)
    {
        if(!Session::get('date')){
            $date = date('Y-m-d');
        }else{
            $date = Session::get('date');
        }
        $data = SystemLoginLog::where('user_id',$user_id)
        ->orderBy('log_time', 'ASC')
        ->whereDate('log_time',$date)
        ->first();

        if(empty($data)){
            return "
            <div class='btn btn-danger btn-sm'><i class='fa fa-times-circle'></i></div>
            ";
        }else{
            return "
            <div class='btn btn-success btn-sm'><i class='fa fa-check-circle'></i></div>
            ";

        }
    }

    public function getAbsensiMonth($user_id)
    {
        // if(!Session::get('date')){
        //     $date     = date('Y-m-d');
        // }else{
        //     $date = Session::get('date');
        // }
        // $day = 
        // $data = SystemLoginLog::where('user_id',$user_id)
        // ->orderBy('log_time', 'ASC')
        // ->whereDay('log_time','>=',01)
        // ->whereDay('log_time','<=',$date)
        // ->whereMonth('log_time',$date)
        // ->whereYear('log_time',$date)
        // ->first();

        // return $data['log_time'];

        if(!Session::get('date')){
            $date       = date('Y-m-d');
        }else{
            $date       = Session::get('date');
        }
        $month          = date('m', strtotime($date));
        $year           = date('Y', strtotime($date));
        $dateformat     = date('Y-m-d', strtotime($date));
        $day            = date('t', strtotime($date));
        $data           = SystemLoginLog::select('*')
            ->where('log_status', 0)
            ->where('user_id',$user_id)
            ->orderBy('log_time', 'ASC')
            ->whereDay('log_time','>=',01)
            ->whereDay('log_time','<=',$day)
            ->whereMonth('log_time',$month)
            ->whereYear('log_time',$year)
            ->groupBy(DB::raw('DATE(log_time)'))
            ->get();

        return count($data);
        
    }

    public function getLetter($number)
    {
        $letter = array(
            1 => 'D',
            2 => 'E',
            3 => 'F',
            4 => 'G',
            5 => 'H',
            6 => 'I',
            7 => 'J',
            8 => 'K',
            9 => 'L',
            10 => 'M',
            11 => 'N',
            12 => 'O',
            13 => 'P',
            14 => 'Q',
            15 => 'R',
            16 => 'S',
            17 => 'T',
            18 => 'U',
            19 => 'V',
            20 => 'W',
            21 => 'X',
            22 => 'Y',
            23 => 'Z',
            24 => 'AA',
            25 => 'AB',
            26 => 'AC',
            27 => 'AD',
            28 => 'AE',
            29 => 'AF',
            30 => 'AG',
            31 => 'AH',
            32 => 'AI',
        );

        return $letter[$number];
    }
    public function getAbsensiDay($user_id, $tgl)
    {
        if(!Session::get('date')){
            $date       = date('Y-m-d');
        }else{
            $date       = Session::get('date');
        }
        $month          = date('m', strtotime($date));
        $year           = date('Y', strtotime($date));
        $dateformat     = date('Y-m-d', strtotime($date));
        $day            = date('d', strtotime($date));

        $data_in = SystemLoginLog::select('log_time')
        ->where('user_id',$user_id)
        ->where('log_status', 0)
        ->orderBy('log_time', 'ASC')
        ->whereDay('log_time',$tgl)
        ->whereMonth('log_time',$month)
        ->whereYear('log_time',$year)
        ->first();

        $data_out = SystemLoginLog::select('log_time')
        ->where('user_id',$user_id)
        ->where('log_status', 1)
        ->orderBy('log_time', 'DESC')
        ->whereDay('log_time',$tgl)
        ->whereMonth('log_time',$month)
        ->whereYear('log_time',$year)
        ->first();

        $login = date('Hi',strtotime($data_in['log_time']));
        $logout = date('Hi',strtotime($data_out['log_time']));
        $diff = $logout - $login;
        

        if(empty($data_in)){
            return "x";
        } else {
            return 'Masuk '.date('H:i', strtotime($data_in['log_time'])).' , '.round($diff/60,1) .' Jam';
        }
    }
    public function exportAttendanceReport()
    {
        if(!Session::get('date')){
            $date     = date('Y-m-d');
        } else {
            $date = Session::get('date');
        }
        $countMonth = date('t', strtotime($date));
        $data = User::select('name','full_name','user_id')
        ->where('user_id', '!=', 55)
        ->where('data_state',0)
        ->where('company_id', Auth::user()->company_id)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($data)>=0){
            $spreadsheet->getProperties()->setCreator("MOZAIC")
                                        ->setLastModifiedBy("MOZAIC")
                                        ->setTitle("Attendance Report")
                                        ->setSubject("")
                                        ->setDescription("Attendance Report")
                                        ->setKeywords("Attendance, Report")
                                        ->setCategory("Attendance Report");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            for ($i=1; $i < $countMonth; $i++) { 
                $spreadsheet->getActiveSheet()->getColumnDimension($this->getLetter($i))->setWidth(25);
            }
            $spreadsheet->getActiveSheet()->getColumnDimension($this->getLetter($i))->setWidth(15);
    
            $spreadsheet->getActiveSheet()->mergeCells('B1:'.$this->getLetter($i).'1');
            $spreadsheet->getActiveSheet()->getStyle('B1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B1')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B3:'.$this->getLetter($i).'3')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B3:'.$this->getLetter($i).'3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('B1',"Laporan Absensi Bulan ".date('F Y',strtotime($date)));	
            $sheet->setCellValue('B3',"No");
            $sheet->setCellValue('C3',"Nama Lengkap");
            for ($i=1; $i < $countMonth ; $i++) { 
                $sheet->setCellValue($this->getLetter($i).'3', $i);
            }
            $sheet->setCellValue($this->getLetter($i).'3', 'Jumlah Masuk');
            $j = 4;
            $no = 1;
            foreach ($data as $key => $val) {
                $sheet = $spreadsheet->getActiveSheet(0);
                $spreadsheet->getActiveSheet()->setTitle("Laporan Absensi");
                $spreadsheet->getActiveSheet()->getStyle('B'.$j.':'.$this->getLetter($i).$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $spreadsheet->getActiveSheet()->getStyle('D'.$j.':'.$this->getLetter($i).$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->setCellValue('B'.$j, $no++);
                $sheet->setCellValue('C'.$j, $val['full_name']);
                for ($i=1; $i < $countMonth ; $i++) { 
                    $sheet->setCellValue($this->getLetter($i).$j, $this->getAbsensiDay($val['user_id'], $i));
                    
                }
                $sheet->setCellValue($this->getLetter($i).$j, $this->getAbsensiMonth($val['user_id']));
                $j++;
            }
            
            
            $filename='Laporan_Absensi.xls';
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
