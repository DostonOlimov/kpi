<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Month;

class MonthController extends Controller
{
      /**
    * Display a listing of the resource.
    *
    * @return Application|Factory|View
       */
    public function index()
    {
        $month = Month::getMonth();
        $roles = Month::orderBy('id','asc')->get();
        return view('month.index', compact('roles','month'));
    }

    /**
    * Show the form for creating a new resource.
    *
    * @return Application|Factory|View
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
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
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
     * @param Month $company
     * @return Application|Factory|View
     */
    public function show(Month $company)
    {
        return view('month.show',compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Month $month
     * @return Application|Factory|View
     */
    public function edit(Month $month)
    {
        return view('month.edit',compact('month'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param Month $month
     * @return RedirectResponse
     */
    public function update(Request $request, Month $month): RedirectResponse
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
     * @param Month $month
     * @return RedirectResponse
     */
    public function destroy(Month $month): RedirectResponse
    {
       // $month->delete();
        return redirect()->route('month.index')->with('error','Ma\'lumotlar o\'chirib bo\'lmadi');
    }
}
