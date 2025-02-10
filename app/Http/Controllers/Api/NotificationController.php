<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    use ResponseTrait;

    public $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/{role}/notifications",
     *     summary="List Of All Notifications",
     *     tags={"Notifications"},
     *     operationId="getAllNotifications",
     *     security={{"bearerAuth": {}}},
     *  @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (user, admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
     *     ),
     *  @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get all notifications with pagination successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="All notifications fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="notifications",
     *                 type="array",
     *                 description="List of notifications",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="87755360-b9b8-4077-824d-27045dd9cddd"),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Dynamic notification data",
     *                         @OA\Property(property="message", type="string", example="تم دفع فاتورة جديدة بنجاح."),
     *                         @OA\AdditionalProperties(type="string", example="value")
     *                     ),
     *                     @OA\Property(property="read", type="string", enum={"yes", "no"}, example="yes"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-11T20:48:46.000000Z")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="currentPage", type="integer", example=1),
     *                     @OA\Property(property="lastPage", type="integer", example=1),
     *                     @OA\Property(property="perPage", type="integer", example=20),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                 ),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="currentPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1"),
     *                     @OA\Property(property="previousPageUrl", type="string", example=null),
     *                     @OA\Property(property="nextPageUrl", type="string", example=null),
     *                     @OA\Property(property="lastPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function getAllNotifications()
    {
        $notifications = $this->user->notifications()->paginate(20);

        return $this->returnPaginationData(true, __('success.notification.get_all_notifications'), 'notifications', NotificationResource::collection($notifications));
    }

    /**
     * @OA\Get(
     *     path="/api/{role}/notifications/unread",
     *     summary="List Of Unread Notifications",
     *     tags={"Notifications"},
     *     operationId="getUnreadNotifications",
     *     security={{"bearerAuth": {}}},
     *  @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (user, admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
     *     ),
     *  @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get unread notifications with pagination successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Unread notifications fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="notifications",
     *                 type="array",
     *                 description="List of unread notifications",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="string", example="87755360-b9b8-4077-824d-27045dd9cddd"),
     *                     @OA\Property(
     *                         property="data",
     *                         type="object",
     *                         description="Dynamic notification data",
     *                         @OA\Property(property="message", type="string", example="تم دفع فاتورة جديدة بنجاح."),
     *                         @OA\AdditionalProperties(type="string", example="value")
     *                     ),
     *                     @OA\Property(property="read", type="string", enum={"yes", "no"}, example="no", description="must take only no"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-11T20:48:46.000000Z")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="currentPage", type="integer", example=1),
     *                     @OA\Property(property="lastPage", type="integer", example=1),
     *                     @OA\Property(property="perPage", type="integer", example=20),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                 ),
     *                 @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="currentPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1"),
     *                     @OA\Property(property="previousPageUrl", type="string", example=null),
     *                     @OA\Property(property="nextPageUrl", type="string", example=null),
     *                     @OA\Property(property="lastPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function getUnreadNotifications()
    {
        $unreadNotifications = $this->user->unreadNotifications()->paginate(20);

        return $this->returnPaginationData(true, __('success.notification.get_unread_notifications'), 'unreadNotifications', NotificationResource::collection($unreadNotifications));
    }

    /**
     * @OA\Patch(
     *     path="/api/{role}/notifications/unread/{id}/mark_as_read",
     *     summary="Mark Notification As Read",
     *     tags={"Notifications"},
     *     operationId="markNotificationAsRead",
     *     security={{"bearerAuth": {}}},
     *  @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (user, admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
     *     ),
     *  @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *   @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The notification ID to make it readable",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="The notification marked as read successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="The notification read successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Notification not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="msg", type="string", example="Unread notification not found."),
     *              @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *           response=401,
     *           description="Unauthorized: Invalid token or expired",
     *           @OA\JsonContent(
     *               @OA\Property(property="status", type="boolean", example=false),
     *               @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *               @OA\Property(property="code", type="integer", example=401)
     *           )
     *     )
     * )
     */

    public function markAsRead($id)
    {
        $isRead = $this->user->unreadNotifications()->where('id', $id)->update(['read_at' => Carbon::now()]);

        if ($isRead === 1) {
            return $this->returnSuccess(__('success.notification.markAsRead'));
        }

        return $this->returnError(__('errors.notification.not_found_unread_notification'), 404);
    }

    /**
     * @OA\Patch(
     *     path="/api/{role}/notifications/unread/mark_as_read",
     *     summary="Mark All Unread Notifications As Read",
     *     tags={"Notifications"},
     *     operationId="markNotificationsAsRead",
     *     security={{"bearerAuth": {}}},
     *  @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (user, admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
     *     ),
     *  @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="All notifications marked as read successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="All unread notifications read successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Unread notifications not found.",
     *          @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(property="msg", type="string", example="Unread notifications not found."),
     *              @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *           response=401,
     *           description="Unauthorized: Invalid token or expired",
     *           @OA\JsonContent(
     *               @OA\Property(property="status", type="boolean", example=false),
     *               @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *               @OA\Property(property="code", type="integer", example=401)
     *           )
     *     )
     * )
     */

    public function markAllAsRead()
    {
        $countReadNotifications = $this->user->unreadNotifications()->update(['read_at' => Carbon::now()]);;

        if ($countReadNotifications > 0) {
            return $this->returnSuccess(__('success.notification.markAllAsRead'));
        }

        return $this->returnError(__('errors.notification.not_found_unread_notifications'), 404);
    }
}
