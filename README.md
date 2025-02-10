# Hotel Room and Service Booking System

This is a hotel room and service booking system.  
It is a system that supports notifications on the app and via email, and is multilingual for booking rooms and services within a hotel. In addition, it includes a control panel for both managers and super managers. The system allows the user to browse room types—each room type comprises a group of rooms and each type has its own set of services—and from there, the user can book a room along with the set of services assigned to that same room type after completing the payment process.

Managers have full control over the system, including the ability to add, delete, and modify room types and assign rooms and services to these types, among other functions.

Super managers can perform everything that managers do, in addition to some extra features such as assigning roles to users.

## Authentication  
- **JWT (JSON Web Token)** is used for authentication and session management, ensuring secure access to the APIs.

## Documentation  
- Documentation is provided through **Swagger**, offering a visual interface to document and test the APIs.

(All system operations require authentication before creating a new account).
 
## Features  

**(Roles: user, admin, super_admin)**

- Select the system language (Arabic, English).
- Register a new account by providing (first name, last name, email address, password) — the account is immediately assigned the role of (user).
- Verify the account by confirming the email via a verification link.
- Request a resend of the verification link if it expires (with the number of resend attempts limited per account).

**Note:** User accounts that are not verified are automatically deleted after a specified period (via scheduled tasks).

- Log in provided that the account is verified (using email and password).
- Log out.
- Manage the session.
- Register using (Google, GitHub) which is immediately verified if linked with an email different from that of the regular account; otherwise, the regular account is linked and login is performed.
- Access account settings to:
  - 1. View the profile.
  - 2. Edit profile information (first name, last name, email address).
  - 3. Add a password to the account (in case the user registered via a social platform and does not have a regular account). This then allows the user to log in with email and password as well.
  - 4. Change the account password if one exists.

**Note:** If the account is linked with a social platform and then the email is changed, the linkage with the regular account will be removed from all associated social platform accounts.

- Display all room types, with filtering possible by (name, capacity, category).
- Display a specific room type.
- Display all rooms belonging to a specific room type.
- Display all services assigned to a specific room type.
- Display all rooms, with filtering possible by (room name, floor, view).
- Display a specific room.
- Retrieve all unavailable dates for a specific room (i.e. dates during which the room cannot be booked).
- Display all services, with filtering possible by (name, category, limited, free).
- Display a specific service.
- Display all room types assigned to a specific service.
- Retrieve the number of available units for a given service (limited) during a specified period.

- Display all notifications.
- Display unread notifications.
- Mark a notification as read.
- Mark all unread notifications as read.

**Only (user):**
- Display all favorite room types (those marked as favorite).
- Mark a specific room type as favorite.
- Remove the favorite mark from a specific room type.
- Display all favorite rooms (those marked as favorite).
- Mark a specific room as favorite.
- Remove the favorite mark from a specific room.
- Display all favorite services (those marked as favorite).
- Mark a specific service as favorite.
- Remove the favorite mark from a specific service.
- Retrieve the total cost of a booking after entering its details (room and services) for a specified period.
- Request payment for a booking after entering its details, via:
  - 1. Initiating payment through (Stripe) which returns: `.total_cost, client_secret, payment_id`
  - 2. Confirming the payment by sending a success or failure status along with the payment_id.
- Display all invoices.
- Display a specific invoice.
- Display all bookings associated with a specific invoice.

**Note:** The status of an invoice is updated according to the payment status, and it is automatically updated after a specified period in case of delayed confirmation (via scheduled tasks).

**Dashboard (admin, super_admin):**
- Display all user accounts.
- Display a specific user account.
- Add a new room type.
- Edit the details of a specific room type.
- Delete a specific room type provided it has no rooms assigned.
- Add a new room.
- Edit the details of a specific room.
- Delete a specific room provided it is not part of any future booking.
- Add a new service.
- Edit the details of a specific service.
- Delete a specific service provided it is not part of any future booking.
- Retrieve all unavailable dates for a service (limited) (i.e. dates during which the service cannot be booked).
- Assign a specific service to a specific room type.
- Remove a specific service from a specific room type.

**Only (super_admin):**
- Assign roles to users (user, admin, super_admin).
- Display all deleted room types.
- Display a specific deleted room type.
- Restore a specific deleted room type.
- Permanently delete a specific room type.
- Display all deleted rooms.
- Display a specific deleted room.
- Restore a specific deleted room.
- Permanently delete a specific room.
- Display all bookings for a specific room.
- Display all deleted services.
- Display a specific deleted service.
- Restore a specific deleted service.
- Permanently delete a specific service.
- Display all bookings for a specific service.
- Display all invoices.
- Display a specific invoice.
- Display all bookings associated with a specific invoice.
