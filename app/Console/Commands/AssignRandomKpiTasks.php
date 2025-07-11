<?php

namespace App\Console\Commands;

use App\Models\Kpi;
use App\Models\User;
use App\Models\UserKpi;
use Illuminate\Console\Command;

class AssignRandomKpiTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:random-kpi-task  {--year= : 2025)} {--month= :  7}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign one random KPI task with max_score = 100 to users who have no tasks';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $month = $this->option('month') ?? date('m');
        $year = $this->option('year') ?? date('Y');

        $users = User::whereNotIn('role_id', [User::ROLE_ADMIN, User::ROLE_MANAGER])->get();

        foreach ($users as $user) {
            // Skip if user already has KPIs for the given month
            $existing = $user->user_kpis
                ->where('year', $year)
                ->where('month', $month)
                ->first();

            if ($existing) {
                continue;
            }

            $defaultKpiIds = [5, 6];
            $defaultTotalScore = 0;

            // Collect default KPIs (5 and 6) first
            $kpiIdsToAssign = [];
            foreach ($defaultKpiIds as $id) {
                $kpi = Kpi::find($id);
                if ($kpi) {
                    $kpiIdsToAssign[] = $id;
                    $defaultTotalScore += $kpi->max_score ?? 0;
                }
            }

            // Stop if default already exceeds 80
            if ($defaultTotalScore >= 80) {
                foreach ($kpiIdsToAssign as $kpiId) {
                    $kpi = Kpi::find($kpiId);
                    UserKpi::create([
                        'user_id'      => $user->id,
                        'year'         => $year,
                        'month'        => $month,
                        'kpi_id'       => $kpiId,
                        'target_score' => min(80, $kpi->max_score),
                    ]);
                }
                continue;
            }

            // Get user working KPI IDs, excluding already selected ones
            $workingKpiIds = $user->working_kpis
                ->pluck('id')
                ->diff($kpiIdsToAssign)
                ->values()
                ->all();

            shuffle($workingKpiIds);

            $currentScore = $defaultTotalScore;

            foreach ($workingKpiIds as $kpiId) {
                $kpi = Kpi::find($kpiId);
                if (!$kpi) continue;

                $score = $kpi->max_score ?? 0;

                // If adding this one would go over 80, skip it
                if (($currentScore + $score) > 80) {
                    continue;
                }

                $kpiIdsToAssign[] = $kpiId;
                $currentScore += $score;

                // If exactly 80, no more needed
                if ($currentScore >= 80) break;
            }

            // Create entries
            foreach ($kpiIdsToAssign as $kpiId) {
                $kpi = Kpi::find($kpiId);
                UserKpi::create([
                    'user_id'      => $user->id,
                    'year'         => $year,
                    'month'        => $month,
                    'kpi_id'       => $kpiId,
                    'target_score' => min(80, $kpi->max_score),
                ]);
            }

            $this->info("✅ Assigned KPIs to user ID {$user->id} with total score: {$currentScore}");
        }
    }
}
