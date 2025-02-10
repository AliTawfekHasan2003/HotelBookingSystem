<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomTypeService extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'room_type_id',
        'service_id',
    ];

    public function service()
    {
         return $this->belongsTo(Service::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function scopeFindByIds(Builder $query, $room_type_id, $service_id)
    {
        return $query->where('room_type_id', $room_type_id)->where('service_id', $service_id);
    }
}
