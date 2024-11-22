<?php

namespace App\Http\Controllers;

use App\Models\BloodInventory;
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
}
