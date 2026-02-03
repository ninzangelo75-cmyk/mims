<?php

namespace App\Http\Controllers;

use App\Models\RequestPtr;
use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RequestPtrController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = RequestPtr::with(['item'])->orderBy('requestedat', 'desc');

        $requests = $query->paginate(15)->through(function ($request) {
            return [
                'req_ptr' => $request->req_ptr,
                'ptr_no' => $request->ptr_no,
                'itemname' => $request->item->itemname ?? 'N/A',
                'req_qty' => $request->req_qty,
                'trans_type' => $request->trans_type,
                'requestedat' => $request->requestedat?->format('Y-m-d H:i:s'),
            ];
        });

        return Inertia::render('Requests/Ptr/Index', [
            'requests' => $requests,
        ]);
    }

    public function create()
    {
        $items = Item::orderBy('itemname')->get(['itemcode', 'itemname']);

        return Inertia::render('Requests/Ptr/Create', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'itemcode' => ['required', 'exists:items,itemcode'],
            'req_qty' => ['required', 'numeric', 'min:0.01'],
            'division' => ['nullable', 'string', 'max:150'],
            'target' => ['nullable', 'string', 'max:150'],
            'trans_type' => ['required', 'in:Donation,Reassignment,Relocate,Others'],
            'trans_type_other' => ['nullable', 'required_if:trans_type,Others', 'string', 'max:150'],
            'remarks' => ['nullable', 'string'],
            'purpose' => ['nullable', 'string'],
        ]);

        // Generate PTR number
        $ptrNo = 'PTR-' . date('Y-m') . '-' . str_pad(
            RequestPtr::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        RequestPtr::create([
            'ptr_no' => $ptrNo,
            'itemcode' => $validated['itemcode'],
            'req_qty' => $validated['req_qty'],
            'division' => $validated['division'] ?? null,
            'target' => $validated['target'] ?? null,
            'trans_type' => $validated['trans_type'],
            'trans_type_other' => $validated['trans_type_other'] ?? null,
            'remarks' => $validated['remarks'] ?? null,
            'purpose' => $validated['purpose'] ?? null,
            'requestedat' => now(),
        ]);

        return redirect()->route('requests.ptr.index')
            ->with('message', 'PTR request created successfully.');
    }
}
