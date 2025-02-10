<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\BaseRoomTypeController;
use App\Http\Resources\RoomTypeResource;
use App\Models\Favorite;
use App\Models\RoomType;
use App\Traits\ResponseTrait;

class RoomTypeController extends BaseRoomTypeController
{
    use ResponseTrait;

    /**   @OA\Get(
     *     path="/api/user/room_types/favorite",
     *     summary="Get List Of Favorite Room Types",
     *     tags={"Room Types"},
     *     operationId="getFavoriteRoomTypes",
     *     security={{"bearerAuth": {}}},
     *     description="Get all favorite room types of user with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Get favorite room types successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Favorite room types fetched successfully."),
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
     *                   @OA\Property(property="monthly_price", type="float", format="date-time", nullable=true, example=33333.00),
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
     *         description="Not room types found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="The favorites list doesn’t contain any room type."),
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
        $roomTypes = RoomType::whereHas('favorites', function ($query) {
            return $query->byUser(auth()->id());
        })->paginate(10);

        if ($roomTypes->isEmpty()) {
            return $this->returnError(__('errors.room_type.not_found_favorite'), 404);
        }

        return  $this->returnPaginationData(true, __('success.room_type.favorite'), 'roomTypes', RoomTypeResource::collection($roomTypes));
    }

    /**   @OA\Post(
     *     path="/api/user/room_types/{id}/favorite/mark_as_favorite",
     *     summary="Mark Room Type As Favorite",
     *     tags={"Room Types"},
     *     operationId="markRoomTypeAsFavorite",
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
     *         description="Room type ID to make it favorite",
     *         @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=201,
     *         description="Room type added to favorites successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room type added to favorites successfully."),
     *             @OA\Property(property="code", type="integer", example=201),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found room type",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room type not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Room type has already been added to favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room type has already been added to favorites."),
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
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return $this->returnError(__('errors.room_type.not_found'), 404);
        }

        $checkInFavorite = $roomType->favorites()->checkIn();

        if ($checkInFavorite) {
            return $this->returnError(__('errors.room_type.already_favorite'), 409);
        }

        Favorite::addFavorite(['type' => 'roomType', 'id' => $roomType->id]);

        return $this->returnSuccess(__('success.room_type.add_to_favorite'), 201);
    }


    /**   @OA\Delete(
     *     path="/api/user/room_types/{id}/favorite/unmark_as_favorite",
     *     summary="Unmark Room Type As Favorite",
     *     tags={"Room Types"},
     *     operationId="unmarkRoomTypeAsFavorite",
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
     *         description="Room type ID to delete from favorites",
     *         @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Room type deleted from favorites successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room type deleted from favorites successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found room type",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room type not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Room type not in favorites.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Room type not in favorites."),
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
        $roomType = RoomType::find($id);

        if (!$roomType) {
            return $this->returnError(__('errors.room_type.not_found'), 404);
        }

        $checkInFavorite = $roomType->favorites()->checkIn();

        if (!$checkInFavorite) {
            return $this->returnError(__('errors.room_type.not_in_favorite'), 409);
        }

        Favorite::destroyFavorite(['type' => 'roomType', 'id' => $roomType->id]);

        return $this->returnSuccess(__('success.room_type.delete_from_favorite'));
    }
}
