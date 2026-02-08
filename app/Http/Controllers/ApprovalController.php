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
        $risQuery = RequestRis::whereNull('approvedat')
            ->with(['item', 'requester'])
            ->orderBy('requestedat');

        if ($request->filled('ris_search')) {
            $search = $request->ris_search;
            $risQuery->where(function ($q) use ($search) {
                $q->where('ris_no', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhereHas('item', function ($item) use ($search) {
                        $item->where('itemname', 'like', "%{$search}%");
                    })
                    ->orWhereHas('requester', function ($user) use ($search) {
                        $user->where('fullname', 'like', "%{$search}%")
                            ->orWhere('username', 'like', "%{$search}%");
                    });
            });
        }

        $risRequests = $risQuery->get()
            ->map(function ($req) {
                return [
                    'req_ris' => $req->req_ris,
                    'request_no' => $req->ris_no,
                    'itemname' => $req->item->itemname ?? 'N/A',
                    'req_qty' => $req->req_qty,
                    'requested_by' => $req->requester?->fullname ?? 'N/A',
                    'department' => $req->department ?? 'N/A',
                    'requestedat' => $req->requestedat?->format('Y-m-d H:i:s'),
                    'status' => $req->approvedat ? 'Approved' : 'Pending',
                    'division' => $req->division ?? 'N/A',
                    'remarks' => $req->remarks ?? 'N/A',
                ];
            });

        $ptrQuery = RequestPtr::whereNull('approvedat')
            ->with(['item'])
            ->orderBy('requestedat');

        if ($request->filled('ptr_search')) {
            $search = $request->ptr_search;
            $ptrQuery->where(function ($q) use ($search) {
                $q->where('ptr_no', 'like', "%{$search}%")
                    ->orWhere('division', 'like', "%{$search}%")
                    ->orWhere('target', 'like', "%{$search}%")
                    ->orWhereHas('item', function ($item) use ($search) {
                        $item->where('itemname', 'like', "%{$search}%");
                    });
            });
        }

        $ptrRequests = $ptrQuery->get()
            ->map(function ($req) {
                return [
                    'req_ptr' => $req->req_ptr,
                    'request_no' => $req->ptr_no,
                    'itemname' => $req->item->itemname ?? 'N/A',
                    'req_qty' => $req->req_qty,
                    'requested_by' => 'N/A',
                    'department' => $req->target ?? $req->division ?? 'N/A',
                    'requestedat' => $req->requestedat?->format('Y-m-d H:i:s'),
                    'status' => $req->approvedat ? 'Approved' : 'Pending',
                    'division' => $req->division ?? 'N/A',
                    'target' => $req->target ?? 'N/A',
                    'trans_type' => $req->trans_type ?? 'N/A',
                    'trans_type_other' => $req->trans_type_other ?? 'N/A',
                    'purpose' => $req->purpose ?? 'N/A',
                    'remarks' => $req->remarks ?? 'N/A',
                ];
            });

        return Inertia::render('Releasing/Index', [
            'risRequests' => $risRequests,
            'ptrRequests' => $ptrRequests,
            'filters' => $request->only(['ris_search', 'ptr_search']),
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
