<?php

namespace App\Http\Controllers\Api;

use App\Events\VerifyEmail;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAndResendEmailRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Traits\ResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    use ResponseTrait;

    /**   @OA\Post(
     *     path="/api/login",
     *     summary="Login",
     *     tags={"Authentication"},
     *     operationId="login",
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User login credentials",
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="alihasan@gmail.com", description="Required. Must exist in the users table. Length: min=11, max=64."),
     *             @OA\Property(property="password", type="string", example="Ali@333222", description="Required. Length: min=8, max=20."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Logged in successfully."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Details of the authenticated user.",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="full_name", type="string", example="Ali Hasan"),
     *                 @OA\Property(property="email", type="string", example="Ali@gmail.com"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-11-06T15:16:50.000000Z"),
     *                 @OA\Property(property="role", type="string", example="admin"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z"),
     *                 @OA\Property(property="verification_attempts", type="integer", example=0),
     *                 @OA\Property(property="last_verification_attempt_at", type="string", format="date-time", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="authorisation",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzM3MjM0OTYxLCJleHAiOjE3MzcyMzg1NjEsIm5iZiI6MTczNzIzNDk2MSwianRpIjoiRjBkcXh1NDJYeU9wYlBLSCIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.i3yO1CtVklzMRu1opuQBSUat-vPppBvh7qHyZviU8H0"),
     *                 @OA\Property(property="type", type="string", example="bearer")
     *           )
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
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The provided email does not exist in our records.")
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string", example="This field is required.")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *         )
     *     ),
     *      @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Unauthorized: Password or email is invalid."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *    )
     * )
     */

    public function login(LoginAndResendEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !$user->email_verified_at) {
            return $this->returnError(__('auth.errors.email.unverified'), 401);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);

        if (!$token) {
            return $this->returnError(__('auth.errors.unauthorized'), 401);
        }

        return $this->returnLoginRefreshSuccess(__('auth.success.login'), 'user', $user, $token);
    }
    /** 
     *@OA\Info(
     *     title="Hotel Booking System APIs",
     *     version="1.0.0",
     * ),    
     * @OA\SecurityScheme(
     *     securityScheme="bearerAuth",
     *     type="http",
     *     scheme="bearer",
     *     bearerFormat="JWT",
     *     description="JWT Bearer Authentication"
     * ),
     *
     *   @OA\Post(
     *     path="/api/register",
     *     summary="Register A New User",
     *     tags={"Authentication"},
     *    operationId="registerUser",
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration details",
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "password"},
     *             @OA\Property(property="first_name", type="string", example="ali", description="Required. Must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores (_). Length: min=3, max=15."),
     *             @OA\Property(property="last_name", type="string", example="hasan", description="Required. Must start with an Arabic or Latin character and can contain Arabic or Latin characters, digits, and underscores (_). Length: min=3, max=15."),
     *             @OA\Property(property="email", type="string", format="email", example="alihasan@gmail.com", description="Required. Must end with @gmail.com domain. Must not already exist in the users table. Length: min=11, max=64."),
     *             @OA\Property(property="password", type="string", example="Ali@333222", description="Required. Must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character (*, @, ., -, +, #, &). Length: min=8, max=20."),
     *             @OA\Property(property="password_confirmation", type="string", example="Ali@333222", description="Must match the password.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Account created successfully, Please check your email to verify your account."),
     *             @OA\Property(property="code", type="integer", example=201)
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
     *                 ),
     *                 @OA\Property(
     *                     property="password",
     *                     type="array",
     *                     @OA\Items(type="string", example="The password must contain at least one lowercase letter, one uppercase letter, one numeric digit, and one special character.")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *         )
     *     )
     * )
     */

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'full_name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user'
        ]);

        event(new VerifyEmail($user));

        return $this->returnSuccess(__('auth.success.register'), 201);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout",
     *     tags={"Authentication"},
     *     operationId="logout",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logout successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Logged out successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
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

    public function logout()
    {
        Auth::logout();

        return $this->returnSuccess(__('auth.success.logout'));
    }

    /**
     * @OA\Post(
     *     path="/api/refresh",
     *     summary="Refresh",
     *     tags={"Authentication"},
     *     operationId="refresh",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Session refreshed successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Session refreshed successfully.."),
     *             @OA\Property(property="code", type="integer", example=200),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 description="Details of the authenticated user.",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="full_name", type="string", example="Ali Hasan"),
     *                 @OA\Property(property="email", type="string", example="Ali@gmail.com"),
     *                 @OA\Property(property="email_verified_at", type="string", format="date-time", example="2024-11-06T15:16:50.000000Z"),
     *                 @OA\Property(property="role", type="string", example="admin"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-10-20T12:26:39.000000Z"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-11-17T12:17:20.000000Z"),
     *                 @OA\Property(property="verification_attempts", type="integer", example=0),
     *                 @OA\Property(property="last_verification_attempt_at", type="string", format="date-time", nullable=true, example=null)
     *             ),
     *             @OA\Property(
     *                 property="authorisation",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL2xvZ2luIiwiaWF0IjoxNzM3MjM0OTYxLCJleHAiOjE3MzcyMzg1NjEsIm5iZiI6MTczNzIzNDk2MSwianRpIjoiRjBkcXh1NDJYeU9wYlBLSCIsInN1YiI6IjEiLCJwcnYiOiIyM2JkNWM4OTQ5ZjYwMGFkYjM5ZTcwMWM0MDA4NzJkYjdhNTk3NmY3In0.i3yO1CtVklzMRu1opuQBSUat-vPppBvh7qHyZviU8H0"),
     *                 @OA\Property(property="type", type="string", example="bearer")
     *           )
     *        )
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

    public function refresh()
    {
        return $this->returnLoginRefreshSuccess(__('auth.success.refresh'), 'user', Auth::user(), Auth::refresh());
    }

    /** 
     * @OA\Get(
     *     path="/api/email/verify/{id}",
     *     summary="Verify Email Address",
     *     description="Verifies the user's email address.",
     *     operationId="verifyEmail",
     *     tags={"Authentication"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="The user ID to verify his email address",
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
     *         description="Your email verified successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Your email verified successfully."),
     *             @OA\Property(property="code", type="integer", example=200)
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Invalid signature.",
     *         @OA\JsonContent(
     *            @OA\Property(property="status", type="boolean", example=false),
     *            @OA\Property(property="msg", type="string", example="Verification link expired, Please request a new one."),
     *            @OA\Property(property="code", type="integer", example=403)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found.",
     *         @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="msg", type="string", example="User not found."),
     *           @OA\Property(property="code", type="integer", example=404)
     *         )
     *     ),
     *     @OA\Response(
     *         response=409,
     *         description="Your email already verified.",
     *         @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="msg", type="string", example="Your email already verified."),
     *           @OA\Property(property="code", type="integer", example=409)
     *         )
     *     )
     * )
     */
    public function verifyEmail(Request $request, $id)
    {
        if (!$request->hasValidSignature()) {
            return $this->returnError(__('auth.errors.email.unvalid_signature'), 403);
        }

        $user = User::find($id);

        if (!$user) {
            return $this->returnError(__('errors.user.not_found'), 404);
        }

        if ($user->hasVerifiedEmail()) {
            return $this->returnError(__('auth.errors.email.already_verified'), 409);
        }

        $user->markEmailAsVerified();

        return $this->returnSuccess(__('auth.success.email.verified'));
    }
    /** 
     *@OA\Post(
     *     path="/api/email/verify/resend",
     *     summary="Resend Verification Email",
     *     tags={"Authentication"},
     *    operationId="ResendVerificationEmail",
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User email address",
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="alihasan@gmail.com", description="Required. Must exist in the users table..Length: min=11, max=64.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resend verification email successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="A verification email has been sent to your email successfully, Please verify your email address to verify your account."),
     *             @OA\Property(property="code", type="integer", example=200)
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
     *                     property="email",
     *                     type="array",
     *                     @OA\Items(type="string", example="The provided email does not exist in our records.")
     *                 )
     *             ),
     *             @OA\Property(property="code", type="integer", example=422)
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found.",
     *         @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="msg", type="string", example="User not found."),
     *           @OA\Property(property="code", type="integer", example=404)
     *        )
     *    ),
     *     @OA\Response(
     *         response=409,
     *         description="Your email already verified.",
     *         @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="msg", type="string", example="Your email already verified."),
     *           @OA\Property(property="code", type="integer", example=409)
     *       )
     *    ),
     *     @OA\Response(
     *         response=429,
     *         description="Exceeded the maximum number of attempts.",
     *         @OA\JsonContent(
     *           @OA\Property(property="status", type="boolean", example=false),
     *           @OA\Property(property="msg", type="string", example="You have exceeded the maximum number of attempts, Please wait few minutes before trying again."),
     *           @OA\Property(property="code", type="integer", example=429)
     *        )
     *    )
     * )
     */
    public function resendVerificationEmail(LoginAndResendEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->returnError(__('errors.user.not_found'), 404);
        }

        if ($user->email_verified_at) {
            return $this->returnError(__('auth.errors.email.already_verified'), 409);
        }

        $maxAttempts = 3;
        $waitingPeriod = 30;

        if ($user->verification_attempts >= $maxAttempts) {
            $lastVerification = Carbon::parse($user->last_verification_attempt_at);
            if (Carbon::now()->diffInMinutes($lastVerification) < $waitingPeriod) {
                return $this->returnError(__('auth.errors.email.many_attempts'), 429);
            }
            $user->verification_attempts = 0;
        }

        event(new VerifyEmail($user));
        $user->verification_attempts++;
        $user->last_verification_attempt_at = Carbon::now();
        $user->save();

        return $this->returnSuccess(__('auth.success.email.resend_verify'));
    }
}
