<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\User;
use App\Models\UserKpi;
use Illuminate\Http\Request;

class WorkingKpiController extends Controller
{
    public function index()
    {
        $query = User::with('working_kpis')
            ->where('role_id','!=',User::ROLE_ADMIN)
            ->where('id', '!=', auth()->id());

        // Filter by department if user is not admin
        if (auth()->user()->role_id == User::ROLE_DIRECTOR) {
            $query->where('work_zone_id', auth()->user()->work_zone_id)
                ->where('role_id', User::ROLE_USER);
        }

        $users = $query->paginate(20);

        return view('working_kpis.index', compact('users'));
    }

    public function create(Request $request)
    {
        $user = User::findOrFail($request->input('user_id'));
        return view('working_kpis.create', compact('user'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'max_score' => 'required|integer|min:0',
        ]);

        Kpi::create(
            [
                'name' => $request->input('name'),
                'max_score' => $request->input('max_score'),
                'user_id' => $request->input('user_id'),
                'parent_id' => 13,
            ]
        );

        return redirect()->route('working-kpis.index')->with('success', 'KPI muvaffaqiyatli yaratildi.');
    }

    public function edit(Request $request)
    {
        $kpi = Kpi::findOrFail($request->input('kpi_id'));
        $user = User::findOrFail($request->input('user_id'));

        return view('working_kpis.edit', compact('kpi', 'user'));
    }

    public function update(Request $request, Kpi $kpi)
    {
        // Check if KPI has any related scores or tasks
//        $hasScore = $kpi->user_kpis()->exists();
//        $hasTask = $kpi->tasks()->exists();
//
//        if ($hasScore || $hasTask) {
//            return redirect()->route('working_kpis.index')->with('error', 'KPI ni o\'zgartirib bo\'lmaydi, chunki unga bog\'liq vazifalar mavjud.');
//        }

        $request->validate([
            'name' => 'required',
        ]);

        $kpi->update($request->all());
        return redirect()->route('working-kpis.index')->with('success', 'KPI muvaffaqiyatli o\'zgartirildi.');
    }

    public function destroy(Kpi $kpi)
    {
        $hasScore = $kpi->kpi_scores()->exists();
        $hasTask = $kpi->tasks()->exists();
        $hasChild = $kpi->children()->exists();

        if ($hasScore || $hasTask || $hasChild) {
            return redirect()->route('working_kpis.index')->with('error', 'KPI ni o\'chirish mumkin emas, chunki unga bog\'liq vazifalar, ballar yoki osti kpilar mavjud.');
        }

        $kpi->delete();
        return redirect()->route('working_kpis.index')->with('success', 'KPI muvaffaqiyatli o\'chirildi.');
    }

    public function getUserKPIs($userId)
    {
        $userKpis = Kpi::where('user_id', $userId)
            ->get();

        return response()->json($userKpis);
    }
}
