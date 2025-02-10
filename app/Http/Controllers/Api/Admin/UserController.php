<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseUserController;
use App\Models\User;
use App\Traits\ResponseTrait;

class UserController extends BaseUserController
{
    use ResponseTrait;

    /**   @OA\Get(
     *     path="/api/{role}/dashboard/users",
     *     summary="Get List Of Users",
     *     tags={"Dashboard/Users"},
     *     operationId="getUsers",
     *     security={{"bearerAuth": {}}},
     *     description="Get all verified users details with 10 pagination", 
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
     *     @OA\Response(
     *         response=200,
     *         description="Get all verified users details successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="All verified users details fetched successfully"),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="users",
     *                 type="array",
     *                 description="List of verified users details",
     *                 @OA\Items(
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="full_name", type="string", example="Ali Hasan"),
     *                   @OA\Property(property="email", type="string", example="Ali@gmail.com"),
     *                   @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-11-06T15:16:50.000000Z"),
     *                   @OA\Property(property="role", type="string", example="admin"),
     *                   @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z"),
     *                   @OA\Property(property="verification_attempts", type="integer", example=0),
     *                   @OA\Property(property="last_verification_attempt_at", type="string", format="date-time", nullable=true, example=null),
     *                   @OA\Property(
     *                      property="social_accounts", 
     *                      type="array", 
     *                      description="Data about social accounts linked to the orginal acount",
     *                      @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=27, description="ID Associated item."),
     *                         @OA\Property(property="user_id", type="integer", example="1"),
     *                         @OA\Property(property="social_id", type="string", example="102026955083455787072"),
     *                         @OA\Property(property="social_name", type="string", example="google"),
     *                         @OA\Property(property="role", type="string", example="admin"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z")
     *                      )
     *                  )
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
     *         description="Not found any user",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Users not found."),
     *             @OA\Property(property="code", type="integer", example=404)
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
     *     )
     * )
     */

    public function index()
    {
        $users = User::whereNotNull('email_verified_at')->with('socialAccounts')->paginate(10);

        if ($users->isEmpty()) {
            return $this->returnError(__('errors.user.not_found_index'), 404);
        }
        return $this->returnPaginationData(true, __('success.user.index'), 'users', $users);
    }

    /**   @OA\Get(
     *     path="/api/{role}/dashboard/users/{id}",
     *     summary="Get User Details",
     *     tags={"Dashboard/Users"},
     *     operationId="getUser",
     *     security={{"bearerAuth": {}}},
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
     *         description="User ID",
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
     *         description="Get verified user details successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="User details fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                   property="user",
     *                   type="object",
     *                   @OA\Property(property="id", type="integer", example=1),
     *                   @OA\Property(property="full_name", type="string", example="Ali Hasan"),
     *                   @OA\Property(property="email", type="string", example="Ali@gmail.com"),
     *                   @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-11-06T15:16:50.000000Z"),
     *                   @OA\Property(property="role", type="string", example="admin"),
     *                   @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                   @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z"),
     *                   @OA\Property(property="verification_attempts", type="integer", example=0),
     *                   @OA\Property(property="last_verification_attempt_at", type="string", format="date-time", nullable=true, example=null),
     *                   @OA\Property(
     *                      property="social_accounts", 
     *                      type="array", 
     *                      description="Data about social accounts linked to the orginal acount",
     *                      @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=27, description="ID Associated item."),
     *                         @OA\Property(property="user_id", type="integer", example="1"),
     *                         @OA\Property(property="social_id", type="string", example="102026955083455787072"),
     *                         @OA\Property(property="social_name", type="string", example="google"),
     *                         @OA\Property(property="role", type="string", example="admin"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z")
     *                      )
     *                  )
     *           )
     *        ) 
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="User not found."),
     *             @OA\Property(property="code", type="integer", example=404)
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
     *     )
     * )
     */

    public function showUser($id)
    {  
        $user = User::whereNotNull('email_verified_at')->with('socialAccounts')->find($id);

        if (!$user) {
            return $this->returnError(__('errors.user.not_found'), 404);
        }

        return $this->returnData(true, __('success.user.show_user'), 'user', $user);
    }
}
