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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_score' => 'nullable|integer|min:0',
            'parent_id' => 'nullable|exists:kpis,id',
        ]);

        Kpi::create($validated);

        return redirect()->route('kpis.index')->with('success', 'KPI muvaffaqiyatli yaratildi.');
    }

    public function edit(Kpi $kpi)
    {
        $categories = Kpi::whereNull('parent_id')->get();
        return view('kpis.edit', compact('kpi', 'categories'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        // Check if KPI has any related scores or tasks
        $hasScore = $kpi->kpi_scores()->exists();
        $hasTask = $kpi->tasks()->exists();

        if ($hasScore || $hasTask) {
            return redirect()->route('kpis.index')->with('error', 'KPI ni o\'zgartirib bo\'lmaydi, chunki unga bog\'liq vazifalar mavjud.');
        }

        $request->validate([
            'name' => 'required',
            'max_score' => 'nullable|integer',
            'parent_id' => 'nullable|exists:kpis,id',
        ]);

        $kpi->update($request->all());
        return redirect()->route('kpis.index')->with('success', 'KPI muvaffaqiyatli o\'zgartirildi.');
    }

    public function destroy(Kpi $kpi)
    {
        $hasScore = $kpi->kpi_scores()->exists();
        $hasTask = $kpi->tasks()->exists();
        $hasChild = $kpi->children()->exists();

        if ($hasScore || $hasTask || $hasChild) {
            return redirect()->route('kpis.index')->with('error', 'KPI ni o\'chirish mumkin emas, chunki unga bog\'liq vazifalar, ballar yoki osti kpilar mavjud.');
        }

        $kpi->delete();
        return redirect()->route('kpis.index')->with('success', 'KPI muvaffaqiyatli o\'chirildi.');
    }
}
