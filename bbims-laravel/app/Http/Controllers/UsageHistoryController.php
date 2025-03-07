<?php

namespace App\Http\Controllers;

use App\Models\UsageHistory;
use Illuminate\Http\Request;

class UsageHistoryController extends Controller
{
    public function index()
    {
        $usageHistory = UsageHistory::all();
        return response()->json($usageHistory);
    }
}
