<?php

namespace App\Http\Controllers;

use App\Models\RequestRis;
use App\Models\RequestPtr;
use App\Models\Receiving;
use App\Models\ReleasingRis;
use App\Models\ReleasingPtr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReleasingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showRis(RequestRis $requestRis)
    {
        // Get all batches for this item
        $allBatches = Receiving::where('itemcode', $requestRis->itemcode)
            ->where('expirydate', '>', now()) // Exclude expired
            ->fefo()
            ->get();

        // Calculate available quantity for each batch
        $batches = $allBatches->map(function ($batch) use ($requestRis) {
            // Get total released from this specific batch
            $released = DB::table('releasing_ris')
                ->join('request_ris', 'releasing_ris.req_ris', '=', 'request_ris.req_ris')
                ->join('receiving', function($join) use ($batch) {
                    $join->on('receiving.itemcode', '=', 'request_ris.itemcode')
                         ->where('receiving.batchno', '=', $batch->batchno);
                })
                ->where('request_ris.itemcode', $requestRis->itemcode)
                ->where('receiving.batchno', $batch->batchno)
                ->sum('releasing_ris.release_qty');

            $availableQty = max(0, $batch->qty - ($released ?? 0));
            
            return [
                'recid' => $batch->recid,
                'batchno' => $batch->batchno,
                'expirydate' => $batch->expirydate->format('Y-m-d'),
                'qty' => $batch->qty,
                'available_qty' => $availableQty,
            ];
        })
        ->filter(fn($batch) => $batch['available_qty'] > 0)
        ->values();

        $requestRis->load(['item', 'requester']);

        return Inertia::render('Releasing/Ris/Show', [
            'request' => [
                'req_ris' => $requestRis->req_ris,
                'ris_no' => $requestRis->ris_no,
                'req_qty' => (float) $requestRis->req_qty,
                'item' => [
                    'itemcode' => $requestRis->item->itemcode ?? null,
                    'itemname' => $requestRis->item->itemname ?? 'N/A',
                ],
            ],
            'batches' => $batches->toArray(),
        ]);
    }

    public function releaseRis(Request $request, RequestRis $requestRis)
    {
        $validated = $request->validate([
            'batches' => ['required', 'array'],
            'batches.*.recid' => ['required', 'exists:receiving,recid'],
            'batches.*.qty' => ['required', 'numeric', 'min:0.01'],
        ], [
            'batches.required' => 'Please select at least one batch to release.',
            'batches.*.recid.required' => 'Batch ID is required.',
            'batches.*.qty.required' => 'Quantity is required.',
            'batches.*.qty.min' => 'Quantity must be greater than 0.',
        ]);

        DB::transaction(function () use ($requestRis, $validated, $request) {
            foreach ($validated['batches'] as $batchData) {
                if (isset($batchData['recid']) && $batchData['qty'] > 0) {
                    ReleasingRis::create([
                        'req_ris' => $requestRis->req_ris,
                        'release_qty' => $batchData['qty'],
                        'releaseby' => $request->user()->useid,
                        'releaseat' => now(),
                    ]);
                }
            }

            // Refresh the request to check if fully released
            $requestRis->refresh();
            if ($requestRis->isFullyReleased()) {
                $requestRis->update(['receivedat' => now()]);
            }
        });

        return redirect()->route('approvals.index')
            ->with('message', 'Stock released successfully.');
    }
}
