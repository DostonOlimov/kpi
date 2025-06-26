<?php
namespace App\Http\Controllers;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SessionController extends Controller
{
    public function changeYear(Request $request): JsonResponse
    {
        $year = $request->input('year');

        session(['year'=>$year]);
        return response()->json(['success' => true]);
    }

    public function changeMonth(Request $request): JsonResponse
    {
        $month = $request->input('month');

        session(['month'=>$month]);
        return response()->json(['success' => true]);
    }
}
