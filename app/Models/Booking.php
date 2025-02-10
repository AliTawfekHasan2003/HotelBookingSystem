<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'bookingable_type',
        'bookingable_id',
        'original_monthly_price',
        'original_daily_price',
        'booking_cost',
    ];

    protected $casts = [
        'original_monthly_price' => 'decimal:2',
        'original_daily_price' => 'decimal:2',
        'booking_cost' => 'decimal:2',
    ];

    public function bookingable()
    {
        return $this->morphTo();
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function scopeFilterByBookingDates(Builder $query, $startDate, $endDate)
    {
        return $query->whereHas('invoice', function ($query) use ($startDate, $endDate) {
            return $query->where('status', '!=', 'cancelled')->where(function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $startDate)->where('end_date', '>=', $endDate)
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        return $query->whereBetween('start_date', [$startDate, $endDate])->orWhereBetween('end_date', [$startDate, $endDate]);
                    });
            });
        });
    }

    public function scopeFilterByBookingNow(Builder $query)
    {
        return  $query->whereHas('invoice', function ($query) {
            return $query->where('status', '!=', 'cancelled')->where('end_date', '>=', Carbon::now());
        });
    }

    public function scopeGetUnavailableDates(Builder $query)
    {
        $dates = $query->filterByBookingNow()->with('invoice:id,start_date,end_date')->get()
            ->pluck('invoice')
            ->map(function ($invoice) {
                return [
                    'start_date' => $invoice->start_date,
                    'end_date' => $invoice->end_date,
                ];
            });

        return  $dates;
    }

    public function scopeIsBooked(Builder $query)
    {
        $isExists = $query->filterByBookingNow()->exists();

        return  $isExists;
    }

    public function scopeIsAvailable(Builder $query, $startDate, $endDate)
    {
        $isExists = $query->filterByBookingDates($startDate, $endDate)->exists();

        return !$isExists;
    }

    public function scopeCountAvailableServiceUnits(Builder $query, $startDate, $endDate, $totalUnits)
    {
        $unavailableUnits = $query->filterByBookingDates($startDate, $endDate)->count();

        $countAvailableServiceUnits = $totalUnits - $unavailableUnits;

        return max($countAvailableServiceUnits, 0);
    }

    public static function addBooking($invoiceId, $monthlyPrice, $dailyPrice, $bookingCost, $model)
    {
        return
            self::create([
                'invoice_id' => $invoiceId,
                'original_monthly_price' => $monthlyPrice,
                'original_daily_price' => $dailyPrice,
                'booking_cost' => $bookingCost,
                'bookingable_type' => $model['type'],
                'bookingable_id' => $model['id'],
            ]);
    }
}
