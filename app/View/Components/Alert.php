<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $message;

    public function __construct()
    {

    }


    public function render()
    {
        return view('components.alert');
    }
}
