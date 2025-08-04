<?php

if (!function_exists('format_date')) {
    function format_date($date, $format = 'd.m.Y')
    {
        return \Carbon\Carbon::parse($date)->format($format);
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
}
if (!function_exists('get_month_name')) {
    function get_month_name($monthNumber)
    {
        $months = [
            1 => 'Yanvar', 2 => 'Fevral', 3 => 'Mart', 4 => 'Aprel',
            5 => 'May', 6 => 'Iyun', 7 => 'Iyul', 8 => 'Avgust',
            9 => 'Sentyabr', 10 => 'Oktyabr', 11 => 'Noyabr', 12 => 'Dekabr'
        ];

        return $months[(int)$monthNumber] ?? null;
    }
}
