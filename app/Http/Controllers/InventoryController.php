<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Receiving;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display inventory listing
     */
    public function index(Request $request)
    {
        $cacheKey = 'inventory_list_' . ($request->user()->department ?? 'all');
        
        $inventory = Cache::remember($cacheKey, 300, function () use ($request) {
            $query = Item::withInventory();

            // Filter by department for USER role
            if ($request->user()->role === 'USER' && $request->user()->department) {
                $query->forDepartment($request->user()->department);
            }

            return $query->get();
        });

        $inventoryData = $inventory->map(function ($item) {
            $remaining = (float) ($item->remaining ?? 0);
            $reorderLevel = 50; // fallback reorder threshold
            $status = $remaining <= 0
                ? 'Critical'
                : ($remaining <= $reorderLevel ? 'Low Stock' : 'In Stock');

            return [
                'itemcode' => $item->itemcode,
                'itemname' => $item->itemname,
                'stockin' => (float) ($item->stockin ?? 0),
                'stockout' => (float) ($item->stockout ?? 0),
                'remaining' => $remaining,
                'reorder_level' => $reorderLevel,
                'status' => $status,
            ];
        });

        $summary = [
            'totalItems' => $inventoryData->count(),
            'lowStockItems' => $inventoryData->whereIn('status', ['Low Stock', 'Critical'])->count(),
            // Approximated total value based on recorded receiving amounts
            'totalValue' => Receiving::sum('totalamount'),
        ];

        return Inertia::render('Inventory/Index', [
            'inventory' => $inventoryData,
            'summary' => $summary,
        ]);
    }
}
