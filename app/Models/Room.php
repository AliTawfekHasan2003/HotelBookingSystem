<?php

namespace App\Models;

use App\Traits\TranslationTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;

class Room extends Model
{
    use HasFactory, TranslationTrait, SoftDeletes;

    protected $fillable = [
        'room_type_id',
        'floor',
        'number',
        'image',
    ];

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable')->withTrashed();
    }

    public function favorites()
    {
        return $this->MorphMany(Favorite::class, 'favoriteable');
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function bookings()
    {
        return $this->morphMany(Booking::class, 'bookingable');
    }

    public function scopeNumber(Builder $query, $number)
    {
        return $query->where('number', $number);
    }

    public function scopeFloor(Builder $query, $floor)
    {
        return $query->where('floor', $floor);
    }

    public function scopeView(Builder $query, $view)
    {
        return $this->translationFilter($query, 'view', $view);
    }

    public static function filterRooms(Request $request, $trashed = false)
    {
        $query = $trashed ? self::query()->onlyTrashed() : self::query();

        $ifCriteria = false;

        if ($request->number) {
            $query->number($request->number);
            $ifCriteria = true;
        }

        if ($request->floor) {
            $query->floor($request->floor);
            $ifCriteria = true;
        }

        if ($request->view) {
            $query->view($request->view);
            $ifCriteria = true;
        }

        return ['query' => $query, 'ifCriteria' =>  $ifCriteria];
    }
}
