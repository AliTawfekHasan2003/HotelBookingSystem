<?php

return [
    'user' => [
        'password_add' => "The password added to your account successfully.",
        'password_update' => "The password updated successfully.",
        'profile_update_with_email' => "Your account details updated successfully. Please check your new email to confirm it. We also recommend updating your email in any linked social media account.",
        'profile_update' => "Your account details updated successfully.",
        'show_profile' => "Your profile details fetched successfully.",
        'show_user' => "User details fetched successfully.",
        'index' => "All verified users details fetched successfully.",
        'role_assign' => "The role assigned to user successfully.",
    ],

    'notification' => [
        'get_all_notifications' => "All notifications fetched successfully.",
        'get_unread_notifications' => "Unread notifications fetched successfully.",
        'markAsRead' => "The notification read successfully.",
        'markAllAsRead' => "All notifications read successfully.",
    ],

    'room_type' => [
        'index' => "Room types fetched successfully.",
        'index_trashed' => "Deleted room types fetched successfully.",
        'show' => "Room type fetched successfully.",
        'show_trashed' => "Deleted room type fetched successfully.",
        'rooms' => "Rooms for this type fetched successfully.",
        'services' => "Services for this type fetched successfully.",
        'favorite' => "Favorite room types fetched successfully.",
        'add_to_favorite' => "Room type added to favorites successfully.",
        'delete_from_favorite' => "Room type deleted from favorites successfully.",
        'create' => "New room type created successfully.",
        'update' => "Room type updated successfully.",
        'soft_delete' => "Room type deleted successfully.",
        'restore' => "Deleted room type restored from deletion successfully.",
        'force_delete' => "Room type permanently deleted successfully.",
    ],

    'room' => [
        'index' => "Rooms fetched successfully.",
        'index_trashed' => "Deleted rooms fetched successfully.",
        'show' => "Room fetched successfully.",
        'show_trashed' => "Deleted room fetched successfully.",
        'bookings' => "Bookings for this room fetched successfully.",
        'favorite' => "Favorite rooms fetched successfully.",
        'unavailable_dates' => "Unavailable dates for this room fetched successfully.",
        'add_to_favorite' => "Room added to favorites successfully.",
        'delete_from_favorite' => "Room deleted from favorites successfully.",
        'create' => "New room created successfully.",
        'update' => "Room updated successfully.",
        'soft_delete' => "Room deleted successfully.",
        'restore' => "Deleted room restored from deletion successfully.",
        'force_delete' => "Room permanently deleted successfully.",
    ],

    'service' => [
        'index' => "Services fetched successfully.",
        'index_trashed' => "Deleted services fetched successfully.",
        'show' => "Service fetched successfully.",
        'show_trashed' => "Deleted service fetched successfully.",
        'room_types' => "Room types associated with this service fetched successfully.",
        'bookings' => "Bookings for this service fetched successfully.",
        'favorite' => "Favorite services fetched successfully.",
        'unavailable_dates' => "Unavailable dates for this service fetched successfully.",
        'add_to_favorite' => "Service added to favorites successfully.",
        'limited_units' => "Number of available units for this service fetched successfully.",
        'delete_from_favorite' => "Service deleted from favorites successfully.",
        'create' => "New service created successfully.",
        'update' => "Service updated successfully.",
        'soft_delete' => "Service deleted successfully.",
        'restore' => "Deleted service restored from deletion successfully.",
        'force_delete' => "Service permanently deleted successfully.",
    ],

    'room_type_service' => [
        'assign' => "Service assigned to room Type successfully.",
        'revoke' => "Service revoked from room Type successfully.",
    ],

    'booking' => [
        'total_cost' => "The total cost for the booking calculate successfully.",
        'payment_intent' => "Payment initiated successfully.",
        'payment_confirm' => "Payment confirmed successfully.",
    ],

    'invoice' => [
        'index_user' => "Your invoices fetched successfully.",
        'index' => "Invoices fetched successfully.",
        'show_user' => "Your invoice fetched successfully.",
        'show' => "Invoice fetched successfully.",
        'bookings_user' => "All bookings for your invoice fetched successfully.",
        'bookings' => "All bookings for this invoice fetched successfully.",
    ],
];
