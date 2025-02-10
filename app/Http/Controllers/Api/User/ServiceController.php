<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Api\BaseServiceController;
use App\Http\Resources\ServiceResource;
use App\Models\Favorite;
use App\Models\Service;
use App\Traits\ResponseTrait;

class ServiceController extends BaseServiceController
{
    use ResponseTrait;

    /**   @OA\Get(
     *     path="/api/user/services/favorite",
     *     summary="Get List Of Favorite Services",
     *     tags={"Services"},
     *     operationId="getFavoriteServices",
     *     security={{"bearerAuth": {}}},
     *     description="Get all favorite services of user with 10 pagination", 
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Get favorite services successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Favorite services fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                property="services",
     *                type="array",
     *                description="List of services",
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
     *         description="No services found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="The favorites list does not contain any services."),
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
        $services = Service::whereHas('favorites', function ($query) {
            return $query->byUser(auth()->id());
        })->paginate(10);

        if ($services->isEmpty()) {
            return $this->returnError(__('errors.service.not_found_favorite'), 404);
        }

        return  $this->returnPaginationData(true, __('success.service.favorite'), 'services', ServiceResource::collection($services));
    }

    /**   @OA\Post(
     *     path="/api/user/services/{id}/favorite/mark_as_favorite",
     *     summary="Mark Service As Favorite",
     *     tags={"Services"},
     *     operationId="markServiceAsFavorite",
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
     *         description="Service ID to make it favorite",
     *         @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=201,
     *         description="Service added to favorites successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Service added to favorites successfully."),
     *             @OA\Property(property="code", type="integer", example=201)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found service",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Service not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Service has already been added to favorites",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Service has already been added to favorites."),
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
        $service = Service::find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        $checkInFavorite = $service->favorites()->checkIn();

        if ($checkInFavorite) {
            return $this->returnError(__('errors.service.already_favorite'), 409);
        }

        Favorite::addFavorite(['type' => 'service', 'id' => $service->id]);

        return $this->returnSuccess(__('success.service.add_to_favorite'), 201);
    }

    /**   @OA\Delete(
     *     path="/api/user/services/{id}/favorite/unmark_as_favorite",
     *     summary="Unmark Service As Favorite",
     *     tags={"Services"},
     *     operationId="unmarkServiceAsFavorite",
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
     *         description="Service ID to delete from favorites",
     *         @OA\Schema(type="integer")
     *    ),
     *    @OA\Response(
     *         response=200,
     *         description="Service deleted from favorites successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Service deleted from favorites successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found service",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Service not found."),
     *             @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Service not in favorites.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Service not in favorites."),
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
        $service = Service::find($id);

        if (!$service) {
            return $this->returnError(__('errors.service.not_found'), 404);
        }

        $checkInFavorite = $service->favorites()->checkIn();

        if (!$checkInFavorite) {
            return $this->returnError(__('errors.service.not_in_favorite'), 409);
        }

        Favorite::destroyFavorite(['type' => 'service', 'id' => $service->id]);

        return $this->returnSuccess(__('success.service.delete_from_favorite'));
    }
}
