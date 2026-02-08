<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\PtrRequest;
use App\Services\DocNumberService;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PtrController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        Roles::require($user, ['REQUESTOR']);

        $data = $request->validate([
            'asset_id' => 'required|integer|exists:assets,id',
            'from_user_id' => 'required|integer|exists:users,useid',
            'to_user_id' => 'required|integer|exists:users,useid|different:from_user_id',
            'reason' => 'required|string',
        ]);

        $asset = Asset::findOrFail($data['asset_id']);
        if ($asset->custodian_user_id != $data['from_user_id']) {
            abort(400, 'Asset custodian does not match from_user_id.');
        }

        $ptr = PtrRequest::create([
            'ptr_no' => DocNumberService::next('PTR', 'ptr_requests', 'ptr_no'),
            'status' => 'DRAFT',
            'asset_id' => $data['asset_id'],
            'from_user_id' => $data['from_user_id'],
            'to_user_id' => $data['to_user_id'],
            'reason' => $data['reason'],
            'created_by_user_id' => $user->useid ?? $user->id,
        ]);

        return response()->json($ptr, 201);
    }

    public function submit(Request $request, PtrRequest $ptr)
    {
        $user = $request->user();
        if ($ptr->created_by_user_id !== ($user->useid ?? $user->id)) {
            abort(403, 'Only creator can submit.');
        }
        if ($ptr->status !== 'DRAFT') {
            abort(400, 'Only DRAFT can be submitted.');
        }

        $ptr->update(['status' => 'SUBMITTED', 'submitted_at' => now()]);
        return response()->json($ptr->fresh());
    }

    public function verify(Request $request, PtrRequest $ptr)
    {
        $user = $request->user();
        Roles::require($user, ['PROPERTY_OFFICER']);

        if ($ptr->status !== 'SUBMITTED') {
            abort(400, 'PTR must be SUBMITTED.');
        }

        Asset::findOrFail($ptr->asset_id);

        $ptr->update([
            'status' => 'VERIFIED',
            'verified_by_user_id' => $user->useid ?? $user->id,
            'verified_at' => now(),
        ]);

        return response()->json($ptr->fresh());
    }

    public function approve(Request $request, PtrRequest $ptr)
    {
        $user = $request->user();
        Roles::require($user, ['APPROVER']);

        if ($ptr->status !== 'VERIFIED') {
            abort(400, 'PTR must be VERIFIED before approval.');
        }

        return DB::transaction(function () use ($ptr, $user) {
            $asset = Asset::where('id', $ptr->asset_id)->lockForUpdate()->firstOrFail();

            $asset->custodian_user_id = $ptr->to_user_id;
            $asset->save();

            $ptr->update([
                'status' => 'COMPLETED',
                'approved_by_user_id' => $user->useid ?? $user->id,
                'approved_at' => now(),
                'completed_at' => now(),
            ]);

            return response()->json($ptr->fresh());
        });
    }

    public function reject(Request $request, PtrRequest $ptr)
    {
        $user = $request->user();
        Roles::require($user, ['APPROVER', 'PROPERTY_OFFICER']);

        if (!in_array($ptr->status, ['SUBMITTED', 'VERIFIED'], true)) {
            abort(400, 'PTR must be SUBMITTED or VERIFIED to reject.');
        }

        $ptr->update(['status' => 'REJECTED']);
        return response()->json($ptr->fresh());
    }

    public function show(PtrRequest $ptr)
    {
        return response()->json($ptr->load('asset', 'fromUser', 'toUser'));
    }
}
