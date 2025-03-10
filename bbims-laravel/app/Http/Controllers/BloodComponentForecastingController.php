<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UsageHistory;
use Carbon\Carbon;

class BloodComponentForecastingController extends Controller
{
    public function forecastComponentUsage(Request $request)
    {
        $period = $request->query('period', 'week');
        $component = $request->query('component', 'Whole Blood');

        // If yearly forecast is requested, call the forecastYearly function
        if ($period === 'year') {
            return $this->forecastYearly($component);
        }

        // Define how many days of past data to use based on the period
        [$days, $groupBy] = match ($period) {
            'month' => [60, 'WEEK(created_at)'], // Use past 60 days (about 2 months), grouped by week
            default => [14, 'DATE(created_at)'], // Use past 14 days (2 weeks), grouped by day
        };

        // Fetch past usage data for the selected component
        $pastData = UsageHistory::selectRaw("DATE_FORMAT(created_at, '%Y-%m-%d') as date, COUNT(id) as total_used")
            ->where('blood_component', $component)
            ->whereDate('created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->toArray();

        // Define how many future days to predict based on the period
        $forecast = $this->generateForecast($pastData, match ($period) {
            'month' => 30, // Predict next 30 days (1 month)
            default => 7, // Predict next 7 days (1 week)
        });

        return response()->json(['past' => $pastData, 'forecast' => $forecast]);
    }

    private function forecastYearly($component = null)
    {
        $currentYear = Carbon::now()->year;

        // Get past 2 years' data grouped by year
        $query = UsageHistory::selectRaw('YEAR(created_at) as year, COUNT(id) as total_used')
            ->whereYear('created_at', '>=', $currentYear - 2)
            ->groupBy('year')
            ->orderBy('year', 'asc');

        if ($component) {
            $query->where('blood_component', $component);
        }

        $pastData = $query->get()->toArray();
        $usageSeries = array_column($pastData, 'total_used');
        $years = array_column($pastData, 'year');

        $forecast = [];

        // If we have at least two years of data, predict for the next 3 years
        if (count($usageSeries) >= 2) {
            $trend = ($usageSeries[1] - $usageSeries[0]) / $usageSeries[0]; // Calculate basic trend growth rate

            for ($i = 1; $i <= 3; $i++) { // Predict for next 3 years
                $nextYear = end($years) + 1;
                $nextUsage = end($usageSeries) * (1 + $trend);

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
        $window = min($days * 2, count($pastData)); // Use a rolling window of twice the forecast length or the available data size

        if ($window > 0) {
            $usageSeries = array_column($pastData, 'total_used');
            $dates = array_column($pastData, 'date');
            $decayFactor = 0.9; // Weight decay factor for recent values

            for ($i = 0; $i < $days; $i++) {
                $subset = array_slice($usageSeries, -$window, $window);
                $weights = array_map(fn($j) => pow($decayFactor, $j), range(0, count($subset) - 1));
                $weightTotal = array_sum($weights);
                $weightedSum = array_sum(array_map(fn($key, $value) => ($weights[$key] / $weightTotal) * $value, array_keys($subset), $subset));

                $lastDate = end($dates);
                $nextDate = Carbon::parse($lastDate)->addDays($i + 1);
                $weekday = $nextDate->format('N'); // Get the day of the week (1=Monday, 7=Sunday)

                // Adjust forecast for weekends and peak weekdays
                if ($weekday == 7) $weightedSum *= 0.85; // Reduce for Sundays
                if ($weekday == 1 || $weekday == 5) $weightedSum *= 1.1; // Increase for Mondays and Fridays

                $forecast[] = [
                    'date' => $nextDate->format('Y-m-d'),
                    'predicted_usage' => round($weightedSum, 2),
                ];

                $usageSeries[] = $weightedSum;
                $dates[] = $nextDate->format('Y-m-d');
            }
        }

        return $forecast;
    }
}
