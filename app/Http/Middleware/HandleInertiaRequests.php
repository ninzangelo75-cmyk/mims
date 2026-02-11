<?php

namespace App\Http\Middleware;

use App\Models\Item;
use App\Models\RequestRis;
use App\Models\RequestPtr;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        if (!$user) {
            $notifications = [];
        } else {
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

                if ($user->role === 'USER' && $user->department) {
                    $inventoryQuery->forDepartment($user->department);
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
                        'itemname' => $item->itemname,
                        'status' => $status,
                    ];
                });

                $notifications = collect();

                $lowStockItems = $inventoryData->where('status', 'Low Stock')->take(3);
                foreach ($lowStockItems as $item) {
                    $notifications->push([
                        'type' => 'low_stock',
                        'message' => 'Low stock: ' . strtoupper($item['itemname']),
                        'href' => '/inventory',
                        'time' => null,
                    ]);
                }

                $expiringItems = $inventoryData->where('status', 'Expiring')->take(3);
                foreach ($expiringItems as $item) {
                    $notifications->push([
                        'type' => 'expiring',
                        'message' => 'Medicine expiring: ' . strtoupper($item['itemname']),
                        'href' => '/inventory',
                        'time' => null,
                    ]);
                }

                $latestRis = RequestRis::orderByDesc('requestedat')->first();
                if ($latestRis) {
                    $notifications->push([
                        'type' => 'new_ris',
                        'message' => 'New RIS request submitted: ' . $latestRis->ris_no,
                        'href' => '/requests/ris',
                        'time' => $latestRis->requestedat,
                    ]);
                }

                $latestPtr = RequestPtr::orderByDesc('requestedat')->first();
                if ($latestPtr) {
                    $notifications->push([
                        'type' => 'new_ptr',
                        'message' => 'New PTR request submitted: ' . $latestPtr->ptr_no,
                        'href' => '/requests/ptr',
                        'time' => $latestPtr->requestedat,
                    ]);
                }

                $pendingRisCount = RequestRis::whereNull('approvedat')->count();
                if ($pendingRisCount > 0) {
                    $notifications->push([
                        'type' => 'pending_ris',
                        'message' => 'Pending RIS approvals: ' . $pendingRisCount,
                        'href' => '/approvals',
                        'time' => null,
                    ]);
                }

                $pendingPtrCount = RequestPtr::whereNull('approvedat')->count();
                if ($pendingPtrCount > 0) {
                    $notifications->push([
                        'type' => 'pending_ptr',
                        'message' => 'Pending PTR approvals: ' . $pendingPtrCount,
                        'href' => '/approvals',
                        'time' => null,
                    ]);
                }

            $notifications = $notifications->take(10)->values()->all();
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user() ? [
                    'useid' => $request->user()->useid,
                    'username' => $request->user()->username,
                    'fullname' => $request->user()->fullname,
                    'role' => $request->user()->role,
                    'department' => $request->user()->department,
                ] : null,
            ],
            'notifications' => $notifications,
            'flash' => [
                'message' => fn () => $request->session()->get('message'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
