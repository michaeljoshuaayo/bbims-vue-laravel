<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
use App\Models\UsageHistory;
use App\Models\BloodRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import the Log facade

class BloodInventoryController extends Controller
{
    public function index()
    {
        return BloodInventory::all();
    }

    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'bloodSerialNumber' => 'required|string|max:255',
            'bloodType' => 'required|string|max:255',
            'bloodComponent' => 'required|string|max:255',
            'expiryDate' => 'required|date',
            'inventoryStatus' => 'required|string|max:255',
        ]);

        try {
            // Create a new BloodInventory record
            $bloodInventory = BloodInventory::create($validatedData);
            return response()->json($bloodInventory, 201);
        } catch (\Exception $e) {
            // Log the error and return a 500 response
            Log::error('Error creating blood inventory: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create blood inventory'], 500);
        }
    }

    public function show($id)
    {
        return BloodInventory::find($id);
    }

    public function update(Request $request, $id)
    {
        $bloodInventory = BloodInventory::findOrFail($id);
        $bloodInventory->update($request->all());
        return response()->json($bloodInventory, 200);
    }

    public function destroy($id)
    {
        BloodInventory::destroy($id);
        return response()->json(null, 204);
    }

    public function deleteMultiple(Request $request)
    {
        $ids = $request->input('ids');
        BloodInventory::whereIn('id', $ids)->delete();
        return response()->json(null, 204);
    }

    public function updateAndLog(Request $request)
    {
        $bloodRequestId = $request->input('bloodRequestId');
        $bloodRequest = BloodRequest::with('requisitionItems')->findOrFail($bloodRequestId);

        foreach ($bloodRequest->requisitionItems as $item) {
            $bloodInventoryItems = BloodInventory::where('bloodType', $item->blood_type)
                ->where('bloodComponent', $item->blood_component)
                ->where('inventoryStatus', 'AVAILABLE')
                ->orderBy('expiryDate', 'asc') // Order by expiry date in ascending order
                ->take($item->quantity)
                ->get();

            if ($bloodInventoryItems->count() < $item->quantity) {
                return response()->json(['error' => 'Not enough blood inventory available'], 400);
            }

            // Ensure we only process the exact quantity needed
            $processedQuantity = 0;
            foreach ($bloodInventoryItems as $inventoryItem) {
                if ($processedQuantity >= $item->quantity) {
                    break;
                }

                $inventoryItem->inventoryStatus = 'USED';
                $inventoryItem->save();

                UsageHistory::create([
                    'blood_request_id' => $bloodRequestId,
                    'blood_serial_number' => $inventoryItem->bloodSerialNumber,
                    'blood_type' => $inventoryItem->bloodType,
                    'blood_component' => $inventoryItem->bloodComponent,
                    'remarks' => $item->remarks,
                ]);

                $processedQuantity++;

                // Automatically delete the 'USED' inventory item
                // $this->deleteUsedInventory($inventoryItem->id);
            }
        }

        return response()->json(['message' => 'Blood inventory updated and usage history logged successfully'], 200);
    }

    private function deleteUsedInventory($id)
    {
        $bloodInventory = BloodInventory::find($id);
        if ($bloodInventory && $bloodInventory->inventoryStatus == 'USED') {
            $bloodInventory->delete();
        }
    }
}
