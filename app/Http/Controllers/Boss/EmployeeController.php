<?php
namespace App\Http\Controllers\Boss;

use App\Http\Controllers\Controller;
use App\Models\RelevantUser;
use App\Models\User;

class EmployeeController extends Controller
{
    
    public function index()
    {
        $employees = RelevantUser::where('boss_id', auth()->id())->with('user')->get();
        return view('boss.employees.index', compact('employees'));
    }

    
    public function show(User $user)
    {
        
        $kpis = $user->user_kpis ?? [];
        
        return view('boss.employees.show', compact('user', 'kpis'));
    }
}