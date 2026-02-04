<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">Receiving</h2>
                        <p class="page-subtitle">Track incoming medicine shipments</p>
                    </div>
                    <button type="button" class="btn-primary" @click="openReceiveModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>Receive Stock</span>
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <SummaryCard title="Pending Receipts" :value="summary.pendingReceipts" icon-color="text-orange-500" icon-bg="bg-orange-50" />
                    <SummaryCard title="Completed Today" :value="summary.completedToday" icon-color="text-green-500" icon-bg="bg-green-50" />
                    <SummaryCard title="Total This Month" :value="summary.totalThisMonth" icon-color="text-indigo-500" icon-bg="bg-indigo-50" />
                </div>

                <div class="overflow-hidden rounded-2xl bg-[#f6fbf6] shadow ring-1 ring-[#cfe8d1]">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Receiving Records</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#e8f5e9]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Batch Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Supplier Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Total Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Date Received</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                                <tr v-if="!receivings.data.length">
                                    <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No receiving records found.
                                    </td>
                                </tr>
                                <tr v-for="record in receivings.data" :key="record.recid" class="hover:bg-[#e8f5e9]">
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">{{ record.batchno || '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">{{ record.supplier || '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">{{ record.total_items ?? 0 }}</td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">{{ formatDate(record.datereceived) }}</td>
                                    <td class="whitespace-nowrap px-6 py-2">
                                        <span :class="statusClass(record)" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold">
                                            {{ statusLabel(record) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-2 text-sm font-medium">
                                        <button type="button" class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-500 text-white shadow-sm transition hover:bg-gray-600" @click="openViewModal(record)">
                                            <span class="sr-only">View</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700" @click="openEditModal(record)">
                                            <span class="sr-only">Edit</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a1.875 1.875 0 012.651 2.651L7.5 18.15 3 19.5l1.35-4.5L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <button type="button" class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-600 text-white shadow-sm transition hover:bg-red-700" @click="openDeleteModal(record)">
                                            <span class="sr-only">Delete</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-8 0l.8 12.1A2 2 0 008.8 21h6.4a2 2 0 001.99-1.9L18 7" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="receivings.links" class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ receivings.from }} to {{ receivings.to }} of {{ receivings.total }} results
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                v-for="link in receivings.links"
                                :key="link.label"
                                :href="link.url || '#'"
                                :class="[
                                    'px-3 py-2 text-sm border rounded transition',
                                    link.active
                                        ? 'bg-[#2e7d32] text-white border-[#2e7d32]'
                                        : 'bg-[#e8f5e9] text-[#1b5e20] border-[#cfe8d1] hover:bg-[#dff1e1]',
                                    !link.url ? 'opacity-50 cursor-not-allowed' : ''
                                ]"
                                v-html="link.label"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="showReceiveModal" :showFooter="false" maxWidth="sm:max-w-3xl" @close="closeReceiveModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Receive Stock</h3>
                <p class="text-sm text-white/80">Record incoming medicine shipments.</p>
            </div>
            <form @submit.prevent="submitReceive">
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="supplier"
                            v-model="receiveForm.supplier"
                            label="Supplier"
                            :disabled="receiveForm.items.length > 0"
                            :error="receiveForm.errors.supplier"
                        />
                        <Input id="referenceno" v-model="receiveForm.referenceno" label="Reference Number" disabled :error="receiveForm.errors.referenceno" />
                    </div>

                    <div class="rounded-xl border border-[#cfe8d1] bg-[#FBFCFA] p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-700">Add Item</p>
                        </div>

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-3">
                                <Input id="batchno" v-model="currentItem.batchno" label="Batch Number" disabled :error="itemErrors.batchno" />
                            </div>
                            <div class="col-span-6">
                                <Select
                                    id="itemcode"
                                    v-model="currentItem.itemcode"
                                    label="Medicine"
                                    :options="itemOptions"
                                    placeholder="Select medicine"
                                    :error="itemErrors.itemcode"
                                />
                            </div>
                            <div class="col-span-3">
                                <Input id="expirydate" v-model="currentItem.expirydate" label="Expiry Date" type="date" :error="itemErrors.expirydate" />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-4">
                                <Input id="qty" v-model="currentItem.qty" label="Quantity" type="number" step="0.01" :error="itemErrors.qty" />
                            </div>
                            <div class="col-span-4">
                                <Input id="uom" v-model="currentItem.uom" label="Unit of Measure" :error="itemErrors.uom" />
                            </div>
                            <div class="col-span-4">
                                <Input id="unitprice" v-model="currentItem.unitprice" label="Unit Price" type="number" step="0.01" :error="itemErrors.unitprice" />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 items-center gap-4">
                            <div class="col-span-3">
                                <p class="text-xs text-gray-500">
                                    Line Total:
                                    <span class="font-semibold text-[#2e7d32]">{{ formatMoney(currentTotal) }}</span>
                                </p>
                            </div>
                            <div class="col-span-9 flex justify-end">
                                <Button type="button" class="btn-primary" @click="addItem">Add Item</Button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <p class="text-sm font-semibold text-gray-700">Items to Receive</p>
                        <p v-if="receiveForm.errors.items" class="text-sm text-red-600">{{ receiveForm.errors.items }}</p>

                        <div v-if="receiveForm.items.length === 0" class="text-sm text-gray-500">No items added yet.</div>

                        <div v-else class="overflow-x-auto rounded-lg border border-[#cfe8d1]">
                            <table class="min-w-full text-sm">
                                <thead class="bg-[#e8f5e9]">
                                    <tr class="text-left text-gray-600">
                                        <th class="py-2 px-3">Item</th>
                                        <th class="py-2 px-3">Batch</th>
                                        <th class="py-2 px-3">Expiry</th>
                                        <th class="py-2 px-3">Qty</th>
                                        <th class="py-2 px-3">UoM</th>
                                        <th class="py-2 px-3">Unit Price</th>
                                        <th class="py-2 px-3">Total</th>
                                        <th class="py-2 px-3"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in receiveForm.items" :key="index" class="border-t">
                                        <td class="py-2 px-3">{{ index + 1 }}</td>
                                        <td class="py-2 px-3">{{ item.batchno }}</td>
                                        <td class="py-2 px-3">{{ item.expirydate }}</td>
                                        <td class="py-2 px-3">{{ item.qty }}</td>
                                        <td class="py-2 px-3">{{ item.uom }}</td>
                                        <td class="py-2 px-3">{{ formatMoney(item.unitprice) }}</td>
                                        <td class="py-2 px-3">{{ formatMoney(itemTotal(item)) }}</td>
                                        <td class="py-2 px-3">
                                            <button type="button" class="text-red-600 hover:text-red-800" @click="removeItem(index)">Remove</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="rounded-lg border border-[#cfe8d1] bg-[#FBFCFA] p-4">
                        <p class="text-sm font-medium text-gray-700">Grand Total: <span class="text-lg font-bold text-[#2e7d32]">{{ formatMoney(grandTotal) }}</span></p>
                    </div>
                </div>

                <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button variant="danger" type="button" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="requestReceiveCancel">Cancel</Button>
                    <Button variant="secondary" type="submit" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" :disabled="receiveForm.processing">Receive Stock</Button>
                </div>
            </form>
        </Modal>

        <Modal :show="showReceiveCancelConfirm" :showFooter="false" @close="closeReceiveCancelConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Discard Transaction?</h3>
                <p class="text-sm text-white/80">You have entered data for this stock receipt.</p>
            </div>
            <p class="text-sm text-gray-700">Canceling will discard the current receiving transaction. Continue?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="closeReceiveCancelConfirm">Keep Editing</Button>
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="confirmReceiveCancel">Discard</Button>
            </div>
        </Modal>

        <Modal :show="showViewModal" :showFooter="false" maxWidth="sm:max-w-4xl" @close="closeViewModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-gray-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Receiving Details</h3>
                <p class="text-sm text-white/80">View receiving information.</p>
            </div>
            <div class="min-h-[420px] space-y-3 text-base text-gray-700">
                <div><span class="font-semibold text-[#1b5e20]">Batch:</span> {{ viewRecord?.batchno || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Supplier:</span> {{ viewRecord?.supplier || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Date Received:</span> {{ formatDate(viewRecord?.datereceived) }}</div>
                <div class="pt-2" v-if="viewRecord?.items?.length">
                    <div class="mt-2 overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">Medicine</th>
                                    <th class="px-3 py-2 text-left font-semibold">Qty</th>
                                    <th class="px-3 py-2 text-left font-semibold">UoM</th>
                                    <th class="px-3 py-2 text-left font-semibold">Unit Price</th>
                                    <th class="px-3 py-2 text-left font-semibold">Total</th>
                                    <th class="px-3 py-2 text-left font-semibold">Expiry</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in viewRecord.items" :key="item.recid">
                                    <td class="px-3 py-2">{{ item.itemname }}</td>
                                    <td class="px-3 py-2">{{ formatNumber(item.qty) }}</td>
                                    <td class="px-3 py-2">{{ item.uom }}</td>
                                    <td class="px-3 py-2">{{ formatMoney(item.unitprice) }}</td>
                                    <td class="px-3 py-2">{{ formatMoney(item.totalamount) }}</td>
                                    <td class="px-3 py-2">{{ formatDate(item.expirydate) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-gray-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-gray-600 hover:bg-[#dff1e1] focus:ring-white h-9" @click="closeViewModal">Close</Button>
                <Button variant="secondary" class="bg-white text-gray-600 hover:bg-[#dff1e1] focus:ring-white h-9" @click="closeViewModal">Done</Button>
            </div>
        </Modal>

        <Modal :show="showBatchEditModal" :showFooter="false" maxWidth="sm:max-w-4xl" @close="closeBatchEditModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Edit Receiving</h3>
                <p class="text-sm text-white/80">Review and manage items in this batch.</p>
            </div>
            <div class="min-h-[420px] space-y-3 text-base text-gray-700">
                <div><span class="font-semibold text-[#1b5e20]">Batch:</span> {{ editBatchRecord?.batchno || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Supplier:</span> {{ editBatchRecord?.supplier || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Date Received:</span> {{ formatDate(editBatchRecord?.datereceived) }}</div>
                <div class="pt-2" v-if="editBatchRecord?.items?.length">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-700">Items</p>
                        <button type="button" class="btn-primary" @click="openBatchAddModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            <span>Add Item</span>
                        </button>
                    </div>
                    <div class="mt-2 overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-100 text-gray-600">
                                <tr>
                                    <th class="px-3 py-2 text-left font-semibold">Medicine</th>
                                    <th class="px-3 py-2 text-left font-semibold">Qty</th>
                                    <th class="px-3 py-2 text-left font-semibold">UoM</th>
                                    <th class="px-3 py-2 text-left font-semibold">Unit Price</th>
                                    <th class="px-3 py-2 text-left font-semibold">Total</th>
                                    <th class="px-3 py-2 text-left font-semibold">Expiry</th>
                                    <th class="px-3 py-2 text-left font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                <tr v-for="item in editBatchRecord.items" :key="item.recid">
                                    <td class="px-3 py-2">{{ item.itemname }}</td>
                                    <td class="px-3 py-2">{{ item.qty }}</td>
                                    <td class="px-3 py-2">{{ item.uom }}</td>
                                    <td class="px-3 py-2">{{ formatMoney(item.unitprice) }}</td>
                                    <td class="px-3 py-2">{{ formatMoney(item.totalamount) }}</td>
                                    <td class="px-3 py-2">{{ item.expirydate || '-' }}</td>
                                    <td class="px-3 py-2">
                                        <button
                                            type="button"
                                            class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700"
                                            @click="openItemEditModal(item)"
                                        >
                                            <span class="sr-only">Edit</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a1.875 1.875 0 012.651 2.651L7.5 18.15 3 19.5l1.35-4.5L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-600 text-white shadow-sm transition hover:bg-red-700"
                                            @click="openItemDeleteModal(item)"
                                        >
                                            <span class="sr-only">Remove</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 7h12m-9 0V5a1 1 0 011-1h4a1 1 0 011 1v2m-8 0l.8 12.1A2 2 0 008.8 21h6.4a2 2 0 001.99-1.9L18 7" />
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="danger" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="requestBatchClose">Cancel</Button>
                <Button
                    variant="secondary"
                    class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9"
                    :disabled="pendingEditsCount === 0"
                    @click="openBatchUpdateConfirm"
                >
                    Update Items
                </Button>
            </div>
        </Modal>

        <Modal :show="showBatchUpdateConfirm" :showFooter="false" @close="closeBatchUpdateConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Update</h3>
                <p class="text-sm text-white/80">Apply changes to inventory stock?</p>
            </div>
            <p class="text-sm text-gray-700">Proceeding will update inventory stock based on the edited items.</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="closeBatchUpdateConfirm">Cancel</Button>
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" :disabled="pendingEditsCount === 0" @click="commitBatchUpdates">Update Items</Button>
            </div>
        </Modal>

        <Modal :show="showBatchCancelConfirm" :showFooter="false" @close="cancelBatchDiscard">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Discard Changes?</h3>
                <p class="text-sm text-white/80">You have unsaved item edits.</p>
            </div>
            <p class="text-sm text-gray-700">Canceling will discard the changes made to items in this batch.</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="cancelBatchDiscard">Keep Editing</Button>
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="confirmBatchDiscard">Discard</Button>
            </div>
        </Modal>

        <Modal :show="showItemEditModal" :showFooter="false" maxWidth="sm:max-w-4xl" @close="closeItemEditModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Edit Item</h3>
                <p class="text-sm text-white/80">Update the item details below.</p>
            </div>
            <form @submit.prevent="submitEdit">
                <div class="space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <Input id="edit_supplier" v-model="editForm.supplier" label="Supplier" :error="editForm.errors.supplier" />
                        <Input id="edit_referenceno" v-model="editForm.referenceno" label="Reference Number" disabled :error="editForm.errors.referenceno" />
                    </div>

                    <div class="rounded-xl border border-[#cfe8d1] bg-[#FBFCFA] p-4 space-y-4">
                        <div class="flex items-center justify-between">
                            <p class="text-sm font-semibold text-gray-700">Add Item</p>
                        </div>

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-3">
                                <Input id="edit_batchno" v-model="editForm.batchno" label="Batch Number" disabled :error="editForm.errors.batchno" />
                            </div>
                            <div class="col-span-6">
                                <Select id="edit_itemcode" v-model="editForm.itemcode" label="Medicine" :options="itemOptions" required :error="editForm.errors.itemcode" />
                            </div>
                            <div class="col-span-3">
                                <Input id="edit_expirydate" v-model="editForm.expirydate" label="Expiry Date" type="date" required :error="editForm.errors.expirydate" />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 gap-4">
                            <div class="col-span-4">
                                <Input id="edit_qty" v-model="editForm.qty" label="Quantity" type="number" step="0.01" required :error="editForm.errors.qty" />
                            </div>
                            <div class="col-span-4">
                                <Input id="edit_uom" v-model="editForm.uom" label="Unit of Measure" required :error="editForm.errors.uom" />
                            </div>
                            <div class="col-span-4">
                                <Input id="edit_unitprice" v-model="editForm.unitprice" label="Unit Price" type="number" step="0.01" required :error="editForm.errors.unitprice" />
                            </div>
                        </div>

                        <div class="grid grid-cols-12 items-center gap-4">
                            <div class="col-span-3">
                                <p class="text-xs text-gray-500">
                                    Line Total:
                                    <span class="font-semibold text-[#2e7d32]">{{ formatMoney(editTotal) }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button variant="danger" type="button" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="requestItemEditClose">Cancel</Button>
                    <Button variant="secondary" type="button" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" :disabled="editForm.processing" @click="openItemUpdateConfirm">Save Changes</Button>
                </div>
            </form>
        </Modal>

        <Modal :show="showBatchAddModal" :showFooter="false" maxWidth="sm:max-w-4xl" @close="requestBatchAddClose">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Add Item</h3>
                <p class="text-sm text-white/80">Add a new item to this batch.</p>
            </div>
            <div class="space-y-6">
                <div class="rounded-xl border border-[#cfe8d1] bg-[#FBFCFA] p-4 space-y-4">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-semibold text-gray-700">Add Item</p>
                    </div>

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-3">
                            <Input id="add_batchno" v-model="addItemForm.batchno" label="Batch Number" disabled :error="addItemErrors.batchno" />
                        </div>
                        <div class="col-span-6">
                            <Select
                                id="add_itemcode"
                                v-model="addItemForm.itemcode"
                                label="Medicine"
                                :options="itemOptions"
                                required
                                :error="addItemErrors.itemcode"
                            />
                        </div>
                        <div class="col-span-3">
                            <Input id="add_expirydate" v-model="addItemForm.expirydate" label="Expiry Date" type="date" required :error="addItemErrors.expirydate" />
                        </div>
                    </div>

                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-4">
                            <Input id="add_qty" v-model="addItemForm.qty" label="Quantity" type="number" step="0.01" required :error="addItemErrors.qty" />
                        </div>
                        <div class="col-span-4">
                            <Input id="add_uom" v-model="addItemForm.uom" label="Unit of Measure" required :error="addItemErrors.uom" />
                        </div>
                        <div class="col-span-4">
                            <Input id="add_unitprice" v-model="addItemForm.unitprice" label="Unit Price" type="number" step="0.01" required :error="addItemErrors.unitprice" />
                        </div>
                    </div>

                    <div class="grid grid-cols-12 items-center gap-4">
                        <div class="col-span-3">
                            <p class="text-xs text-gray-500">
                                Line Total:
                                <span class="font-semibold text-[#2e7d32]">{{ formatMoney(addItemTotal) }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="danger" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="requestBatchAddClose">Cancel</Button>
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="addItemToBatch">Add Item</Button>
            </div>
        </Modal>

        <Modal :show="showAddItemConfirm" :showFooter="false" @close="showAddItemConfirm = false">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Add</h3>
                <p class="text-sm text-white/80">Add this item to the batch?</p>
            </div>
            <p class="text-sm text-gray-700">Please confirm you want to add the item to the list.</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="showAddItemConfirm = false">Cancel</Button>
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="confirmAddItem">Add Item</Button>
            </div>
        </Modal>

        <Modal :show="showAddItemCancelConfirm" :showFooter="false" @close="closeAddItemCancelConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Discard Changes?</h3>
                <p class="text-sm text-white/80">You have entered item details.</p>
            </div>
            <p class="text-sm text-gray-700">Canceling will discard the current item entry. Continue?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="closeAddItemCancelConfirm">Keep Editing</Button>
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="confirmAddItemDiscard">Discard</Button>
            </div>
        </Modal>

        <Modal :show="showItemCancelConfirm" :showFooter="false" @close="closeItemCancelConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Discard Changes?</h3>
                <p class="text-sm text-white/80">You have unsaved edits.</p>
            </div>
            <p class="text-sm text-gray-700">Canceling will discard changes made to this item. Continue?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="closeItemCancelConfirm">Keep Editing</Button>
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="confirmItemDiscard">Discard</Button>
            </div>
        </Modal>

        <Modal :show="showItemUpdateConfirm" :showFooter="false" @close="closeItemUpdateConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Update</h3>
                <p class="text-sm text-white/80">Apply changes to this item?</p>
            </div>
            <p class="text-sm text-gray-700">Please confirm you want to save the changes for "{{ editItemRecord?.itemname }}".</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="closeItemUpdateConfirm">Cancel</Button>
                <Button variant="secondary" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" :disabled="editForm.processing" @click="confirmItemUpdate">Save Changes</Button>
            </div>
        </Modal>

        <Modal :show="showItemDeleteModal" :showFooter="false" @close="closeItemDeleteModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-red-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
                <p class="text-sm text-white/80">This action cannot be undone.</p>
            </div>
            <p class="text-sm text-gray-700">Are you sure you want to delete "{{ itemDeleteRecord?.itemname }}"?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-red-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="closeItemDeleteModal">Cancel</Button>
                <Button variant="danger" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="submitItemDelete">Delete</Button>
            </div>
        </Modal>

        <Modal :show="showDeleteModal" :showFooter="false" @close="closeDeleteModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-red-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
                <p class="text-sm text-white/80">This action cannot be undone.</p>
            </div>
            <p class="text-sm text-gray-700">Are you sure you want to delete batch "{{ deleteRecord?.batchno }}"?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-red-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="closeDeleteModal">Cancel</Button>
                <Button variant="danger" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="openBatchDeleteConfirm">Delete</Button>
            </div>
        </Modal>

        <Modal :show="showBatchDeleteConfirm" :showFooter="false" @close="closeBatchDeleteConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-red-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
                <p class="text-sm text-white/80">You are about to delete this batch.</p>
            </div>
            <p class="text-sm text-gray-700">Proceed with deleting batch "{{ deleteRecord?.batchno }}"?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-red-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="closeBatchDeleteConfirm">Cancel</Button>
                <Button variant="danger" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="submitDelete">Delete</Button>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed, defineComponent, ref, watch } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import Button from '@/Components/Button.vue';
import type { Item, Receiving } from '@/Types';

interface ReceivingRecord extends Receiving {
    itemname?: string;
    total_items?: number;
    items?: Array<{
        recid: number;
        itemcode: number;
        itemname: string;
        qty: number;
        uom: string;
        unitprice: number;
        totalamount: number;
        expirydate: string | null;
        isNew?: boolean;
    }>;
}

type ReceivingItem = NonNullable<ReceivingRecord['items']>[number];

interface PendingItemEdit {
    recid: number;
    itemcode: number;
    qty: string;
    uom: string;
    unitprice: string;
    batchno: string;
    expirydate: string;
    supplier: string;
    referenceno: string;
}

interface Summary {
    pendingReceipts: number;
    completedToday: number;
    totalThisMonth: number;
}

interface Props {
    receivings: {
        data: ReceivingRecord[];
        links?: any[];
        from?: number;
        to?: number;
        total?: number;
    };
    summary: Summary;
    filters?: {
        search?: string;
    };
    items: Item[];
}

const props = defineProps<Props>();
const { receivings, summary } = props;

const showReceiveModal = ref(false);
const showReceiveCancelConfirm = ref(false);
const showViewModal = ref(false);
const showBatchEditModal = ref(false);
const showItemEditModal = ref(false);
const showItemDeleteModal = ref(false);
const showItemUpdateConfirm = ref(false);
const showBatchUpdateConfirm = ref(false);
const showBatchCancelConfirm = ref(false);
const showBatchDeleteConfirm = ref(false);
const showItemCancelConfirm = ref(false);
const showBatchAddModal = ref(false);
const showAddItemConfirm = ref(false);
const showAddItemCancelConfirm = ref(false);
const showDeleteModal = ref(false);

const viewRecord = ref<ReceivingRecord | null>(null);
const editBatchRecord = ref<ReceivingRecord | null>(null);
const editItemRecord = ref<ReceivingItem | null>(null);
const itemDeleteRecord = ref<ReceivingItem | null>(null);
const pendingItemEdits = ref<Record<number, PendingItemEdit>>({});
const itemEditSnapshot = ref<PendingItemEdit | null>(null);
const pendingItemAdds = ref<Array<PendingItemEdit & { tempId: number }>>([]);
const tempItemId = ref(-1);
const deleteRecord = ref<ReceivingRecord | null>(null);

const receiveForm = useForm({
    supplier: '',
    referenceno: '',
    items: [] as Array<{
        itemcode: number | '';
        batchno: string;
        expirydate: string;
        qty: string;
        uom: string;
        unitprice: string;
    }>,
});

const currentItem = ref({
    itemcode: '' as number | '',
    batchno: '',
    expirydate: '',
    qty: '',
    uom: '',
    unitprice: '',
});

const itemErrors = ref({
    itemcode: '',
    batchno: '',
    expirydate: '',
    qty: '',
    uom: '',
    unitprice: '',
});

const editForm = useForm({
    itemcode: '' as number | '',
    supplier: '',
    referenceno: '',
    qty: '',
    uom: '',
    unitprice: '',
    batchno: '',
    expirydate: '',
});

const addItemForm = ref({
    itemcode: '' as number | '',
    batchno: '',
    expirydate: '',
    qty: '',
    uom: '',
    unitprice: '',
});

const addItemErrors = ref({
    itemcode: '',
    batchno: '',
    expirydate: '',
    qty: '',
    uom: '',
    unitprice: '',
});

const itemOptions = computed(() => {
    return props.items.map(item => ({
        value: item.itemcode,
        label: item.itemname,
    }));
});

const pendingEditsCount = computed(() => Object.keys(pendingItemEdits.value).length + pendingItemAdds.value.length);

const getNextCode = (prefix: string, values: Array<string | null | undefined>) => {
    const max = values.reduce((acc, value) => {
        if (!value || !value.startsWith(prefix)) return acc;
        const num = parseInt(value.replace(prefix, ''), 10);
        return Number.isNaN(num) ? acc : Math.max(acc, num);
    }, 0);
    const next = max + 1;
    return `${prefix}${String(next).padStart(6, '0')}`;
};

const generateNextReference = () => {
    const existing = receivings.data.map(record => record.referenceno);
    return getNextCode('RS-', existing);
};

const generateNextBatch = () => {
    const existing = [
        ...receivings.data.map(record => record.batchno),
        ...receiveForm.items.map(item => item.batchno),
    ];
    return getNextCode('BN-', existing);
};

watch(
    () => currentItem.value.itemcode,
    (value) => {
        if (!value) return;
        const selected = props.items.find(item => item.itemcode === value);
        if (selected && selected.default_uom && !currentItem.value.uom) {
            currentItem.value.uom = selected.default_uom;
        }
    }
);

watch(
    () => addItemForm.value.itemcode,
    (value) => {
        if (!value) return;
        const selected = props.items.find(item => item.itemcode === value);
        if (selected && selected.default_uom && !addItemForm.value.uom) {
            addItemForm.value.uom = selected.default_uom;
        }
    }
);

const itemLabel = (itemcode: number | '') => {
    if (!itemcode) return 'N/A';
    const match = props.items.find(item => item.itemcode === itemcode);
    return match ? `${match.itemname}${match.default_uom ? ` (${match.default_uom})` : ''}` : String(itemcode);
};

const currentTotal = computed(() => {
    const qty = parseFloat(currentItem.value.qty) || 0;
    const price = parseFloat(currentItem.value.unitprice) || 0;
    return qty * price;
});

const editTotal = computed(() => {
    const qty = parseFloat(editForm.qty) || 0;
    const price = parseFloat(editForm.unitprice) || 0;
    return qty * price;
});

const addItemTotal = computed(() => {
    const qty = parseFloat(addItemForm.value.qty) || 0;
    const price = parseFloat(addItemForm.value.unitprice) || 0;
    return qty * price;
});

const hasAddItemData = computed(() => {
    return !!(
        addItemForm.value.itemcode ||
        addItemForm.value.expirydate ||
        addItemForm.value.qty ||
        addItemForm.value.uom ||
        addItemForm.value.unitprice
    );
});

const grandTotal = computed(() => {
    return receiveForm.items.reduce((sum, item) => sum + itemTotal(item), 0);
});

const itemTotal = (item: { qty: string; unitprice: string }) => {
    const qty = parseFloat(item.qty) || 0;
    const price = parseFloat(item.unitprice) || 0;
    return qty * price;
};

const formatNumber = (value: number | string) => {
    const num = typeof value === 'number' ? value : parseFloat(value);
    return Number.isFinite(num) ? num.toFixed(2) : '0.00';
};

const formatMoney = (value: number | string) => {
    const num = typeof value === 'number' ? value : parseFloat(value);
    if (!Number.isFinite(num)) return '0.00';
    return num.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
};

const formatDate = (value?: string | null) => {
    if (!value) return '-';
    const date = new Date(value);
    return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString();
};

const statusLabel = (record: ReceivingRecord) => {
    if (record.referenceno) return 'Completed';
    return 'Pending';
};

const statusClass = (record: ReceivingRecord) => {
    return record.referenceno ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800';
};

const resetItemErrors = () => {
    itemErrors.value = {
        itemcode: '',
        batchno: '',
        expirydate: '',
        qty: '',
        uom: '',
        unitprice: '',
    };
};

const addItem = () => {
    resetItemErrors();
    receiveForm.clearErrors('items');
    receiveForm.clearErrors('supplier');

    if (!receiveForm.supplier) {
        receiveForm.setError('supplier', 'Required');
        return;
    }

    if (!currentItem.value.itemcode) itemErrors.value.itemcode = 'Required';
    if (!currentItem.value.batchno) itemErrors.value.batchno = 'Required';
    if (!currentItem.value.expirydate) itemErrors.value.expirydate = 'Required';
    if (!currentItem.value.qty) itemErrors.value.qty = 'Required';
    if (!currentItem.value.uom) itemErrors.value.uom = 'Required';
    if (!currentItem.value.unitprice) itemErrors.value.unitprice = 'Required';

    if (Object.values(itemErrors.value).some(error => error)) return;

    receiveForm.items.push({ ...currentItem.value });
    currentItem.value = {
        itemcode: '',
        batchno: currentItem.value.batchno,
        expirydate: '',
        qty: '',
        uom: '',
        unitprice: '',
    };
};

const removeItem = (index: number) => {
    receiveForm.items.splice(index, 1);
};

const openReceiveModal = () => {
    showReceiveModal.value = true;
    if (!receiveForm.referenceno) {
        receiveForm.referenceno = generateNextReference();
    }
    if (!currentItem.value.batchno) {
        currentItem.value.batchno = generateNextBatch();
    }
};

const hasReceiveData = computed(() => {
    return !!(
        receiveForm.supplier ||
        receiveForm.items.length ||
        currentItem.value.itemcode ||
        currentItem.value.expirydate ||
        currentItem.value.qty ||
        currentItem.value.uom ||
        currentItem.value.unitprice
    );
});

const requestReceiveCancel = () => {
    if (hasReceiveData.value) {
        showReceiveCancelConfirm.value = true;
        return;
    }
    closeReceiveModal();
};

const closeReceiveCancelConfirm = () => {
    showReceiveCancelConfirm.value = false;
};

const confirmReceiveCancel = () => {
    showReceiveCancelConfirm.value = false;
    closeReceiveModal();
};

const closeReceiveModal = () => {
    showReceiveModal.value = false;
    receiveForm.reset();
    receiveForm.clearErrors();
    receiveForm.items = [];
    currentItem.value = { itemcode: '', batchno: '', expirydate: '', qty: '', uom: '', unitprice: '' };
};

const submitReceive = () => {
    receiveForm.clearErrors();
    if (receiveForm.items.length === 0) {
        receiveForm.setError('items', 'Please add at least one item before receiving stock.');
        return;
    }
    receiveForm.post('/receiving', {
        onSuccess: () => {
            closeReceiveModal();
        },
    });
};

const openViewModal = (record: ReceivingRecord) => {
    viewRecord.value = record;
    showViewModal.value = true;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewRecord.value = null;
};

const openEditModal = (record: ReceivingRecord) => {
    editBatchRecord.value = record;
    showBatchEditModal.value = true;
};

const closeBatchEditModal = () => {
    showBatchEditModal.value = false;
    editBatchRecord.value = null;
};

const requestBatchClose = () => {
    if (pendingEditsCount.value > 0) {
        showBatchCancelConfirm.value = true;
        return;
    }
    closeBatchEditModal();
};

const cancelBatchDiscard = () => {
    showBatchCancelConfirm.value = false;
};

const confirmBatchDiscard = () => {
    pendingItemEdits.value = {};
    pendingItemAdds.value = [];
    showBatchCancelConfirm.value = false;
    closeBatchEditModal();
};

const openItemEditModal = (item: ReceivingItem) => {
    editItemRecord.value = item;
    editForm.itemcode = item.itemcode;
    editForm.qty = String(item.qty ?? '');
    editForm.uom = item.uom || '';
    editForm.unitprice = String(item.unitprice ?? '');
    editForm.batchno = editBatchRecord.value?.batchno || '';
    editForm.expirydate = item.expirydate || '';
    editForm.supplier = editBatchRecord.value?.supplier || '';
    editForm.referenceno = editBatchRecord.value?.referenceno || '';
    editForm.clearErrors();
    itemEditSnapshot.value = {
        recid: item.recid,
        itemcode: item.itemcode,
        qty: String(item.qty ?? ''),
        uom: item.uom || '',
        unitprice: String(item.unitprice ?? ''),
        batchno: editBatchRecord.value?.batchno || '',
        expirydate: item.expirydate || '',
        supplier: editBatchRecord.value?.supplier || '',
        referenceno: editBatchRecord.value?.referenceno || '',
    };
    showItemEditModal.value = true;
};

const hasItemEditChanges = () => {
    if (!itemEditSnapshot.value) return false;
    return (
        Number(editForm.itemcode) !== itemEditSnapshot.value.itemcode ||
        editForm.qty !== itemEditSnapshot.value.qty ||
        editForm.uom !== itemEditSnapshot.value.uom ||
        editForm.unitprice !== itemEditSnapshot.value.unitprice ||
        editForm.batchno !== itemEditSnapshot.value.batchno ||
        editForm.expirydate !== itemEditSnapshot.value.expirydate ||
        editForm.supplier !== itemEditSnapshot.value.supplier ||
        editForm.referenceno !== itemEditSnapshot.value.referenceno
    );
};

const requestItemEditClose = () => {
    if (hasItemEditChanges()) {
        showItemCancelConfirm.value = true;
        return;
    }
    closeItemEditModal();
};

const closeItemCancelConfirm = () => {
    showItemCancelConfirm.value = false;
};

const confirmItemDiscard = () => {
    showItemCancelConfirm.value = false;
    closeItemEditModal();
};

const closeItemEditModal = () => {
    showItemEditModal.value = false;
    editItemRecord.value = null;
    itemEditSnapshot.value = null;
};

const openItemUpdateConfirm = () => {
    showItemUpdateConfirm.value = true;
};

const closeItemUpdateConfirm = () => {
    showItemUpdateConfirm.value = false;
};

const confirmItemUpdate = () => {
    showItemUpdateConfirm.value = false;
    applyItemEditLocal();
};

const openBatchUpdateConfirm = () => {
    showBatchUpdateConfirm.value = true;
};

const closeBatchUpdateConfirm = () => {
    showBatchUpdateConfirm.value = false;
};

const openBatchAddModal = () => {
    addItemForm.value = {
        itemcode: '',
        batchno: editBatchRecord.value?.batchno || '',
        expirydate: '',
        qty: '',
        uom: '',
        unitprice: '',
    };
    addItemErrors.value = {
        itemcode: '',
        batchno: '',
        expirydate: '',
        qty: '',
        uom: '',
        unitprice: '',
    };
    showBatchAddModal.value = true;
};

const requestBatchAddClose = () => {
    if (hasAddItemData.value) {
        showAddItemCancelConfirm.value = true;
        return;
    }
    closeBatchAddModal();
};

const closeAddItemCancelConfirm = () => {
    showAddItemCancelConfirm.value = false;
};

const confirmAddItemDiscard = () => {
    showAddItemCancelConfirm.value = false;
    closeBatchAddModal();
};

const closeBatchAddModal = () => {
    showBatchAddModal.value = false;
    showAddItemConfirm.value = false;
    showAddItemCancelConfirm.value = false;
};

const addItemToBatch = () => {
    addItemErrors.value = {
        itemcode: '',
        batchno: '',
        expirydate: '',
        qty: '',
        uom: '',
        unitprice: '',
    };

    if (!addItemForm.value.itemcode) addItemErrors.value.itemcode = 'Required';
    if (!addItemForm.value.batchno) addItemErrors.value.batchno = 'Required';
    if (!addItemForm.value.expirydate) addItemErrors.value.expirydate = 'Required';
    if (!addItemForm.value.qty) addItemErrors.value.qty = 'Required';
    if (!addItemForm.value.uom) addItemErrors.value.uom = 'Required';
    if (!addItemForm.value.unitprice) addItemErrors.value.unitprice = 'Required';

    if (Object.values(addItemErrors.value).some(error => error)) return;

    showAddItemConfirm.value = true;
};

const confirmAddItem = () => {
    showAddItemConfirm.value = false;

    const qty = parseFloat(addItemForm.value.qty) || 0;
    const price = parseFloat(addItemForm.value.unitprice) || 0;
    const itemName = props.items.find(item => item.itemcode === Number(addItemForm.value.itemcode))?.itemname || 'N/A';
    const tempId = tempItemId.value--;

    const newItem: ReceivingItem = {
        recid: tempId,
        itemcode: Number(addItemForm.value.itemcode),
        itemname: itemName,
        qty,
        uom: addItemForm.value.uom,
        unitprice: Number(addItemForm.value.unitprice) || 0,
        totalamount: qty * price,
        expirydate: addItemForm.value.expirydate,
        isNew: true,
    };

    if (editBatchRecord.value) {
        editBatchRecord.value = {
            ...editBatchRecord.value,
            items: [...(editBatchRecord.value.items || []), newItem],
        };
    }

    pendingItemAdds.value = [
        ...pendingItemAdds.value,
        {
            recid: tempId,
            tempId,
            itemcode: Number(addItemForm.value.itemcode),
            qty: addItemForm.value.qty,
            uom: addItemForm.value.uom,
            unitprice: addItemForm.value.unitprice,
            batchno: addItemForm.value.batchno,
            expirydate: addItemForm.value.expirydate,
            supplier: editBatchRecord.value?.supplier || '',
            referenceno: editBatchRecord.value?.referenceno || '',
        },
    ];

    closeBatchAddModal();
};

const applyItemEditLocal = () => {
    if (!editItemRecord.value || !editBatchRecord.value) return;

    const payload: PendingItemEdit = {
        recid: editItemRecord.value.recid,
        itemcode: Number(editForm.itemcode),
        qty: editForm.qty,
        uom: editForm.uom,
        unitprice: editForm.unitprice,
        batchno: editForm.batchno,
        expirydate: editForm.expirydate,
        supplier: editForm.supplier,
        referenceno: editForm.referenceno,
    };

    const itemName = props.items.find(item => item.itemcode === payload.itemcode)?.itemname || editItemRecord.value.itemname;
    const qty = parseFloat(payload.qty) || 0;
    const price = parseFloat(payload.unitprice) || 0;

    if (editItemRecord.value.isNew) {
        pendingItemAdds.value = pendingItemAdds.value.map(item =>
            item.recid === payload.recid ? { ...item, ...payload } : item
        );
    } else {
        pendingItemEdits.value = {
            ...pendingItemEdits.value,
            [payload.recid]: payload,
        };
    }

    const updatedItems = (editBatchRecord.value.items || []).map(item =>
        item.recid === payload.recid
            ? {
                  ...item,
                  itemcode: payload.itemcode,
                  itemname: itemName,
                  qty,
                  uom: payload.uom,
                  unitprice: Number(payload.unitprice) || 0,
                  totalamount: qty * price,
                  expirydate: payload.expirydate,
              }
            : item
    );

    editBatchRecord.value = {
        ...editBatchRecord.value,
        items: updatedItems,
    };

    closeItemEditModal();
};

const commitBatchUpdates = async () => {
    showBatchUpdateConfirm.value = false;
    const edits = Object.values(pendingItemEdits.value);
    const adds = pendingItemAdds.value;
    if (!edits.length && !adds.length) {
        closeBatchEditModal();
        return;
    }

    const updateItem = (item: PendingItemEdit) =>
        new Promise<void>((resolve) => {
            router.put(
                `/receiving/${item.recid}`,
                {
                    itemcode: item.itemcode,
                    supplier: item.supplier || null,
                    referenceno: item.referenceno || null,
                    qty: item.qty,
                    uom: item.uom,
                    unitprice: item.unitprice,
                    batchno: item.batchno,
                    expirydate: item.expirydate,
                },
                {
                    preserveScroll: true,
                    onSuccess: () => resolve(),
                    onError: () => resolve(),
                }
            );
        });

    for (const edit of edits) {
        await updateItem(edit);
    }

    if (adds.length) {
        await new Promise<void>((resolve) => {
            router.post(
                '/receiving',
                {
                    supplier: editBatchRecord.value?.supplier || null,
                    referenceno: editBatchRecord.value?.referenceno || null,
                    department: null,
                    items: adds.map(item => ({
                        itemcode: item.itemcode,
                        qty: item.qty,
                        uom: item.uom,
                        unitprice: item.unitprice,
                        batchno: item.batchno,
                        expirydate: item.expirydate,
                    })),
                },
                {
                    preserveScroll: true,
                    onSuccess: () => resolve(),
                    onError: () => resolve(),
                }
            );
        });
    }

    pendingItemEdits.value = {};
    pendingItemAdds.value = [];
    router.reload({
        only: ['receivings', 'summary'],
        onSuccess: () => {
            closeBatchEditModal();
        },
    });
};

const openItemDeleteModal = (item: ReceivingItem) => {
    itemDeleteRecord.value = item;
    showItemDeleteModal.value = true;
};

const closeItemDeleteModal = () => {
    showItemDeleteModal.value = false;
    itemDeleteRecord.value = null;
};

const submitItemDelete = () => {
    if (!itemDeleteRecord.value) return;
    if (itemDeleteRecord.value.isNew) {
        if (editBatchRecord.value) {
            editBatchRecord.value = {
                ...editBatchRecord.value,
                items: (editBatchRecord.value.items || []).filter(item => item.recid !== itemDeleteRecord.value?.recid),
            };
        }
        pendingItemAdds.value = pendingItemAdds.value.filter(item => item.recid !== itemDeleteRecord.value?.recid);
        closeItemDeleteModal();
        return;
    }
    router.delete(`/receiving/${itemDeleteRecord.value.recid}`, {
        onSuccess: () => {
            closeItemDeleteModal();
            router.reload({ only: ['receivings', 'summary'] });
        },
    });
};

const openDeleteModal = (record: ReceivingRecord) => {
    deleteRecord.value = record;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    deleteRecord.value = null;
};

const openBatchDeleteConfirm = () => {
    showBatchDeleteConfirm.value = true;
};

const closeBatchDeleteConfirm = () => {
    showBatchDeleteConfirm.value = false;
};

const submitDelete = () => {
    if (!deleteRecord.value) return;
    router.delete(`/receiving/${deleteRecord.value.recid}`, {
        onSuccess: () => {
            showBatchDeleteConfirm.value = false;
            closeDeleteModal();
            router.reload({ only: ['receivings', 'summary'] });
        },
    });
};

const SummaryCard = defineComponent({
    name: 'SummaryCard',
    props: {
        title: { type: String, required: true },
        value: { type: [Number, String], required: true },
        iconColor: { type: String, required: true },
        iconBg: { type: String, required: true },
    },
    template: `
        <div class="flex items-center justify-between rounded-xl bg-[#f6fbf6] p-5 shadow-sm ring-1 ring-[#cfe8d1]">
            <div>
                <p class="page-subtitle">{{ title }}</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ value }}</p>
            </div>
            <div :class="['flex h-12 w-12 items-center justify-center rounded-xl', iconBg]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" :class="iconColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
            </div>
        </div>
    `,
});
</script>
