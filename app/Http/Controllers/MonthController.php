<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Month;

class MonthController extends Controller
{
      /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $month = Month::getMonth();
        $roles = Month::orderBy('id','asc')->paginate(10);
        return view('month.index', compact('roles','month'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        $month = Month::getMonth();
        return view('month.create',[
            'months' => $month,
        ]);
    }

    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        $request->validate([
           // 'month_id' => 'required',
            'month_id' => ['required', 'integer', 'unique:months'],
            'days' => 'integer'
        ]);
        
        Month::create($request->post());

        return redirect()->route('month.index')->with('success','Oy ish kuni muvaffaqatli yaratildi.');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\company  $company
    * @return \Illuminate\Http\Response
    */
    public function show(Month $company)
    {
        return view('month.show',compact('company'));
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Month  $month
    * @return \Illuminate\Http\Response
    */
    public function edit(Month $month)
    {
        return view('month.edit',compact('month'));
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Month  $month
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Month $month)
    {
        $request->validate([
            'month_id' => 'required',
            'days' => 'integer'
        ]);
        
        $month->fill($request->post())->save();

        return redirect()->route('month.index')->with('success','Ma\'lumotlar muvaffaqiyatli o\'zgartirildi');
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Month  $month
    * @return \Illuminate\Http\Response
    */
    public function destroy(Month $month)
    {
        $month->delete();
        return redirect()->route('month.index')->with('success','Ma\'lumotlar o\'chirildi');
    }
}
