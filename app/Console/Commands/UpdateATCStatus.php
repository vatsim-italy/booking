<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class UpdateATCStatus extends Command
{
    protected $signature = 'users:update-atc';
    protected $description = 'Update users with ATC status from external API';

    public function handle(): int
    {
        $this->info('Fetching ATC status from VATITA API...');

        try {
            // Fetch all users from VATITA API in one request
            $response = Http::withToken(env('VATITA_API_TOKEN'))
                ->timeout(10)
                ->get('https://training.vatita.net/api/users', [
                    'include' => ['allUsers', 'endorsements'],
                ]);

            if (!$response->ok()) {
                $this->error("VATITA API returned status {$response->status()}");
                return 1;
            }

            $vatitaUsers = $response->json()['data'] ?? [];
            $this->info("Fetched " . count($vatitaUsers) . " users from VATITA API.");

            // Loop through all users in the DB
            $dbUsers = User::all();

            foreach ($dbUsers as $user) {
                $vatitaUser = collect($vatitaUsers)->firstWhere('id', intval($user->id));
                if ($vatitaUser) {
                    $visiting = $vatitaUser['endorsements']['visiting'] ?? null;
                    $atc_active = $vatitaUser['atc_active'] ?? false;
                    $is_visiting = (is_array($visiting) && count($visiting) > 0) ?? false;

                    // Only update if changed
                    if ($user->is_active_atc !== $atc_active || $user->is_visiting_atc !== $is_visiting) {
                        $user->is_active_atc = $atc_active;
                        $user->is_visiting_atc = $is_visiting;
                        $user->save();
                        $this->info("Updated user {$user->id}: active={$atc_active}, visiting={$is_visiting}");
                    }
                }
            }

            $this->info('ATC status update complete.');
        } catch (\Throwable $e) {
            $this->error("Failed to fetch or update ATC status: " . $e->getMessage());
            \Log::warning("VATITA API update failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
