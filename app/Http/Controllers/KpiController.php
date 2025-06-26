<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use Illuminate\Http\Request;

class KpiController extends Controller
{
    public function index()
    {
        $kpis = Kpi::whereNull('parent_id')->with('children')->get();
        return view('kpis.index', compact('kpis'));
    }

    public function create()
    {
        $categories = Kpi::whereNull('parent_id')->get();
        return view('kpis.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'max_score' => 'nullable|integer',
            'parent_id' => 'nullable|exists:kpis,id',
        ]);

        Kpi::create($request->all());
        return redirect()->route('kpis.index')->with('success', 'KPI saved.');
    }

    public function edit(Kpi $kpi)
    {
        $categories = Kpi::whereNull('parent_id')->get();
        return view('kpis.edit', compact('kpi', 'categories'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        $request->validate([
            'name' => 'required',
            'max_score' => 'nullable|integer',
            'parent_id' => 'nullable|exists:kpis,id',
        ]);

        $kpi->update($request->all());
        return redirect()->route('kpis.index')->with('success', 'KPI updated.');
    }

    public function destroy(Kpi $kpi)
    {
     //   $kpi->delete();
        return redirect()->route('kpis.index')->with('success', 'KPI deleted.');
    }
}
