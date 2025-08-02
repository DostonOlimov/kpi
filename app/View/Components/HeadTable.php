<?php

namespace App\View\Components;

use App\Models\Month;
use Illuminate\View\Component;

class HeadTable extends Component
{

    public function __construct()
    {
    }


    public function render()
    {

        return view('components.head_table');
    }
}
