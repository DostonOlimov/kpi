<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class TaskController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required',
        'child_id' => 'required|exists:kpis,id',
        'file' => 'nullable|file|max:2048',
    ]);

    $filePath = null;
    if ($request->hasFile('file')) {
        $filePath = $request->file('file')->store('tasks', 'public');
    }

    $task = Task::create([
        'kpi_id' => $request->child_id,
        'name' => $request->title,
        'description' => $request->description,
        'file_path' => $filePath,
        'user_id' => auth()->id(),
        'month' =>  session('month') ?? date('m'),
        'year' => session('year') ?? date('Y'),
    ]);

    return response()->json([
        'id' => $task->id,
        'title' => $task->title,
        'description' => $task->description,
        'file_url' => $filePath ? asset('storage/' . $filePath) : null,
    ]);
}

public function destroy($id)
{
    $task = Task::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
    if ($task->file_path) {
        Storage::disk('public')->delete($task->file_path);
    }
    $task->delete();

    return response()->json(['success' => true]);
}


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
            'user_name' => Auth::user()->name,
            'formatted_date' => $comment->created_at->format('M d, Y H:i')
        ]);
    }

    public function scoreKPI(Request $request, $childId)
    {
        $request->validate([
            'score' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:1000'
        ]);

        $kpi = KPI::findOrFail($childId);

        $kpi->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'scored_by' => Auth::id(),
            'scored_at' => now()
        ]);

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
