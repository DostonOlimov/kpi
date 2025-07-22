<?php

namespace App\Http\Controllers;

use App\Models\KpiCriteriaScore;
use App\Models\Month;
use App\Models\Score;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\KPI;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\UserKpi;
use Illuminate\Support\Facades\Auth;

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

    public function addComment(Request $request, Task $task)
    {
        $request->validate([
            'comment' => 'required|string|max:1000'
        ]);

        $comment = TaskComment::create([
            'task_id' => $task->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ]);

        $task->is_checked = true;
        $task->save();

        return response()->json([
            'success' => true,
            'comment' => $comment->comment,
            'user_name' => Auth::user()->first_name . ' ' . Auth::user()->last_name,
            'formatted_date' => $comment->created_at->format('M d, Y H:i')
        ]);
    }

    public function scoreKPI(Request $request, $childId)
    {
        $kpi = KPI::findOrFail($childId);

        $maxScore = $kpi->max_score ?? 100;

        $request->validate([
            'score' => "required|numeric|min:0|max:$maxScore",
            'feedback' => 'nullable|string|max:1000',
            'user_id' => 'required|exists:users,id',
            'type' => 'required|integer'
        ]);

        KpiScore::updateOrCreate(
            [
                'kpi_id' => $childId,
                'user_id' => $request->user_id,
                'type'    => $request->type, // make sure this exists in the request
                'month'     => session('month') ?? (int)date('m'),
                'year'      => session('year') ?? (int)date('Y'),
            ],
            [
                'feedback'  => $request->feedback,
                'score'     => $request->score,
                'is_active' => true,
                'scored_by' => auth()->id(),
                'scored_at' => now(),
            ]
        );

        Task::where('kpi_id', $childId)
            ->where('user_id', $request->user_id)
            ->update(['is_checked' => true]);

        UserKpi::where('user_id', $request->user_id)
            ->where('kpi_id', $childId)
            ->update(['current_score' => $request->score]);



        return response()->json([
            'success' => true,
            'score' => $request->score,
            'feedback' => $request->feedback
        ]);
    }

    public function employeeList()
    {
        $user = auth()->user();
        $users = User::whereNotIn('role_id',[User::ROLE_ADMIN,User::ROLE_MANAGER])
            ->get();
        return view('director.employees', ["users" => $users]);
    }

    public function check_user(Kpi $kpi, User $user)
    {
        $userKpi = UserKpi::with(['kpi', 'tasks','kpi.criterias'])
            ->where('kpi_id',$kpi->id)
            ->where('user_id', $user->id)// eager load tasks too
            ->firstOrFail();

        $reviewedTasks = $this->countReviewedTasks($userKpi);

        $month = session('month') ?? (int)date('m');
        $month_name = Month::getMonth($month);

        return view('commission.checking', [
            'user_kpi' => $userKpi,
            'user' => $user,
            'kpi' => $kpi,
            'month_name' => $month_name,
            'reviewed_tasks' => $reviewedTasks,
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


        return redirect()->route('behaviour.list')->with('success','Muvaffaqatli saqlandi.');
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

    public function check_user_update(Kpi $kpi, User $user, Request $request)
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


        return redirect()->route('behaviour.list')->with('success','Muvaffaqatli saqlandi.');
    }

    private function countReviewedTasks($userKpi): int
    {
        return $userKpi->tasks->whereNotNull('score')->count();
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

    public function updateCriteriaScore(Request $request)
    {
        try {
            $request->validate([
                'criteria_score_id' => 'required|integer',
                'score' => 'required|numeric'
            ]);

            $criteriaScore = KpiCriteriaScore::findOrFail($request->input('criteria_score_id'));
            $criteriaScore->score = $request->input('score');
            $criteriaScore->save();


            // Update or create the score
            $userKpiScore = UserKpiScore::updateOrCreate(
                [
                    'user_kpi_id' => $userKpi->id,
                    'criteria_id' => $request->criteria_id
                ],
                [
                    'score' => $request->score
                ]
            );

            // Get band name for display
            $criteria = Criteria::with('bands')->find($request->criteria_id);
            $band = $criteria->bands->where('fine_ball', $request->score)->first();
            $bandName = $band ? $band->name : 'Unknown';

            // Recalculate total score
            $totalScore = $this->calculateTotalScore($userKpi);
            $userKpi->update(['current_score' => $totalScore]);

            return response()->json([
                'success' => true,
                'message' => 'Score updated successfully',
                'band_name' => $bandName,
                'total_score' => $totalScore
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating score: ' . $e->getMessage()
            ]);
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
            $score = $userKpi->score ?? new UserKpiScore();
            $score->user_kpi_id = $userKpi->id;
            $score->feedback = $request->feedback;
            $score->save();

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

    private function calculateTotalScore($userKpi)
    {
        // Implement your total score calculation logic here
        // This is just an example - adjust based on your business logic
        $scores = UserKpiScore::where('user_kpi_id', $userKpi->id)->get();
        return $scores->sum('score');
    }
}
