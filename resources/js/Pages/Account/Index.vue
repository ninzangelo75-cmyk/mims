<template>
    <AppLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="page-title">User Management</h2>
                    <p class="page-subtitle">Create and manage system users</p>
                </div>
            </div>
        </template>

        <div class="space-y-6">
            <!-- Create user form -->
            <div class="rounded-2xl border border-[#cfe8d1] bg-[#f6fbf6] p-6 shadow-sm">
                <h3 class="mb-4 text-base font-semibold text-gray-900">Add New User</h3>
                <form class="grid gap-4 md:grid-cols-2" @submit.prevent="submit">
                    <Input
                        id="fullname"
                        v-model="form.fullname"
                        label="Full name"
                        required
                        :error="form.errors.fullname"
                    />
                    <Input
                        id="username"
                        v-model="form.username"
                        label="Username"
                        required
                        :error="form.errors.username"
                    />
                    <Input
                        id="password"
                        v-model="form.password"
                        label="Password"
                        type="password"
                        required
                        :error="form.errors.password"
                    />
                    <Select
                        id="role"
                        v-model="form.role"
                        label="Role"
                        :options="roleOptions"
                        placeholder="Select role"
                        required
                        :error="form.errors.role"
                    />
                    <Input
                        id="department"
                        v-model="form.department"
                        label="Department"
                        :error="form.errors.department"
                    />
                    <Input
                        id="designation"
                        v-model="form.designation"
                        label="Designation"
                        :error="form.errors.designation"
                    />
                    <Input
                        id="emailadd"
                        v-model="form.emailadd"
                        label="Email"
                        type="email"
                        :error="form.errors.emailadd"
                    />
                    <Input
                        id="contact"
                        v-model="form.contact"
                        label="Contact"
                        :error="form.errors.contact"
                    />

                    <div class="md:col-span-2 mt-2 flex justify-end">
                        <Button type="submit" :disabled="form.processing">
                            Create User
                        </Button>
                    </div>
                </form>
            </div>

            <!-- Users table -->
            <div class="overflow-hidden rounded-2xl border border-[#cfe8d1] bg-[#f6fbf6] shadow-sm">
                <div class="border-b border-gray-200 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Existing Users</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Username
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Department
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Contact
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    Created
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-[#f6fbf6]">
                            <tr v-if="!users.data.length">
                                <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                            <tr v-for="user in users.data" :key="user.useid" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ user.fullname }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    {{ user.username }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm">
                                    <span
                                        :class="[
                                            'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold',
                                            roleBadgeClass(user.role)
                                        ]"
                                    >
                                        {{ roleLabel(user.role) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    {{ user.department || '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-900">
                                    {{ user.contact || user.emailadd || '—' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-500">
                                    {{ user.created_at || '—' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div
                    v-if="users.links"
                    class="flex flex-col gap-3 border-t border-gray-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div class="text-sm text-gray-700">
                        Showing {{ users.from }} to {{ users.to }} of {{ users.total }} results
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <Link
                            v-for="link in users.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'px-3 py-2 text-sm border rounded transition',
                                link.active
                                    ? 'bg-indigo-600 text-white border-indigo-600'
                                    : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50',
                                !link.url ? 'opacity-50 cursor-not-allowed' : ''
                            ]"
                            v-html="link.label"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import Button from '@/Components/Button.vue';
import type { User } from '@/Types';

interface UsersResponse {
    data: User[];
    links?: any[];
    from?: number;
    to?: number;
    total?: number;
}

interface Props {
    users: UsersResponse;
    roles: string[];
}

const props = defineProps<Props>();
const page = usePage();
const currentUser = computed(() => page.props.auth.user as any);

const users = props.users;

const form = useForm({
    fullname: '',
    username: '',
    password: '',
    role: '',
    department: '',
    designation: '',
    emailadd: '',
    contact: '',
});

const roleOptions = computed(() =>
    props.roles.map((r) => ({
        value: r,
        label: r === 'ADMIN' ? 'Administrator' : r === 'STAFF' ? 'Staff' : 'Encoder',
    })),
);

const submit = () => {
    form.post('/account', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('fullname', 'username', 'password', 'department', 'designation', 'emailadd', 'contact');
        },
    });
};

const roleLabel = (role: string) => {
    if (role === 'ADMIN') return 'Administrator';
    if (role === 'STAFF') return 'Staff';
    if (role === 'USER') return 'Encoder';
    return role;
};

const roleBadgeClass = (role: string) => {
    if (role === 'ADMIN') return 'bg-purple-100 text-purple-800';
    if (role === 'STAFF') return 'bg-blue-100 text-blue-800';
    return 'bg-green-100 text-green-800';
};
</script>




