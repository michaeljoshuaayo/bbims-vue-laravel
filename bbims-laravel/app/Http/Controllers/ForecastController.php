<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ForecastController extends Controller
{
    public function getDailyUsage()
    {

        $usageData = DB::table('usage_histories')
            ->select(DB::raw('DATE(created_at) as usage_date'), DB::raw('COUNT(*) as daily_usage'))
            ->groupBy('usage_date')
            ->orderBy('usage_date')
            ->pluck('daily_usage');

        return response()->json($usageData);
    }

    public function getComponentUsage()
{
    $raw = DB::table('usage_histories')
        ->select(DB::raw('DATE(created_at) as usage_date'), 'blood_component', DB::raw('COUNT(*) as count'))
        ->groupBy('usage_date', 'blood_component')
        ->orderBy('usage_date')
        ->get();

    // Structure into component-wise daily time series
    $grouped = [];

    // First, group by date to know all dates
    $dates = $raw->pluck('usage_date')->unique()->sort()->values();

    // Pre-fill all components with 0s
    foreach ($raw->pluck('blood_component')->unique() as $component) {
        $grouped[$component] = array_fill(0, count($dates), 0);
    }

    foreach ($raw as $row) {
        $dateIndex = $dates->search($row->usage_date);
        $grouped[$row->blood_component][$dateIndex] = $row->count;
    }

    return response()->json($grouped);
}


}
