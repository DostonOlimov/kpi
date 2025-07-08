<?php

namespace App\Http\Controllers;

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
        $users = User::with('tasks' )
            ->whereNotIn('role_id',[User::ROLE_ADMIN,User::ROLE_MANAGER])
            ->get();
        return view('commission.employees', ["users" => $users]);
    }

}
