<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\Admin\ServiceController as AdminServiceController;
use App\Http\Resources\BookingResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ImageTrait;
use App\Traits\ResponseTrait;
use App\Traits\TranslationTrait;
use Illuminate\Http\Request;

class ServiceController extends AdminServiceController
{
    use ResponseTrait, TranslationTrait, ImageTrait;

    /**   @OA\Get(
     *     path="/api/super_admin/dashboard/services/deleted",
     *     summary="Get List Of Deleted Services",
     *     tags={"Dashboard/Services"},
     *     operationId="getDeletedServices",
     *     security={{"bearerAuth": {}}},
     *     description="Get all deleted services with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Parameter(
     *         name="is_limited",
     *         in="query",
     *         description="Filter by limited.",
     *         required=false,
     *         @OA\Schema(type="integer", enum={1,0})
     *     ),
     *     @OA\Parameter(
     *         name="is_free",
     *         in="query",
     *         description="Filter by free",
     *         required=false,
     *         @OA\Schema(type="integer", enum={1,0})
     *     ),
     *     @OA\Parameter(
     *         name="name",
     *         in="query",
     *         description="Filter by name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get deleted services successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted services fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                property="services",
     *                type="array",
     *                description="List of deleted services",
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
     *                 @OA\Property(property="monthly_price", type="float", example=33333.09)
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
     *         description="No deleted services found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="No deleted services found matching the given criteria."),
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
        $result = Service::filterServices($request, true);
        $query = $result['query'];
        $ifCriteria = $result['ifCriteria'];

        $services = $query->paginate(10);

        if ($services->isEmpty()) {
            if ($ifCriteria)
                return $this->returnError(__('errors.service.not_found_index_trashed_with_criteria'), 404);
            else
                return $this->returnError(__('errors.service.not_found_index_trashed'), 404);
        }

        return  $this->returnPaginationData(true, __('success.service.index_trashed'), 'services', ServiceResource::collection($services));
    }

    /**   @OA\Get(
     *     path="/api/super_admin/dashboard/services/deleted/{id}",
     *     summary="Show Deleted Service",
     *     tags={"Dashboard/Services"},
     *     operationId="showDeletedService",
     *     security={{"bearerAuth": {}}},
     *     description="Get deleted service", 
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
     *         description="Get deleted service successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted service fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="room",
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
     *                 @OA\Property(property="monthly_price", type="float", example=33333.09)
     *             )
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
        $service = Service::onlyTrashed()->find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        return $this->returnData(true, __('success.service.show_trashed'), 'service', new ServiceResource($service));
    }

    /**   @OA\Post(
     *     path="/api/super_admin/dashboard/services/deleted/{id}/restore",
     *     summary="Restore Deleted Service",
     *     tags={"Dashboard/Services"},
     *     operationId="restoreDeletedService",
     *     security={{"bearerAuth": {}}},
     *     description="Restore deleted service", 
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
     *         description="Deleted service restored from deletion successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Deleted service restored from deletion successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
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
     *             @OA\Property(property="msg", type="string", example="Unable to restore this service."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function trashedRestore($id)
    {
        $service = Service::onlyTrashed()->find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('restore', $service);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.service.restore'), 500);
        }

        $service->roomTypeServices()->whereHas('roomType', function ($query) {
            return $query->whereNull('deleted_at');
        })->onlyTrashed()->restore();

        $service->restore();

        return $this->returnSuccess(__('success.service.restore'));
    }

    /**   @OA\Delete(
     *     path="/api/super_admin/dashboard/services/deleted/{id}/force",
     *     summary="Force Delete Service",
     *     tags={"Dashboard/Services"},
     *     operationId="forceDeleteService",
     *     security={{"bearerAuth": {}}},
     *     description="Force delete service", 
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
     *         description="Service permanently deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Service permanently deleted successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
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
     *             @OA\Property(property="msg", type="string", example="Unable to permanently delete this service."),
     *             @OA\Property(property="code", type="integer", example=500)
     *         )
     *     )
     * )
     */

    public function trashedForceDelete($id)
    {
        $service = Service::onlyTrashed()->find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        $ifSuccess = $this->handelSoftDeletingTranslations('force', $service);

        if (!$ifSuccess) {
            return $this->returnError(__('errors.service.force_delete'), 500);
        }

        $service->roomTypeServices()->onlyTrashed()->forceDelete();
        $this->imageDelete($service->image);
        $service->forceDelete();

        return $this->returnSuccess(__('success.service.force_delete'));
    }

    /**  @OA\Get(
     *     path="/api/super_admin/dashboard/services/{id}/bookings",
     *     summary="Get List Of Bookings For Service",
     *     tags={"Dashboard/Services"},
     *     operationId="getBookingsService",
     *     security={{"bearerAuth": {}}},
     *     description="Get all bookings for service with 10 pagination", 
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
     *    @OA\Response(
     *         response=200,
     *         description="Get bookings for this service successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Bookings for this service fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="bookings",
     *                 type="array",
     *                 description="List of bookings",
     *                 @OA\Items(
     *                    type="object",
     *                    @OA\Property(property="id", type="integer", example=1),
     *                    @OA\Property(property="invoice_id", type="integer", example=12),
     *                    @OA\Property(property="bookingable_type", type="string", example="service"),
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
     *         description="Service not found or No bookings found for this service",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="No bookings found for this service."),
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
        $service = Service::find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        $bookings = $service->bookings()->paginate(10);

        if ($bookings->isEmpty()) {
            return $this->returnError(__('errors.service.not_found_bookings'));
        }

        return $this->returnPaginationData(true, __('success.service.bookings'), 'bookings', BookingResource::collection($bookings));
    }
}
