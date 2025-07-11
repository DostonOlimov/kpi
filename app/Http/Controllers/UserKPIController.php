<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\User;
use App\Models\UserKpi;
use Illuminate\Http\Request;

class UserKPIController extends Controller
{
    public function index()
    {
        $query = User::where('role_id','!=',User::ROLE_ADMIN)
            ->where('id', '!=', auth()->id());

        // Filter by department if user is not admin
        if (auth()->user()->role_id == User::ROLE_DIRECTOR) {
            $query->where('work_zone_id', auth()->user()->work_zone_id)
              ->where('role_id', User::ROLE_USER);
        }

        $users = $query->get();
        $kpis = KPI::with('children')->whereNull('parent_id')->where('type',Kpi::TYPE_1)->get();

        return view('user-kpis.index', compact('users', 'kpis'));
    }


    public function getUserKPIs($userId)
    {
        $userKpis = UserKpi::with('kpi')
            ->whereHas('kpi', function ($query) {
                $query->where('type', Kpi::TYPE_1)
                      ->whereNotNull('max_score');
            })
            ->where('user_id', $userId)
            ->get();

        return response()->json($userKpis);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'kpi_id' => 'required|exists:kpis,id'
        ]);

        $kpi = KPI::find($request->kpi_id);

        if ($kpi->isCategory()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot assign a category to user. Please select a specific KPI.'
            ], 400);
        }

        // Calculate total assigned max_score for this user
        $totalMaxScore = UserKPI::where('user_id', $request->user_id)
            ->join('kpis', 'user_kpis.kpi_id', '=', 'kpis.id')
            ->sum('kpis.max_score');

        // Check if adding this KPI exceeds 80
        if (($totalMaxScore + $kpi->max_score) > 80) {
            return response()->json([
                'success' => false,
                'message' => 'Maxsimal ball miqdori 80 dan oshmasligi kerak.'
            ], 400);
        }

        $userKpi = UserKPI::updateOrCreate(
            [
                'user_id' => $request->user_id,
                'kpi_id' => $request->kpi_id,
                'month' => session('month') ?? (int)date('m'),
                'year' => session('year') ?? (int)date('Y')
            ],
            [
                'target_score' => $kpi->max_score
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'KPI assigned successfully!',
            'data' => $userKpi->load('kpi')
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'target_score' => 'nullable|integer|min:0',
            'current_score' => 'nullable|integer|min:0'
        ]);

        $userKpi = UserKPI::findOrFail($id);
        $userKpi->update($request->only(['target_score', 'current_score']));

        return response()->json([
            'success' => true,
            'message' => 'KPI updated successfully!',
            'data' => $userKpi->load('kpi')
        ]);
    }

    public function destroy($id)
    {
        $userKpi = UserKPI::findOrFail($id);

        if ($userKpi->current_score || $userKpi->tasks->count() > 0 ) {
            return response()->json([
                'success' => false,
                'message' => 'KPI ni o‘chirish mumkin emas. Unda joriy ball yoki bog‘liq vazifalar mavjud.'
            ], 400);
        }

        $userKpi->delete();

        return response()->json([
            'success' => true,
            'message' => 'KPI o\'chirildi!'
        ]);
    }

    public function getKPIsByCategory($categoryId,Request $request)
    {
        if($categoryId == 13){
            $user_id = $request->get('user_id');
            $kpis = KPI::where('parent_id', $categoryId)
                ->where('user_id', $user_id)
                ->whereNotNull('max_score')
                ->orderBy('parent_id')
                ->get();
        }else{
            $kpis = KPI::where('parent_id', $categoryId)
                ->whereNotNull('max_score')
                ->orderBy('parent_id')
                ->get();
        }
        return response()->json($kpis);
    }
}
