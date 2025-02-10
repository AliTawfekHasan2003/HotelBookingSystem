<?php

namespace App\Traits;


trait CalculateCostTrait
{
    use ResponseTrait;

    public function handelDate($startDate, $endDate)
    {
        $countMonth = $startDate->copy()->diffInMonths($endDate);
        $countDay = $startDate->copy()->addMonths($countMonth)->diffInDays($endDate);
 
        return [
            'countMonth' => $countMonth,
            'countDay' => $countDay,
        ];
    }

    public function handelPrice($startDate, $endDate, $monthlyPrice, $dailyPrice)
    {
        $date =  $this->handelDate($startDate, $endDate); 
        $cost = ($date['countMonth'] * $monthlyPrice) + ($date['countDay'] * $dailyPrice);
        
        return $cost;
    }

    public function calculateServicesCost($services, $startDate, $endDate)
    {
        $servicesCost = 0.00;

        foreach ($services as $service) {  
            if (!$service->is_free) {
                $servicesCost += $this->handelPrice($startDate, $endDate, $service->monthly_price, $service->daily_price);
            }
        } 

        return $servicesCost;
    }
}
