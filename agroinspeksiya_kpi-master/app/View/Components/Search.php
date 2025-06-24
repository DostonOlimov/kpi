<?php

namespace App\View\Components;

use App\Models\Month;
use Illuminate\View\Component;

class Search extends Component
{
    public $url;

    public function __construct($url)
    {
    $this->url = $url;
    }


    public function render()
    {
        $months = Month::getMonth();
        $years = ['2022','2023'];
        return view('components.search',compact('months','years'));
    }
}
