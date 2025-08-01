<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KPI;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class asdf extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        // Get user's KPIs with tasks and scores
        $kpis = KPI::with([
            'children' => function($query) use ($userId) {
                $query->with([
                    'tasks' => function($taskQuery) use ($userId) {
                        $taskQuery->where('user_id', $userId)
                                 ->with(['comments.user']);
                    }
                ]);
            }
        ])
        ->whereNull('parent_id')
        ->get();

        // Process KPIs to add user-specific data
        $kpis = $kpis->map(function($category) use ($userId) {
            $category->user_tasks_count = 0;
            $totalScore = 0;
            $scoredChildren = 0;

            $category->children = $category->children->map(function($child) use ($userId, &$totalScore, &$scoredChildren) {
                // Get user's tasks for this child
                $child->user_tasks = $child->tasks->where('user_id', $userId);

                // Count user tasks for this category
                $userTasksCount = $child->user_tasks->count();

                if ($child->score) {
                    $totalScore += $child->score;
                    $scoredChildren++;
                }

                return $child;
            });

            // Calculate average score for category
            $category->average_score = $scoredChildren > 0 ? $totalScore / $scoredChildren : null;

            return $category;
        });

        // Calculate user statistics
        $userStats = $this->calculateUserStats($userId);

        // Get achievements
        $achievements = $this->getUserAchievements($userId, $userStats);

        return view('user.results', compact('kpis', 'userStats', 'achievements'));
    }

    private function calculateUserStats($userId)
    {
        $totalTasks = Task::where('user_id', $userId)->count();
        $reviewedTasks = Task::where('user_id', $userId)
                            ->whereHas('comments')
                            ->count();

        $scoredKPIs = KPI::whereHas('tasks', function($query) use ($userId) {
                         $query->where('user_id', $userId);
                     })
                     ->whereNotNull('score')
                     ->count();

        $averageScore = KPI::whereHas('tasks', function($query) use ($userId) {
                           $query->where('user_id', $userId);
                       })
                       ->whereNotNull('score')
                       ->avg('score') ?? 0;

        $completionRate = $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0;
        $reviewRate = $totalTasks > 0 ? ($reviewedTasks / $totalTasks) * 100 : 0;

        $totalKPIs = KPI::whereHas('tasks', function($query) use ($userId) {
                        $query->where('user_id', $userId);
                     })->count();

        $scoringProgress = $totalKPIs > 0 ? ($scoredKPIs / $totalKPIs) * 100 : 0;

        return [
            'total_tasks' => $totalTasks,
            'reviewed_tasks' => $reviewedTasks,
            'scored_kpis' => $scoredKPIs,
            'average_score' => round($averageScore, 1),
            'completion_rate' => round($completionRate, 1),
            'review_rate' => round($reviewRate, 1),
            'scoring_progress' => round($scoringProgress, 1)
        ];
    }

    private function getUserAchievements($userId, $userStats)
    {
        $achievements = [];

        // First Task Achievement
        if ($userStats['total_tasks'] >= 1) {
            $achievements[] = [
                'icon' => '🎯',
                'title' => 'First Step',
                'description' => 'Submitted your first task'
            ];
        }

        // Task Master Achievement
        if ($userStats['total_tasks'] >= 10) {
            $achievements[] = [
                'icon' => '📝',
                'title' => 'Task Master',
                'description' => 'Submitted 10+ tasks'
            ];
        }

        // High Performer Achievement
        if ($userStats['average_score'] >= 80) {
            $achievements[] = [
                'icon' => '⭐',
                'title' => 'High Performer',
                'description' => 'Maintained 80+ average score'
            ];
        }

        // Perfect Score Achievement
        if ($userStats['average_score'] >= 95) {
            $achievements[] = [
                'icon' => '🏆',
                'title' => 'Excellence',
                'description' => 'Achieved 95+ average score'
            ];
        }

        // Consistent Performer Achievement
        if ($userStats['completion_rate'] >= 90) {
            $achievements[] = [
                'icon' => '🎖️',
                'title' => 'Consistent Performer',
                'description' => '90%+ task completion rate'
            ];
        }

        // Feedback Champion Achievement
        if ($userStats['review_rate'] >= 80) {
            $achievements[] = [
                'icon' => '💬',
                'title' => 'Feedback Champion',
                'description' => '80%+ of tasks reviewed'
            ];
        }

        return $achievements;
    }
}
