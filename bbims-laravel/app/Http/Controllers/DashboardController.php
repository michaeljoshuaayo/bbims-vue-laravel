<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\UsageHistory;
use Illuminate\Support\Facades\DB;

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
    public function getDistributedBloodData()
    {
        $data = DB::table('usage_histories')
            ->select('blood_type')
            ->selectRaw("COUNT(CASE WHEN blood_component = 'Whole Blood' THEN 1 END) as `Whole Blood`")
            ->selectRaw("COUNT(CASE WHEN blood_component = 'Packed RBC' THEN 1 END) as `Packed RBC`")
            ->selectRaw("COUNT(CASE WHEN blood_component = 'Fresh Frozen Plasma' THEN 1 END) as `Fresh Frozen Plasma`")
            ->selectRaw("COUNT(CASE WHEN blood_component = 'Platelet Concentrate' THEN 1 END) as `Platelet Concentrate`")
            ->groupBy('blood_type')
            ->get();
    
        return response()->json($data);
    }
}
