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

        $kpi = Kpi::create(
            [
                'name' => $request->input('name'),
                'max_score' => $request->input('max_score'),
                'user_id' => $request->input('user_id'),
                'parent_id' => 13,
            ]
        );

        return redirect()->to(
            route('working-kpis.show', $kpi->id) . '?user_id=' . $request->input('user_id')
        )->with('success', 'KPI muvaffaqiyatli yaratildi.');
    }

    public function edit(Request $request,Kpi $working_kpi)
    {
        $kpi = $working_kpi;
        $user = User::findOrFail($working_kpi->user_id);

        return view('working_kpis.edit', compact('kpi', 'user'));
    }

    public function update(Request $request, Kpi $working_kpi)
    {
        // Check if KPI has any related scores or tasks
        $hasScore = $working_kpi->user_kpis()->exists();

        if ($hasScore) {
            return  redirect()->to(
                route('working-kpis.show', $working_kpi->id) . '?user_id=' . $working_kpi->user_id
            )->with('error', 'Ko\'rsatkichni o\'zgartirib bo\'lmaydi, chunki unga bog\'liq vazifalar mavjud.');
        }

        $request->validate([
            'name' => 'required',
        ]);

        $working_kpi->update($request->all());

        return redirect()->to(
            route('working-kpis.show', $working_kpi->id) . '?user_id=' . $working_kpi->user_id
        )->with('success', 'Ko\'rsatkich muvaffaqiyatli o\'zgartirildi.');
    }

    public function destroy(Kpi $working_kpi)
    {
        $hasScore = $working_kpi->user_kpis()->exists();
        $hasChild = $working_kpi->children()->exists();

        if ($hasScore || $hasChild) {
            return  redirect()->to(
                route('working-kpis.show', $working_kpi->id) . '?user_id=' . $working_kpi->user_id
            )->with('error', 'Ushbu ko\'rsatkichni o\'zgartirib bo\'lmaydi, chunki unga bog\'liq vazifalar mavjud.');
        }

        $working_kpi->delete();
        return redirect()->to(
            route('working-kpis.show', $working_kpi->id) . '?user_id=' . $working_kpi->user_id
        )->with('success',  'Ko\'rsatkich muvaffaqiyatli o\'chirildi.');
    }

    public function show(Kpi $kpi,Request $request)
    {
        $user_id = $request->get('user_id');
        $user = User::findOrFail($user_id);

        $userKpis = Kpi::where('user_id', $user_id)
            ->whereNotNull('parent_id')
            ->get();

        return view('working_kpis.show', compact('user', 'userKpis'));
    }

    public function getUserKPIs($userId)
    {
        $userKpis = Kpi::where('user_id', $userId)
            ->whereNotNull('parent_id')
            ->get();

        return response()->json($userKpis);
    }
}
