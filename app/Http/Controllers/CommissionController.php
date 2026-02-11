<?php

namespace App\Http\Controllers;

use App\Models\KpiCriteria;
use App\Models\KpiCriteriaScore;
use App\Models\Month;
use App\Models\Score;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\Kpi;
use App\Models\Task;
use App\Models\UserKpi;

class CommissionController extends Controller
{
    public function dashboard()
    {
        $kpis = KPI::with(['children.tasks.user', 'children.tasks.comments.user'])
            ->whereNull('parent_id')
            ->get();

        $totalTasks = Task::count();
        $pendingReviews = Task::whereDoesntHave('comments')->count();
        $reviewedTasks = Task::whereHas('comments')->count();
        $scoredKPIs = KPI::whereNotNull('score')->count();

        return view('commission.dashboard', compact(
            'kpis',
            'totalTasks',
            'pendingReviews',
            'reviewedTasks',
            'scoredKPIs'
        ));
    }

    public function employeeList()
    {
        $user = auth()->user();
        $users = User::whereNotIn('role_id',[User::ROLE_ADMIN,User::ROLE_MANAGER])
            ->paginate(10);
        return view('director.employees', ["users" => $users]);
    }

    public function getUserKpiData(int $userId,int $kpiId)
    {
        $user = User::find($userId);
        $kpi = Kpi::find($kpiId);

        if (!$user || !$kpi) {
            return response()->json(['error' => 'User or KPI not found'], 404);
        }

        $userKpi = $user->user_kpis()->where('kpi_id', $kpiId)->first();

        return response()->json([
            'hasScore' => $userKpi && $userKpi->current_score,
            'currentScore' => $userKpi ? $userKpi->current_score : null,
            'userId' => $userId,
            'kpiId' => $kpiId
        ]);
    }


    public function check_user(Kpi $kpi, User $user)
    {
        $userKpi = UserKpi::with(['kpi', 'tasks','kpi.criterias'])
            ->where('kpi_id',$kpi->id)
            ->where('user_id', $user->id)// eager load tasks too
            ->firstOrFail();

        $month = session('month') ?? (int)date('m');
        $month_name = Month::getMonth($month);

        return view('commission.checking', [
            'user_kpi' => $userKpi,
            'user' => $user,
            'kpi' => $kpi,
            'month_name' => $month_name
        ]);
    }

    public function check_user_store(Kpi $kpi, User $user, Request $request)
    {
        $userKpi = UserKpi::where('user_id', $user->id)
            ->where('kpi_id',$kpi->id)
            ->with(['kpi.parent', 'kpi.children', 'tasks','kpi.criterias']) // eager load tasks too
            ->firstOrFail();

            $maxFine = 0;
            $fine = 0;
            foreach ($userKpi->kpi->criterias as $criteria) {
                if(array_key_exists($criteria->id, $request->input('scores'))){
                    KpiCriteriaScore::updateOrCreate([
                        'kpi_criteria_id' => $criteria->id,
                        'user_kpi_id' => $userKpi->id,
                        ],[
                        'score' => $request->input('scores')[$criteria->id],
                    ]);
                    $maxFine += $criteria->bands->max('fine_ball');
                    $fine += $request->input('scores')[$criteria->id];
                }
            }
            $ball = $userKpi->target_score * (($maxFine-$fine)/$maxFine);

            $score = Score::create([
                'user_kpi_id' => $userKpi->id,
                'score' => $ball,
                'feedback'=> $request->input('feedback'),
                'scored_by' => auth()->id(),
                'type' => 2,
            ]);

            $userKpi->current_score = $ball;
            $userKpi->score_id = $score->id;
            $userKpi->save();


        return redirect()->route('commission.band_scores.list',$kpi->type)->with('success','Muvaffaqatli saqlandi.');
    }

    public function check_user_edit(Kpi $kpi, User $user)
    {
        $userKpi = UserKpi::with(['kpi', 'tasks','kpi.criterias'])
            ->where('kpi_id',$kpi->id)
            ->where('user_id', $user->id)// eager load tasks too
            ->firstOrFail();

        $month = session('month') ?? (int)date('m');
        $month_name = Month::getMonth($month);

        $criteriaScores = KpiCriteriaScore::where('user_kpi_id', $userKpi->id)
            ->pluck('score', 'kpi_criteria_id');

        return view('commission.checking_edit', [
            'user_kpi' => $userKpi,
            'user' => $user,
            'kpi' => $kpi,
            'month_name' => $month_name,
            'criteria_scores' => $criteriaScores,
        ]);
    }

