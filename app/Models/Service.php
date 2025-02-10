<?php

namespace App\Models;

use App\Traits\ResponseTrait;
use App\Traits\TranslationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Service extends Model
{
    use ResponseTrait, TranslationTrait, SoftDeletes;

    protected $fillable = [
        'image',
        'is_limited',
        'total_units',
        'is_free',
        'daily_price',
        'monthly_price',
    ];

    protected $casts = [
        'daily_price' => 'decimal:2',
        'monthly_price' => 'decimal:2',
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable')->withTrashed();
    }

    public function favorites()
    {
        return $this->MorphMany(Favorite::class, 'favoriteable');
    }

    public function roomTypeServices()
    {
        return $this->hasMany(RoomTypeService::class);
    }

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'room_type_services');
    }

    public function bookings()
    {
        return $this->MorphMany(Booking::class, 'bookingable');
    }

    public function scopeIsLimited(Builder $query, $isLimited)
    {
        return $query->where('is_limited', (int) $isLimited);
    }

    public function scopeIsFree(Builder $query, $isFree)
    {
        return $query->where('is_free', (int) $isFree);
    }

    public function scopeName(Builder $query, $name)
    {
        return $this->translationFilter($query, 'name', $name);
    }

    public function scopeCategory(Builder $query, $category)
    {
        return $this->translationFilter($query, 'category', $category);
    }

    public static function filterServices(Request $request, $trashed = false)
    {
        $query = $trashed ? self::query()->onlyTrashed() : self::query();

        $ifCriteria = false;

        if ($request->filled('is_limited')) {
            $query->isLimited($request->is_limited);
            $ifCriteria = true;
        }

        if ($request->filled('is_free')) {
            $query->isFree($request->is_free);
            $ifCriteria = true;
        }

        if ($request->filled('name')) {
            $query->name($request->name);
            $ifCriteria = true;
        }

        if ($request->filled('category')) {
            $query->category($request->category);
            $ifCriteria = true;
        }

        return ['query' => $query, 'ifCriteria' =>  $ifCriteria];
    }
}
