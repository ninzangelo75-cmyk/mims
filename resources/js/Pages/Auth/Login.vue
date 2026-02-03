<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center px-4">
        <div class="bg-[#f6fbf6] rounded-2xl shadow-xl ring-1 ring-[#cfe8d1] w-full max-w-md p-8">
            <div class="flex items-center justify-center mb-8">
                <div class="bg-[#2e7d32] p-3 rounded-xl">
                    <!-- Pill icon (inline SVG, lucide-style) -->
                    <svg class="w-10 h-10 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 13.5 13.5 10.5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 17a5 5 0 0 1 0-7l3-3a5 5 0 0 1 7 7l-3 3a5 5 0 0 1-7 0Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.5 8.5 15.5 15.5" />
                    </svg>
                </div>
            </div>

            <h2 class="text-3xl font-bold text-center text-gray-900 mb-2">
                Welcome Back
            </h2>
            <p class="text-center text-gray-600 mb-8">
                Sign in to access your inventory system
            </p>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Username
                    </label>
                    <div class="relative">
                        <!-- Mail icon -->
                        <svg
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 4h16v16H4z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="m22 6-10 7L2 6" />
                        </svg>
                        <input
                            id="username"
                            v-model="form.username"
                            type="text"
                            autocomplete="username"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                            placeholder="Enter your username"
                            required
                        />
                    </div>
                    <p v-if="errors.username" class="mt-2 text-sm text-red-600">
                        {{ errors.username }}
                    </p>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                        Password
                    </label>
                    <div class="relative">
                        <!-- Lock icon -->
                        <svg
                            class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                        <input
                            id="password"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#2e7d32]"
                            placeholder="Enter your password"
                            required
                        />
                    </div>
                    <p v-if="errors.password" class="mt-2 text-sm text-red-600">
                        {{ errors.password }}
                    </p>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input
                            id="remember-me"
                            v-model="form.remember"
                            type="checkbox"
                            class="w-4 h-4 text-indigo-600 border-gray-300 rounded"
                        />
                        <span class="ml-2 page-subtitle">Remember me</span>
                    </label>
                    <a href="#" class="text-sm text-indigo-600 hover:text-indigo-700">
                        Forgot password?
                    </a>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-[#2e7d32] text-white py-3 rounded-lg font-semibold hover:bg-[#256628] transition-colors disabled:opacity-50"
                >
                    <span v-if="form.processing">Signing in...</span>
                    <span v-else>Sign In</span>
                </button>
            </form>

            <div class="mt-6 text-center">
                <p class="page-subtitle">
                    Demo credentials: use your username and password
                </p>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    username: '',
    password: '',
    remember: false,
});

const props = defineProps<{
    errors?: Record<string, string>;
}>();

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>




