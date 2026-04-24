<?php

if (!function_exists('get_default_parent_work_zone_id')) {
    function get_default_parent_work_zone_id()
    {
        return auth()->user()->work_zone->parent_id ?? 32;
    }
}

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


if (!function_exists('getDatepicker')) {

    function getDatepicker()
    {

        $dateformate = 'd-m-Y';

        if (!empty($dateformate)) {

            if ($dateformate == 'm-d-Y') {

                $dateformats = "mm-dd-yyyy";

                return $dateformats;

            } elseif ($dateformate == 'Y-m-d') {

                $dateformats = "yyyy-mm-dd";

                return $dateformats;

            } elseif ($dateformate == 'd-m-Y') {

                $dateformats = "dd-mm-yyyy";

                return $dateformats;

            } elseif ($dateformate == 'M-d-Y') {

                $dateformats = "MM-dd-yyyy";

                return $dateformats;

            }


        }

    }

}

