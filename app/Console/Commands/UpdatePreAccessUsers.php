<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class UpdatePreAccessUsers extends Command
{
    protected $signature = 'users:update-preaccess';
    protected $description = 'Update users with pre-access from external JSON';

    public function handle(): int
    {
        $this->info('Fetching pre-access URLs...');

        $urlsListResponse = Http::get('https://cdn.vatita.net/Eventi/bookings_preaccess/index.json');
        if (!$urlsListResponse->ok()) {
            $this->error('Failed to fetch URLs list.');
            return 1;
        }

        $urls = $urlsListResponse->json(); // ["https://cdn.vatita.net/Eventi/bookings_preaccess/streamers.json", ...]

        foreach ($urls as $url) {
            $this->info("Fetching user IDs from $url ...");

            $response = Http::get($url);
            if (!$response->ok()) {
                $this->warn("Failed to fetch $url, skipping...");
                continue;
            }

            $userIds = $response->json(); // ["10000011", ...]

            foreach ($userIds as $userId) {
                $user = User::where('id', $userId)->first();
                if (!$user) continue;

                if (!$user->is_preaccess) {
                    $user->is_preaccess = true;
                    $user->save();
                    $this->info("Updated user {$user->id} to pre-access.");
                }
            }
        }

        $this->info('Done.');
        return 0;
    }
}
