<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\SocialAccount;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'password',
        'email_verified_at',
        'last_verification_attempt_at',
        'verification_attempts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_verification_attempt_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getFirstNameAttribute()
    {
        $name = explode(" ", $this->full_name);

        return $name[0] ?? '';
    }
    public function getLastNameAttribute()
    {
        $name = explode(" ", $this->full_name);

        return $name[1] ?? '';
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function checkHasRole($role)
    {
        $hasRole = false;

        if ($this->role === $role) {
            $hasRole = true;
        }

        return $hasRole;
    }
}
