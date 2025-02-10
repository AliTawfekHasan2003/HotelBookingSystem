<?php

return [
    'success' => [
        'register' => "Account created  successfully, Please check your email to verify your account.",
        'login' => "Logged in successfully.",
        'refresh' => "Session refreshed successfully.",
        'logout' => "Logged out successfully.",
        'email' => [
            'verified' => "Your email verified successfully.",
            'resend_verify' => "A verification email has been sent to your email successfully, Please verify your email address to verify your account."
        ],
        'social' => [
            'register' => "A new account has been successfully created and linked with your :provider account.",
            'login' => "Successfully logged in using :provider.",
        ],
    ],
    'errors' => [
        'unauthorized' => "Unauthorized: Password or email is invalid.",
        'email' => [
            'unverified' => "Please verify your email, Then log in again.",
            'unvalid_signature' => "Verification link expired, Please request a new one.",
            'already_verified' => "Your email already verified.",
            'many_attempts' => "You have exceeded the maximum number of attempts, Please wait few minutes before trying again."
        ],
        'token_expired' => "Your session has expired, Please log in again.",
        'token_invalid' => "Your session is invalid, Please log in again.",
        'token_not_provided' => "No valid session found, Please log in to continue.",
        'social' => [
            'email_or_name' => "Email or name is missing. Please ensure that email and name permissions are enabled in your social account settings, then try again.",
        ],
    ],
];
