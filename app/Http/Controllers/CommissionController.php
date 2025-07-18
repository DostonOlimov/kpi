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
use App\Models\KPIScore;
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

    public function check_user(int $type, User $user, Request $request)
    {
        $userKpis = UserKpi::where('user_id', $user->id)
            ->whereHas('kpi', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->with(['kpi.parent', 'kpi.children', 'tasks','kpi.criterias']) // eager load tasks too
            ->get();

        $reviewedTasks = $this->countReviewedTasks($userKpis);

        $month = session('month') ?? (int)date('m');
        $month_name = Month::getMonth($month);

        return view('commission.checking', [
            'user_kpis' => $userKpis,
            'user' => $user,
            'type' => $type,
            'month_name' => $month_name,
            'reviewed_tasks' => $reviewedTasks,
        ]);
    }

    public function check_user_store(int $type, User $user, Request $request)
    {
        $userKpis = UserKpi::where('user_id', $user->id)
            ->whereHas('kpi', function ($query) use ($type) {
                $query->where('type', $type);
            })
            ->with(['kpi.parent', 'kpi.children', 'tasks','kpi.criterias']) // eager load tasks too
            ->get();

        foreach ($userKpis as $userKpi) {
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

        }

        return redirect()->route('days.list')->with('success','Muvaffaqatli saqlandi.');
    }

    private function countReviewedTasks($userKpis): int
    {
        return $userKpis->sum(fn($userKpi) =>
        $userKpi->tasks->whereNotNull('score')->count()
        );
    }

}
