<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\UserKpi;
use App\Models\User;
use App\Models\Month;
use App\Models\WorkZone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function index()
    {
        $kpis = Kpi::whereNull('parent_id')->with('children')->where('type','!=',Kpi::SELF_BY_PERSON)->paginate(10);
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
        $hasScore = $kpi->user_kpis()->exists();


        return redirect()->route('kpis.index')->with('error', 'KPI ni o\'zgartirib bo\'lmaydi, chunki unga bog\'liq vazifalar mavjud.');

//        $request->validate([
//            'name' => 'required',
//            'max_score' => 'nullable|integer',
//            'parent_id' => 'nullable|exists:kpis,id',
//        ]);
//
//        $kpi->update($request->all());
//        return redirect()->route('kpis.index')->with('success', 'KPI muvaffaqiyatli o\'zgartirildi.');
    }

    public function destroy(Kpi $kpi)
    {
        return redirect()->route('kpis.index')->with('error', 'KPI ni o\'chirish mumkin emas, chunki unga bog\'liq vazifalar, ballar yoki osti kpilar mavjud.');
//        $hasScore = $kpi->user_kpis()->exists();
//        $hasChild = $kpi->children()->exists();

//        if ($hasScore || $hasChild) {
//            return redirect()->route('kpis.index')->with('error', 'KPI ni o\'chirish mumkin emas, chunki unga bog\'liq vazifalar, ballar yoki osti kpilar mavjud.');
//        }
//
//        $kpi->delete();
//        return redirect()->route('kpis.index')->with('success', 'KPI muvaffaqiyatli o\'chirildi.');
    }

    /**
     * Display user's KPIs filtered by month and year.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function userKpis(Request $request)
    {
        $month = $this->month;
        $year = $this->year;
        
        $userKpis = UserKpi::with(['kpi', 'score'])
            ->where('user_id', Auth::id())
            ->where('month', $month)
            ->where('year', $year)
            ->get();
        
        $months = Month::getMonth();
        $years = range(date('Y') - 2, date('Y') + 1);
        
        return view('user-kpis.my-kpis', compact('userKpis', 'month', 'year', 'months', 'years'));
    }

    /**
     * Display all users with their total KPI scores.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function usersKpiDashboard(Request $request)
    {
        $month = $this->month;
        $year = $this->year;
        $workZoneId = (int) $request->input('work_zone_id', get_default_parent_work_zone_id());
        $childWorkZoneId = $request->input('child_work_zone_id');
        
        // Build query
        $query = User::with(['work_zone', 'role'])
            ->whereHas('user_kpis', function($query) use ($month, $year) {
                $query->where('month', $month)
                      ->where('year', $year);
            });
        
        // Filter by department if user is not admin
        if (auth()->user()->role_id == User::ROLE_DIRECTOR) {
            $query->where('work_zone_id', auth()->user()->work_zone_id)
              ->where('role_id', User::ROLE_USER);
        }
        
        // Apply work zone filters
        if ($childWorkZoneId) {
            $query->where('work_zone_id', $childWorkZoneId);
        } elseif ($workZoneId) {
            // Get all child work zones of the selected parent
            $childIds = WorkZone::where('parent_id', $workZoneId)->pluck('id');
            $query->whereIn('work_zone_id', $childIds);
        }
        
        $users = $query
            ->withCount(['user_kpis as total_kpis' => function($query) use ($month, $year) {
                $query->where('month', $month)
                      ->where('year', $year);
            }])
            ->withSum(['user_kpis as total_target_score' => function($query) use ($month, $year) {
                $query->where('month', $month)
                      ->where('year', $year);
            }], 'target_score')
            ->withSum(['user_kpis as total_current_score' => function($query) use ($month, $year) {
                $query->where('month', $month)
                      ->where('year', $year);
            }], 'current_score')
            ->get();
        
        return view('user-kpis.dashboard', compact(
            'users',
            'workZoneId',
            'childWorkZoneId',
            'month',
            'year',
            // 'defaultParentWorkZoneId'
        ));
    }

    /**
     * Display specific user's KPIs by month and year.
     *
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Contracts\View\View
     */
    public function userKpisDetail($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        
        // Get parent KPIs (categories)
        $parentKpis = Kpi::whereNull('parent_id')
            ->where('type', '!=', Kpi::PERMANENT)
            ->orderBy('sort')
            ->get();
        
        // Get user KPIs with their parent KPIs
        $userKpis = UserKpi::with(['kpi.parent', 'score'])
            ->where('user_id', $userId)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->get();
        
        // Group user KPIs by parent KPI
        $groupedKpis = $userKpis->groupBy(function($userKpi) {
            return $userKpi->kpi->parent_id ?? $userKpi->kpi->id;
        });
        
        return view('user-kpis.user-detail', compact('user', 'parentKpis', 'groupedKpis', 'userKpis'));
    }

    /**
     * Refresh/initialize KPIs for a user.
     *
     * @param int $userId
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function refreshUserKpis($userId, Request $request)
    {
        $user = User::findOrFail($userId);
        $month = $this->month;
        $year = $this->year;
        
        // Get KPIs that need to be created
        $kpis = Kpi::whereNotNull('parent_id')
            ->whereIn('type', [
                Kpi::ACTIVITY,
                Kpi::BEHAVIOUR,
                Kpi::IJRO,
                Kpi::PERMANENT
            ])->get();
        
        // Create UserKpi records if they don't exist
        foreach ($kpis as $kpi) {
            UserKpi::firstOrCreate([
                'user_id' => $user->id,
                'kpi_id' => $kpi->id,
                'month' => $month,
                'year' => $year,
            ], [
                'target_score' => $kpi->max_score,
            ]);
        }
        
        return redirect()->back()->with('message', 'KPI lar muvaffaqiyatli yangilandi!');
    }
}
