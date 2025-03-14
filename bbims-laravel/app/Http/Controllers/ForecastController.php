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
}
