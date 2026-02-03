<?php

namespace App\Http\Controllers;

use App\Models\Receiving;
use App\Models\Item;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ReceivingController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->middleware('auth');
        $this->inventoryService = $inventoryService;
    }

    /**
     * Display a listing of receiving records
     */
    public function index(Request $request)
    {
        $query = Receiving::with('item')->orderBy('datereceived', 'desc');

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('item', function ($q) use ($search) {
                $q->where('itemname', 'like', "%{$search}%");
            })->orWhere('batchno', 'like', "%{$search}%")
              ->orWhere('supplier', 'like', "%{$search}%");
        }

        $receivings = $query->paginate(15)->through(function ($receiving) {
            return [
                'recid' => $receiving->recid,
                'datereceived' => $receiving->datereceived?->format('Y-m-d H:i:s'),
                'itemname' => $receiving->item->itemname ?? 'N/A',
                'batchno' => $receiving->batchno,
                'qty' => $receiving->qty,
                'uom' => $receiving->uom,
                'unitprice' => $receiving->unitprice,
                'totalamount' => $receiving->totalamount,
                'expirydate' => $receiving->expirydate?->format('Y-m-d'),
                'supplier' => $receiving->supplier,
            ];
        });

        $summary = [
            'pendingReceipts' => Receiving::count(),
            'completedToday' => Receiving::whereDate('datereceived', now()->toDateString())->count(),
            'totalThisMonth' => Receiving::whereYear('datereceived', now()->year)
                ->whereMonth('datereceived', now()->month)
                ->count(),
        ];

        return Inertia::render('Receiving/Index', [
            'receivings' => $receivings,
            'summary' => $summary,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new receiving record
     */
    public function create()
    {
        $items = Item::orderBy('itemname')->get(['itemcode', 'itemname', 'default_uom']);

        return Inertia::render('Receiving/Create', [
            'items' => $items,
        ]);
    }

    /**
     * Store a newly created receiving record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier' => ['nullable', 'string', 'max:200'],
            'referenceno' => ['nullable', 'string', 'max:150'],
            'department' => ['nullable', 'string', 'max:150'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.itemcode' => ['required', 'exists:items,itemcode'],
            'items.*.qty' => ['required', 'numeric', 'min:0.01'],
            'items.*.uom' => ['required', 'string', 'max:50'],
            'items.*.unitprice' => ['required', 'numeric', 'min:0'],
            'items.*.batchno' => ['required', 'string', 'max:100'],
            'items.*.expirydate' => ['required', 'date', 'after:today'],
        ]);

        try {
            $this->inventoryService->recordStockInMany(
                $validated['items'],
                $validated['supplier'] ?? null,
                $validated['referenceno'] ?? null,
                $validated['department'] ?? null
            );

            return redirect()->route('receiving.index')
                ->with('message', 'Stock received successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to record stock-in: ' . $e->getMessage()])
                ->withInput();
        }
    }
}
