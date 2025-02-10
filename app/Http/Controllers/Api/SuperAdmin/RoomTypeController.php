<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\Admin\RoomTypeController as AdminRoomTypeController;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\TranslationTrait;
use Illuminate\Http\Request;

class RoomTypeController extends AdminRoomTypeController
{
    use ResponseTrait, TranslationTrait, ImageTrait;

    /**   @OA\Get(
     *     path="/api/super_admin/dashboard/room_types/deleted",
     *     summary="Get List Of Deleted Room Types",
     *     tags={"Dashboard/RoomTypes"},
     *     operationId="getDeletedRoomTypes",
     *     security={{"bearerAuth": {}}},
     *     description="Get all deleted room types with 10 pagination", 
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
     *         description="Filter by capacity",
     *         required=false,
     *         @OA\Schema(type="integer", description="Get room types that have a capacity greater than or equal to the given value")
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
     *         description="Get deleted room types successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted room types fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="roomTypes",
     *                 type="array",
     *                 description="List of deleted room types",
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
     *         description="No deleted room types found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="No deleted room types found matching the given criteria."),
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
        $result = RoomType::filterRoomTypes($request, true);
        $query = $result['query'];
        $ifCriteria = $result['ifCriteria'];

        $roomTypes = $query->paginate(10);

        if ($roomTypes->isEmpty()) {
            if ($ifCriteria)
                return $this->returnError(__('errors.room_type.not_found_index_trashed_with_criteria'), 404);
            else
                return $this->returnError(__('errors.room_type.not_found_index_trashed'), 404);
        }

        return  $this->returnPaginationData(true, __('success.room_type.index_trashed'), 'roomTypes', RoomTypeResource::collection($roomTypes));
    }

    /**   @OA\Get(
     *     path="/api/super_admin/dashboard/room_types/deleted/{id}",
     *     summary="Show Deleted Room Type",
     *     tags={"Dashboard/RoomTypes"},
     *     operationId="showDeletedRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Get deleted room type", 
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
     *         description="Get deleted room type successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted room type fetched successfully."),
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
        $roomType = RoomType::onlyTrashed()->find($id);

        if (!$roomType) {
            return $this->returnError(__('errors.room_type.not_found'), 404);
        }

        return $this->returnData(true, __('success.room_type.show_trashed'), 'roomType', new RoomTypeResource($roomType));
    }

    /**   @OA\Post(
     *     path="/api/super_admin/dashboard/room_types/deleted/{id}/restore",
     *     summary="Restore Deleted Room Type",
     *     tags={"Dashboard/RoomTypes"},
     *     operationId="restoreDeletedRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Restore deleted room type", 
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
     *         description="Deleted room type restored from deletion successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted room type restored from deletion successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
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
        $roomType = RoomType::onlyTrashed()->find($id);

        if (!$roomType) {
            return $this->returnError(__('errors.room_type.not_found'), 404);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('restore', $roomType);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.room_type.restore'), 500);
        }

        $roomType->roomTypeServices()->whereHas('service', function ($query) {
            return $query->whereNull('deleted_at');
        })->onlyTrashed()->restore();

        $roomType->restore();

        return $this->returnSuccess(__('success.room_type.restore'));
    }

    /**   @OA\Delete(
     *     path="/api/super_admin/dashboard/room_types/deleted/{id}/force",
     *     summary="Force Delete Room Type",
     *     tags={"Dashboard/RoomTypes"},
     *     operationId="forceDeleteRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Force delete room type", 
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
     *         description="Room type permanently deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room type permanently deleted successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
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
     *             @OA\Property(property="msg", type="string", example="Unable to permanently delete this room type."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function trashedForceDelete($id)
    {
        $roomType = RoomType::onlyTrashed()->find($id);

        if (!$roomType) {
            return $this->returnError(__('errors.room_type.not_found'), 404);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('force', $roomType);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.room_type.force_delete'), 500);
        }

        $roomType->roomTypeServices()->onlyTrashed()->forceDelete();;
        $this->imageDelete($roomType->image);
        $roomType->forceDelete();

        return $this->returnSuccess(__('success.room_type.force_delete'));
    }
}
