<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkZone;

class WorkController extends Controller
{
      /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        $works = WorkZone::orderBy('id','asc')->paginate(10);
        return view('works.index', compact('works'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
        return view('works.create');
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
            'name' => 'required',
        ]);
        
        WorkZone::create($request->post());

        return redirect()->route('works.index')->with('success','Ish joyi muvaffaqatli yaratildi.');
    }

    /**
    * Display the specified resource.
    *
    * @param  \App\company  $company
    * @return \Illuminate\Http\Response
    */
    public function show(WorkZone $work)
    {
        return view('works.show',compact('work'));
    }

    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Company  $company
    * @return \Illuminate\Http\Response
    */
    public function edit(WorkZone $work)
    {
        return view('works.edit',compact('work'));
    }

    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\company  $company
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, WorkZone $work)
    {
        $request->validate([
            'name' => 'required',
        ]);
        
        $work->fill($request->post())->save();

        return redirect()->route('works.index')->with('success','Ma\'lumotlar muvaffaqiyatli o\'zgartirildi');
    }

    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Company  $company
    * @return \Illuminate\Http\Response
    */
    public function destroy(WorkZone $work)
    {
        $work->delete();
        return redirect()->route('works.index')->with('success','Ma\'lumotlar muvaffaqiyatli o\'chirildi');
    }
}
