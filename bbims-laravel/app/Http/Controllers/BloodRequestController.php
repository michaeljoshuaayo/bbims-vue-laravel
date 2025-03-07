<?php

namespace App\Http\Controllers;

use App\Models\BloodRequest;
use App\Models\RequisitionItem;
use App\Models\BloodInventory;
use App\Models\UsageHistory;
use Illuminate\Http\Request;

class BloodRequestController extends Controller
{
    public function accept(Request $request, $id)
    {
        $bloodRequest = BloodRequest::with('requisitionItems')->findOrFail($id);
        $insufficientItems = [];

        foreach ($bloodRequest->requisitionItems as $item) {
            $bloodInventoryItems = BloodInventory::where('bloodType', $item->blood_type)
                ->where('bloodComponent', $item->blood_component)
                ->where('inventoryStatus', 'AVAILABLE')
                ->orderBy('expiryDate', 'asc') // Order by expiry date in ascending order
                ->take($item->quantity)
                ->get();

            if ($bloodInventoryItems->count() < $item->quantity) {
                $insufficientItems[] = [
                    'blood_type' => $item->blood_type,
                    'blood_component' => $item->blood_component,
                    'requested_quantity' => $item->quantity,
                    'available_quantity' => $bloodInventoryItems->count()
                ];
            }
        }

        if (!empty($insufficientItems)) {
            foreach ($insufficientItems as $item) {
                if ($item['available_quantity'] == 0) {
                    return response()->json(['message' => 'Cannot proceed with 0 available products', 'insufficientItems' => $insufficientItems], 400);
                }
            }

            if (!$request->input('force', false)) {
                return response()->json(['insufficientItems' => $insufficientItems], 400);
            }
        }

        // Update inventory and log usage history
        foreach ($bloodRequest->requisitionItems as $item) {
            $bloodInventoryItems = BloodInventory::where('bloodType', $item->blood_type)
                ->where('bloodComponent', $item->blood_component)
                ->where('inventoryStatus', 'AVAILABLE')
                ->orderBy('expiryDate', 'asc')
                ->take($item->quantity)
                ->get();

            $processedQuantity = 0;
            foreach ($bloodInventoryItems as $inventoryItem) {
                if ($processedQuantity >= $item->quantity) {
                    break;
                }

                $inventoryItem->inventoryStatus = 'USED';
                $inventoryItem->save();

                UsageHistory::create([
                    'blood_request_id' => $bloodRequest->id,
                    'blood_serial_number' => $inventoryItem->bloodSerialNumber,
                    'blood_type' => $inventoryItem->bloodType,
                    'blood_component' => $inventoryItem->bloodComponent,
                    'remarks' => $item->remarks,
                ]);

                $processedQuantity++;
            }
        }

        $bloodRequest->status = 'Accepted';
        $bloodRequest->save();

        return response()->json(['message' => 'Blood request accepted and inventory updated'], 200);
    }
}
