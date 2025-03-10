<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsageHistory;
use Carbon\Carbon;

class ForecastingController extends Controller
{
    public function forecastUsage(Request $request)
    {
        $period = $request->query('period', 'week'); // Default to 'week'

        if ($period === 'year') {
            return $this->forecastYearly();
        }

        // Define the number of days based on the selected period
        $days = match ($period) {
            'month' => 30,
            'year' => 365, // This is for detailed daily data, not yearly summary
            default => 7, // Default to week
        };

        // Fetch past usage data
        $pastData = UsageHistory::selectRaw('DATE(created_at) as date, COUNT(id) as total_used')
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days * 2)) // Use twice the period for trend detection
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();

        $forecast = $this->generateForecast($pastData, $days);

        return response()->json([
            'past' => $pastData,
            'forecast' => $forecast,
        ]);
    }

    private function forecastYearly()
    {
        $currentYear = Carbon::now()->year;

        // Get past 2 years' data grouped by year
        $pastData = UsageHistory::selectRaw('YEAR(created_at) as year, COUNT(id) as total_used')
            ->whereYear('created_at', '>=', $currentYear - 2) // Get last 2 years
            ->groupBy('year')
            ->orderBy('year', 'asc')
            ->get()
            ->toArray();

        $usageSeries = array_column($pastData, 'total_used');
        $years = array_column($pastData, 'year');

        $forecast = [];

        if (count($usageSeries) >= 2) {
            $trend = ($usageSeries[1] - $usageSeries[0]) / $usageSeries[0]; // Basic trend growth rate

            for ($i = 1; $i <= 3; $i++) { // Predict for 3 future years
                $nextYear = end($years) + 1;
                $nextUsage = end($usageSeries) * (1 + $trend); // Apply growth trend

                $forecast[] = [
                    'year' => $nextYear,
                    'predicted_usage' => round($nextUsage, 2),
                ];

                $usageSeries[] = $nextUsage;
                $years[] = $nextYear;
            }
        }

        return response()->json([
            'past' => $pastData,
            'forecast' => $forecast,
        ]);
    }

    private function generateForecast($pastData, $days)
    {
        $forecast = [];
        $window = min($days * 2, count($pastData)); // Ensure at least 'days' worth of history

        if ($window > 0) {
            $usageSeries = array_column($pastData, 'total_used');
            $dates = array_column($pastData, 'date');

            // Define decay factor for weighted moving average
            $decayFactor = 0.9;

            for ($i = 0; $i < $days; $i++) { // Predict for the chosen period
                $subset = array_slice($usageSeries, -$window, $window); // Get last N values

                // Apply exponentially decaying weights
                $weights = array_map(fn($j) => pow($decayFactor, $j), range(0, count($subset) - 1));

                // Compute weighted moving average
                $weightTotal = array_sum($weights);
                $weightedSum = array_sum(array_map(fn($key, $value) => ($weights[$key] / $weightTotal) * $value, array_keys($subset), $subset));

                // Adjust for seasonality (weekends and peak days)
                $lastDate = end($dates);
                $nextDate = Carbon::parse($lastDate)->addDays($i + 1);
                $weekday = $nextDate->format('N'); // 1 (Monday) to 7 (Sunday)

                if ($weekday == 7) $weightedSum *= 0.85; // Lower demand on Sundays
                if ($weekday == 1 || $weekday == 5) $weightedSum *= 1.1; // Higher on Mondays & Fridays

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

        return $forecast;
    }
}
