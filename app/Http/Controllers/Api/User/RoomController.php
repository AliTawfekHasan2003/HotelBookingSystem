<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\BaseRoomController;
use App\Models\Room;
use App\Traits\ResponseTrait;
use App\Http\Resources\RoomResource;
use App\Models\Favorite;

class RoomController extends BaseRoomController
{
    use ResponseTrait;

    /**   @OA\Get(
     *     path="/api/user/rooms/favorite",
     *     summary="Get List Of Favorite Rooms",
     *     tags={"Rooms"},
     *     operationId="getFavoriteRooms",
     *     security={{"bearerAuth": {}}},
     *     description="Get all favorite rooms of user with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Get favorite rooms successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Favorite rooms fetched successfully."),
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
     *                  ) 
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
     *         description="Not rooms found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="The favorites list does not contain any rooms."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="You don’t have permission to access this section."),
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

    public function getFavorite()
    {
        $rooms = Room::whereHas('favorites', function ($query) {
            return $query->byUser(auth()->id());
        })->paginate(10);

        if ($rooms->isEmpty()) {
            return $this->returnError(__('errors.room.not_found_favorite'), 404);
        }

        return  $this->returnPaginationData(true, __('success.room.favorite'), 'rooms', RoomResource::collection($rooms));
    }

    /**   @OA\Post(
     *     path="/api/user/rooms/{id}/favorite/mark_as_favorite",
     *     summary="Mark Room As Favorite",
     *     tags={"Rooms"},
     *     operationId="markRoomAsFavorite",
     *     security={{"bearerAuth": {}}},
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
     *         description="Room ID to make it favorite",
     *         @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=201,
     *         description="Room added to favorites successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room added to favorites successfully."),
     *             @OA\Property(property="code", type="integer", example=201),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found room",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Room has already been added to favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room has already been added to favorites."),
     *             @OA\Property(property="code", type="integer", example=409)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="You don’t have permission to access this section."),
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

    public function markAsFavorite($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return $this->returnError(__('errors.room.not_found'), 404);
        }

        $checkInFavorite = $room->favorites()->checkIn();

        if ($checkInFavorite) {
            return $this->returnError(__('errors.room.already_favorite'), 409);
        }

        Favorite::addFavorite(['type' => 'room', 'id' => $room->id]);

        return $this->returnSuccess(__('success.room.add_to_favorite'), 201);
    }

    /**   @OA\Delete(
     *     path="/api/user/rooms/{id}/favorite/unmark_as_favorite",
     *     summary="Unmark Room As Favorite",
     *     tags={"Rooms"},
     *     operationId="unmarkRoomAsFavorite",
     *     security={{"bearerAuth": {}}},
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
     *         description="Room ID to delete from favorites",
     *         @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Room deleted from favorites successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room deleted from favorites successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found room",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Room not in favorites.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room not in favorites."),
     *             @OA\Property(property="code", type="integer", example=409)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="You don’t have permission to access this section."),
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

    public function unmarkAsFavorite($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return $this->returnError(__('errors.room.not_found'), 404);
        }

        $checkInFavorite = $room->favorites()->checkIn();
        
        if (!$checkInFavorite) {
            return $this->returnError(__('errors.room.not_in_favorite'), 409);
        }

        Favorite::destroyFavorite(['type' => 'room', 'id' => $room->id]);

        return $this->returnSuccess(__('success.room.delete_from_favorite'));
    }
}
