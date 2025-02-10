<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseRoomController as ApiBaseRoomController;
use App\Http\Requests\StoreOrUpdateRoomRequest;
use App\Http\Resources\RoomResource;
use App\Models\Room;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\TranslationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RoomController extends ApiBaseRoomController
{
    use ResponseTrait, TranslationTrait, ImageTrait;

    /**   @OA\Post(
     *     path="/api/{role}/dashboard/rooms",
     *     summary="Store New Room",
     *     tags={"Dashboard/Rooms"},
     *     operationId="storeRoom",
     *     security={{"bearerAuth": {}}},
     *     description="Store room",
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
     *          description="Room details",
     *          @OA\MediaType(
     *            mediaType="multipart/form-data",
     *             @OA\Schema(
     *             required={"room_type_id", "view_en", "view_ar", "description_en", "description_ar", "floor", "number", "image"},
     *             @OA\Property(property="room_type_id", type="integer", example=23, description="Required. Must exist in the room types table."),
     *             @OA\Property(property="view_en", type="string", example="City View", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="view_ar", type="string", example="إطلالة على المدينة", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="description_en", type="string", example="A cozy standard room featuring a comfortable double bed, a private bathroom, and a workspace. Ideal for business travelers and short stays", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=10."),
     *             @OA\Property(property="description_ar", type="string", example="غرفة قياسية مريحة تحتوي على سرير مزدوج مريح، حمام خاص، ومنطقة عمل. مثالية للمسافرين من رجال الأعمال والإقامات القصيرة", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=10."),
     *             @OA\Property(property="floor", type="integer", example=5, description="Required. The combination of floor and room number must be unique.Number of room. Length: min=1, max=50."),
     *             @OA\Property(property="number", type="integer", example=3, description="Required. The combination of floor and room number must be unique. Length: min=1, max=50."),
     *             @OA\Property(property="image", type="string", format="binary", description="Required. Image file. Max size: 2MB.")
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Room created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="New room created successfully."),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(
     *                 property="room",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="room_type_id", type="integer", example=23),
     *                 @OA\Property(property="floor", type="integer", example=5),
     *                 @OA\Property(property="number", type="integer", example=3),
     *                 @OA\Property(property="view", type="string", example="City View"),
     *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/rooms\/1733570972_202858.jpg"),
     *                 @OA\Property(property="description", type="string", example="A cozy standard room featuring a comfortable double bed, a private bathroom, and a workspace. Ideal for business travelers and short stays"),
     *             ) 
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
     *             @OA\Property(
     *                   property="room_type_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided room type does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="view_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="view_ar",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،).")
     *                 ),
     *                 @OA\Property(
     *                   property="description_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="description_ar",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،).")
     *                 ),
     *                 @OA\Property(
     *                   property="number",
     *                   type="array",
     *                   @OA\Items(type="string", example="The combination of floor and room number must be unique.")
     *                 ),
     *                 @OA\Property(
     *                   property="floor",
     *                   type="array",
     *                   @OA\Items(type="string", example="The combination of floor and room number must be unique.")
     *                ),
     *                @OA\Property(
     *                   property="image",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be an image.")
     *                )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
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
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="An unexpected error occurred, Please try again later."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function store(StoreOrUpdateRoomRequest $request)
    {
        DB::beginTransaction();
        try {
            $imagePath = $this->imageStore($request->image, 'rooms');

            $room = Room::create([
                'room_type_id' => $request->room_type_id,
                'floor' => $request->floor,
                'number' => $request->number,
                'image' => $imagePath,
            ]);

            $room->translations()->createMany([
                [
                    'attribute' => 'view',
                    'value' => $request->view_en,
                    'language' => 'en'
                ],
                [
                    'attribute' => 'view',
                    'value' => $request->view_ar,
                    'language' => 'ar'
                ],
                [
                    'attribute' => 'description',
                    'value' => $request->description_en,
                    'language' => 'en'
                ],
                [
                    'attribute' => 'description',
                    'value' => $request->description_ar,
                    'language' => 'ar'
                ],
            ]);
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Failed to create new room:" . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        DB::commit();

        return $this->returnData(true, __('success.room.create'), 'room', new RoomResource($room), 201);
    }

    /**  @OA\Post(
     *     path="/api/{role}/dashboard/rooms/{id}",
     *     summary="Update Room",
     *     tags={"Dashboard/Rooms"},
     *     operationId="updateRoom",
     *     security={{"bearerAuth": {}}},
     *     description="Update room",
     *    @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"admin", "super_admin"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *          required=true,
     *          description="Room details.",
     *          @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *             @OA\Property(property="room_type_id", type="integer", example=23, description="Nullable. Must exist in the room types table."),
     *             @OA\Property(property="view_en", type="string", example="City View", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="view_ar", type="string", example="إطلالة على المدينة", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="description_en", type="string", example="A cozy standard room featuring a comfortable double bed, a private bathroom, and a workspace. Ideal for business travelers and short stays", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=10."),
     *             @OA\Property(property="description_ar", type="string", example="غرفة قياسية مريحة تحتوي على سرير مزدوج مريح، حمام خاص، ومنطقة عمل. مثالية للمسافرين من رجال الأعمال والإقامات القصيرة", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=10."),
     *             @OA\Property(property="floor", type="integer", example=5, description="Nullable. The combination of floor and room number must be unique.Number of room. Length: min=1, max=50."),
     *             @OA\Property(property="number", type="integer", example=3, description="Nullable. The combination of floor and room number must be unique. Length: min=1, max=50."),
     *             @OA\Property(property="image", type="string", format="binary", description="Nullable. Image file. Max size: 2MB."),
     *             @OA\Property(property="_method", type="string", description="Override method to PATCH", default = "Patch")
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room updated successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="room",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="room_type_id", type="integer", example=23),
     *                 @OA\Property(property="floor", type="integer", example=5),
     *                 @OA\Property(property="number", type="integer", example=3),
     *                 @OA\Property(property="view", type="string", example="City View"),
     *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/rooms\/1733570972_202858.jpg"),
     *                 @OA\Property(property="description", type="string", example="A cozy standard room featuring a comfortable double bed, a private bathroom, and a workspace. Ideal for business travelers and short stays"),
     *             ) 
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
     *             @OA\Property(
     *                   property="room_type_id",
     *                   type="array",
     *                   @OA\Items(type="string", example="The provided room type does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                   property="view_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="view_ar",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،).")
     *                 ),
     *                 @OA\Property(
     *                   property="description_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="description_ar",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،).")
     *                 ),
     *                 @OA\Property(
     *                   property="number",
     *                   type="array",
     *                   @OA\Items(type="string", example="The combination of floor and room number must be unique.")
     *                 ),
     *                 @OA\Property(
     *                   property="floor",
     *                   type="array",
     *                   @OA\Items(type="string", example="The combination of floor and room number must be unique.")
     *                ),
     *                @OA\Property(
     *                   property="image",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be an image.")
     *                )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="An unexpected error occurred, Please try again later."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function update(StoreOrUpdateRoomRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $room = Room::find($id);
            if (!$room) {
                return $this->returnError(__('errors.room.not_found'), 404);
            }

            if ($request->image) {
                $oldImagePath = $room->image;
                $newImagePath = $this->imageReplace($oldImagePath, $request->image, 'rooms');
            }

            $room->update([
                'room_type_id' => $request->room_type_id ?? $room->room_type_id,
                'floor' => $request->floor ?? $room->floor,
                'number' => $request->number ?? $room->number,
                'image' => $newImagePath ?? $room->image,
            ]);

            $translations = [
                'view' => ['en' => $request->view_en, 'ar' => $request->view_ar],
                'description' => ['en' => $request->description_en, 'ar' => $request->description_ar],
            ];

            foreach ($translations as $attribute => $languages) {
                foreach ($languages as $language => $value)
                    if ($value) {
                        $translation = $room->translations()->attribute($attribute)->language($language)->first();
                        if ($translation) {
                            $translation->updateTranslation($value);
                        }
                    }
            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Failed to update room:" . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        DB::commit();

        return $this->returnData(true, __('success.room.update'), 'room', new RoomResource($room));
    }

    /**   @OA\Delete(
     *     path="/api/{role}/dashboard/rooms/{id}",
     *     summary="Soft Delete Room",
     *     tags={"Dashboard/Rooms"},
     *     operationId="softDeleteRoom",
     *     security={{"bearerAuth": {}}},
     *     description="Soft delete room",
     *    @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"admin", "super_admin"})
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Room ID",
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room deleted successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *        )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="This room is booked",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Can’t delete, this room is booked"),
     *             @OA\Property(property="code", type="integer", example=409)
     * 
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
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="An unexpected error occurred",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unable to delete this room type."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        $room = Room::find($id);

        if (!$room) {
            return $this->returnError(__('errors.room.not_found'), 404);
        }

        if ($room->bookings()->isBooked()) {
            return $this->returnError(__('errors.room.can’t_delete_booked'), 409);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('soft', $room);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.room.soft_delete'), 500);
        }

        $room->favorites()->delete();
        $room->delete();

        return $this->returnSuccess(__('success.room.soft_delete'));
    }
}
