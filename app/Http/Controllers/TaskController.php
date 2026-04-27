<?php

namespace App\Http\Controllers;

use App\Models\Kpi;
use App\Models\Score;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\TaskScore;
use App\Services\EmployeeTaskScoringService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

    public function aiScore($id, EmployeeTaskScoringService $scorer)
    {
        $task = Task::findOrFail($id);

        // Load userKpi without global scope (which filters by session year/month)
        $userKpi = \App\Models\UserKpi::withoutGlobalScopes()
            ->with('kpi')
            ->find($task->user_kpi_id);

        if (!$userKpi) {
            return response()->json([
                'message' => 'Xatolik',
                'error' => 'Vazifa uchun KPI bog\'lanmagan'
            ], 400);
        }

        if (!$userKpi->kpi) {
            return response()->json([
                'message' => 'Xatolik',
                'error' => 'KPI ma\'lumotlari topilmadi'
            ], 400);
        }

        $extractedText = '';

        if ($task->file_path) {
            $fullPath = storage_path('app/public/' . $task->file_path);

            if (!file_exists($fullPath)) {
                return response()->json([
                    'message' => 'Fayl topilmadi',
                    'error' => 'Attached file does not exist at: ' . $task->file_path
                ], 404);
            }

            try {
                $extractedText = $scorer->extractText($fullPath);
            } catch (\Throwable $e) {
                $extractedText = '';
                Log::warning('Text extraction failed for task ' . $id . ': ' . $e->getMessage());
            }
        }

        $taskContext = "Task: {$task->name}\nDescription: {$task->description}";
        $fullText = $extractedText ? $taskContext . "\n\nFile content:\n" . $extractedText : $taskContext;
        try {
            
            $scoreData = $scorer->scoreWithGemini($fullText, $userKpi->kpi, $task);

            if (!$scoreData || !isset($scoreData['score'])) {
                return response()->json([
                    'message' => 'AI baholashda xatolik',
                    'error' => 'Invalid score response from AI'
                ], 500);
            }

            $feedback = $scoreData['feedback'] ?? 'AI tomonidan baholangan';
            if (empty(trim($feedback))) {
                $feedback = 'AI tomonidan baholangan';
            }

            $score = TaskScore::create([
                'task_id' => $task->id,
                'score'   => $scoreData['score'],
                'feedback'=> $feedback,
                'user'    => 'AI Baholovchi',
            ]);

            $task->update([
                'task_score_id'  => $score->id,
                'score'          => $scoreData['score'],
                'extracted_text' => $extractedText,
            ]);

            return response()->json([
                'message'   => 'Muvaffaqiyatli baholandi',
                'score'     => $score->score,
                'feedback'  => $score->feedback,
                'max_score' => $userKpi->kpi->max_score ?? 10,
            ]);
        } catch (\Throwable $e) {
            Log::error('AI scoring failed for task ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'message' => 'AI baholashda xatolik yuz berdi',
                'error'   => $e->getMessage()
            ], 500);
        }
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
