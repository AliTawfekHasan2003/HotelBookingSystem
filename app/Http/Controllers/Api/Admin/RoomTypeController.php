<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseRoomTypeController;
use App\Http\Requests\StoreOrUpdateRoomTypeRequest;
use App\Http\Resources\RoomTypeResource;
use App\Models\RoomType;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\TranslationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class RoomTypeController extends BaseRoomTypeController
{
    use ResponseTrait, ImageTrait, TranslationTrait;

    /**   @OA\Post(
     *     path="/api/{role}/dashboard/room_types",
     *     summary="Store New Room Type",
     *     tags={"Dashboard/RoomTypes"},
     *     operationId="storeRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Store room type",
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
     *          description="Room type details",
     *          @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *             required={"name_en", "name_ar", "category_en", "category_ar", "description_en", "description_ar", "capacity", "daily_price", "monthly_price", "image"},
     *             @OA\Property(property="name_en", type="string", example="Luxury Rooms", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="name_ar", type="string", example="غرف فاخرة", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="category_en", type="string", example="Suite", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="category_ar", type="string", example="جناح", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="description_en", type="string", example="A spacious rooms with modern amenities", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=10."),
     *             @OA\Property(property="description_ar", type="string", example="غرف واسعة مع وسائل الراحة الحديثة", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=10."),
     *             @OA\Property(property="capacity", type="integer", example=5, description="Required. Number of guests. Length: min=1, max=10."),
     *             @OA\Property(property="daily_price", type="float", example=120.56, description="Required. Must have exactly 2 decimal places. Price per day for all rooms that belong to this type. Length: min=1.00 max=999999.99"),
     *             @OA\Property(property="monthly_price", type="float", example=1120.56, description="Required.Must have exactly 2 decimal places. Price per month for all rooms that belong to this type. Length: min=1.00 max=999999.99"),
     *             @OA\Property(property="image", type="string", format="binary", description="Required. Image file. Max size: 2MB.")
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Room type created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="New room type created successfully."),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(
     *                 property="roomType",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="name", type="string", example="Luxury Rooms"),
     *                 @OA\Property(property="category", type="string", example="Suite"),
     *                 @OA\Property(property="capacity", type="integer", example=5),
     *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/room_types\/1733570972_202858.jpg"),
     *                 @OA\Property(property="description", type="string", example="A spacious rooms with modern amenities"),
     *                 @OA\Property(property="count_rooms", type="integer", example=4),
     *                 @OA\Property(property="daily_price", type="float", example=120.56),
     *                 @OA\Property(property="monthly_price", type="float", example=1120.56)
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
     *                 @OA\Property(
     *                   property="name_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="name_ar",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،).")
     *                 ),
     *                 @OA\Property(
     *                   property="category_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="category_ar",
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
     *                   property="capacity",
     *                   type="array",
     *                   @OA\Items(type="string", example="The capacity must not exceed 10 persone.")
     *                 ),
     *                 @OA\Property(
     *                   property="daily_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be numeric and must have exactly 2 decimal places.")
     *                 ),
     *                 @OA\Property(
     *                   property="monthly_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be numeric and must have exactly 2 decimal places.")
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

    public function store(StoreOrUpdateRoomTypeRequest $request)
    {
        DB::beginTransaction();
        try {
            $imagePath = $this->imageStore($request->image, 'room_types');

            $roomType = RoomType::create([
                'capacity' => $request->capacity,
                'daily_price' => $request->daily_price,
                'monthly_price' => $request->monthly_price,
                'image' => $imagePath,
            ]);

            $roomType->translations()->createMany([
                [
                    'attribute' => 'name',
                    'value' => $request->name_en,
                    'language' => 'en'
                ],
                [
                    'attribute' => 'name',
                    'value' => $request->name_ar,
                    'language' => 'ar'
                ],
                [
                    'attribute' => 'category',
                    'value' => $request->category_en,
                    'language' => 'en'
                ],
                [
                    'attribute' => 'category',
                    'value' => $request->category_ar,
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
            Log::error("Failed to create new room type:" . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        DB::commit();

        return $this->returnData(true, __('success.room_type.create'), 'rooomType', new RoomTypeResource($roomType), 201);
    }

    /**  @OA\Post(
     *     path="/api/{role}/dashboard/room_types/{id}",
     *     summary="Update Room Type",
     *     tags={"Dashboard/RoomTypes"},
     *     operationId="updateRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Update room type",
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
     *         description="Room type ID",
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
     *          description="Room type details.",
     *          @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *             @OA\Property(property="name_en", type="string", example="Luxury Rooms", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="name_ar", type="string", example="غرف فاخرة", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="category_en", type="string", example="Suite", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="category_ar", type="string", example="جناح", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="description_en", type="string", example="A spacious rooms with modern amenities", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=10."),
     *             @OA\Property(property="description_ar", type="string", example="غرف واسعة مع وسائل الراحة الحديثة", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=10."),
     *             @OA\Property(property="capacity", type="integer", example=5, description="Nullable. Number of guests. Length: min=1, max=10."),
     *             @OA\Property(property="daily_price", type="float", example=120.56, description="Nullable. Must have exactly 2 decimal places. Price per day for all rooms that belong to this type. Length: min=1.00 max=999999.99"),
     *             @OA\Property(property="monthly_price", type="float", example=1120.56, description="Nullable.Must have exactly 2 decimal places. Price per month for all rooms that belong to this type. Length: min=1.00 max=999999.99"),
     *             @OA\Property(property="image", type="string", format="binary", description="Nullable. Image file. Max size: 2MB."),
     *             @OA\Property(property="_method", type="string", description="Override method to PATCH", default = "Patch")
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Room type updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room type updated successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="roomType",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=10),
     *                 @OA\Property(property="name", type="string", example="Luxury Rooms"),
     *                 @OA\Property(property="category", type="string", example="Suite"),
     *                 @OA\Property(property="capacity", type="integer", example=5),
     *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/room_types\/1733570972_202858.jpg"),
     *                 @OA\Property(property="description", type="string", example="A spacious rooms with modern amenities"),
     *                 @OA\Property(property="count_rooms", type="integer", example=4),
     *                 @OA\Property(property="daily_price", type="float", example=120.56),
     *                 @OA\Property(property="monthly_price", type="float", example=1120.56)
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
     *                 @OA\Property(
     *                   property="name_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="name_ar",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،).")
     *                 ),
     *                 @OA\Property(
     *                   property="category_en",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,).")
     *                 ),
     *                 @OA\Property(
     *                   property="category_ar",
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
     *                   property="capacity",
     *                   type="array",
     *                   @OA\Items(type="string", example="The capacity must not exceed 10 persone.")
     *                 ),
     *                 @OA\Property(
     *                   property="daily_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be numeric and must have exactly 2 decimal places.")
     *                 ),
     *                 @OA\Property(
     *                   property="monthly_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be numeric and must have exactly 2 decimal places.")
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
     *         description="Room Type not found",
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

    public function update(StoreOrUpdateRoomTypeRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $roomType = RoomType::with(['translations'])->find($id);
            if (!$roomType) {
                return $this->returnError(__('errors.room_type.not_found'), 404);
            }

            if ($request->image) {
                $oldImagePath = $roomType->image;
                $newImagePath = $this->imageReplace($oldImagePath, $request->image, 'room_types');
            }

            $roomType->update([
                'capacity' => $request->capacity ?? $roomType->capacity,
                'daily_price' => $request->daily_price ?? $roomType->daily_price,
                'monthly_price' => $request->monthly_price ?? $roomType->monthly_price,
                'image' => $newImagePath ?? $roomType->image,
            ]);

            $translations = [
                'name' => ['en' => $request->name_en, 'ar' => $request->name_ar],
                'category' => ['en' => $request->category_en, 'ar' => $request->category_ar],
                'description' => ['en' => $request->description_en, 'ar' => $request->description_ar],
            ];

            foreach ($translations as $attribute => $languages) {
                foreach ($languages as $language => $value)
                    if ($value) {
                        $translation = $roomType->translations()->attribute($attribute)->language($language)->first();
                        if ($translation) {
                            $translation->updateTranslation($value);
                        }
                    }
            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Failed to update room type:" . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        DB::commit();

        return $this->returnData(true, __('success.room_type.update'), 'roomType', new RoomTypeResource($roomType));
    }

   /**  @OA\Delete(
     *     path="/api/{role}/dashboard/room_types/{id}",
     *     summary="Soft Delete Room Type",
     *     tags={"Dashboard/RoomTypes"},
     *     operationId="softDeleteRoomType",
     *     security={{"bearerAuth": {}}},
     *     description="Soft delete room type",
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
     *         description="Room type ID",
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
     *         description="Room type deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Room type deleted successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *        )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="This room type has rooms",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unable to delete a room type before deleting all its rooms."),
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
        $roomType = RoomType::with('rooms')->find($id);
 
        if (!$roomType) {
            return $this->returnError(__('errors.room_type.not_found'), 404);
        }

        if ($roomType->rooms->isNotEmpty()) {
            return $this->returnError(__('errors.room_type.has_rooms'), 409);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('soft', $roomType);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.room_type.soft_delete'), 500);
        }

        
        $roomType->roomTypeServices()->delete();
        $roomType->favorites()->delete();
        $roomType->delete();

        return $this->returnSuccess(__('success.room_type.soft_delete'));
    }
}
