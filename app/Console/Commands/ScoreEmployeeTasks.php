<?php

namespace App\Console\Commands;

use App\Models\UserKpi;
use Illuminate\Console\Command;
use App\Services\EmployeeTaskScoringService;

class ScoreEmployeeTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:score-ai';

    protected $description = 'Score unscored employee tasks using AI every 3 hours';

    public function handle(EmployeeTaskScoringService $scorer)
    {
        $user_kpis = UserKpi::whereNull('ai_score')->get();

        if ($user_kpis->isEmpty()) {
            $this->info('No new tasks to score.');
            return;
        }

        foreach ($user_kpis as $user_kpi) {

            $task = $user_kpi->kpi->tasks->where('user_id', $user_kpi->user_id)->first();
            if($task){

                $filePath = null;

                if($task->file_path){
                    $filePath = storage_path('app/public/' . $task->file_path);
                }


                 try {
                    $text = $scorer->extractText($filePath);

                     $scoreData = $scorer->scoreWithGemini($text,$user_kpi->kpi, $task);


                     $user_kpi->update([
                         'ai_extracted_text' => $text,
                         'current_score' => $scoreData['score'],
                         'ai_score' => $scoreData['score'],
                         'ai_feedback' => $scoreData['feedback'],
                     ]);

                     $this->info("Scored task: {$user_kpi->id} (Score: {$scoreData['score']})");
                 } catch (\Throwable $e) {
                     $this->error("Failed to score task ID {$user_kpi->id}: " . $e->getMessage());
                 }
            }

        }
    }
}
