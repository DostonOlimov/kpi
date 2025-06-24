<?php

namespace App\View\Components;

use App\Models\Month;
use Illuminate\View\Component;

class SelectMonth extends Component
{
    public $url;

    public function __construct($url)
    {
    $this->url = $url;
    }


    public function render()
    {
        $months = Month::getMonth();
        $years = [2023,2022];
        return view('components.select-month',compact('months','years'));
    }
}
