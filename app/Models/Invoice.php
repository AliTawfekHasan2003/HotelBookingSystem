<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'count_month',
        'count_day',
        'total_cost',
        'status',
        'payment_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'total_cost' => 'decimal:2',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeByUser(Builder $query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function scopeStatus(Builder $query, $status)
    {
        return $query->where('status', $status);
    }

    public static function addInvoice($startDate, $endDate, $countMonth, $countDay, $totalCost, $paymentId)
    {
        return
            self::create([
                'user_id' => auth()->id(),
                'start_date' => $startDate,
                'end_date' => $endDate,
                'count_month' => $countMonth,
                'count_day' => $countDay,
                'total_cost' => round($totalCost, 2),
                'status' => 'pending',
                'payment_id' => $paymentId,
            ]);
    }

    public static function filterInvoices(Request $request, $user_id = null)
    {
        $query = self::query();

        $ifCriteria = false;

        if ($request->status) {
            $query->status($request->status);
            $ifCriteria = true;
        }

        if ($user_id) {
            $query->byUser($user_id);
        }

        return ['query' => $query, 'ifCriteria' =>  $ifCriteria];
    }
}
