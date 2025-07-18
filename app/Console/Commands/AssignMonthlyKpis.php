<?php

namespace App\Console\Commands;

use App\Models\Kpi;
use App\Models\User;
use App\Models\UserKpi;
use Illuminate\Console\Command;

class AssignMonthlyKpis extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kpis:assign {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign default KPIs to a user or all users for the current month';

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
        $month = now()->month;
        $year = now()->year;

        $userId = $this->argument('userId');

        // Determine target users
        $users = $userId
            ? User::where('id', $userId)->get()
            : User::whereNotIn('role_id', [User::ROLE_ADMIN,User::ROLE_MANAGER])->get();

        $kpis = Kpi::whereNotNull('parent_id')
            ->whereIn('type', [
                Kpi::ACTIVITY,
                Kpi::BEHAVIOUR,
                Kpi::IJRO
            ])->get();

        foreach ($users as $user) {
            foreach ($kpis as $kpi) {
                UserKpi::firstOrCreate([
                    'user_id' => $user->id,
                    'kpi_id' => $kpi->id,
                    'month' => $month,
                    'year' => $year,
                ], [
                    'target_score' => $kpi->max_score,
                ]);
            }

            $this->info("Default KPIs assigned to user #{$user->id} for {$month}/{$year}");
        }

        return 0;
    }
}
