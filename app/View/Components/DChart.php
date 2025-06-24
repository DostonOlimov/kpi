<?php

namespace App\View\Components;

use App\Models\Director;
use App\Models\Month;
use App\Models\Razdel;
use Illuminate\View\Component;

class DChart extends Component
{
    public $chart_data ;
    public $labels;


    public function __construct( $data1,$data2)
    {

        $this->chart_data = $data1;
        $this->labels = $data2;
    }


    public function render()
    {
        return view('components.d_chart',
            [
                'chart_data' => $this->chart_data,
                'labels' => $this->labels,
            ]);
    }
}
