<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'favoriteable_type',
        'favoriteable_id',
    ];

    public function favoriteable()
    {
        return $this->morphTo();
    }

    public function scopeByUser(Builder $query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function scopeCheckIn(Builder $query)
    {
        return $query->byUser(auth()->id())->exists();
    }

    public static function addFavorite($model)
    {
        Favorite::create([
            'user_id' => auth()->id(),
            'favoriteable_type' => $model['type'],
            'favoriteable_id' => $model['id'],
        ]);
    }

    public static function destroyFavorite($model)
    {
        return self::byUser(auth()->id())->where('favoriteable_type', $model['type'])->where('favoriteable_id', $model['id'])->delete();
    }
}
