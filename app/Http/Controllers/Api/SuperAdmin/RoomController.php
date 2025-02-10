<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\Admin\RoomController as AdminRoomController;
use App\Http\Resources\BookingResource;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\TranslationTrait;
use Illuminate\Http\Request;


class RoomController extends AdminRoomController
{
    use ResponseTrait, TranslationTrait, ImageTrait;

    /**   @OA\Get(
     *     path="/api/super_admin/dashboard/rooms/deleted",
     *     summary="Get List Of Deleted Rooms",
     *     tags={"Dashboard/Rooms"},
     *     operationId="getDeletedRooms",
     *     security={{"bearerAuth": {}}},
     *     description="Get all deleted rooms with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="number",
     *         in="query",
     *         description="Filter by number.",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="floor",
     *         in="query",
     *         description="Filter by floor",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="view",
     *         in="query",
     *         description="Filter by view",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get deleted rooms successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted rooms fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="rooms",
     *                 type="array",
     *                 description="List of deleted rooms",
     *                 @OA\Items(
     *                    type="object",
     *                    @OA\Property(property="id", type="integer", example=1),
     *                    @OA\Property(property="room_type_id", type="integer", example=23),
     *                    @OA\Property(property="floor", type="integer", example=2),
     *                    @OA\Property(property="number", type="integer", example=5),
     *                    @OA\Property(property="view", type="string", example="Sea view"),
     *                    @OA\Property(property="image", type="string", example="\/storage\/Image\/rooms\/1733570972_202858.jpg"),
     *                    @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
     *                 )
     *              ), 
     *             @OA\Property(
     *                 property="pagination",
     *                 type="object",
     *                 @OA\Property(
     *                     property="meta",
     *                     type="object",
     *                     @OA\Property(property="currentPage", type="integer", example=1),
     *                     @OA\Property(property="lastPage", type="integer", example=1),
     *                     @OA\Property(property="perPage", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                  ),
     *                  @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="currentPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1"),
     *                     @OA\Property(property="previousPageUrl", type="string", example=null),
     *                     @OA\Property(property="nextPageUrl", type="string", example=null),
     *                     @OA\Property(property="lastPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1")
     *                  )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No deleted rooms found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="No deleted rooms found matching the given criteria."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable perform this action",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Super Admins only can perform this action."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function trashedIndex(Request $request)
    {
        $result = Room::filterRooms($request, true);
        $query = $result['query'];
        $ifCriteria = $result['ifCriteria'];

        $rooms = $query->paginate(10);

        if ($rooms->isEmpty()) {
            if ($ifCriteria)
                return $this->returnError(__('errors.room.not_found_index_trashed_with_criteria'), 404);
            else
                return $this->returnError(__('errors.room.not_found_index_trashed'), 404);
        }

        return  $this->returnPaginationData(true, __('success.room.index_trashed'), 'rooms', RoomResource::collection($rooms));
    }

    /**   @OA\Get(
     *     path="/api/super_admin/dashboard/rooms/deleted/{id}",
     *     summary="Show Deleted Room",
     *     tags={"Dashboard/Rooms"},
     *     operationId="showDeletedRoom",
     *     security={{"bearerAuth": {}}},
     *     description="Get deleted room", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get deleted room successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted room fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="room",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="room_type_id", type="integer", example=23),
     *                 @OA\Property(property="floor", type="integer", example=2),
     *                 @OA\Property(property="number", type="integer", example=5),
     *                 @OA\Property(property="view", type="string", example="Sea view"),
     *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/rooms\/1733570972_202858.jpg"),
     *                 @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable perform this action",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Super Admins only can perform this action."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function trashedShow($id)
    {
        $room = Room::onlyTrashed()->find($id);

        if (!$room) {
            return $this->returnError(__('errors.room.not_found'), 404);
        }

        return $this->returnData(true, __('success.room.show_trashed'), 'room', new RoomResource($room));
    }

    /**   @OA\Post(
     *     path="/api/super_admin/dashboard/rooms/deleted/{id}/restore",
     *     summary="Restore Deleted Room",
     *     tags={"Dashboard/Rooms"},
     *     operationId="restoreDeletedRoom",
     *     security={{"bearerAuth": {}}},
     *     description="Restore deleted room", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Deleted room restored from deletion successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted room restored from deletion successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable perform this action",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Super Admins only can perform this action."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unable to restore this room type."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function trashedRestore($id)
    {
        $room = Room::onlyTrashed()->find($id);

        if (!$room) {
            return $this->returnError(__('errors.room.not_found'), 404);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('restore', $room);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.room.restore'), 500);
        }
        $room->restore();

        return $this->returnSuccess(__('success.room.restore'));
    }

    /**  @OA\Delete(
     *     path="/api/super_admin/dashboard/rooms/deleted/{id}/force",
     *     summary="Force Delete Room",
     *     tags={"Dashboard/Rooms"},
     *     operationId="forceDeleteRoom",
     *     security={{"bearerAuth": {}}},
     *     description="Force delete room", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room permanently deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room permanently deleted successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable perform this action",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Super Admins only can perform this action."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unable to permanently delete this room."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function trashedForceDelete($id)
    {
        $room = Room::onlyTrashed()->find($id);

        if (!$room) {
            return $this->returnError(__('errors.room.not_found'), 404);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('force', $room);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.room.force_delete'), 500);
        }
        $this->imageDelete($room->image);
        $room->forceDelete();

        return $this->returnSuccess(__('success.room.force_delete'));
    }

    /**  @OA\Get(
     *     path="/api/super_admin/dashboard/rooms/{id}/bookings",
     *     summary="Get List Of Bookings For Room",
     *     tags={"Dashboard/Rooms"},
     *     operationId="getBookingsRoom",
     *     security={{"bearerAuth": {}}},
     *     description="Get all bookings for room with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Response(
     *         response=200,
     *         description="Get bookings for this room successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Bookings for this room fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="bookings",
     *                 type="array",
     *                 description="List of bookings",
     *                 @OA\Items(
     *                    type="object",
     *                    @OA\Property(property="id", type="integer", example=1),
     *                    @OA\Property(property="invoice_id", type="integer", example=12),
     *                    @OA\Property(property="bookingable_type", type="string", example="room"),
     *                    @OA\Property(property="bookingable_id", type="integer", example=5),
     *                    @OA\Property(property="original_monthly_price", type="float", example=234.55),
     *                    @OA\Property(property="original_daily_price", type="float", example=2345.89),
     *                    @OA\Property(property="booking_cost", type="float", example=666676.78),
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
     *                     @OA\Property(property="perPage", type="integer", example=10),
     *                     @OA\Property(property="total", type="integer", example=5)
     *                  ),
     *                  @OA\Property(
     *                     property="links",
     *                     type="object",
     *                     @OA\Property(property="currentPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1"),
     *                     @OA\Property(property="previousPageUrl", type="string", example=null),
     *                     @OA\Property(property="nextPageUrl", type="string", example=null),
     *                     @OA\Property(property="lastPageUrl", type="string", example="http://127.0.0.1:8000/api/notifications?page=1")
     *                  )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Room not found or No bookings found for this room",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="No bookings found for this room."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable perform this action",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Super Admins only can perform this action."),
     *             @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function bookings($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return $this->returnError(__('errors.room.not_found'), 404);
        }

        $bookings = $room->bookings()->paginate(10);

        if ($bookings->isEmpty()) {
            return $this->returnError(__('errors.room.not_found_bookings'), 404);
        }

        return $this->returnPaginationData(true, __('success.room.bookings'), 'bookings', BookingResource::collection($bookings));
    }
}
