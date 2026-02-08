<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\RequestRis;
use App\Models\RequestPtr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $reorderLevel = 50;
        $today = Carbon::today();
        $expiringCutoff = $today->copy()->addDays(14);

        $expirySubquery = DB::table('receiving')
            ->selectRaw('itemcode, MIN(expirydate) as next_expiry')
            ->groupBy('itemcode');

        $inventoryQuery = Item::withInventory()
            ->leftJoinSub($expirySubquery, 'expiry', function ($join) {
                $join->on('expiry.itemcode', '=', 'items.itemcode');
            })
            ->addSelect(DB::raw('expiry.next_expiry as next_expiry'));

        if ($request->user()->role === 'USER' && $request->user()->department) {
            $inventoryQuery->forDepartment($request->user()->department);
        }

        $inventory = $inventoryQuery->get();

        $inventoryData = $inventory->map(function ($item) use ($reorderLevel, $today, $expiringCutoff) {
            $remaining = (float) ($item->remaining ?? 0);
            $expiryDate = $item->next_expiry ? Carbon::parse($item->next_expiry) : null;
            $isExpiring = $expiryDate
                ? $expiryDate->betweenIncluded($today, $expiringCutoff)
                : false;

            $status = $isExpiring
                ? 'Expiring'
                : ($remaining <= $reorderLevel ? 'Low Stock' : 'In Stock');

            return [
                'itemcode' => $item->itemcode,
                'itemname' => $item->itemname,
                'remaining' => $remaining,
                'reorder_level' => $reorderLevel,
                'expiry_date' => $expiryDate?->toDateString(),
                'status' => $status,
            ];
        });

        $totalItems = $inventoryData->count();
        $itemsInStock = $inventoryData->where('remaining', '>', 0)->count();
        $lowStockCount = $inventoryData->where('remaining', '<=', $reorderLevel)->count();
        $expiringSoonCount = $inventoryData->where('status', 'Expiring')->count();

        $pendingRisCount = RequestRis::whereNull('approvedat')->count();
        $pendingPtrCount = RequestPtr::whereNull('approvedat')->count();

        $inventoryStatus = $inventoryData
            ->sortBy(function ($item) {
                return $item['expiry_date'] ?? '9999-12-31';
            })
            ->take(5)
            ->values();

        $recentRis = RequestRis::with('requester')
            ->orderByDesc('requestedat')
            ->limit(5)
            ->get()
            ->map(function ($request) {
                return [
                    'type' => 'RIS',
                    'request_no' => $request->ris_no,
                    'department' => $request->department,
                    'requested_by' => $request->requester?->fullname ?? 'N/A',
                    'status' => $request->approvedat ? 'Approved' : 'Pending',
                    'requested_at' => $request->requestedat ?? $request->created_at,
                ];
            });

        $recentPtr = RequestPtr::orderByDesc('requestedat')
            ->limit(5)
            ->get()
            ->map(function ($request) {
                return [
                    'type' => 'PTR',
                    'request_no' => $request->ptr_no,
                    'department' => $request->target ?? $request->division,
                    'requested_by' => 'N/A',
                    'status' => $request->approvedat ? 'Approved' : 'Pending',
                    'requested_at' => $request->requestedat ?? $request->created_at,
                ];
            });

        $recentRequests = $recentRis
            ->merge($recentPtr)
            ->sortByDesc('requested_at')
            ->take(5)
            ->values()
            ->map(function ($item) {
                return [
                    'type' => $item['type'],
                    'request_no' => $item['request_no'],
                    'department' => $item['department'],
                    'requested_by' => $item['requested_by'],
                    'status' => $item['status'],
                ];
            });

        $notifications = collect();

        $lowStockItems = $inventoryData->where('status', 'Low Stock')->take(2);
        foreach ($lowStockItems as $item) {
            $notifications->push([
                'type' => 'low_stock',
                'message' => 'Low stock: ' . $item['itemname'],
            ]);
        }

        $expiringItems = $inventoryData->where('status', 'Expiring')->take(2);
        foreach ($expiringItems as $item) {
            $notifications->push([
                'type' => 'expiring',
                'message' => 'Medicine expiring: ' . $item['itemname'],
            ]);
        }

        $latestRis = RequestRis::orderByDesc('requestedat')->first();
        if ($latestRis) {
            $notifications->push([
                'type' => 'new_ris',
                'message' => 'New RIS request submitted: ' . $latestRis->ris_no,
            ]);
        }

        $latestPtr = RequestPtr::orderByDesc('requestedat')->first();
        if ($latestPtr) {
            $notifications->push([
                'type' => 'new_ptr',
                'message' => 'New PTR request submitted: ' . $latestPtr->ptr_no,
            ]);
        }

        if ($pendingRisCount > 0) {
            $notifications->push([
                'type' => 'pending_ris',
                'message' => 'Pending RIS approvals: ' . $pendingRisCount,
            ]);
        }

        if ($pendingPtrCount > 0) {
            $notifications->push([
                'type' => 'pending_ptr',
                'message' => 'Pending PTR approvals: ' . $pendingPtrCount,
            ]);
        }

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalItems' => $totalItems,
                'itemsInStock' => $itemsInStock,
                'lowStock' => $lowStockCount,
                'pendingRis' => $pendingRisCount,
                'pendingPtr' => $pendingPtrCount,
                'expiringSoon' => $expiringSoonCount,
            ],
            'inventoryStatus' => $inventoryStatus,
            'recentRequests' => $recentRequests,
            'notifications' => $notifications->take(6)->values(),
        ]);
    }
}
