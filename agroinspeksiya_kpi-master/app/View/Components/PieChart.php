<?php

namespace App\View\Components;

use App\Models\Director;
use App\Models\Month;
use App\Models\Razdel;
use Illuminate\View\Component;

class PieChart extends Component
{
    public $data ;


    public function __construct( $data1)
    {

        $this->data = $data1;
    }


    public function render()
    {
        return view('components.pie_chart',
            [
                'data' => $this->data,
            ]);
    }
}
