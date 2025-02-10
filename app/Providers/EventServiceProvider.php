<?php

namespace App\Providers;

use App\Events\VerifyEmail;
use App\Listeners\SendVerificationEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\EmailUpdated;
use App\Events\InvoicePaid;
use App\Listeners\SendEmailUpdatedNotification;
use App\Listeners\SendInvoicePaidNotification;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        VerifyEmail::class => [
            SendVerificationEmail::class
        ],

        EmailUpdated::class => [
            SendEmailUpdatedNotification::class
        ],

        InvoicePaid::class => [
            SendInvoicePaidNotification::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
