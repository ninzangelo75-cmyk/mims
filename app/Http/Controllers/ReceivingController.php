<?php

namespace App\Http\Controllers;

use App\Models\Receiving;
use App\Models\Item;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $query = DB::table('receiving as r')
            ->leftJoin('items as i', 'r.itemcode', '=', 'i.itemcode')
            ->select(
                DB::raw('MIN(r.recid) as recid'),
                'r.batchno',
                'r.supplier',
                'r.referenceno',
                DB::raw('COUNT(*) as total_items'),
                DB::raw('MAX(r.datereceived) as datereceived'),
                DB::raw('MIN(r.itemcode) as itemcode'),
                DB::raw('MIN(i.itemname) as itemname'),
                DB::raw('SUM(r.qty) as qty'),
                DB::raw('MIN(r.uom) as uom'),
                DB::raw('AVG(r.unitprice) as unitprice'),
                DB::raw('SUM(r.totalamount) as totalamount'),
                DB::raw('MIN(r.expirydate) as expirydate'),
                DB::raw('MIN(r.department) as department')
            )
            ->groupBy('r.batchno', 'r.supplier', 'r.referenceno')
            ->orderByDesc('datereceived');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('i.itemname', 'like', "%{$search}%")
                    ->orWhere('r.batchno', 'like', "%{$search}%")
                    ->orWhere('r.supplier', 'like', "%{$search}%")
                    ->orWhere('r.referenceno', 'like', "%{$search}%");
            });
        }

        $receivings = $query->paginate(15);

        $batchNumbers = $receivings->pluck('batchno')->filter()->unique()->values();
        $batchItems = $batchNumbers->isEmpty()
            ? collect()
            : Receiving::with('item')
                ->whereIn('batchno', $batchNumbers)
                ->orderBy('batchno')
                ->orderBy('datereceived')
                ->get()
                ->groupBy('batchno');

        $receivings = $receivings->through(function ($receiving) use ($batchItems) {
            $items = $batchItems->get($receiving->batchno, collect())
                ->map(function ($item) {
                    return [
                        'recid' => $item->recid,
                        'itemcode' => $item->itemcode,
                        'itemname' => $item->item->itemname ?? 'N/A',
                        'qty' => $item->qty,
                        'uom' => $item->uom,
                        'unitprice' => $item->unitprice,
                        'totalamount' => $item->totalamount,
                        'expirydate' => $item->expirydate?->format('Y-m-d'),
                    ];
                })
                ->values()
                ->all();

            return [
                'recid' => $receiving->recid,
                'datereceived' => $receiving->datereceived ? date('Y-m-d H:i:s', strtotime($receiving->datereceived)) : null,
                'itemcode' => $receiving->itemcode,
                'itemname' => $receiving->itemname ?? 'N/A',
                'batchno' => $receiving->batchno,
                'qty' => $receiving->qty,
                'uom' => $receiving->uom,
                'unitprice' => $receiving->unitprice,
                'totalamount' => $receiving->totalamount,
                'expirydate' => $receiving->expirydate ? date('Y-m-d', strtotime($receiving->expirydate)) : null,
                'supplier' => $receiving->supplier,
                'referenceno' => $receiving->referenceno,
                'department' => $receiving->department,
                'total_items' => (int) ($receiving->total_items ?? 0),
                'items' => $items,
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
            'items' => Item::orderBy('itemname')->get(['itemcode', 'itemname', 'default_uom', 'description', 'brand', 'dosage_form', 'strength']),
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

            Cache::flush();

            return redirect()->route('receiving.index')
                ->with('message', 'Stock received successfully.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to record stock-in: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Update an existing receiving record
     */
    public function update(Request $request, Receiving $receiving)
    {
        $validated = $request->validate([
            'itemcode' => ['required', 'exists:items,itemcode'],
            'supplier' => ['nullable', 'string', 'max:200'],
            'referenceno' => ['nullable', 'string', 'max:150'],
            'qty' => ['required', 'numeric', 'min:0.01'],
            'uom' => ['required', 'string', 'max:50'],
            'unitprice' => ['required', 'numeric', 'min:0'],
            'batchno' => ['required', 'string', 'max:100'],
            'expirydate' => ['required', 'date', 'after:today'],
            'department' => ['nullable', 'string', 'max:150'],
        ]);

        $previousItemcode = $receiving->itemcode;
        $validated['totalamount'] = $validated['qty'] * $validated['unitprice'];

        $receiving->update($validated);

        cache()->forget("inventory_item_{$previousItemcode}");
        cache()->forget("inventory_item_{$receiving->itemcode}");
        Cache::flush();

        return redirect()->route('receiving.index')
            ->with('message', 'Receiving record updated successfully.');
    }

    /**
     * Delete a receiving record
     */
    public function destroy(Receiving $receiving)
    {
        $itemcode = $receiving->itemcode;
        $receiving->delete();

        cache()->forget("inventory_item_{$itemcode}");
        Cache::flush();

        return redirect()->route('receiving.index')
            ->with('message', 'Receiving record deleted successfully.');
    }
}
