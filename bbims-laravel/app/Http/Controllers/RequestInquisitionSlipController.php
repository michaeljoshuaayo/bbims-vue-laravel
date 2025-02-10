<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BloodRequest;
use App\Models\RequisitionItem;

class RequestInquisitionSlipController extends Controller
{
    public function index()
    {
        $bloodRequests = BloodRequest::with('requisitionItems')->get();
        return response()->json($bloodRequests);
    }

    public function store(Request $request)
    {
        $request->validate([
            'requestingFacility' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'pathologist' => 'required|string|max:255',
            'facilityTransacNum' => 'required|string|max:255',
            'requestedBy' => 'required|string|max:255',
            'requisitionItems' => 'required|array',
            'requisitionItems.*.bloodComponent' => 'required|string|max:255',
            'requisitionItems.*.bloodType' => 'required|string|max:255',
            'requisitionItems.*.quantity' => 'required|integer',
            'requisitionItems.*.remarks' => 'nullable|string|max:255',
            'uncrossmatchedQuantities' => 'required|array',
            'crossmatchedQuantities' => 'required|array',
        ]);

        $bloodRequest = BloodRequest::create([
            'requesting_facility' => $request->requestingFacility,
            'address' => $request->address,
            'pathologist' => $request->pathologist,
            'facility_transac_num' => $request->facilityTransacNum,
            'requested_by' => $request->requestedBy,
            'uncrossmatched_whole_blood_a_plus' => $request->uncrossmatchedQuantities['wholeBlood']['APlus'],
            'uncrossmatched_whole_blood_a_minus' => $request->uncrossmatchedQuantities['wholeBlood']['AMinus'],
            'uncrossmatched_whole_blood_b_plus' => $request->uncrossmatchedQuantities['wholeBlood']['BPlus'],
            'uncrossmatched_whole_blood_b_minus' => $request->uncrossmatchedQuantities['wholeBlood']['BMinus'],
            'uncrossmatched_whole_blood_o_plus' => $request->uncrossmatchedQuantities['wholeBlood']['OPlus'],
            'uncrossmatched_whole_blood_o_minus' => $request->uncrossmatchedQuantities['wholeBlood']['OMinus'],
            'uncrossmatched_whole_blood_ab_plus' => $request->uncrossmatchedQuantities['wholeBlood']['ABPlus'],
            'uncrossmatched_whole_blood_ab_minus' => $request->uncrossmatchedQuantities['wholeBlood']['ABMinus'],
            'uncrossmatched_packed_rbc_a_plus' => $request->uncrossmatchedQuantities['packedRBC']['APlus'],
            'uncrossmatched_packed_rbc_a_minus' => $request->uncrossmatchedQuantities['packedRBC']['AMinus'],
            'uncrossmatched_packed_rbc_b_plus' => $request->uncrossmatchedQuantities['packedRBC']['BPlus'],
            'uncrossmatched_packed_rbc_b_minus' => $request->uncrossmatchedQuantities['packedRBC']['BMinus'],
            'uncrossmatched_packed_rbc_o_plus' => $request->uncrossmatchedQuantities['packedRBC']['OPlus'],
            'uncrossmatched_packed_rbc_o_minus' => $request->uncrossmatchedQuantities['packedRBC']['OMinus'],
            'uncrossmatched_packed_rbc_ab_plus' => $request->uncrossmatchedQuantities['packedRBC']['ABPlus'],
            'uncrossmatched_packed_rbc_ab_minus' => $request->uncrossmatchedQuantities['packedRBC']['ABMinus'],
            'crossmatched_whole_blood_a_plus' => $request->crossmatchedQuantities['wholeBlood']['APlus'],
            'crossmatched_whole_blood_a_minus' => $request->crossmatchedQuantities['wholeBlood']['AMinus'],
            'crossmatched_whole_blood_b_plus' => $request->crossmatchedQuantities['wholeBlood']['BPlus'],
            'crossmatched_whole_blood_b_minus' => $request->crossmatchedQuantities['wholeBlood']['BMinus'],
            'crossmatched_whole_blood_o_plus' => $request->crossmatchedQuantities['wholeBlood']['OPlus'],
            'crossmatched_whole_blood_o_minus' => $request->crossmatchedQuantities['wholeBlood']['OMinus'],
            'crossmatched_whole_blood_ab_plus' => $request->crossmatchedQuantities['wholeBlood']['ABPlus'],
            'crossmatched_whole_blood_ab_minus' => $request->crossmatchedQuantities['wholeBlood']['ABMinus'],
            'crossmatched_packed_rbc_a_plus' => $request->crossmatchedQuantities['packedRBC']['APlus'],
            'crossmatched_packed_rbc_a_minus' => $request->crossmatchedQuantities['packedRBC']['AMinus'],
            'crossmatched_packed_rbc_b_plus' => $request->crossmatchedQuantities['packedRBC']['BPlus'],
            'crossmatched_packed_rbc_b_minus' => $request->crossmatchedQuantities['packedRBC']['BMinus'],
            'crossmatched_packed_rbc_o_plus' => $request->crossmatchedQuantities['packedRBC']['OPlus'],
            'crossmatched_packed_rbc_o_minus' => $request->crossmatchedQuantities['packedRBC']['OMinus'],
            'crossmatched_packed_rbc_ab_plus' => $request->crossmatchedQuantities['packedRBC']['ABPlus'],
            'crossmatched_packed_rbc_ab_minus' => $request->crossmatchedQuantities['packedRBC']['ABMinus'],
            'status' => 'Pending',
        ]);

        foreach ($request->requisitionItems as $item) {
            RequisitionItem::create([
                'blood_request_id' => $bloodRequest->id,
                'blood_component' => $item['bloodComponent'],
                'blood_type' => $item['bloodType'],
                'quantity' => $item['quantity'],
                'remarks' => $item['remarks'],
            ]);
        }

        return response()->json(['message' => 'Blood request created successfully'], 201);
    }