    public function updateCriteriaScore(Request $request)
    {
        $validated = $request->validate([
            'user_id'     => 'required|integer|exists:users,id',
            'kpi_id'      => 'required|integer',
            'criteria_id' => 'required|integer',
            'score'       => 'required|numeric'
        ]);

        try {
            // Retrieve UserKpi
            $userKpi = UserKpi::where('user_id', $validated['user_id'])
                ->where('kpi_id', $validated['kpi_id'])
                ->firstOrFail();

            // Retrieve KpiCriteriaScore
            $criteriaScore = KpiCriteriaScore::where('user_kpi_id', $userKpi->id)
                ->where('kpi_criteria_id', $validated['criteria_id'])
                ->firstOrFail();

            $oldScore = $criteriaScore->score;
            $newScore = $validated['score'];

            // Update criteria score
            $criteriaScore->score = $newScore;
            $criteriaScore->save();

            if ($oldScore !== $newScore) {
                // Recalculate max fine
                $maxFine = $userKpi->kpi->criterias->sum(fn($criteria) => $criteria->bands->max('fine_ball') ?? 0);

                // Avoid division by zero
                if ($maxFine > 0 && $userKpi->target_score > 0) {
                    $adjustedScore = $userKpi->target_score * (
                            (($userKpi->current_score / $userKpi->target_score) * $maxFine + $oldScore - $newScore) / $maxFine
                        );
                    $userKpi->current_score = $adjustedScore;
                }

                $userKpi->save();

                // Update overall score (if exists)
                if ($userKpi->score) {
                    $userKpi->score->score = $newScore;
                    $userKpi->score->save();
                }
            }

            // Determine band name
            $criteria = KpiCriteria::with('bands')->find($validated['criteria_id']);
            $band = $criteria?->bands->firstWhere('fine_ball', $newScore);
            $bandName = $band?->name ?? 'Unknown';

            return response()->json([
                'success'     => true,
                'message'     => 'Score updated successfully',
                'band_name'   => $bandName,
                'total_score' => $userKpi->current_score,
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating score: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function updateComments(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|integer',
                'kpi_id' => 'required|integer',
                'feedback' => 'nullable|string'
            ]);

            $userKpi = UserKpi::where('user_id', $request->user_id)
                ->where('kpi_id', $request->kpi_id)
                ->first();

            if (!$userKpi) {
                return response()->json(['success' => false, 'message' => 'User KPI not found']);
            }

            // Update or create the main score record with feedback
            $userKpi->score->feedback = $request->feedback;
            $userKpi->score->save();

            return response()->json([
                'success' => true,
                'message' => 'Comments updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating comments: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Save selected KPI to session
     */
    public function saveSelectedKpi(Request $request)
    {
        $request->validate([
            'kpi_id' => 'required|integer',
            'kpi_type' => 'required|integer'
        ]);

        // Save the selected KPI ID to session with a key that includes the KPI type
        session(['selected_kpi_id_' . $request->kpi_type => $request->kpi_id]);

        return response()->json(['success' => true]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function scoresList($id)
    {
        // Only allow ID 2 or 4
        if (!in_array((int) $id, [Kpi::BEHAVIOUR, Kpi::IJRO])) {
            abort(404); // or return redirect()->back()->with('error', 'Noto‘g‘ri KPI turi.');
        }

        $month_id = session('month') ?? (int) date('m');
        $year = session('year') ?? (int) date('Y');

        $users = User::with(['working_days', 'work_zone'])
            ->whereNotIn('role_id', [User::ROLE_MANAGER, User::ROLE_ADMIN])
            ->get();

        $groupedUsers = $users->groupBy(fn($user) => $user->work_zone?->name ?? 'Boshqalar');

        $days = Month::where('year', '=', $year)->where('month_id', $month_id)->first()?->days;
        $month_name = Month::getMonth($month_id);
        $kpis = Kpi::where('type', $id)->whereNotNull('parent_id')->get();

        // Get the selected KPI from session or request
        $selectedKpiId = session('selected_kpi_id_' . $id, request('kpi_id'));

        $title = 'Mehnat intizomiga rioya qilinganligi';

        return view('commission.user_band_scores', compact(
            'users', 'title', 'days', 'groupedUsers', 'month_name', 'month_id', 'year', 'kpis', 'id', 'selectedKpiId'
        ));
    }

}