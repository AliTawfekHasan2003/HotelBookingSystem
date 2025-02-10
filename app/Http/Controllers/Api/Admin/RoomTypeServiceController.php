<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomTypeServiceRequest;
use App\Models\RoomTypeService;
use App\Traits\ResponseTrait;

class RoomTypeServiceController extends Controller
{
    use ResponseTrait;

    /**   @OA\Post(
     *     path="/api/{role}/dashboard/room_type_services",
     *     summary="Assign Service To Room Type",
     *     tags={"Dashboard/RoomTypeServices"},
     *     operationId="assignServiceToRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Assign service to room type",
     *    @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"admin", "super_admin"})
     *    ),
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"room_type_id", "service_id"},
     *             @OA\Property(property="room_type_id", type="integer", example=1, description="Required. Must exist in the room types table."),
     *             @OA\Property(property="service_id", type="integer", example=2, description="Required. Must exist in the services table."),
     *          )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service assigned to room Type successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Service assigned to room Type successfully."),
     *             @OA\Property(property="code", type="integer", example=201),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(
     *                 property="msg",
     *                 type="object",
     *                @OA\Property(
     *                   property="room_type_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided room type does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="service_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided service does not exist in our records.")
     *                 )
     *             ) 
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Admins and Super Admins only can access this section."),
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
     *         response=409,
     *         description="Service has already been assigned to room type",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Service has already been assigned to room type."),
     *             @OA\Property(property="code", type="integer", example=409)
     *         )
     *     )
     * )
     */

    public function store(RoomTypeServiceRequest $request)
    {
        $isExists = RoomTypeService::findByIds($request->room_type_id, $request->service_id)->exists();

        if ($isExists) {
            return $this->returnError(__('errors.room_type_service.already_assign'), 409);
        }

        RoomTypeService::create([
            'room_type_id' => $request->room_type_id,
            'service_id' => $request->service_id,
        ]);

        return $this->returnSuccess(__('success.room_type_service.assign'), 201);
    }

    /**   @OA\Delete(
     *     path="/api/{role}/dashboard/room_type_services",
     *     summary="Revoke Service From Room Type",
     *     tags={"Dashboard/RoomTypeServices"},
     *     operationId="revokeServiceFromRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Assign service to room type",
     *    @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"admin", "super_admin"})
     *    ),
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *             required={"room_type_id", "service_id"},
     *             @OA\Property(property="room_type_id", type="integer", example=1, description="Required. Must exist in the room types table."),
     *             @OA\Property(property="service_id", type="integer", example=2, description="Required. Must exist in the services table."),
     *          )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service revoked from room Type successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Service revoked from room Type successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(
     *                 property="msg",
     *                 type="object",
     *                @OA\Property(
     *                   property="room_type_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided room type does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="service_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided service does not exist in our records.")
     *                 )
     *             ) 
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Unable access this section",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Admins and Super Admins only can access this section."),
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
     *         response=409,
     *         description="This service not assign to room type",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="This service not assign to room type."),
     *             @OA\Property(property="code", type="integer", example=409)
     *         )
     *     )
     * )
     */

    public function destroy(RoomTypeServiceRequest $request)
    {
        $roomTypeService = RoomTypeService::findByIds($request->room_type_id, $request->service_id)->first();

        if (!$roomTypeService) {
            return $this->returnError(__('errors.room_type_service.not_assign'), 409);
        }

        $roomTypeService->forceDelete();

        return $this->returnSuccess(__('success.room_type_service.revoke'));
    }
}
