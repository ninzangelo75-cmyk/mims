<template>
    <div class="rounded-2xl border border-[#cfe8d1] bg-[#f6fbf6] p-5 shadow-sm">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Filters</h3>
                <p class="text-xs text-gray-600">Apply consistent filters across reports.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" class="btn-secondary">Reset</button>
                <button type="button" class="btn-primary">Apply</button>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <label class="text-xs font-semibold text-gray-600">Date Range</label>
                <input type="text" class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Select range" />
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Warehouse / Location</label>
                <select class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option>All</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Category</label>
                <select class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option>All</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Status</label>
                <select class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option>All</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Requesting Department</label>
                <select class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option>All</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Requested By / Approved By</label>
                <select class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option>All</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Item</label>
                <select class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option>All</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold text-gray-600">Supplier</label>
                <select class="mt-1 w-full rounded-lg border border-gray-300 px-3 py-2 text-sm">
                    <option>All</option>
                </select>
            </div>
        </div>
    </div>
</template>
