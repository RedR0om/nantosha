<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicNav from '@/components/PublicNav.vue';
import { CheckCircle, Package, FileText, CreditCard, Truck } from 'lucide-vue-next';
</script>

<template>
    <Head title="How to Order - 購入までの流れ | Nantosha Import & Export Division" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <div class="py-12">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    How to Order
                </h1>
                <p class="text-2xl text-gray-600">購入までの流れ</p>
            </div>

            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 mb-8 rounded">
                <p class="text-gray-700">
                    <strong>Support Policy:</strong> We handle only the sourcing and export procedures. 
                    Personal import is your responsibility, and we provide support throughout the process.
                </p>
            </div>

            <div class="space-y-8">
                <!-- Step 1 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">1</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <Package class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">Select Quantity</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Choose the quantity of Ivermectin capsules you need. We offer packages from 50 to 1,000 capsules with volume discounts.
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">
                                <strong>Available quantities:</strong> 50, 100, 200, 300, 500, or 1,000 capsules
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">2</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <FileText class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">Enter Shipping Info & Health Check Sheet</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Provide your shipping address and complete the Health Check Sheet. This information is required for personal import procedures and to ensure safe usage.
                        </p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded">
                            <p class="text-sm text-gray-700">
                                <strong>Important:</strong> The Health Check Sheet helps the manufacturing doctor provide minimal advice based on your health information.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">3</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <CreditCard class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">Bank Transfer</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Complete payment via bank transfer. You will receive payment instructions after confirming your order.
                        </p>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">
                                Payment must be completed before order processing begins.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-green-600">4</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <Truck class="w-6 h-6 text-green-600" />
                            <h2 class="text-2xl font-bold text-gray-900">Order Processing & Shipping</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Once payment is confirmed, we process your order and ship it from the Philippines. Delivery typically takes 3–10 days.
                        </p>
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
                            <p class="text-sm text-gray-700">
                                <strong>Re-delivery Guarantee:</strong> Free reshipment if the package is lost during transit.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Step 5 -->
                <div class="flex gap-6">
                    <div class="flex-shrink-0">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <CheckCircle class="w-8 h-8 text-green-600" />
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-3">
                            <h2 class="text-2xl font-bold text-gray-900">Receive Your Order</h2>
                        </div>
                        <p class="text-gray-600 mb-4">
                            Your order will be delivered to your specified address. Please ensure someone is available to receive the package.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Important Notes -->
            <div class="mt-12 bg-red-50 border-l-4 border-red-500 p-6 rounded">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Important Notes</h3>
                <ul class="space-y-2 text-gray-700">
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span><strong>Risks:</strong> Potential for wrong medication, incorrect dosage without a doctor, and customs delays.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span><strong>Prohibitions:</strong> Resale or transfer to third parties is strictly prohibited (except for doctors with proper Yakkan Shoumei permits).</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-red-500">•</span>
                        <span><strong>Doctors:</strong> Doctors purchasing for patient prescriptions require "Yakkan Shoumei" (Medicine Import Confirmation).</span>
                    </li>
                </ul>
            </div>
        </div>
        </div>
    </div>
</template>

