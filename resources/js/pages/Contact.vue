<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import PublicNav from '@/components/PublicNav.vue';
import { Mail, Phone, MapPin, GraduationCap, Briefcase, Award, BookOpen } from 'lucide-vue-next';
</script>

<template>
    <Head title="Contact Us - お問い合わせ | Nantosha Import & Export Division" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <div class="py-12">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    Contact Us
                </h1>
                <p class="text-2xl text-gray-600">お問い合わせ</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Dr. Allan Landrito Profile -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        Profile
                    </h2>
                    
                    <div class="bg-white border border-gray-200 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">
                            Dr. Allan Landrito
                        </h3>
                        
                        <div class="space-y-6">
                            <!-- Education -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <GraduationCap class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Education</h4>
                                    <p class="text-sm text-gray-600">
                                        Graduated from De La Salle University College of Medicine.
                                    </p>
                                </div>
                            </div>

                            <!-- Career -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <Briefcase class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Career</h4>
                                    <p class="text-sm text-gray-600">
                                        Former Health Officer for Muntinlupa City. Currently Director of Allan Landrito Molecular Nutrition Clinic.
                                    </p>
                                </div>
                            </div>

                            <!-- Awards -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <Award class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Awards</h4>
                                    <ul class="text-sm text-gray-600 space-y-1 list-disc list-inside">
                                        <li>2021 Outstanding Citizen & Humanitarian Award</li>
                                        <li>2023 Modern Hero of Asia Award</li>
                                        <li>2024 Outstanding Public Health Promoter Award</li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Books -->
                            <div class="flex items-start gap-4">
                                <div class="flex-shrink-0 w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                    <BookOpen class="w-5 h-5 text-gray-600" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-1">Books</h4>
                                    <p class="text-sm text-gray-600">
                                        "COVID-19 Now: Prevention and Immediate Treatment" (2021).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        Get in Touch
                    </h2>
                    <p class="text-gray-600 mb-8">
                        Have questions about our Ivermectin capsules or need assistance with your order? 
                        We're here to help. Please contact us using the information below.
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <Mail class="w-6 h-6 text-gray-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Email</h3>
                                <p class="text-gray-600">info@nantosha.com</p>
                                <p class="text-sm text-gray-500 mt-1">We typically respond within 24 hours</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <Phone class="w-6 h-6 text-gray-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Phone</h3>
                                <p class="text-gray-600">+81-XX-XXXX-XXXX</p>
                                <p class="text-sm text-gray-500 mt-1">Business hours: Mon-Fri 9:00-18:00 JST</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <MapPin class="w-6 h-6 text-gray-600" />
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-1">Address</h3>
                                <p class="text-gray-600">
                                    Nantosha Import & Export Division<br>
                                    南東舎輸出入部<br>
                                    Tokyo, Japan
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded">
                        <h3 class="font-semibold text-gray-900 mb-2">
                            For Medical Professionals
                        </h3>
                        <p class="text-sm text-gray-700">
                            Doctors requiring "Yakkan Shoumei" (Medicine Import Confirmation) for patient prescriptions 
                            should contact us directly for special arrangements.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>
</template>

