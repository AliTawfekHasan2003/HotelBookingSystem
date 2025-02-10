<?php

namespace App\Listeners;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class SendVerificationEmail
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
    public function handle(object $event)
    {
        $user = $event->user;

        if (!$user) {
            Log::error("Verification email could not be sent because user is missing in the event data.");
            
            return;
        }

        $verificationUrl =  URL::temporarySignedRoute(
            'verify.email',
            now()->addMinutes(60),
            ['id' => $user->id]
        );

        $user->notify(new VerifyEmailNotification($verificationUrl));
    }
}
