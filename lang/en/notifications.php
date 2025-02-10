<?php

return [
    'email' => [
        'salutation' => "Best Wishes",
        'greeting' => "Hello ",
        'Thank_line' => "Thank you for using our application.",
        'verify_email' => [
            'subject' => "Verify Your Email Address.",
            'line1' => "Click the button below to verify your email address.",
            'action' => "Verify Email.",
        ],

        'invoice_paid' => [
            'user' => [
                'subject' => "Your Invoice Was Paid Successfully",
                'line1' => "The total cost is: ",
                'line2' => "You can receive the Room from ",
                'line3' => "to ",
            ],

            'super_admin' => [
                'subject' => "New Invoice Paid Successfully",
                'line1' => "The invoice was paid by ",
                'line2' => "The total cost is ",
                'line3' => "Booking period from  ",
                'lin4' => "to ",
            ],
        ],
    ],

    'database' => [
        'email_updated' => "Your email has been updated and the links to your social media accounts have been removed. Please update your email in your accounts to re-lin.",
        'invoice_paid' => [
            'user' => "Your invoice paid successfully.",
            'super_admin' => "A new invoice paid successfully.",
        ],
    ],
];
