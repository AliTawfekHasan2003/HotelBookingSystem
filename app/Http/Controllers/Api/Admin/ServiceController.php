<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseServiceController;
use App\Http\Requests\StoreOrUpdateServiceRequest;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\TranslationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ServiceController extends BaseServiceController
{
    use ResponseTrait, TranslationTrait, ImageTrait;

    /**   @OA\Post(
     *     path="/api/{role}/dashboard/services",
     *     summary="Store New Service",
     *     tags={"Dashboard/Services"},
     *     operationId="storeService",
     *     security={{"bearerAuth": {}}},
     *     description="Store service",
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
     *          description="Service details",
     *          @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *             required={"name_en", "name_ar", "category_en", "category_ar", "description_en", "description_ar", "is_limited", "is_free", "image"},
     *             @OA\Property(property="name_en", type="string", example="Spa Treatment", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="name_ar", type="string", example="علاج سبا", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="category_en", type="string", example="Wellness", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="category_ar", type="string", example="عافية", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="description_en", type="string", example="A relaxing 60_minute spa session with aromatherapy oils and expert massage techniques", description="Required. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=10."),
     *             @OA\Property(property="description_ar", type="string", example="جلسة سبا مريحة لمدة 60 دقيقة مع زيوت العلاج العطري وتقنيات التدليك الاحترافية", description="Required. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=10."),
     *             @OA\Property(property="is_limited", type="boolean", enum={1, 0} , example=1, description="Required."),
     *             @OA\Property(property="total_units", type="integer", example=3, description="Required if is_limited=true. If is_limited = false must be 0. Length: if is_limited=true => min=1,."),
     *             @OA\Property(property="is_free", type="boolean", enum={1, 0}, example=0, description="Required."),
     *             @OA\Property(property="daily_price", type="float", example=120.56, description="Must have exactly 2 decimal places. Required if is_free=false. If is_free=false must be 0.00 . Length: if is_free=true => min=1.00 max=999999.99"),
     *             @OA\Property(property="monthly_price", type="float", example=1120.56, description="Must have exactly 2 decimal places. Required if is_free=false. If is_free=false must be 0.00 . Length: if is_free=true => min=1.00 max=999999.99"),
     *             @OA\Property(property="image", type="string", format="binary", description="Required. Image file. Max size: 2MB.")
     *            )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Service created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="New service created successfully."),
     *             @OA\Property(property="code", type="integer", example=201),
     *             @OA\Property(
     *                 property="service",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Spa Treatment"),
     *                 @OA\Property(property="category", type="string", example="Wellness"),
     *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/services\/1733570972_202858.jpg"),
     *                 @OA\Property(property="description", type="string", example="A relaxing 60_minute spa session with aromatherapy oils and expert massage techniques"),
     *                 @OA\Property(property="is_limited", type="boolean", example=1),
     *                 @OA\Property(property="total_units", type="integer", example=3, description="Total units for service if its limited"),
     *                 @OA\Property(property="is_free", type="boolean", example=0),
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
     *                   property="is_limited",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be a boolean value.")
     *                 ),
     *                 @OA\Property(
     *                   property="total_units",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field is required.")
     *                 ),
     *                 @OA\Property(
     *                   property="is_free",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be a boolean value.")
     *                ),
     *                @OA\Property(
     *                   property="daily_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field is required.")
     *                ),
     *                @OA\Property(
     *                   property="monthly_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field is required.")
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

    public function store(StoreOrUpdateServiceRequest $request)
    {
        DB::beginTransaction();
        try {
            $imagePath = $this->imageStore($request->image, 'services');

            $service = Service::create([
                'is_limited' => $request->is_limited,
                'total_units' => $request->total_units ?? 0,
                'is_free' => $request->is_free,
                'daily_price' => $request->daily_price ?? 0.00,
                'monthly_price' => $request->monthly_price ?? 0.00,
                'image' => $imagePath,
            ]);

            $service->translations()->createMany([
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
            Log::error("Failed to create new service:" . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        DB::commit();

        return $this->returnData(true, __('success.service.create'), 'service', new ServiceResource($service), 201);
    }

    /**  @OA\Post(
     *     path="/api/{role}/dashboard/services/{id}",
     *     summary="Update Service",
     *     tags={"Dashboard/Services"},
     *     operationId="updateService",
     *     security={{"bearerAuth": {}}},
     *     description="Update service",
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
     *         description="Service ID",
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
     *          description="Service details.",
     *          @OA\MediaType(
     *            mediaType="multipart/form-data",
     *            @OA\Schema(
     *             @OA\Property(property="name_en", type="string", example="Spa Treatment", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="name_ar", type="string", example="علاج سبا", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="category_en", type="string", example="Wellness", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=3."),
     *             @OA\Property(property="category_ar", type="string", example="عافية", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=3."),
     *             @OA\Property(property="description_en", type="string", example="A relaxing 60_minute spa session with aromatherapy oils and expert massage techniques", description="Nullable. Must start with a Latin character and can contain Latin characters, digits, underscores (_), dots (.), and commas (,). Length: min=10."),
     *             @OA\Property(property="description_ar", type="string", example="جلسة سبا مريحة لمدة 60 دقيقة مع زيوت العلاج العطري وتقنيات التدليك الاحترافية", description="Nullable. Must start with a Arabic character and can contain Arabic characters, digits, underscores(_), dots(.), and commas(،). Length: min=10."),
     *             @OA\Property(property="is_limited", type="boolean", enum={1, 0}, example=1, description="Nullable."),
     *             @OA\Property(property="total_units", type="integer", example=3, description="Required if is_limited=true. If is_limited = false must be 0. Length: if is_limited=true => min=1,."),
     *             @OA\Property(property="is_free", type="boolean",  enum={1, 0}, example=0, description="Nullable."),
     *             @OA\Property(property="daily_price", type="float", example=120.56, description="Must have exactly 2 decimal places. Required if is_free=false. If is_free=false must be 0.00 . Length: if is_free=true => min=1.00 max=999999.99"),
     *             @OA\Property(property="monthly_price", type="float", example=1120.56, description="Must have exactly 2 decimal places. Required if is_free=false. If is_free=false must be 0.00 . Length: if is_free=true => min=1.00 max=999999.99"),
     *             @OA\Property(property="image", type="string", format="binary", description="Nullable. Image file. Max size: 2MB."),
     *             @OA\Property(property="_method", type="string", description="Override method to PATCH", default = "Patch")
     *           )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Service updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Service updated successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="service",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Spa Treatment"),
     *                 @OA\Property(property="category", type="string", example="Wellness"),
     *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/services\/1733570972_202858.jpg"),
     *                 @OA\Property(property="description", type="string", example="A relaxing 60_minute spa session with aromatherapy oils and expert massage techniques"),
     *                 @OA\Property(property="is_limited", type="boolean", example=1),
     *                 @OA\Property(property="total_units", type="integer", example=3, description="Total units for service if its limited"),
     *                 @OA\Property(property="is_free", type="boolean", example=0),
     *                 @OA\Property(property="daily_price", type="float", example=120.56),
     *                 @OA\Property(property="monthly_price", type="float", example=1120.56)
     *             ) 
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *              @OA\Property(property="status", type="boolean", example=false),
     *              @OA\Property(
     *                 property="msg",
     *                 type="object",
     *                @OA\Property(
     *                 property="name_en",
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
     *                   property="is_limited",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be a boolean value.")
     *                 ),
     *                 @OA\Property(
     *                   property="total_units",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field is required.")
     *                 ),
     *                 @OA\Property(
     *                   property="is_free",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field must be a boolean value.")
     *                ),
     *                @OA\Property(
     *                   property="daily_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field is required.")
     *                ),
     *                @OA\Property(
     *                   property="monthly_price",
     *                   type="array",
     *                   @OA\Items(type="string", example="This field is required.")
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
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Service not found."),
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

    public function update(StoreOrUpdateserviceRequest $request, $id)
    {
        DB::beginTransaction();
        try {
            $service = Service::find($id);
            if (!$service) {
                return $this->returnError(__('errors.service.not_found'), 404);
            }

            if ($request->image) {
                $oldImagePath = $service->image;
                $newImagePath = $this->imageReplace($oldImagePath, $request->image, 'services');
            }

            $isLimited = $request->is_limited ?? $service->is_limited;
            $isFree = $request->is_free ?? $service->is_free;
            $totalUnits = ($isLimited == false)  ? 0 : ($request->total_units ?? $service->total_units);
            $dailyPrice = ($isFree == true) ? 0.00 : ($request->daily_price ?? $service->daily_price);
            $monthlyPrice = ($isFree == true) ? 0.00 : ($request->monthly_price ?? $service->monthly_price);

            $service->update([
                'is_limited' => $isLimited,
                'total_units' => $totalUnits,
                'is_free' => $isFree,
                'daily_price' => $dailyPrice,
                'monthly_price' => $monthlyPrice,
                'image' => $newImagePath  ?? $service->image,
            ]);

            $translations = [
                'name' => ['en' => $request->name_en, 'ar' => $request->name_ar],
                'category' => ['en' => $request->category_en, 'ar' => $request->category_ar],
                'description' => ['en' => $request->description_en, 'ar' => $request->description_ar],
            ];

            foreach ($translations as $attribute => $languages) {
                foreach ($languages as $language => $value)
                    if ($value) {
                        $translation = $service->translations()->attribute($attribute)->language($language)->first();
                        if ($translation) {
                            $translation->updateTranslation($value);
                        }
                    }
            }
        } catch (Throwable $e) {
            DB::rollBack();
            Log::error("Failed to update service:" . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        DB::commit();

        return $this->returnData(true, __('success.service.update'), 'service', new ServiceResource($service));
    }

    /**  @OA\Delete(
     *     path="/api/{role}/dashboard/services/{id}",
     *     summary="Soft Delete Service",
     *     tags={"Dashboard/Services"},
     *     operationId="softDeleteService",
     *     security={{"bearerAuth": {}}},
     *     description="Soft delete service",
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
     *         description="Service ID",
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
     *         description="Service deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Service deleted successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *        )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="This service is booked",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Can’t delete, this service is booked."),
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
     *         description="Service not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Service not found."),
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
     *             @OA\Property(property="msg", type="string", example="Unable to delete this service."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function destroy($id)
    {
        $service = Service::find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        if ($service->bookings()->isBooked()) {
            return $this->returnError(__('errors.service.can’t_delete_booked'), 409);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('soft', $service);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.service.soft_delete'), 500);
        }

        $service->roomTypeServices()->delete();
        $service->favorites()->delete();
        $service->delete();

        return $this->returnSuccess(__('success.service.soft_delete'));
    }

    /**   @OA\Get(
     *     path="/api/{role}/dashboard/services/{id}/unavailable_dates",
     *     summary="Get Unavailable Dates",
     *     tags={"Dashboard/Services"},
     *     operationId="getUnavailableDatesForService",
     *     security={{"bearerAuth": {}}},
     *     description="List of unavailable dates for service", 
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
     *    ),
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Service ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get unavailable dates for service successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Unavailable dates for this service fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="unavailableDates",
     *                 type="array",
     *                 description="List of unavailable dates for service",
     *                 @OA\Items(
     *                   type="object",
     *                   @OA\Property(property="start_date", type="string", format="date-time", example="2025-01-12T00:00:00.000000Z"),
     *                   @OA\Property(property="end_date", type="string", format="date-time", example="2025-02-22T00:00:00.000000Z")
     *                )
     *             )
     *        )
     *     ),
     *      @OA\Response(
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
     *         description="Service not found or No unavailable dates found for service.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="No unavailable dates found for this service."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="This service is not limited",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="This service is not limited."),
     *             @OA\Property(property="code", type="integer", example=409)
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
        $service = service::find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        if (!$service->is_limited) {
            return $this->returnError(__('errors.service.not_limited'), 409);
        }

        $unavailableDates = $service->bookings()->getUnavailableDates();

        if ($unavailableDates->isEmpty()) {
            return $this->returnError(__('errors.service.not_found_dates'), 404);
        }

        return $this->returnData(true, __('success.service.unavailable_dates'), 'unavailableDates', $unavailableDates);
    }
}
