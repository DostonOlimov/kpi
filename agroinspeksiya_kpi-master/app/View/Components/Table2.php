<?php

namespace App\View\Components;

use App\Models\Director;
use App\Models\Month;
use App\Models\Razdel;
use Illuminate\View\Component;

class Table2 extends Component
{
    public $data1 ;
    public $data2;
    public $data3;
    public $data4;

    public function __construct( $data1,$data2,$data3,$data4)
    {

        $this->data1 = $data1;
        $this->data2 = $data2;
        $this->data3 = $data3;
        $this->data4 = $data4;
    }


    public function render()
    {
        $razdel = Razdel::all();
        return view('components.table2',
            [
            'razdel' => $razdel,
            'data1' => $this->data1,
            'data2' => $this->data2,
            'data3' => $this->data3,
            'data4' => $this->data4,
        ]);
    }
}
