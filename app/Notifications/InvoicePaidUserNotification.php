<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class InvoicePaidUserNotification extends Notification
{
    use Queueable;

    public $invoiceDetails;

    /**
     * Create a new notification instance.
     */
    public function __construct($invoiceDetails)
    {
        $this->invoiceDetails = $invoiceDetails;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.email.invoice_paid.user.subject'))
            ->greeting(__('notifications.email.greeting') . $notifiable->first_name . " !")
            ->line(__('notifications.email.invoice_paid.user.line1') . $this->invoiceDetails['total_cost'])
            ->line(__('notifications.email.invoice_paid.user.line2') . $this->invoiceDetails['start_date'] . __('notifications.email.invoice_paid.user.line3') . $this->invoiceDetails['end_date'])
            ->line(__('notifications.email.Thank_line'))
            ->salutation(__('notifications.email.salutation'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => __('notifications.database.invoice_paid.user'),
            'invoice_id' => $this->invoiceDetails['invoice_id'],
        ];
    }
}
