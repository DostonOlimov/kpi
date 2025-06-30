<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KPI;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\KPIScore;
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



        return response()->json([
            'success' => true,
            'score' => $request->score,
            'feedback' => $request->feedback
        ]);
    }

    public function getStats()
    {
        return response()->json([
            'total_tasks' => Task::count(),
            'pending_reviews' => Task::whereDoesntHave('comments')->count(),
            'reviewed_tasks' => Task::whereHas('comments')->count(),
            'scored_kpis' => KPI::whereNotNull('score')->count()
        ]);
    }
}
