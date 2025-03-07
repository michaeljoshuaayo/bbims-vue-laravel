<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\RequisitionItem;
use App\Models\BloodInventory;
use App\Models\UsageHistory;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    public function accept($id)
    {
        $bloodRequest = BloodRequest::with('requisitionItems')->findOrFail($id);

        foreach ($bloodRequest->requisitionItems as $item) {
            $bloodInventoryItems = BloodInventory::where('bloodType', $item->blood_type)
                ->where('bloodComponent', $item->blood_component)
                ->where('inventoryStatus', 'AVAILABLE')
                ->take($item->quantity)
                ->get();

            if ($bloodInventoryItems->count() < $item->quantity) {
                return response()->json(['error' => 'Not enough blood inventory available'], 400);
            }
        }

        $bloodRequest->status = 'Accepted';
        $bloodRequest->save();

        return response()->json(['message' => 'Blood request accepted and inventory updated'], 200);
    }
}
