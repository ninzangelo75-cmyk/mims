<?php

namespace App\Http\Controllers;

use App\Models\RequestRis;
use App\Models\RequestPtr;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $risRequests = RequestRis::whereNull('approvedat')
            ->with(['item', 'requester'])
            ->orderBy('requestedat')
            ->get()
            ->map(function ($req) {
                return [
                    'req_ris' => $req->req_ris,
                    'ris_no' => $req->ris_no,
                    'itemname' => $req->item->itemname ?? 'N/A',
                    'req_qty' => $req->req_qty,
                    'requestedat' => $req->requestedat?->format('Y-m-d H:i:s'),
                ];
            });

        $ptrRequests = RequestPtr::whereNull('approvedat')
            ->with(['item'])
            ->orderBy('requestedat')
            ->get()
            ->map(function ($req) {
                return [
                    'req_ptr' => $req->req_ptr,
                    'ptr_no' => $req->ptr_no,
                    'itemname' => $req->item->itemname ?? 'N/A',
                    'req_qty' => $req->req_qty,
                    'trans_type' => $req->trans_type,
                ];
            });

        return Inertia::render('Releasing/Index', [
            'risRequests' => $risRequests,
            'ptrRequests' => $ptrRequests,
        ]);
    }

    public function approveRis(Request $request, RequestRis $requestRis)
    {
        $requestRis->update([
            'approvedby' => $request->user()->useid,
            'approvedat' => now(),
        ]);

        return redirect()->route('approvals.index')
            ->with('message', 'RIS request approved successfully.');
    }

    public function approvePtr(Request $request, RequestPtr $requestPtr)
    {
        $requestPtr->update([
            'approvedby' => $request->user()->useid,
            'approvedat' => now(),
        ]);

        return redirect()->route('approvals.index')
            ->with('message', 'PTR request approved successfully.');
    }
}
