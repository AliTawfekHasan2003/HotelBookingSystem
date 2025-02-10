<?php

namespace App\Http\Controllers\Api;

use App\Events\EmailUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\SetPasswordRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Resources\UserResource;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Events\VerifyEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseUserController extends Controller
{
    use ResponseTrait;

    public $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * @OA\Get(
     *     path="/api/{role}/settings/profile", 
     *     summary="Get Profile Details",
     *     tags={"Settings"},
     *     operationId="getProfile",
     *     security={{"bearerAuth": {}}},
     *  @OA\Parameter(
     *         name="role",
     *         in="path",
     *         description="User role (user, admin, super_admin)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"user", "admin", "super_admin"})
     *     ),
     *  @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Get profile details successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Your profile details fetched successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *               property="profile",
     *               type="object",
     *               description="Details of the profile",
     *               @OA\Property(property="id", type="integer", example=1),
     *               @OA\Property(property="first_name", type="string", example="Ali"),
     *               @OA\Property(property="last_name", type="string", example="Hasan"),
     *               @OA\Property(property="full_name", type="string", example="Ali Hasan"),
     *               @OA\Property(property="email", type="string", example="Ali@gmail.com"),
     *               @OA\Property(property="role", type="string", example="user"),
     *               @OA\Property(
     *                 property="social_accounts",
     *                 type="array", 
     *                 description="List of social accounts name",
     *                 @OA\Items(type="string", example="google")
     *               )
     *             )
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

    public function showProfile()
    {
        $user = $this->user->load('socialAccounts');

        return $this->returnData(true, __('success.user.show_profile'), 'profile', new UserResource($user));
    }

    /**   @OA\Patch(
     *     path="/api/{role}/settings/profile",
     *     summary="Update Profile Details",
     *     tags={"Settings"},
     *     operationId="updateProfile",
     *     security={{"bearerAuth": {}}},
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
     *     @OA\RequestBody(
     *         required=true,
     *         description="Data for updating profile",
     *         @OA\JsonContent(
     *             @OA\Property(property="first_name", type="string", example="ali", description="Nullable.Must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores (_). Length: min=3, max=15."),
     *             @OA\Property(property="last_name", type="string", example="hasan", description="Nullable.Must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores (_). Length: min=3, max=15."),
     *             @OA\Property(property="email", type="string", format="email", example="alihasan@gmail.com", description="Nullable.Must end with @gmail.com domain. Must not already exist in the users table. Length: min=11, max=64."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update profile details successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Your profile details updated successfully. Please check your new email to confirm it. We also recommend updating your email in any linked social media account."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *               property="profile",
     *               type="object",
     *               description="Details of the profile",
     *               @OA\Property(property="id", type="integer", example=1),
     *               @OA\Property(property="first_name", type="string", example="Ali"),
     *               @OA\Property(property="last_name", type="string", example="Hasan"),
     *               @OA\Property(property="full_name", type="string", example="Ali Hasan"),
     *               @OA\Property(property="email", type="string", example="Ali@gmail.com"),
     *               @OA\Property(property="role", type="string", example="user"),
     *               @OA\Property(
     *                 property="social_accounts",
     *                 type="array", 
     *                 description="List of social accounts name",
     *                 @OA\Items(type="string", example="google")
     *               )
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
     *                     property="first_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The first name must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores(_).")
     *                 ),
     *                 @OA\Property(
     *                     property="last_name",
     *                     type="array",
     *                     @OA\Items(type="string", example="The last name must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores(_).")
     *                 ),
     *                 @OA\Property(
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The email address has already been taken.")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
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

    public function updateProfile(UpdateProfileRequest $request)
    {
        DB::beginTransaction();
        try {
            $oldEmail = $this->user->email;
            $newEmail = $request->email;

            $this->user->update([
                'full_name' => ($request->first_name ?? $this->user->first_name) . ' ' . ($request->last_name ?? $this->user->last_name),
                'email' => $request->email ?? $this->user->email,
            ]);

            if ($newEmail && $newEmail !== $oldEmail) {
                $this->user->update([
                    'email_verified_at' => null,
                    'last_verification_attempt_at' => null,
                    'verification_attempts' => 0,
                ]);

                if ($this->user->socialAccounts()->exists()) {
                    $this->user->socialAccounts()->delete();
                    $this->user->load('socialAccounts');
                }

                event(new VerifyEmail($this->user));
                event(new EmailUpdated($this->user, $oldEmail, $newEmail));
                DB::commit();

                return $this->returnData(true, __('success.user.profile_update_with_email'), 'profile', new UserResource($this->user));
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to update profile for user: " . $e->getMessage());

            return $this->returnError(__('errors.unexpected_error'), 500);
        }

        return $this->returnData(true, __('success.user.profile_update'), 'profile', new UserResource($this->user));
    }

    /**   @OA\Post(
     *     path="/api/{role}/settings/password",
     *     summary="Set Password",
     *     tags={"Settings"},
     *     operationId="setPassword",
     *     security={{"bearerAuth": {}}},
     *    description = "Add password if account hasn’t password",
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *           @OA\Property(property="password", type="string", example="Ali@333222", description="Required. Must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character (*, @, ., -, +, #, &). Length: min=8, max=20."),
     *           @OA\Property(property="password_confirmation", type="string", example="Ali@333222", description="Must match the password.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Add password to account successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="The password added to your account successfully."),
     *             @OA\Property(property="code", type="integer", example=201)
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
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string", example="The password must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character.")
     *                 ),
     *                 @OA\Property(
     *                     property="password_confirmation",
     *                     type="array",
     *                     @OA\Items(type="string", example="The password confirmation must match the password.")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
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
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="if account has already been password",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="The password has already been added."),
     *             @OA\Property(property="code", type="integer", example=409)
     *         )
     *     )
     * )
     */

    public function setPassword(SetPasswordRequest $request)
    {
        if (!is_null($this->user->password)) {
            return $this->returnError(__('errors.user.password_already_added'), 409);
        }

        $this->user->password = Hash::make($request->password);
        $this->user->save();

        return $this->returnSuccess(__('success.user.password_add'), 201);
    }

    /**   @OA\Patch(
     *     path="/api/{role}/settings/password",
     *     summary="Update Password",
     *     tags={"Settings"},
     *     operationId="updatePassword",
     *     security={{"bearerAuth": {}}},
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
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *           @OA\Property(property="current_password", type="string", example="Ali@333222", description="Required. Length: min=8, max=20."),
     *           @OA\Property(property="new_password", type="string", example="AliHasan@333222", description="Required. Must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character (*, @, ., -, +, #, &). Length: min=8, max=20."),
     *           @OA\Property(property="new_password_confirmation", type="string", example="AliHasan@333222", description="Must match the new_password.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Update password successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="The password updated successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
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
     *                     property="current_password",
     *                     type="array",
     *                     @OA\Items(type="string", example="This field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="new_password",
     *                     type="array",
     *                     @OA\Items(type="string", example="The password must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character.")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized: Invalid token or expired or current password is incorrect.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Your session is invalid, Please log in again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *     )
     * )
     */

    public function updatePassword(UpdatePasswordRequest $request)
    {
        if (!Hash::check($request->current_password, $this->user->password)) {
            return $this->returnError(__('errors.user.password_current_incorrect'), 401);
        }

        $this->user->password = Hash::make($request->new_password);
        $this->user->save();

        return $this->returnSuccess(__('success.user.password_update'));
    }
}
