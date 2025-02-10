<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\InvoicePaidSuperAdminNotification;
use App\Notifications\InvoicePaidUserNotification;

class SendInvoicePaidNotification
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
        $invoice = $event->invoice;

        $invoiceDetails = [
            'total_cost' => $invoice->total_cost . " $",
            'start_date' => $invoice->start_date,
            'end_date' => $invoice->end_date,
            'invoice_id' => $invoice->id,
        ];

        $superAdmins = User::where('role', 'super_admin')->get();

        if ($superAdmins->isNotEmpty()) {
            foreach ($superAdmins as $superAdmin) {
                $superAdmin->notify(new InvoicePaidSuperAdminNotification($invoiceDetails));
            }
        }

        $user = User::find($invoice->user_id);

        if ($user) {
            $user->notify(new InvoicePaidUserNotification($invoiceDetails));
        }
    }
}
