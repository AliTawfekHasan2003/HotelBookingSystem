<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\RoomResource;
use App\Http\Resources\RoomTypeResource;
use App\Http\Resources\ServiceResource;
use App\Models\RoomType;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BaseRoomTypeController extends Controller
{
  use ResponseTrait;

  /**   @OA\Get(
   *     path="/api/{role}/room_types",
   *     summary="Get List Of Room Types",
   *     tags={"Room Types"},
   *     operationId="getRoomTypes",
   *     security={{"bearerAuth": {}}},
   *     description="Get all room types with 10 pagination", 
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
   *         name="capacity",
   *         in="query",
   *         description="Filter by capacity.",
   *         required=false,
   *         @OA\Schema(type="integer", description=" Get room types that have a capacity greater than or equal to the given value")
   *     ),
   *     @OA\Parameter(
   *         name="name",
   *         in="query",
   *         description="Filter by name",
   *         required=false,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Parameter(
   *         name="category",
   *         in="query",
   *         description="Filter by category",
   *         required=false,
   *         @OA\Schema(type="string")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Get room types successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Room types fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="roomTypes",
   *                 type="array",
   *                 description="List of room types",
   *                 @OA\Items(
   *                   type="object",
   *                   @OA\Property(property="id", type="integer", example=1),
   *                   @OA\Property(property="name", type="string", example="royal rooms"),
   *                   @OA\Property(property="category", type="string", example="master"),
   *                   @OA\Property(property="capacity", type="integer", example=5),
   *                   @OA\Property(property="image", type="string", example="\/storage\/Image\/room_types\/1733570972_202858.jpg"),
   *                   @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
   *                   @OA\Property(property="count_rooms", type="integer", example=4),
   *                   @OA\Property(property="daily_price", type="float", example=12222.45),
   *                   @OA\Property(property="monthly_price", type="float", example=33333.00),
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
   *         description="No room types found",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="No room types found matching the given criteria."),
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
    $result = RoomType::filterRoomTypes($request);
    $query = $result['query'];
    $ifCriteria = $result['ifCriteria'];

    $roomTypes = $query->paginate(10);

    if ($roomTypes->isEmpty()) {
      if ($ifCriteria)
        return $this->returnError(__('errors.room_type.not_found_index_with_criteria'), 404);
      else
        return $this->returnError(__('errors.room_type.not_found_index'), 404);
    }

    return  $this->returnPaginationData(true, __('success.room_type.index'), 'roomTypes', RoomTypeResource::collection($roomTypes));
  }

  /**   @OA\Get(
   *     path="/api/{role}/room_types/{id}",
   *     summary="Show Room Type",
   *     tags={"Room Types"},
   *     operationId="showRoomType",
   *     security={{"bearerAuth": {}}},
   *     description="Get room type", 
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
   *         description="Room type ID",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Get room type successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Room type fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="roomType",
   *                 type="object",
   *                 @OA\Property(property="id", type="integer", example=1),
   *                 @OA\Property(property="name", type="string", example="royal rooms"),
   *                 @OA\Property(property="category", type="string", example="master"),
   *                 @OA\Property(property="capacity", type="integer", example=5),
   *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/room_types\/1733570972_202858.jpg"),
   *                 @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
   *                 @OA\Property(property="count_rooms", type="integer", example=4),
   *                 @OA\Property(property="daily_price", type="float", example=12222.45),
   *                 @OA\Property(property="monthly_price", type="float", example=33333.00),
   *             ) 
   *         )
   *     ),
   *     @OA\Response(
   *         response=404,
   *         description="Room type not found",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="Room type not found."),
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
    $roomType = RoomType::find($id);

    if (!$roomType) {
      return $this->returnError(__('errors.room_type.not_found'), 404);
    }

    return $this->returnData(true, __('success.room_type.show'), 'roomType', new RoomTypeResource($roomType));
  }

  /**   @OA\Get(
   *     path="/api/{role}/room_types/{id}/rooms",
   *     summary="Get List Of Rooms That Belong To Specific Room Type",
   *     tags={"Room Types"},
   *     operationId="getRoomsForType",
   *     security={{"bearerAuth": {}}},
   *     description="Get all rooms that belong to specific room type with 10 pagination", 
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
   *     ),
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         required=true,
   *         description="Room type ID",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Get Rooms for this type successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Rooms for this type fetched successfully."),
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
   *         description="Not room type or rooms for this type found",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="No rooms found for this type."),
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

  public function rooms($id)
  {
    $roomType = RoomType::find($id);

    if (!$roomType) {
      return $this->returnError(__('errors.room_type.not_found'), 404);
    }

    $rooms = $roomType->rooms()->paginate(10);

    if ($rooms->isEmpty()) {
      return $this->returnError(__('errors.room_type.not_found_rooms'), 404);
    }

    return $this->returnPaginationData(true, __('success.room_type.rooms'), 'rooms',  RoomResource::collection($rooms));
  }

  /**   @OA\Get(
   *     path="/api/{role}/room_types/{id}/services",
   *     summary="Get List Of Services That Assigned To Specific Room Type",
   *     tags={"Room Types"},
   *     operationId="getservicesForType",
   *     security={{"bearerAuth": {}}},
   *     description="Get all services that assigned to specific room type with 10 pagination", 
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
   *     ),
   *     @OA\Parameter(
   *         name="id",
   *         in="path",
   *         required=true,
   *         description="Room type ID",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Get services for this type successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Services for this type fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="services",
   *                 type="array",
   *                 description="List of services",
   *                @OA\Items(
   *                 type="object",
   *                 @OA\Property(property="id", type="integer", example=1),
   *                 @OA\Property(property="name", type="string", example="parking service"),
   *                 @OA\Property(property="category", type="string", example="cars"),
   *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/services\/1733570972_202858.jpg"),
   *                 @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
   *                 @OA\Property(property="is_limited", type="boolean", example=1),
   *                 @OA\Property(property="total_units", type="integer", example=7, description="Total units for service if its limited"),
   *                 @OA\Property(property="is_free", type="boolean", example=1),
   *                 @OA\Property(property="daily_price", type="float", example=12222.45),
   *                 @OA\Property(property="monthly_price", type="float", example=33333.00),
   *               )   
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
   *         description="Not room type or services for this type found",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="No services found for this type."),
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

  public function services($id)
  {
    $roomType = RoomType::find($id);

    if (!$roomType) {
      return $this->returnError(__('errors.room_type.not_found'), 404);
    }

    $services = $roomType->services()->paginate(10);

    if ($services->isEmpty()) {
      return $this->returnError(__('errors.room_type.not_found_services'), 404);
    }

    return $this->returnPaginationData(true, __('success.room_type.services'), 'services',  ServiceResource::collection($services));
  }
}
