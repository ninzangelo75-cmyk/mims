<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
        <!-- Header -->
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-[#2e7d32] rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <h1 class="text-xl font-bold text-gray-900">SKPH - MIMS</h1>
                    </div>
                    <Link
                        href="/login"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-[#2e7d32] hover:bg-[#256628] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#2e7d32] transition-colors"
                    >
                        Sign In
                    </Link>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-16">
                <h2 class="text-4xl sm:text-5xl font-extrabold text-gray-900 mb-4">
                    Welcome to Medicine Inventory
                    <span class="text-indigo-600">Management System</span>
                </h2>
                <p class="text-xl text-gray-600 max-w-3xl mx-auto">
                    A comprehensive solution for managing medicine inventory with real-time tracking, 
                    batch management, and compliance monitoring for healthcare facilities.
                </p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
                <!-- Feature 1 -->
                <div class="bg-[#f6fbf6] rounded-lg shadow-md ring-1 ring-[#cfe8d1] p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Real-Time Inventory</h3>
                    <p class="text-gray-600">
                        Track medicine stock levels in real-time with accurate calculations derived from movement logs.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-[#f6fbf6] rounded-lg shadow-md ring-1 ring-[#cfe8d1] p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Batch Tracking</h3>
                    <p class="text-gray-600">
                        Monitor batch numbers and expiry dates with FEFO (First Expired First Out) compliance.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-[#f6fbf6] rounded-lg shadow-md ring-1 ring-[#cfe8d1] p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Secure & Auditable</h3>
                    <p class="text-gray-600">
                        Complete audit trail with role-based access control for compliance and security.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-[#f6fbf6] rounded-lg shadow-md ring-1 ring-[#cfe8d1] p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Request Management</h3>
                    <p class="text-gray-600">
                        Streamlined RIS and PTR request workflows with approval and release tracking.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-[#f6fbf6] rounded-lg shadow-md ring-1 ring-[#cfe8d1] p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Expiry Alerts</h3>
                    <p class="text-gray-600">
                        Automated notifications for expiring medicines and low stock levels.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-[#f6fbf6] rounded-lg shadow-md ring-1 ring-[#cfe8d1] p-6 hover:shadow-lg transition-shadow">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Analytics & Reports</h3>
                    <p class="text-gray-600">
                        Comprehensive reporting for stock movements, usage patterns, and compliance metrics.
                    </p>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="bg-[#2e7d32] rounded-2xl shadow-xl p-12 text-center">
                <h3 class="text-3xl font-bold text-white mb-4">
                    Ready to Get Started?
                </h3>
                <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    Sign in to access your medicine inventory management dashboard and start managing your inventory efficiently.
                </p>
                <Link
                    href="/login"
                    class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-colors shadow-lg"
                >
                    Sign In to Your Account
                    <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </Link>
            </div>

            <!-- Footer -->
            <footer class="mt-16 text-center text-gray-600">
                <p class="text-sm">
                    © {{ new Date().getFullYear() }} Medicine Inventory Management System. 
                    Built for healthcare facilities and government compliance.
                </p>
            </footer>
        </main>
    </div>
</template>

<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>




