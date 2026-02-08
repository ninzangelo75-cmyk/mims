<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\RisRequest;
use App\Services\DocNumberService;
use App\Support\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RisController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();
        Roles::require($user, ['REQUESTOR']);

        $data = $request->validate([
            'purpose' => 'required|string',
            'lines' => 'required|array|min:1',
            'lines.*.consumable_item_id' => 'required|integer|exists:consumable_items,id',
            'lines.*.qty_requested' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($user, $data) {
            $ris = RisRequest::create([
                'ris_no' => DocNumberService::next('RIS', 'ris_requests', 'ris_no'),
                'purpose' => $data['purpose'],
                'status' => 'DRAFT',
                'created_by_user_id' => $user->useid ?? $user->id,
            ]);

            foreach ($data['lines'] as $ln) {
                $ris->lines()->create([
                    'consumable_item_id' => $ln['consumable_item_id'],
                    'qty_requested' => $ln['qty_requested'],
                ]);
            }

            return response()->json($ris->load('lines'), 201);
        });
    }

    public function submit(Request $request, RisRequest $ris)
    {
        $user = $request->user();
        if ($ris->created_by_user_id !== ($user->useid ?? $user->id)) {
            abort(403, 'Only creator can submit.');
        }
        if ($ris->status !== 'DRAFT') {
            abort(400, 'Only DRAFT can be submitted.');
        }

        $ris->update(['status' => 'SUBMITTED', 'submitted_at' => now()]);
        return response()->json($ris->fresh()->load('lines'));
    }

    public function approve(Request $request, RisRequest $ris)
    {
        $user = $request->user();
        Roles::require($user, ['APPROVER']);

        if ($ris->status !== 'SUBMITTED') {
            abort(400, 'RIS must be SUBMITTED.');
        }

        $ris->update([
            'status' => 'APPROVED',
            'approved_by_user_id' => $user->useid ?? $user->id,
            'approved_at' => now(),
        ]);

        return response()->json($ris->fresh()->load('lines'));
    }

    public function reject(Request $request, RisRequest $ris)
    {
        $user = $request->user();
        Roles::require($user, ['APPROVER']);

        if ($ris->status !== 'SUBMITTED') {
            abort(400, 'RIS must be SUBMITTED.');
        }

        $ris->update(['status' => 'REJECTED']);
        return response()->json($ris->fresh()->load('lines'));
    }

    public function issue(Request $request, RisRequest $ris)
    {
        $user = $request->user();
        Roles::require($user, ['SUPPLY_OFFICER']);

        if ($ris->status !== 'APPROVED') {
            abort(400, 'RIS must be APPROVED.');
        }

        return DB::transaction(function () use ($ris) {
            $ris->load('lines');

            foreach ($ris->lines as $ln) {
                $inv = Inventory::where('consumable_item_id', $ln->consumable_item_id)
                    ->lockForUpdate()
                    ->first();
                if (!$inv) {
                    abort(404, "Inventory record not found for item {$ln->consumable_item_id}");
                }
                if ($inv->qty_on_hand < $ln->qty_requested) {
                    abort(400, "Insufficient stock for item {$ln->consumable_item_id}. On-hand={$inv->qty_on_hand}, Requested={$ln->qty_requested}");
                }
            }

            foreach ($ris->lines as $ln) {
                $inv = Inventory::where('consumable_item_id', $ln->consumable_item_id)
                    ->lockForUpdate()
                    ->first();
                $inv->qty_on_hand -= $ln->qty_requested;
                $inv->save();

                $ln->qty_issued = $ln->qty_requested;
                $ln->save();
            }

            $ris->status = 'ISSUED';
            $ris->issued_at = now();
            $ris->save();

            return response()->json($ris->fresh()->load('lines'));
        });
    }

    public function show(RisRequest $ris)
    {
        return response()->json($ris->load('lines.item', 'createdBy', 'approvedBy'));
    }
}