    public function show($id)
    {
        $bloodRequest = BloodRequest::with('requisitionItems')->findOrFail($id);
        return response()->json($bloodRequest);
    }

    public function showRequisitionItems($bloodRequestId)
    {
        $requisitionItems = RequisitionItem::where('blood_request_id', $bloodRequestId)->get();
        return response()->json($requisitionItems);
    }

    
    public function update(Request $request, $id)
    {
        $request->validate([
            'requestingFacility' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'pathologist' => 'required|string|max:255',
            'facilityTransacNum' => 'required|string|max:255',
            'requestedBy' => 'required|string|max:255',
            'requisitionItems' => 'required|array',
            'requisitionItems.*.bloodComponent' => 'required|string|max:255',
            'requisitionItems.*.bloodType' => 'required|string|max:255',
            'requisitionItems.*.quantity' => 'required|integer',
            'requisitionItems.*.remarks' => 'nullable|string|max:255',
            'uncrossmatchedQuantities.wholeBlood.APlus' => 'required|integer',
            'uncrossmatchedQuantities.wholeBlood.AMinus' => 'required|integer',
            'uncrossmatchedQuantities.wholeBlood.BPlus' => 'required|integer',
            'uncrossmatchedQuantities.wholeBlood.BMinus' => 'required|integer',
            'uncrossmatchedQuantities.wholeBlood.OPlus' => 'required|integer',
            'uncrossmatchedQuantities.wholeBlood.OMinus' => 'required|integer',
            'uncrossmatchedQuantities.wholeBlood.ABPlus' => 'required|integer',
            'uncrossmatchedQuantities.wholeBlood.ABMinus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.APlus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.AMinus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.BPlus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.BMinus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.OPlus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.OMinus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.ABPlus' => 'required|integer',
            'uncrossmatchedQuantities.packedRBC.ABMinus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.APlus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.AMinus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.BPlus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.BMinus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.OPlus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.OMinus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.ABPlus' => 'required|integer',
            'crossmatchedQuantities.wholeBlood.ABMinus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.APlus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.AMinus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.BPlus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.BMinus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.OPlus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.OMinus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.ABPlus' => 'required|integer',
            'crossmatchedQuantities.packedRBC.ABMinus' => 'required|integer',
        ]);

        $bloodRequest = BloodRequest::findOrFail($id);
        $bloodRequest->update([
            'requesting_facility' => $request->requestingFacility,
            'address' => $request->address,
            'pathologist' => $request->pathologist,
            'facility_transac_num' => $request->facilityTransacNum,
            'requested_by' => $request->requestedBy,
            'uncrossmatched_whole_blood_a_plus' => $request->uncrossmatchedQuantities['wholeBlood']['APlus'],
            'uncrossmatched_whole_blood_a_minus' => $request->uncrossmatchedQuantities['wholeBlood']['AMinus'],
            'uncrossmatched_whole_blood_b_plus' => $request->uncrossmatchedQuantities['wholeBlood']['BPlus'],
            'uncrossmatched_whole_blood_b_minus' => $request->uncrossmatchedQuantities['wholeBlood']['BMinus'],
            'uncrossmatched_whole_blood_o_plus' => $request->uncrossmatchedQuantities['wholeBlood']['OPlus'],
            'uncrossmatched_whole_blood_o_minus' => $request->uncrossmatchedQuantities['wholeBlood']['OMinus'],
            'uncrossmatched_whole_blood_ab_plus' => $request->uncrossmatchedQuantities['wholeBlood']['ABPlus'],
            'uncrossmatched_whole_blood_ab_minus' => $request->uncrossmatchedQuantities['wholeBlood']['ABMinus'],
            'uncrossmatched_packed_rbc_a_plus' => $request->uncrossmatchedQuantities['packedRBC']['APlus'],
            'uncrossmatched_packed_rbc_a_minus' => $request->uncrossmatchedQuantities['packedRBC']['AMinus'],
            'uncrossmatched_packed_rbc_b_plus' => $request->uncrossmatchedQuantities['packedRBC']['BPlus'],
            'uncrossmatched_packed_rbc_b_minus' => $request->uncrossmatchedQuantities['packedRBC']['BMinus'],
            'uncrossmatched_packed_rbc_o_plus' => $request->uncrossmatchedQuantities['packedRBC']['OPlus'],
            'uncrossmatched_packed_rbc_o_minus' => $request->uncrossmatchedQuantities['packedRBC']['OMinus'],
            'uncrossmatched_packed_rbc_ab_plus' => $request->uncrossmatchedQuantities['packedRBC']['ABPlus'],
            'uncrossmatched_packed_rbc_ab_minus' => $request->uncrossmatchedQuantities['packedRBC']['ABMinus'],
            'crossmatched_whole_blood_a_plus' => $request->crossmatchedQuantities['wholeBlood']['APlus'],
            'crossmatched_whole_blood_a_minus' => $request->crossmatchedQuantities['wholeBlood']['AMinus'],
            'crossmatched_whole_blood_b_plus' => $request->crossmatchedQuantities['wholeBlood']['BPlus'],
            'crossmatched_whole_blood_b_minus' => $request->crossmatchedQuantities['wholeBlood']['BMinus'],
            'crossmatched_whole_blood_o_plus' => $request->crossmatchedQuantities['wholeBlood']['OPlus'],
            'crossmatched_whole_blood_o_minus' => $request->crossmatchedQuantities['wholeBlood']['OMinus'],
            'crossmatched_whole_blood_ab_plus' => $request->crossmatchedQuantities['wholeBlood']['ABPlus'],
            'crossmatched_whole_blood_ab_minus' => $request->crossmatchedQuantities['wholeBlood']['ABMinus'],
            'crossmatched_packed_rbc_a_plus' => $request->crossmatchedQuantities['packedRBC']['APlus'],
            'crossmatched_packed_rbc_a_minus' => $request->crossmatchedQuantities['packedRBC']['AMinus'],
            'crossmatched_packed_rbc_b_plus' => $request->crossmatchedQuantities['packedRBC']['BPlus'],
            'crossmatched_packed_rbc_b_minus' => $request->crossmatchedQuantities['packedRBC']['BMinus'],
            'crossmatched_packed_rbc_o_plus' => $request->crossmatchedQuantities['packedRBC']['OPlus'],
            'crossmatched_packed_rbc_o_minus' => $request->crossmatchedQuantities['packedRBC']['OMinus'],
            'crossmatched_packed_rbc_ab_plus' => $request->crossmatchedQuantities['packedRBC']['ABPlus'],
            'crossmatched_packed_rbc_ab_minus' => $request->crossmatchedQuantities['packedRBC']['ABMinus'],
        ]);

        $bloodRequest->requisitionItems()->delete();
        foreach ($request->requisitionItems as $item) {
            RequisitionItem::create([
                'blood_request_id' => $bloodRequest->id,
                'blood_component' => $item['bloodComponent'],
                'blood_type' => $item['bloodType'],
                'quantity' => $item['quantity'],
                'remarks' => $item['remarks'],
            ]);
        }

        return response()->json(['message' => 'Blood request updated successfully'], 200);
    }

    public function destroy($id)
    {
        $bloodRequest = BloodRequest::findOrFail($id);
        $bloodRequest->delete();
        return response()->json(['message' => 'Blood request deleted successfully'], 200);
    }

    public function deleteMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:blood_requests,id',
        ]);

        BloodRequest::whereIn('id', $request->ids)->delete();
        return response()->json(['message' => 'Blood requests deleted successfully'], 200);
    }
}
