<?php

namespace App\Console\Commands;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteUnverifiedUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "users:delete-unverified-users";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Deletes users who have not verified their email within 5 days";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $fiveDaysAgo = Carbon::now()->subDays(5);

        $countUnverifiedUsers = User::where('email_verified_at', null)->where('created_at', '<', $fiveDaysAgo)->delete();

        if ($countUnverifiedUsers > 0) {
            Log::info("Successfully deleted {$countUnverifiedUsers} unverified users older than 5 days.");
        } else {
            Log::info("No unverified users older than 5 days were found.");
        }
    }
}
