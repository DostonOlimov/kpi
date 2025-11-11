<?php

namespace App\Exports;

use App\Models\EmployeeDays;
use App\Models\Month;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class SummaExport implements FromCollection,WithHeadings,WithStyles
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings():array{
        return[
            ['Xodimlarning baholash tizimi orqali ustamalarni belgilangan qiymat asosida taqsimlangan jadvali'],
            [
            'Ismi',
            'Familyasi',
            'Otasining ismi',
            'Lavozimi',
            'Bo\'lim',
            'Oy nomi',
            'Oylik maoshi',
            'Ish kunlari soni',
            'Koefitsent',
            'To\'plangan ball',
            'Ustama foizi',
            'Ustama',
            'Ijtimoiy soliq',
            'Jami summa',
            'Qoldiqqa nisbatan',
            '%',
            'Qoldiqqa nisbatan ustama',
            'Qoldiqqa nistbatan soliq',
            'Jami'
            ]

        ];
    }
    public function collection()
    {

        $GLOBALS['month'] = session('month') ?: (int)date('m');
       $data = DB::table('employees_summa')
           ->join('users', 'users.id', '=', 'employees_summa.user_id')
           ->join('work_zones', 'users.work_zone_id', '=', 'work_zones.id')
           ->leftJoin('employee_days', function ($join) {
               $join->on('employee_days.user_id', '=', 'employees_summa.user_id')
                ->where('employee_days.month_id', '=', $GLOBALS['month']);
           })
            ->where('employees_summa.month', '=', $GLOBALS['month'])
           ->select([
               'users.first_name',
               'users.last_name',
               'users.father_name',
               'users.lavozimi',
               'work_zones.name as yunalishi',
               'employee_days.month_id',
               'users.salary',
               'employee_days.days as ish_kuni',
               'employees_summa.rating',
               'employees_summa.current_ball',
               'employees_summa.ustama',
               'employees_summa.created_at',
               'employees_summa.total_summa',
               'employees_summa.active_summa',
               'employees_summa.foiz',
               'employees_summa.new_ustama',
               'employees_summa.updated_at',
               'employees_summa.new_total',


           ])
           ->get();

       foreach ( $data as $key => $item)
       {
           $item->month_id =  get_month_name( $GLOBALS['month']);
           $item->created_at = 0.25 * $item->ustama;
           $item->updated_at = 0.25 * $item->new_ustama;
       }
    return $data;
//       echo "<pre>";print_r($data);die();
    }
     public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('A1:S1');
        $sheet->getStyle('A1:S2')->getFont()->setBold(true);
        $sheet->getStyle('A1:C100')->getAlignment()->setHorizontal('center');
        $sheet->getStyle('F3:S100')->getAlignment()->setHorizontal('center');
        $sheet->getRowDimension(1)->setRowHeight(70);
        $sheet->getRowDimension(2)->setRowHeight(50);

        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(60);
        $sheet->getColumnDimension('F')->setWidth(15);
        $sheet->getColumnDimension('G')->setWidth(15);
        $sheet->getColumnDimension('H')->setWidth(15);
        $sheet->getColumnDimension('I')->setWidth(10);
        $sheet->getColumnDimension('J')->setWidth(10);
        $sheet->getColumnDimension('K')->setWidth(30);
        $sheet->getColumnDimension('L')->setWidth(30);
        $sheet->getColumnDimension('M')->setWidth(30);
        $sheet->getColumnDimension('N')->setWidth(30);
        $sheet->getColumnDimension('O')->setWidth(30);
        $sheet->getColumnDimension('P')->setWidth(30);
        $sheet->getColumnDimension('Q')->setWidth(20);
        $sheet->getColumnDimension('R')->setWidth(30);
        $sheet->getColumnDimension('S')->setWidth(30);
        $sheet->getDefaultColumnDimension()->setAutoSize(false);
    }
}
