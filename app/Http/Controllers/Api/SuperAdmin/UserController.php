<?php

namespace App\Http\Controllers\Api\SuperAdmin;

use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Requests\AssignRoleRequest;
use App\Models\User;
use App\Traits\ResponseTrait;

class UserController extends AdminUserController
{
    use ResponseTrait;

    /**   @OA\Patch(
     *     path="/api/super_admin/dashboard/users/{id}/assign_role",
     *     summary="Assign Role To User",
     *     tags={"Dashboard/Users"},
     *     operationId="assignRole",
     *     security={{"bearerAuth": {}}},
     *    @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The user ID",
     *         @OA\Schema(type="integer")
     *     ),
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *    ),
     *    @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *           @OA\Property(property="role", type="string", example="Ali@333222", description="Required. Must in ['user', 'admin', 'super_admin]", example="admin")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assigne role successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="The role assigned to user successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *        )
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
     *                     property="role",
     *                     type="array",
     *                     @OA\Items(type="string", example="The role must be one of the following: [user, admin, super_admin].")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *          )
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
     *         response=409,
     *         description="This role has already been assigned to user",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="This role has already been assigned to user."),
     *             @OA\Property(property="code", type="integer", example=409)
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
     *         description="Unauthorized: Invalid token or expired",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function assignRole(AssignRoleRequest $request, $id)
    {
        $user = User::find($id);
        $role = $request->role;

        if (!$user) {
            return $this->returnError(__('errors.user.not_found'), 404);
        }

        if ($user->checkHasRole($role)) {
            return $this->returnError(__('errors.user.role_already_assigned'), 409);
        }

        $user->role = $role;
        $user->save();

        return $this->returnSuccess(__('success.user.role_assign'));
    }
}
