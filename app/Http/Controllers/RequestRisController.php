<?php

namespace App\Http\Controllers;

use App\Models\RequestRis;
use App\Models\Item;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RequestRisController extends Controller
{
    protected $availabilityService;

    public function __construct(AvailabilityService $availabilityService)
    {
        $this->middleware('auth');
        $this->availabilityService = $availabilityService;
    }

    public function index(Request $request)
    {
        $query = RequestRis::with(['item', 'requester'])->orderBy('requestedat', 'desc');

        if ($request->user()->role === 'USER') {
            $query->where('requestedby', $request->user()->useid);
        }

        $requests = $query->paginate(15)->through(function ($request) {
            return [
                'req_ris' => $request->req_ris,
                'ris_no' => $request->ris_no,
                'itemname' => $request->item->itemname ?? 'N/A',
                'req_qty' => $request->req_qty,
                'isavailable' => $request->isavailable,
                'requested_by' => $request->requester?->fullname ?? 'N/A',
                'requestedat' => $request->requestedat?->format('Y-m-d H:i:s'),
            ];
        });

        $pendingCount = (clone $query)->where('isavailable', false)->count();
        $approvedToday = (clone $query)->where('isavailable', true)
            ->whereDate('requestedat', now()->toDateString())
            ->count();
        $totalThisMonth = (clone $query)
            ->whereYear('requestedat', now()->year)
            ->whereMonth('requestedat', now()->month)
            ->count();

        return Inertia::render('Requests/Ris/Index', [
            'requests' => $requests,
            'summary' => [
                'pending' => $pendingCount,
                'approvedToday' => $approvedToday,
                'totalThisMonth' => $totalThisMonth,
            ],
        ]);
    }

    public function create()
    {
        $items = Item::orderBy('itemname')->get(['itemcode', 'itemname']);

        return Inertia::render('Requests/Ris/Create', [
            'items' => $items,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'itemcode' => ['required', 'exists:items,itemcode'],
            'req_qty' => ['required', 'numeric', 'min:0.01'],
            'division' => ['nullable', 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'remarks' => ['nullable', 'string'],
        ]);

        // Check availability
        $availability = $this->availabilityService->checkAvailability(
            $validated['itemcode'],
            $validated['req_qty']
        );

        // Generate RIS number
        $risNo = 'RIS-' . date('Y-m') . '-' . str_pad(
            RequestRis::whereYear('created_at', date('Y'))
                ->whereMonth('created_at', date('m'))
                ->count() + 1,
            4,
            '0',
            STR_PAD_LEFT
        );

        RequestRis::create([
            'ris_no' => $risNo,
            'itemcode' => $validated['itemcode'],
            'req_qty' => $validated['req_qty'],
            'division' => $validated['division'] ?? null,
            'department' => $validated['department'] ?? $request->user()->department,
            'remarks' => $validated['remarks'] ?? null,
            'isavailable' => $availability['available'],
            'requestedby' => $request->user()->useid,
            'requestedat' => now(),
        ]);

        return redirect()->route('requests.ris.index')
            ->with('message', 'RIS request created successfully.');
    }
}
