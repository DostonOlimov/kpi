<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{

    public function index()
    {
        $user = auth()->user();
        if ($user->role_id == 1){
            return redirect(route('employees.list'));
        } elseif ($user->role_id == 4){
            return redirect(route('commission.list'));
        } elseif ($user->role_id == 3) {
            return redirect(route('profile.list'));
        } elseif ($user->role_id == 2) {
            return redirect(route('director.list'));
        } elseif ($user->role_id == 6) {
            return redirect(route('bugalter.list'));
        } elseif ($user->role_id == 7) {
            return redirect(route('commission.section'));
        }

    }

}
