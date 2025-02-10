<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use App\Traits\SocialCallbackTrait;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthSocialController extends Controller
{
    use ResponseTrait, SocialCallbackTrait;

    /**   @OA\Get(
     *     path="/api/auth/google",
     *     summary="Redirect To Google For Authentication",
     *     tags={"Authentication"},
     *     operationId="redirectToGoogle",
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to Google authentication page"
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

    public function redirectToGoogle()
    {
        try {
            return Socialite::driver('google')->stateless()->redirect();
        } catch (Throwable $e) {
            Log::error('An unexpected error occurred' . $e);
            $this->returnError(__('unexpected_error'), 500);
        }
    }

    /**   @OA\Get(
     *     path="/api/auth/google/callback",
     *     summary="Handle Google OAuth2 Callback",
     *     tags={"Authentication"},
     *     operationId="googleCallback",
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Register by your google account successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="A new account has been successfully created and linked with your google account."),
     *             @OA\Property(property="code", type="integer", example=201),
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
     *      @OA\Response(
     *         response=200,
     *         description="Login by your google account successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Successfully logged in using google."),
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
     *         description="Email or Name is missing",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Email or Name is missing. Please ensure that email and name permissions are enabled in your social account settings, then try again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *    ),
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

    public function googleCallback()
    {
        return $this->handleSocialCallback('google');
    }

    /**   @OA\Get(
     *     path="/api/auth/github",
     *     summary="Redirect To Github For Authentication",
     *     tags={"Authentication"},
     *     operationId="redirectToGithub",
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=302,
     *         description="Redirect to Github authentication page"
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

    public function redirectToGithub()
    {
        try {
            return Socialite::driver('github')->stateless()->redirect();
        } catch (Throwable $e) {
            Log::error('An unexpected error occurred' . $e);
            $this->returnError(__('unexpected_error'), 500);
        }
    }

    /**   @OA\Get(
     *     path="/api/auth/github/callback",
     *     summary="Handle Github OAuth2 Callback",
     *     tags={"Authentication"},
     *     operationId="githubCallback",
     *    @OA\Parameter(
     *         name="Accept-language",
     *         in="header",
     *         description="Set the language for the response (e.g., 'en' for English, 'ar' for Arabic)",
     *         required=true,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Register by your github account successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="A new account has been successfully created and linked with your github account."),
     *             @OA\Property(property="code", type="integer", example=201),
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
     *      @OA\Response(
     *         response=200,
     *         description="Login by your github account successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="msg", type="string", example="Successfully logged in using github."),
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
     *         description="Email or Name is missing",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="msg", type="string", example="Email or Name is missing. Please ensure that email and name permissions are enabled in your social account settings, then try again."),
     *             @OA\Property(property="code", type="integer", example=401)
     *         )
     *    ),
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

    public function githubCallback()
    {
        return $this->handleSocialCallback('github');
    }
}
