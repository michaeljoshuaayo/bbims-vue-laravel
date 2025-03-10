<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\UsageHistory;

class DashboardController extends Controller
{
    public function getDashboardData()
    {
        $bloodUnitsInStock = BloodInventory::count();
        $distributedBloodUnits = UsageHistory::count();
        $bloodRequests = BloodRequest::count();
        $pendingRequests = BloodRequest::where('status', 'Pending')->count();
        $acceptedRequests = BloodRequest::where('status', 'Accepted')->count();
        $discardedBloodUnits = BloodInventory::where('inventoryStatus', 'Discarded')->count();
        $newUnitsLast24Hours = BloodInventory::where('created_at', '>=', now()->subDay())->count();

        return response()->json([
            'bloodUnitsInStock' => $bloodUnitsInStock,
            'distributedBloodUnits' => $distributedBloodUnits,
            'bloodRequests' => $bloodRequests,
            'pendingRequests' => $pendingRequests,
            'acceptedRequests' => $acceptedRequests,
            'discardedBloodUnits' => $discardedBloodUnits,
            'newUnitsLast24Hours' => $newUnitsLast24Hours,
        ]);
    }
}
