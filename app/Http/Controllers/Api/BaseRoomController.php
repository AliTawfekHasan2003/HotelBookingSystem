<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Http\Resources\RoomResource;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BaseRoomController extends Controller
{
  use ResponseTrait;

  /**   @OA\Get(
   *     path="/api/{role}/rooms",
   *     summary="Get List Of Rooms",
   *     tags={"Rooms"},
   *     operationId="getRooms",
   *     security={{"bearerAuth": {}}},
   *     description="Get all rooms with 10 pagination", 
   *    @OA\Parameter(
   *         name="role",
   *         in="path",
   *         description="User role (user, admin, super_admin)",
   *         required=true,
   *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
   *    ),
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
   *         description="Get rooms successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Rooms fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="rooms",
   *                 type="array",
   *                 description="List of rooms",
   *                 @OA\Items(
   *                   type="object",
   *                   @OA\Property(property="id", type="integer", example=1),
   *                   @OA\Property(property="room_type_id", type="integer", example=23),
   *                   @OA\Property(property="floor", type="integer", example=2),
   *                   @OA\Property(property="number", type="integer", example=5),
   *                   @OA\Property(property="view", type="string", example="Sea view"),
   *                   @OA\Property(property="image", type="string", example="\/storage\/Image\/rooms\/1733570972_202858.jpg"),
   *                   @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
   *                )
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
   *         description="No rooms found",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="No rooms found matching the given criteria."),
   *             @OA\Property(property="code", type="integer", example=404)
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

  public function index(Request $request)
  {
    $result = Room::filterRooms($request);
    $query = $result['query'];
    $ifCriteria = $result['ifCriteria'];

    $rooms = $query->paginate(10);

    if ($rooms->isEmpty()) {
      if ($ifCriteria)
        return $this->returnError(__('errors.room.not_found_index_with_criteria'), 404);
      else
        return $this->returnError(__('errors.room.not_found_index'), 404);
    }

    return  $this->returnPaginationData(true, __('success.room.index'), 'rooms', RoomResource::collection($rooms));
  }

  /**   @OA\Get(
   *     path="/api/{role}/rooms/{id}",
   *     summary="Show Room",
   *     tags={"Rooms"},
   *     operationId="showRoom",
   *     security={{"bearerAuth": {}}},
   *     description="Get room", 
   *    @OA\Parameter(
   *         name="role",
   *         in="path",
   *         description="User role (user, admin, super_admin)",
   *         required=true,
   *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
   *    ),
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
   *         description="Get room successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Room fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="room",
   *                 type="object",
   *                   @OA\Property(property="id", type="integer", example=1),
   *                   @OA\Property(property="room_type_id", type="integer", example=23),
   *                   @OA\Property(property="floor", type="integer", example=2),
   *                   @OA\Property(property="number", type="integer", example=5),
   *                   @OA\Property(property="view", type="string", example="Sea view"),
   *                   @OA\Property(property="image", type="string", example="\/storage\/Image\/rooms\/1733570972_202858.jpg"),
   *                   @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
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

  public function show($id)
  {
    $room = Room::with('translations')->find($id);

    if (!$room) {
      return $this->returnError(__('errors.room.not_found'), 404);
    }

    return $this->returnData(true, __('success.room.show'), 'room', new RoomResource($room));
  }

  /**   @OA\Get(
   *     path="/api/{role}/rooms/{id}/unavailable_dates",
   *     summary="Get Unavailable Dates",
   *     tags={"Rooms"},
   *     operationId="getUnavailableDatesForRoom",
   *     security={{"bearerAuth": {}}},
   *     description="List of unavailable dates for room", 
   *    @OA\Parameter(
   *         name="role",
   *         in="path",
   *         description="User role (user, admin, super_admin)",
   *         required=true,
   *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
   *    ),
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
   *         description="Get unavailable dates for room successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Unavailable dates for this room fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="unavailableDates",
   *                 type="array",
   *                 description="List of unavailable dates for room",
   *                 @OA\Items(
   *                   type="object",
   *                   @OA\Property(property="start_date", type="string", format="date-time", example="2025-01-12T00:00:00.000000Z"),
   *                   @OA\Property(property="end_date", type="string", format="date-time", example="2025-02-22T00:00:00.000000Z")
   *                )
   *             )
   *        )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Room not found or No unavailable dates found for room.",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="No unavailable dates found for this room."),
   *             @OA\Property(property="code", type="integer", example=404)
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

  public function unavailableDates($id)
  {
    $room = Room::find($id);

    if (!$room) {
      return $this->returnError(__('errors.room.not_found'), 404);
    }

    $unavailableDates = $room->bookings()->getUnavailableDates();

    if ($unavailableDates->isEmpty()) {
      return $this->returnError(__('errors.room.not_found_dates'), 404);
    }

    return $this->returnData(true, __('success.room.unavailable_dates'), 'unavailableDates', $unavailableDates);
  }
}
