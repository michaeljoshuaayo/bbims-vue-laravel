<?php

namespace App\Http\Controllers;

use App\Models\UsageHistory;
use Carbon\Carbon;

class ForecastingController extends Controller
{
    public function forecastUsage()
    {
        // Get past 14 days of blood usage for better trend and seasonality detection
        $pastData = UsageHistory::selectRaw('DATE(created_at) as date, COUNT(id) as total_used')
            ->whereDate('created_at', '>=', Carbon::now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();

        $forecast = [];
        $window = min(14, count($pastData)); // Use available data if less than 14 days

        if ($window > 0) {
            $usageSeries = array_column($pastData, 'total_used');
            $dates = array_column($pastData, 'date');

            // Define decay factor (recent data has more weight)
            $decayFactor = 0.9;
            
            for ($i = 0; $i < 7; $i++) { // Predict next 7 days
                $subset = array_slice($usageSeries, -$window, $window); // Get last N values
                
                // Apply exponentially decaying weights
                $weights = [];
                for ($j = 0; $j < count($subset); $j++) {
                    $weights[] = pow($decayFactor, $j);
                }

                // Compute weighted moving average
                $weightedSum = 0;
                $weightTotal = array_sum($weights);
                foreach ($subset as $key => $value) {
                    $weightedSum += ($weights[$key] / $weightTotal) * $value;
                }

                // Adjust for seasonality (detect weekly trend)
                $lastDate = end($dates);
                $nextDate = Carbon::parse($lastDate)->addDays($i + 1);
                $weekday = $nextDate->format('N'); // 1 (Monday) to 7 (Sunday)

                // Adjust weekend demand (lower usage on Sundays)
                if ($weekday == 7) {
                    $weightedSum *= 0.85; // Reduce by 15%
                }
                
                // Adjust for peak weekday demand (e.g., Monday/Friday peaks)
                if ($weekday == 1 || $weekday == 5) {
                    $weightedSum *= 1.1; // Increase by 10%
                }

                // Store the prediction
                $forecast[] = [
                    'date' => $nextDate->format('Y-m-d'),
                    'predicted_usage' => round($weightedSum, 2),
                ];

                // Append prediction to series
                $usageSeries[] = $weightedSum;
                $dates[] = $nextDate->format('Y-m-d');
            }
        }

        return response()->json([
            'past' => $pastData,
            'forecast' => $forecast,
        ]);
    }
}
