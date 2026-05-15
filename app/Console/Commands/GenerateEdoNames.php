<?php

namespace App\Console\Commands;

use App\Models\EdoUserName;
use App\Models\User;
use Illuminate\Console\Command;

class GenerateEdoNames extends Command
{
    protected $signature = 'edo:generate-names {userId? : Specific user ID} {--force : Overwrite existing names}';

    protected $description = 'Generate default EDO name variations for users based on first_name, last_name, father_name';

    /**
     * Get the initial letter(s) for a name part, handling Uzbek Sh/Ch digraphs.
     */
    private function getInitial(string $name): string
    {
        if (strlen($name) < 2) {
            return mb_strtoupper(mb_substr($name, 0, 1));
        }

        $firstTwo = mb_strtoupper(mb_substr($name, 0, 2));

        if (in_array($firstTwo, ['SH', 'CH'])) {
            return $firstTwo;
        }

        return mb_strtoupper(mb_substr($name, 0, 1));
    }

    /**
     * Generate name variations for a user.
     */
    private function generateVariations(User $user): array
    {
        $first  = $user->first_name ?? '';
        $last   = $user->last_name ?? '';
        $father = $user->father_name ?? '';

        if (empty($first) || empty($last)) {
            return [];
        }

        $fi = $this->getInitial($first);
        $mi = $this->getInitial($father);
        $lastUpper = mb_strtoupper($last);
        $lastTitle = mb_convert_case($last, MB_CASE_TITLE, 'UTF-8');

        $variations = [
            "{$fi}.{$mi}.{$lastUpper}",      // D.R.FARMONOV
            "{$fi}. {$lastUpper}",            // D. FARMONOV
            "{$fi}. {$lastTitle}",            // D. Farmonov
            "{$fi}.{$mi}.{$lastTitle}",       // D.R.Farmonov
        ];

        return array_unique($variations);
    }

    public function handle()
    {
        $userId = $this->argument('userId');
        $force  = $this->option('force');

        $users = User::whereNotIn('role_id', [User::ROLE_ADMIN, User::ROLE_MANAGER])
            ->when($userId, function ($query) use ($userId) {
                $query->where('id', $userId);
            })
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No users found.');
            return 1;
        }

        $created = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $variations = $this->generateVariations($user);

            if (empty($variations)) {
                $this->warn("User #{$user->id}: skipped — missing first_name or last_name");
                $skipped++;
                continue;
            }

            if (!$force) {
                $existing = $user->edoNames()->pluck('name')->toArray();
                $variations = array_diff($variations, $existing);
            } else {
                $user->edoNames()->delete();
            }

            foreach ($variations as $name) {
                EdoUserName::create([
                    'user_id' => $user->id,
                    'name'    => $name,
                ]);
                $created++;
            }

            $this->info("User #{$user->id} ({$user->full_name}): added " . count($variations) . ' name(s)');
        }

        $this->newLine();
        $this->info("Done. Created: {$created}, Skipped: {$skipped}");

        return 0;
    }
}
