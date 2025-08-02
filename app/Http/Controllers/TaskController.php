<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\Score;
use App\Models\Task;
use App\Models\TaskScore;
use App\Services\EmployeeTaskScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class TaskController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'child_id' => 'required',
            'file' => 'nullable|file|max:5120|mimes:doc,docx,xls,xlsx,pdf',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('tasks', 'public');
        }

        $task = Task::create([
            'user_kpi_id' => $request->child_id,
            'name' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
        ]);

        return response()->json([
            'id' => $task->id,
            'title' => $task->name,
            'description' => $task->description,
            'file_url' => $filePath ? asset('storage/' . $filePath) : null,
        ]);
    }
    public function update($id, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'file' => 'nullable|file|max:5120|mimes:doc,docx,xls,xlsx,pdf',
        ]);

        $task = Task::findOrFail($id);

        if (!is_null($task->score)) {
            return response()->json(['message' => 'Baho qo‘yilgan vazifani tahrirlab bo‘lmaydi.'], 403);
        }

        if ($request->hasFile('file')) {
            $task->file_path = $request->file('file')->store('tasks', 'public');
        }

        $task->update([
            'name' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'id' => $task->id,
            'title' => $task->name,
            'description' => $task->description,
            'file_url' => $task->file_path ? asset('storage/' . $task->file_path) : null,
            'score' => $task->score,
        ]);
    }
    public function destroy($id)
    {
        $task = Task::where('id', $id)->firstOrFail();

        if (!is_null($task->score)) {
            return response()->json(['message' => 'Baho qo‘yilgan vazifani tahrirlab bo‘lmaydi.'], 403);
        }

        $task->delete();

        return response()->json(['success' => true]);
    }

    public function aiScore($id,EmployeeTaskScoringService $scorer)
    {
        $task = Task::findOrFail($id);

        if($task) {

            $filePath = null;

            if ($task->file_path) {
                $filePath = storage_path('app/public/' . $task->file_path);
            }

            try {
                $text = $scorer->extractText($filePath);

                $scoreData = $scorer->scoreWithGemini($text, $task->user_kpi->kpi, $task);

                $score = TaskScore::create(
                    [
                        'task_id' => $task->id,
                        'score' => $scoreData['score'],
                        'feedback' => $scoreData['feedback'],
                    ]
                );

                $task->update([
                    'task_score_id' => $score->id,
                    'score' => $scoreData['score'],
                    'extracted_text' => $text
                ]);
            } catch (\Throwable $e) {
                $this->error("Failed to score task: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => 'Scored',
            'score' => $score->score,
            'feedback' => $score->feedback,
            'max_score' => $task->user_kpi->kpi->max_score ?? 10,
        ]);
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
