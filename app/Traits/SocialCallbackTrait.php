<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;


trait SocialCallbackTrait
{
    use ResponseTrait;

    public function handleSocialCallback($provider)
    {
        DB::beginTransaction();
        try {
            $providerUser = Socialite::driver($provider)->scopes(['read:user'])->stateless()->user();
            $email = $providerUser->getEmail();
            $user_name = $providerUser->getName();

            if (is_null($email) || is_null($user_name)) {
                return $this->returnError(__('auth.errors.social.email_or_name'), 401);
            }

            $user = User::with('socialAccounts')->where('email', $email)->first();

            if (!$user) {
                $newUser = User::create([
                    'full_name' => $user_name,
                    'email' => $email,
                    'email_verified_at' => Carbon::now(),
                ]);

                $newUser->socialAccounts()->create([
                    'social_id' => $providerUser->getId(),
                    'social_name' => $provider,
                ]);

                $token = Auth::login($newUser);

                DB::commit();

                return $this->returnLoginRefreshSuccess(__('auth.success.social.register', ['provider' => $provider]), 'user', $newUser, $token);
            } else {
                $socialExists = $user->socialAccounts()->where('social_id', $providerUser->getId())->exists();

                if (!$socialExists) {
                    $user->socialAccounts()->create([
                        'social_id' => $providerUser->getId(),
                        'social_name' => $provider,
                    ]);
                }

                if (!$user->email_verified_at) {
                    $user->email_verified_at = Carbon::now();
                    $user->save();
                }

                $token = Auth::login($user);

                DB::commit();
            }
        } catch (\Exception $e) {
            Log::error($provider . " login error:" . $e->getMessage());

            DB::rollBack();
            return $this->returnError(__('errors.unexpected_error'), 500);
        }
        return $this->returnLoginRefreshSuccess(__('auth.success.social.login', ['provider' => $provider]), 'user', $user, $token);
    }
}
