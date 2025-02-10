<?php

return [
    'required' => "This field is required.",
    'string' => "This field must be a string.",
    'integer' => "This field must be a integer.",
    'decimal' => "This field must be numeric and must have exactly :number decimal places.",
    'boolean' => "This field must be a boolean value.",
    'email' => "The email address must be a valid Gmail address.",
    'file_image' => "This field must be an image.",
    'array' => "This field must be an array.",
    'date' => "This field must be a date.",
    'date_format' => "The date must be in the format (YYYY-MM-DD).",
    'min' => [
        'password' => "The password must be at least :min characters long.",
        'string' => "This field must be at least :min characters long.",
        'capacity' => "The capacity must be at least :min person.",
        'price' => "The price must be at least :min.",
        'floor' => "The floor must be at least :min.",
        'number' => "The room number must be at least :min.",
        'units' => "The service units count must be at least :min.",
    ],

    'max' => [
        'password' => "The password must not exceed :max characters in length.",
        'string' => "This field must not exceed :max characters in length.",
        'capacity' => "The capacity must not exceed :max persone.",
        'price' => "The price must not exceed :max.",
        'image' => "The image size must not exceed :max KB.",
        'floor' => "The floor must not exceed :max",
        'number' => "The room number must not exceed :max",
    ],

    'unique' => [
        'email' => "The email address has already been taken.",
        'floor_number' => "The combination of floor and room number must be unique.",
    ],

    'confirmed' => [
        'password' => "The password confirmation does not match.",
    ],

    'regex' => [
        'email' => "Please enter a valid email address in the correct format (e.g., user@example.com).",
        'password' => "The password must contain at least one lowercase letter, one uppercase letter, a number, and one of the following symbols: (+-*.@).",
        'first_name' => "The first name must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores(_).",
        'last_name' => "The last name must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores(_).",
        'translation_en' => "This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).",
        'translation_ar' => "This field must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،).",
        'payment_method' => "The payment method ID must start with (pm_) and contain only letters, numbers, and underscores(_).",
        'payment_id' => "The payment ID must start with (pi_) and contain only letters, numbers, and underscores(_).",
    ],

    'exists' => [
        'email' => "The provided email does not exist in our records.",
        'room_type_id' => "The provided room type does not exist in our records.",
        'room_id' => "The provided room type does not exist in our records.",
        'service_id' => "The provided service does not exist in our records.",
    ],

    'in' => [
        'role' => "The role must be one of the following: [user, admin, super_admin].",
        'price' => "The price value must be :value.",
        'units' => "The service units count must be :value.",
        'status' => "The status must be one of the following: [succeeded, failed].",
    ],

    'date_after' => [
        'start' => "The start date must be after today.",
        'end' =>  "The end date must be after start date.",
    ],

    'distinct' => [
        'service' => "You can't provide the service more than once.",
    ],
];
