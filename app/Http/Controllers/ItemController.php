<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Item::class);
        
        $query = Item::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('itemname', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('itemcode', 'asc')->paginate(15)->through(function ($item) {
            return [
                'itemcode' => $item->itemcode,
                'itemname' => $item->itemname,
                'description' => $item->description,
                'brand' => $item->brand,
                'dosage_form' => $item->dosage_form,
                'strength' => $item->strength,
                'default_uom' => $item->default_uom,
            ];
        });

        return Inertia::render('Items/Index', [
            'items' => $items,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Item::class);
        
        return Inertia::render('Items/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Item::class);
        
        $validated = $request->validate([
            'itemname' => ['required', 'string', 'max:200', 'unique:items,itemname'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:200'],
            'dosage_form' => ['nullable', 'string', 'max:100'],
            'strength' => ['nullable', 'string', 'max:100'],
            'default_uom' => ['nullable', 'string', 'max:50'],
        ]);

        Item::create($validated);

        return redirect()->route('items.index')
            ->with('message', 'Medicine item created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return Inertia::render('Items/Show', [
            'item' => $item,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        $this->authorize('update', $item);
        
        return Inertia::render('Items/Edit', [
            'item' => $item,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $this->authorize('update', $item);
        
        $validated = $request->validate([
            'itemname' => ['required', 'string', 'max:200', 'unique:items,itemname,' . $item->itemcode . ',itemcode'],
            'description' => ['nullable', 'string'],
            'brand' => ['nullable', 'string', 'max:200'],
            'dosage_form' => ['nullable', 'string', 'max:100'],
            'strength' => ['nullable', 'string', 'max:100'],
            'default_uom' => ['nullable', 'string', 'max:50'],
        ]);

        $item->update($validated);

        return redirect()->route('items.index')
            ->with('message', 'Medicine item updated successfully.');
    }

    /**
     * Remove the specified resource from storage (soft delete).
     */
    public function destroy(Item $item)
    {
        $this->authorize('delete', $item);
        
        $item->delete();

        return redirect()->route('items.index')
            ->with('message', 'Medicine item deleted successfully.');
    }
}
