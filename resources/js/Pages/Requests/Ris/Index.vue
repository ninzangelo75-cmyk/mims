<template>
    <AppLayout>
        <div class="py-6">
            <div class="mx-auto max-w-7xl space-y-4 px-0 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="page-title">Request</h2>
                        <p class="page-subtitle">Submit and track medicine requests</p>
                    </div>
                    <button
                        type="button"
                        class="btn-primary min-w-[190px]"
                        @click="openRequestModal"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>New RIS Request</span>
                    </button>
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <SummaryCard
                        title="Pending Requests"
                        :value="summary.pending"
                        icon-color="text-orange-500"
                        icon-bg="bg-orange-50"
                    />
                    <SummaryCard
                        title="Approved Today"
                        :value="summary.approvedToday"
                        icon-color="text-green-500"
                        icon-bg="bg-green-50"
                    />
                    <SummaryCard
                        title="Total This Month"
                        :value="summary.totalThisMonth"
                        icon-color="text-indigo-500"
                        icon-bg="bg-indigo-50"
                    />
                </div>

                <div class="overflow-hidden rounded-2xl bg-[#f6fbf6] shadow ring-1 ring-[#cfe8d1]">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">RIS Request History</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#e8f5e9]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        RIS Request ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Item
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Quantity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Requested By
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Department
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Request Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                                <tr v-if="!requests.data.length">
                                    <td colspan="7" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No requests found.
                                    </td>
                                </tr>
                                <tr v-for="request in requests.data" :key="request.req_ris" class="hover:bg-[#e8f5e9]">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ formatReqId(request.req_ris) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.itemname }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.req_qty }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.requested_by }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.department || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatDate(request.requestedat) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span :class="statusClass(request.isavailable)" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold">
                                            {{ statusLabel(request.isavailable) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                        <button
                                            type="button"
                                            class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-500 text-white shadow-sm transition hover:bg-gray-600"
                                            @click="openViewModal(request)"
                                        >
                                            <span class="sr-only">View</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700"
                                            @click="openEditModal(request)"
                                        >
                                            <span class="sr-only">Edit</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a1.875 1.875 0 012.651 2.651L7.5 18.15 3 19.5l1.35-4.5L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-600 text-white shadow-sm transition hover:bg-red-700"
                                            @click="openDeleteModal(request)"
                                        >
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

                    <div v-if="requests.links" class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ requests.from }} to {{ requests.to }} of {{ requests.total }} results
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                v-for="link in requests.links"
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

                <div class="mt-8 flex items-center justify-end">
                    <button type="button" class="btn-primary min-w-[190px]" @click="openPtrRequestModal">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>New PTR Request</span>
                    </button>
                </div>

                <div class="overflow-hidden rounded-2xl bg-[#f6fbf6] shadow ring-1 ring-[#cfe8d1]">
                    <div class="border-b border-gray-200 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">PTR Request History</h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-[#e8f5e9]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        PTR Request ID
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Item
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Quantity
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Requested By
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Department
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                                <tr v-if="!ptrRequests.data.length">
                                    <td colspan="8" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No PTR requests found.
                                    </td>
                                </tr>
                                <tr v-for="request in ptrRequests.data" :key="request.req_ptr" class="hover:bg-[#e8f5e9]">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900">
                                        {{ formatPtrId(request.req_ptr) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.itemname }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.req_qty }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.requested_by }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ request.department || '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                        {{ formatDate(request.requestedat) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span :class="statusClass(request.isavailable)" class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold">
                                            {{ statusLabel(request.isavailable) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium">
                                        <button
                                            type="button"
                                            class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-gray-500 text-white shadow-sm transition hover:bg-gray-600"
                                            @click="openPtrViewModal(request)"
                                        >
                                            <span class="sr-only">View</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-7.5 9.75-7.5S21.75 12 21.75 12s-3.75 7.5-9.75 7.5S2.25 12 2.25 12z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="mr-1 inline-flex h-8 w-8 items-center justify-center rounded-md bg-blue-600 text-white shadow-sm transition hover:bg-blue-700"
                                            @click="openPtrEditModal(request)"
                                        >
                                            <span class="sr-only">Edit</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 3.487a1.875 1.875 0 012.651 2.651L7.5 18.15 3 19.5l1.35-4.5L16.862 3.487z" />
                                            </svg>
                                        </button>
                                        <button
                                            type="button"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-red-600 text-white shadow-sm transition hover:bg-red-700"
                                            @click="openPtrDeleteModal(request)"
                                        >
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

                    <div v-if="ptrRequests.links" class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-700">
                            Showing {{ ptrRequests.from }} to {{ ptrRequests.to }} of {{ ptrRequests.total }} results
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <Link
                                v-for="link in ptrRequests.links"
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

        <Modal :show="showRequestModal" :showFooter="false" maxWidth="sm:max-w-4xl" @close="closeRequestModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">New Request</h3>
                <p class="text-sm text-white/80">Submit a medicine request.</p>
            </div>
            <form @submit.prevent="openRisSubmitConfirm">
                <div class="space-y-6">
                    <div class="relative">
                        <Select
                            id="itemcode"
                            v-model="form.itemcode"
                            label="Medicine"
                            :options="itemOptions"
                            required
                            @update:modelValue="showRisItemWarning = false"
                        />
                        <div
                            v-if="showRisItemWarning"
                            class="absolute left-28 top-16 z-10 flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 shadow"
                        >
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-orange-500 text-white text-xs font-bold">!</span>
                            <span>Please fill out this field.</span>
                            <span class="absolute -top-2 left-4 h-0 w-0 border-l-8 border-r-8 border-b-8 border-l-transparent border-r-transparent border-b-white"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <Input
                                id="req_qty"
                                v-model="form.req_qty"
                                label="Quantity"
                                type="number"
                                step="0.01"
                                required
                                @update:modelValue="showRisQtyWarning = false"
                            />
                            <div
                                v-if="showRisQtyWarning"
                                class="absolute left-24 top-16 z-10 flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 shadow"
                            >
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-orange-500 text-white text-xs font-bold">!</span>
                                <span>Please fill out this field.</span>
                                <span class="absolute -top-2 left-4 h-0 w-0 border-l-8 border-r-8 border-b-8 border-l-transparent border-r-transparent border-b-white"></span>
                            </div>
                        </div>

                        <Input
                            id="division"
                            v-model="form.division"
                            label="Division"
                            :error="form.errors.division"
                        />
                    </div>

                    <Input
                        id="department"
                        v-model="form.department"
                        label="Department"
                        :error="form.errors.department"
                    />

                    <Input
                        id="remarks"
                        v-model="form.remarks"
                        label="Remarks"
                        type="textarea"
                        :error="form.errors.remarks"
                    />
                </div>

                <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button
                        variant="danger"
                        type="button"
                        class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9"
                        @click="requestRisCancel"
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="secondary"
                        type="button"
                        class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9"
                        :disabled="form.processing"
                        @click="openRisSubmitConfirm"
                    >
                        Submit Request
                    </Button>
                </div>
            </form>
        </Modal>

        <Modal :show="showRisSubmitConfirm" :showFooter="false" @close="closeRisSubmitConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Submit</h3>
                <p class="text-sm text-white/80">Submit this RIS request?</p>
            </div>
            <p class="text-sm text-gray-700">Please confirm you want to submit the RIS request.</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="closeRisSubmitConfirm">
                    Cancel
                </Button>
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="confirmRisSubmit">
                    Submit
                </Button>
            </div>
        </Modal>

        <Modal :show="showRisCancelConfirm" :showFooter="false" @close="closeRisCancelConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Discard Request?</h3>
                <p class="text-sm text-white/80">You have entered data.</p>
            </div>
            <p class="text-sm text-gray-700">Canceling will discard the current RIS request. Continue?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="closeRisCancelConfirm">
                    Keep Editing
                </Button>
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="confirmRisCancel">
                    Discard
                </Button>
            </div>
        </Modal>

        <Modal :show="showEditModal" :showFooter="false" @close="closeEditModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Edit Request</h3>
                <p class="text-sm text-white/80">Update the request details.</p>
            </div>
            <form @submit.prevent="submitEdit">
                <div class="space-y-6">
                    <div class="rounded-lg border border-gray-200 bg-white p-4 text-sm text-gray-700">
                        <div class="grid grid-cols-2 gap-3">
                            <div><span class="font-semibold text-[#1b5e20]">RIS No:</span> {{ editingRequest?.ris_no }}</div>
                            <div><span class="font-semibold text-[#1b5e20]">Requested By:</span> {{ editingRequest?.requested_by }}</div>
                            <div><span class="font-semibold text-[#1b5e20]">Requested At:</span> {{ formatDate(editingRequest?.requestedat || null) }}</div>
                            <div><span class="font-semibold text-[#1b5e20]">Status:</span> {{ statusLabel(!!editingRequest?.isavailable) }}</div>
                        </div>
                    </div>
                    <Select
                        id="edit_itemcode"
                        v-model="editForm.itemcode"
                        label="Medicine"
                        :options="itemOptions"
                        required
                        :error="editForm.errors.itemcode"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="edit_req_qty"
                            v-model="editForm.req_qty"
                            label="Quantity"
                            type="number"
                            step="0.01"
                            required
                            :error="editForm.errors.req_qty"
                        />

                        <Input
                            id="edit_division"
                            v-model="editForm.division"
                            label="Division"
                            :error="editForm.errors.division"
                        />
                    </div>

                    <Input
                        id="edit_department"
                        v-model="editForm.department"
                        label="Department"
                        :error="editForm.errors.department"
                    />

                    <Input
                        id="edit_remarks"
                        v-model="editForm.remarks"
                        label="Remarks"
                        type="textarea"
                        :error="editForm.errors.remarks"
                    />
                </div>

                <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button
                        variant="danger"
                        type="button"
                        class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9"
                        @click="closeEditModal"
                    >
                        Cancel
                    </Button>
                    <Button
                        variant="secondary"
                        type="submit"
                        class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9"
                        :disabled="editForm.processing"
                    >
                        Update Request
                    </Button>
                </div>
            </form>
        </Modal>

        <Modal :show="showViewModal" :showFooter="false" @close="closeViewModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-gray-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Request Details</h3>
                <p class="text-sm text-white/80">View request information.</p>
            </div>
            <div class="space-y-3 text-sm text-gray-700">
                <div><span class="font-semibold text-[#1b5e20]">RIS No:</span> {{ viewRequest?.ris_no }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Item:</span> {{ viewRequest?.itemname }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Quantity:</span> {{ viewRequest?.req_qty }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Division:</span> {{ viewRequest?.division || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Department:</span> {{ viewRequest?.department || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Remarks:</span> {{ viewRequest?.remarks || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Requested By:</span> {{ viewRequest?.requested_by }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Requested At:</span> {{ formatDate(viewRequest?.requestedat || null) }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Status:</span> {{ statusLabel(!!viewRequest?.isavailable) }}</div>
            </div>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-gray-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-gray-600 hover:bg-gray-50 focus:ring-white h-9" @click="closeViewModal">Close</Button>
            </div>
        </Modal>

        <Modal :show="showDeleteModal" :showFooter="false" @close="closeDeleteModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-red-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
                <p class="text-sm text-white/80">This action cannot be undone.</p>
            </div>
            <p class="text-sm text-gray-700">Are you sure you want to delete request "{{ requestToDelete?.ris_no }}"?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-red-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="closeDeleteModal">
                    Cancel
                </Button>
                <Button variant="danger" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="deleteRequest">
                    Delete
                </Button>
            </div>
        </Modal>

        <Modal :show="showPtrRequestModal" :showFooter="false" maxWidth="sm:max-w-4xl" @close="closePtrRequestModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">New PTR Request</h3>
                <p class="text-sm text-white/80">Submit a property transfer request.</p>
            </div>
            <form @submit.prevent="openPtrSubmitConfirm">
                <div class="space-y-6">
                    <div class="relative">
                        <Select
                            id="ptr_itemcode"
                            v-model="ptrForm.itemcode"
                            label="Item"
                            :options="itemOptions"
                            required
                            @update:modelValue="showPtrItemWarning = false"
                        />
                        <div
                            v-if="showPtrItemWarning"
                            class="absolute left-24 top-16 z-10 flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 shadow"
                        >
                            <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-orange-500 text-white text-xs font-bold">!</span>
                            <span>Please fill out this field.</span>
                            <span class="absolute -top-2 left-4 h-0 w-0 border-l-8 border-r-8 border-b-8 border-l-transparent border-r-transparent border-b-white"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="relative">
                            <Input
                                id="ptr_req_qty"
                                v-model="ptrForm.req_qty"
                                label="Quantity"
                                type="number"
                                step="0.01"
                                required
                                @update:modelValue="showPtrQtyWarning = false"
                            />
                            <div
                                v-if="showPtrQtyWarning"
                                class="absolute left-24 top-16 z-10 flex items-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700 shadow"
                            >
                                <span class="inline-flex h-5 w-5 items-center justify-center rounded bg-orange-500 text-white text-xs font-bold">!</span>
                                <span>Please fill out this field.</span>
                                <span class="absolute -top-2 left-4 h-0 w-0 border-l-8 border-r-8 border-b-8 border-l-transparent border-r-transparent border-b-white"></span>
                            </div>
                        </div>
                        <Input
                            id="ptr_division"
                            v-model="ptrForm.division"
                            label="Division"
                            :error="ptrForm.errors.division"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="ptr_target"
                            v-model="ptrForm.target"
                            label="Target"
                            :error="ptrForm.errors.target"
                        />
                        <Select
                            id="ptr_trans_type_new"
                            v-model="ptrForm.trans_type"
                            label="Transfer Type"
                            :options="ptrTypeOptions"
                            required
                            :error="ptrForm.errors.trans_type"
                        />
                    </div>

                    <Input
                        id="ptr_trans_type_other_new"
                        v-model="ptrForm.trans_type_other"
                        label="Other Type"
                        :disabled="ptrForm.trans_type !== 'Others'"
                        :error="ptrForm.errors.trans_type_other"
                    />

                    <Input
                        id="ptr_remarks"
                        v-model="ptrForm.remarks"
                        label="Remarks"
                        type="textarea"
                        :error="ptrForm.errors.remarks"
                    />

                    <Input
                        id="ptr_purpose"
                        v-model="ptrForm.purpose"
                        label="Purpose"
                        type="textarea"
                        :error="ptrForm.errors.purpose"
                    />
                </div>

                <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button variant="danger" type="button" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="requestPtrCancel">
                        Cancel
                    </Button>
                    <Button variant="secondary" type="button" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" :disabled="ptrForm.processing" @click="openPtrSubmitConfirm">
                        Submit PTR Request
                    </Button>
                </div>
            </form>
        </Modal>

        <Modal :show="showPtrSubmitConfirm" :showFooter="false" @close="closePtrSubmitConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Submit</h3>
                <p class="text-sm text-white/80">Submit this PTR request?</p>
            </div>
            <p class="text-sm text-gray-700">Please confirm you want to submit the PTR request.</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="closePtrSubmitConfirm">
                    Cancel
                </Button>
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="confirmPtrSubmit">
                    Submit
                </Button>
            </div>
        </Modal>

        <Modal :show="showPtrCancelConfirm" :showFooter="false" @close="closePtrCancelConfirm">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-[#2e7d32] px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Discard Request?</h3>
                <p class="text-sm text-white/80">You have entered data.</p>
            </div>
            <p class="text-sm text-gray-700">Canceling will discard the current PTR request. Continue?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-[#2e7d32] px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="closePtrCancelConfirm">
                    Keep Editing
                </Button>
                <Button variant="secondary" class="bg-white text-[#2e7d32] hover:bg-[#dff1e1] focus:ring-white h-9" @click="confirmPtrCancel">
                    Discard
                </Button>
            </div>
        </Modal>

        <Modal :show="showPtrViewModal" :showFooter="false" @close="closePtrViewModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-gray-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">PTR Request Details</h3>
                <p class="text-sm text-white/80">View request information.</p>
            </div>
            <div class="space-y-3 text-sm text-gray-700">
                <div><span class="font-semibold text-[#1b5e20]">PTR Request ID:</span> {{ formatPtrId(ptrViewRequest?.req_ptr || '') }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Item:</span> {{ ptrViewRequest?.itemname }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Quantity:</span> {{ ptrViewRequest?.req_qty }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Division:</span> {{ ptrViewRequest?.division || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Target:</span> {{ ptrViewRequest?.target || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Type:</span> {{ ptrViewRequest?.trans_type }}</div>
                <div v-if="ptrViewRequest?.trans_type === 'Others'"><span class="font-semibold text-[#1b5e20]">Other Type:</span> {{ ptrViewRequest?.trans_type_other || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Purpose:</span> {{ ptrViewRequest?.purpose || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Remarks:</span> {{ ptrViewRequest?.remarks || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Requested By:</span> {{ ptrViewRequest?.requested_by || 'N/A' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Department:</span> {{ ptrViewRequest?.department || '-' }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Request Date:</span> {{ formatDate(ptrViewRequest?.requestedat || null) }}</div>
                <div><span class="font-semibold text-[#1b5e20]">Status:</span> {{ statusLabel(!!ptrViewRequest?.isavailable) }}</div>
            </div>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-gray-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-gray-600 hover:bg-gray-50 focus:ring-white h-9" @click="closePtrViewModal">Close</Button>
            </div>
        </Modal>

        <Modal :show="showPtrEditModal" :showFooter="false" @close="closePtrEditModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-blue-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Edit PTR Request</h3>
                <p class="text-sm text-white/80">Update the PTR request details.</p>
            </div>
            <form @submit.prevent="submitPtrEdit">
                <div class="space-y-6">
                    <Select
                        id="ptr_itemcode"
                        v-model="ptrEditForm.itemcode"
                        label="Medicine"
                        :options="itemOptions"
                        required
                        :error="ptrEditForm.errors.itemcode"
                    />

                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="ptr_req_qty"
                            v-model="ptrEditForm.req_qty"
                            label="Quantity"
                            type="number"
                            step="0.01"
                            required
                            :error="ptrEditForm.errors.req_qty"
                        />

                        <Input
                            id="ptr_division"
                            v-model="ptrEditForm.division"
                            label="Division"
                            :error="ptrEditForm.errors.division"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <Input
                            id="ptr_target"
                            v-model="ptrEditForm.target"
                            label="Target"
                            :error="ptrEditForm.errors.target"
                        />
                        <Select
                            id="ptr_trans_type"
                            v-model="ptrEditForm.trans_type"
                            label="Transfer Type"
                            :options="ptrTypeOptions"
                            required
                            :error="ptrEditForm.errors.trans_type"
                        />
                    </div>

                    <Input
                        id="ptr_trans_type_other"
                        v-model="ptrEditForm.trans_type_other"
                        label="Other Type"
                        :disabled="ptrEditForm.trans_type !== 'Others'"
                        :error="ptrEditForm.errors.trans_type_other"
                    />

                    <Input
                        id="ptr_remarks"
                        v-model="ptrEditForm.remarks"
                        label="Remarks"
                        type="textarea"
                        :error="ptrEditForm.errors.remarks"
                    />

                    <Input
                        id="ptr_purpose"
                        v-model="ptrEditForm.purpose"
                        label="Purpose"
                        type="textarea"
                        :error="ptrEditForm.errors.purpose"
                    />
                </div>

                <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-blue-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                    <Button variant="danger" type="button" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" @click="closePtrEditModal">Cancel</Button>
                    <Button variant="secondary" type="submit" class="bg-white text-blue-600 hover:bg-blue-50 focus:ring-white h-9" :disabled="ptrEditForm.processing">Update PTR Request</Button>
                </div>
            </form>
        </Modal>

        <Modal :show="showPtrDeleteModal" :showFooter="false" @close="closePtrDeleteModal">
            <div class="-mx-6 -mt-6 mb-6 rounded-t-lg bg-red-600 px-6 py-4 text-white">
                <h3 class="text-lg font-semibold">Confirm Delete</h3>
                <p class="text-sm text-white/80">This action cannot be undone.</p>
            </div>
            <p class="text-sm text-gray-700">Are you sure you want to delete PTR request "{{ ptrRequestToDelete?.ptr_no }}"?</p>
            <div class="-mx-6 -mb-6 mt-5 rounded-b-lg bg-red-600 px-6 py-4 flex items-center justify-end space-x-2 min-h-[56px]">
                <Button variant="secondary" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="closePtrDeleteModal">
                    Cancel
                </Button>
                <Button variant="danger" class="bg-white text-red-600 hover:bg-red-50 focus:ring-white h-9" @click="deletePtrRequest">
                    Delete
                </Button>
            </div>
        </Modal>
    </AppLayout>
</template>

<script setup lang="ts">
import { defineComponent, computed, ref, watch } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Modal from '@/Components/Modal.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import Button from '@/Components/Button.vue';
import type { Item } from '@/Types';

interface RequestItem {
    req_ris: number;
    ris_no: string;
    itemcode: number;
    itemname: string;
    req_qty: number;
    division?: string;
    department?: string;
    remarks?: string;
    requested_by: string;
    requestedat: string | null;
    isavailable: boolean;
}

interface PtrRequestItem {
    req_ptr: number;
    ptr_no: string;
    itemcode: number;
    itemname: string;
    req_qty: number;
    division?: string;
    target?: string;
    trans_type: string;
    trans_type_other?: string;
    remarks?: string;
    purpose?: string;
    requested_by?: string;
    department?: string;
    requestedat: string | null;
    isavailable?: boolean;
}

interface Summary {
    pending: number;
    approvedToday: number;
    totalThisMonth: number;
}

interface Props {
    requests: {
        data: RequestItem[];
        links?: any[];
        from?: number;
        to?: number;
        total?: number;
    };
    summary: Summary;
    items: Item[];
    ptrRequests: {
        data: PtrRequestItem[];
        links?: any[];
        from?: number;
        to?: number;
        total?: number;
    };
}

const props = defineProps<Props>();
const { requests, summary, ptrRequests } = props;

const showRequestModal = ref(false);
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const showViewModal = ref(false);
const showRisSubmitConfirm = ref(false);
const showRisCancelConfirm = ref(false);
const showRisItemWarning = ref(false);
const showRisQtyWarning = ref(false);
const editingRequest = ref<RequestItem | null>(null);
const requestToDelete = ref<RequestItem | null>(null);
const viewRequest = ref<RequestItem | null>(null);
const showPtrViewModal = ref(false);
const showPtrEditModal = ref(false);
const showPtrDeleteModal = ref(false);
const editingPtrRequest = ref<PtrRequestItem | null>(null);
const ptrRequestToDelete = ref<PtrRequestItem | null>(null);
const ptrViewRequest = ref<PtrRequestItem | null>(null);
const showPtrRequestModal = ref(false);
const showPtrSubmitConfirm = ref(false);
const showPtrCancelConfirm = ref(false);
const showPtrItemWarning = ref(false);
const showPtrQtyWarning = ref(false);

const itemOptions = computed(() => {
    return props.items.map(item => ({
        value: item.itemcode,
        label: item.itemname,
    }));
});

const form = useForm({
    itemcode: '',
    req_qty: '',
    division: '',
    department: '',
    remarks: '',
});

const editForm = useForm({
    itemcode: '',
    req_qty: '',
    division: '',
    department: '',
    remarks: '',
});

const ptrEditForm = useForm({
    itemcode: '',
    req_qty: '',
    division: '',
    target: '',
    trans_type: 'Donation',
    trans_type_other: '',
    remarks: '',
    purpose: '',
});

const ptrForm = useForm({
    itemcode: '',
    req_qty: '',
    division: '',
    target: '',
    trans_type: 'Donation',
    trans_type_other: '',
    remarks: '',
    purpose: '',
});

const openRequestModal = () => {
    showRequestModal.value = true;
};

const closeRequestModal = () => {
    showRequestModal.value = false;
    showRisItemWarning.value = false;
    showRisQtyWarning.value = false;
};

const submitRequest = () => {
    form.post('/requests/ris', {
        onSuccess: () => {
            showRequestModal.value = false;
            form.reset();
        },
    });
};

const hasRisFormData = computed(() => {
    return !!(
        form.itemcode ||
        form.req_qty ||
        form.division ||
        form.department ||
        form.remarks
    );
});

const openRisSubmitConfirm = () => {
    form.clearErrors();
    showRisItemWarning.value = false;
    showRisQtyWarning.value = false;
    if (!form.itemcode) {
        showRisItemWarning.value = true;
    }
    if (!form.req_qty) {
        showRisQtyWarning.value = true;
    }
    if (showRisItemWarning.value || showRisQtyWarning.value) {
        return;
    }
    showRisSubmitConfirm.value = true;
};

watch(
    () => [form.itemcode, form.req_qty],
    ([itemcode, reqQty]) => {
        if (itemcode) {
            showRisItemWarning.value = false;
        }
        if (reqQty) {
            showRisQtyWarning.value = false;
        }
    }
);

const closeRisSubmitConfirm = () => {
    showRisSubmitConfirm.value = false;
};

const confirmRisSubmit = () => {
    showRisSubmitConfirm.value = false;
    submitRequest();
};

const requestRisCancel = () => {
    if (hasRisFormData.value) {
        showRisCancelConfirm.value = true;
        return;
    }
    closeRequestModal();
};

const closeRisCancelConfirm = () => {
    showRisCancelConfirm.value = false;
};

const confirmRisCancel = () => {
    showRisCancelConfirm.value = false;
    closeRequestModal();
};

const openPtrRequestModal = () => {
    showPtrRequestModal.value = true;
};

const closePtrRequestModal = () => {
    showPtrRequestModal.value = false;
    showPtrItemWarning.value = false;
    showPtrQtyWarning.value = false;
};

const submitPtrRequest = () => {
    ptrForm.post('/requests/ptr', {
        onSuccess: () => {
            showPtrRequestModal.value = false;
            ptrForm.reset();
        },
    });
};

const hasPtrFormData = computed(() => {
    return !!(
        ptrForm.itemcode ||
        ptrForm.req_qty ||
        ptrForm.division ||
        ptrForm.target ||
        ptrForm.trans_type_other ||
        ptrForm.remarks ||
        ptrForm.purpose
    );
});

const openPtrSubmitConfirm = () => {
    ptrForm.clearErrors();
    showPtrItemWarning.value = false;
    showPtrQtyWarning.value = false;
    if (!ptrForm.itemcode) {
        showPtrItemWarning.value = true;
    }
    if (!ptrForm.req_qty) {
        showPtrQtyWarning.value = true;
    }
    if (showPtrItemWarning.value || showPtrQtyWarning.value) {
        return;
    }
    showPtrSubmitConfirm.value = true;
};

watch(
    () => [ptrForm.itemcode, ptrForm.req_qty],
    ([itemcode, reqQty]) => {
        if (itemcode) {
            showPtrItemWarning.value = false;
        }
        if (reqQty) {
            showPtrQtyWarning.value = false;
        }
    }
);

const closePtrSubmitConfirm = () => {
    showPtrSubmitConfirm.value = false;
};

const confirmPtrSubmit = () => {
    showPtrSubmitConfirm.value = false;
    submitPtrRequest();
};

const requestPtrCancel = () => {
    if (hasPtrFormData.value) {
        showPtrCancelConfirm.value = true;
        return;
    }
    closePtrRequestModal();
};

const closePtrCancelConfirm = () => {
    showPtrCancelConfirm.value = false;
};

const confirmPtrCancel = () => {
    showPtrCancelConfirm.value = false;
    closePtrRequestModal();
};

const openEditModal = (request: RequestItem) => {
    editingRequest.value = request;
    editForm.itemcode = request.itemcode;
    editForm.req_qty = String(request.req_qty ?? '');
    editForm.division = request.division || '';
    editForm.department = request.department || '';
    editForm.remarks = request.remarks || '';
    editForm.clearErrors();
    showEditModal.value = true;
};

const closeEditModal = () => {
    showEditModal.value = false;
    editingRequest.value = null;
};

const submitEdit = () => {
    if (!editingRequest.value) return;
    editForm.put(`/requests/ris/${editingRequest.value.req_ris}`, {
        onSuccess: () => {
            showEditModal.value = false;
            editingRequest.value = null;
        },
    });
};

const openViewModal = (request: RequestItem) => {
    viewRequest.value = request;
    showViewModal.value = true;
};

const closeViewModal = () => {
    showViewModal.value = false;
    viewRequest.value = null;
};

const openDeleteModal = (request: RequestItem) => {
    requestToDelete.value = request;
    showDeleteModal.value = true;
};

const closeDeleteModal = () => {
    showDeleteModal.value = false;
    requestToDelete.value = null;
};

const deleteRequest = () => {
    if (!requestToDelete.value) return;
    router.delete(`/requests/ris/${requestToDelete.value.req_ris}`, {
        onSuccess: () => {
            closeDeleteModal();
        },
    });
};

const openPtrViewModal = (request: PtrRequestItem) => {
    ptrViewRequest.value = request;
    showPtrViewModal.value = true;
};

const closePtrViewModal = () => {
    showPtrViewModal.value = false;
    ptrViewRequest.value = null;
};

const openPtrEditModal = (request: PtrRequestItem) => {
    editingPtrRequest.value = request;
    ptrEditForm.itemcode = request.itemcode;
    ptrEditForm.req_qty = String(request.req_qty ?? '');
    ptrEditForm.division = request.division || '';
    ptrEditForm.target = request.target || '';
    ptrEditForm.trans_type = request.trans_type || 'Donation';
    ptrEditForm.trans_type_other = request.trans_type_other || '';
    ptrEditForm.remarks = request.remarks || '';
    ptrEditForm.purpose = request.purpose || '';
    ptrEditForm.clearErrors();
    showPtrEditModal.value = true;
};

const closePtrEditModal = () => {
    showPtrEditModal.value = false;
    editingPtrRequest.value = null;
};

const submitPtrEdit = () => {
    if (!editingPtrRequest.value) return;
    ptrEditForm.put(`/requests/ptr/${editingPtrRequest.value.req_ptr}`, {
        onSuccess: () => {
            showPtrEditModal.value = false;
            editingPtrRequest.value = null;
        },
    });
};

const openPtrDeleteModal = (request: PtrRequestItem) => {
    ptrRequestToDelete.value = request;
    showPtrDeleteModal.value = true;
};

const closePtrDeleteModal = () => {
    showPtrDeleteModal.value = false;
    ptrRequestToDelete.value = null;
};

const deletePtrRequest = () => {
    if (!ptrRequestToDelete.value) return;
    router.delete(`/requests/ptr/${ptrRequestToDelete.value.req_ptr}`, {
        onSuccess: () => {
            closePtrDeleteModal();
        },
    });
};

const formatReqId = (id: number | string) => {
    const padded = String(id).padStart(6, '0');
    return `RISREQ-${padded}`;
};

const formatPtrId = (id: number | string) => {
    const padded = String(id).padStart(6, '0');
    return `PTRREQ-${padded}`;
};

const ptrTypeOptions = [
    { value: 'Donation', label: 'Donation' },
    { value: 'Reassignment', label: 'Reassignment' },
    { value: 'Relocate', label: 'Relocate' },
    { value: 'Others', label: 'Others' },
];

const formatDate = (value: string | null) => {
    if (!value) return '—';
    return new Date(value).toLocaleDateString();
};

const statusLabel = (isAvailable: boolean) => (isAvailable ? 'Approved' : 'Pending');

const statusClass = (isAvailable: boolean) => {
    return isAvailable ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800';
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
