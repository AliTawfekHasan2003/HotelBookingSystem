<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceLimitedRequest;
use App\Http\Resources\RoomTypeResource;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Contracts\Translation\TranslatorTrait;

class BaseServiceController extends Controller
{
  use ResponseTrait, TranslatorTrait;

  /**   @OA\Get(
   *     path="/api/{role}/services",
   *     summary="Get List Of Services",
   *     tags={"Services"},
   *     operationId="getServices",
   *     security={{"bearerAuth": {}}},
   *     description="Get all services with 10 pagination", 
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
   *         description="Get services successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Services fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="services",
   *                 type="array",
   *                 description="List of services",
   *                 @OA\Items(
   *                   type="object",
   *                   @OA\Property(property="id", type="integer", example=1),
   *                   @OA\Property(property="name", type="string", example="parking service"),
   *                   @OA\Property(property="category", type="string", example="cars"),
   *                   @OA\Property(property="image", type="string", example="\/storage\/Image\/services\/1733570972_202858.jpg"),
   *                   @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
   *                   @OA\Property(property="is_limited", type="boolean", example=1),
   *                   @OA\Property(property="total_units", type="integer", example=7, description="Total units for service if its limited"),
   *                   @OA\Property(property="is_free", type="boolean", example=1),
   *                   @OA\Property(property="daily_price", type="float", example=12222.45),
   *                   @OA\Property(property="monthly_price", type="float", example=33333.10)
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
   *         description="No services found",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="No services found matching the given criteria."),
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
    $result = Service::filterServices($request);
    $query = $result['query'];
    $ifCriteria = $result['ifCriteria'];

    $services = $query->paginate(10);

    if ($services->isEmpty()) {
      if ($ifCriteria)
        return $this->returnError(__('errors.service.not_found_index_with_criteria'), 404);
      else
        return $this->returnError(__('errors.service.not_found_index'), 404);
    }

    return  $this->returnPaginationData(true, __('success.service.index'), 'services', ServiceResource::collection($services));
  }

  /**   @OA\Get(
   *     path="/api/{role}/services/{id}",
   *     summary="Show Service",
   *     tags={"Services"},
   *     operationId="showService",
   *     security={{"bearerAuth": {}}},
   *     description="Get service", 
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
   *         description="Service ID",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Get service successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Service fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="service",
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
   *                 @OA\Property(property="monthly_price", type="float", example=33333.00)
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
    $service = Service::find($id);

    if (!$service) {
      return $this->returnError(__('errors.service.not_found'), 404);
    }

    return $this->returnData(true, __('success.service.show'), 'service', new ServiceResource($service));
  }

  /**   @OA\Get(
   *     path="/api/{role}/services/{id}/room_types",
   *     summary="Get List Of Room Types Associated With Specific Service",
   *     tags={"Services"},
   *     operationId="getRoomTypesForService",
   *     security={{"bearerAuth": {}}},
   *     description="Get all room types associated with specific service with 10 pagination", 
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
   *         description="Service ID",
   *         @OA\Schema(type="integer")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Get all room types associated with this service successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Room types associated with this service fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(
   *                 property="roomTypes",
   *                 type="array",
   *                 description="List of room types",
   *                @OA\Items(
   *                 type="object",
   *                 @OA\Property(property="id", type="integer", example=1),
   *                 @OA\Property(property="name", type="string", example="royal rooms"),
   *                 @OA\Property(property="category", type="string", example="master"),
   *                 @OA\Property(property="capacity", type="integer", example=5),
   *                 @OA\Property(property="image", type="string", example="\/storage\/Image\/room_types\/1733570972_202858.jpg"),
   *                 @OA\Property(property="description", type="string", example="dddddddddddddddddddddddddddddddddddddddddd"),
   *                 @OA\Property(property="count_rooms", type="integer", example=4),
   *                 @OA\Property(property="daily_price", type="float", example=12222.45),
   *                 @OA\Property(property="monthly_price", type="float", example=33333.90),
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
   *         description="No service or room types associated with this service found",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(property="msg", type="string", example="No room types associated with this service were found."),
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

  public function roomTypes($id)
  {
    $service = Service::find($id);

    if (!$service) {
      return $this->returnError(__('errors.service.not_found'), 404);
    }

    $roomTypes = $service->roomTypes()->paginate(10);

    if ($roomTypes->isEmpty()) {
      return $this->returnError(__('errors.service.not_found_room_types'), 404);
    }

    return $this->returnPaginationData(true, __('success.service.room_types'), 'roomTypes',  RoomTypeResource::collection($roomTypes));
  }

  /**   @OA\Get(
   *     path="/api/{role}/services/{id}/available_units",
   *     summary="Get Number Available Units For Specific Service",
   *     tags={"Services"},
   *     operationId="getNumberAvailableUnitsForService",
   *     security={{"bearerAuth": {}}},
   *     description="Get number available units during a period for a limited service", 
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
   *         description="Limited service ID",
   *         @OA\Schema(type="integer")
   *     ),
   *      @OA\Parameter(
   *         name="start_date",
   *         in="query",
   *         required=true,
   *         description="Start date of period",
   *         @OA\Schema(type="string", format="date", example="2025-12-01", description="Required. Must be a valid date in Y-m-d format and must be after today's date.")
   *     ),
   *      @OA\Parameter(
   *         name="end_date",
   *         in="query",
   *         required=true,
   *         description="End date of period",
   *         @OA\Schema(type="string", format="date", example="2025-12-10", description="Required. Must be a valid date in Y-m-d format and must be after the start_date.")
   *     ),
   *     @OA\Response(
   *         response=200,
   *         description="Get number of available units for this service during specified period successfully",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=true),
   *             @OA\Property(property="msg", type="string", example="Number of available units for this service fetched successfully."),
   *             @OA\Property(property="code", type="integer", example=200),
   *             @OA\Property(property="countAvailableServiceUnits", type="integer", example=5),
   *        )
   *     ),
   *      @OA\Response(
   *         response=422,
   *         description="Validation error",
   *         @OA\JsonContent(
   *             @OA\Property(property="status", type="boolean", example=false),
   *             @OA\Property(
   *                 property="msg",
   *                 type="object",
   *                 @OA\Property(
   *                     property="start_date",
   *                     type="array",
   *                     @OA\Items(type="string", example="The start date must be after today.")
   *                 ),
   *                 @OA\Property(
   *                     property="end_date",
   *                     type="array",
   *                     @OA\Items(type="string", example="The end date must be after start date.")
   *                 )
   *             ),
   *             @OA\Property(property="code", type="integer", example=422)
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

  public function limitedUnits(ServiceLimitedRequest $request, $id)
  {
    $service = Service::find($id);

    if (!$service) {
      return $this->returnError(__('errors.service.not_found'), 404);
    }

    if (!$service->is_limited) {
      return $this->returnError(__('errors.service.not_limited'), 409);
    }

    $startDate = Carbon::parse($request->start_date);
    $endDate = Carbon::parse($request->end_date);

    $countAvailableServiceUnits = $service->bookings()->countAvailableServiceUnits($startDate, $endDate, $service->total_units);

    return $this->returnData(true, __('success.service.limited_units'), 'countAvailableServiceUnits', $countAvailableServiceUnits);
  }
}
