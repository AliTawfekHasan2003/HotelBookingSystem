<?php

namespace App\Listeners;

use App\Notifications\EmailUpdatedNotification;

class SendEmailUpdatedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $user = $event->user;

        $user->notify(new EmailUpdatedNotification($event->oldEmail, $event->newEmail));
    }
}
