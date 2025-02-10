<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteOldNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "notifications:delete-old";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Delete read notifications older than one month";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $oneMonthAgo = Carbon::now()->subMonth();

        $countNotifications = Notification::where('read_at', '<', $oneMonthAgo)->delete();

        if ($countNotifications > 0) {
            Log::info("Successfully deleted {$countNotifications} notifications read older than one month.");
        } else {
            Log::info("No notifications read older than one month found.");
        }
    }
}
