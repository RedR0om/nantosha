<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { useLanguage } from '@/composables/useLanguage';
import { translateText } from '@/composables/useTranslation';
import { Mail, Send, CheckCircle } from 'lucide-vue-next';

const { language } = useLanguage();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const submitted = ref(false);

const submit = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            submitted.value = true;
            form.reset();
            setTimeout(() => {
                submitted.value = false;
            }, 5000);
        },
    });
};

const texts = ref({
    title: 'Contact Us',
    subtitle: 'お問い合わせ',
    inquiryForm: 'Inquiry Form',
    inquiryFormDesc: 'Please fill out the form below and we will get back to you as soon as possible.',
    name: 'Name',
    namePlaceholder: 'Your full name',
    email: 'Email',
    emailPlaceholder: 'your.email@example.com',
    phone: 'Phone',
    phonePlaceholder: 'Your phone number (optional)',
    subject: 'Subject',
    subjectPlaceholder: 'What is your inquiry about?',
    message: 'Message',
    messagePlaceholder: 'Please provide details about your inquiry...',
    submit: 'Send Inquiry',
    submitting: 'Sending...',
    success: 'Thank you! Your inquiry has been submitted successfully. We will get back to you soon.',
    required: 'This field is required',
    invalidEmail: 'Please enter a valid email address',
});

const translated = ref<Record<string, string>>({});

const translateAll = async () => {
    const keys = Object.keys(texts.value) as Array<keyof typeof texts.value>;
    for (const key of keys) {
        try {
            translated.value[key] = await translateText(texts.value[key], language.value, 'auto');
        } catch (error) {
            translated.value[key] = texts.value[key];
        }
    }
};

watch(language, translateAll, { immediate: true });
onMounted(translateAll);
</script>

<template>
    <Head title="Contact Us - お問い合わせ | Nantosha Import & Export Division" />
    
    <div class="min-h-screen bg-white">
        <PublicNav />
        <div class="py-12">
            <div class="container mx-auto px-4 max-w-4xl">
                <div class="text-center mb-12">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">
                        {{ translated.title || texts.title }}
                    </h1>
                    <p class="text-2xl text-gray-600">{{ translated.subtitle || texts.subtitle }}</p>
                </div>

                <!-- Success Message -->
                <div
                    v-if="submitted"
                    class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4 flex items-center gap-3"
                >
                    <CheckCircle class="w-5 h-5 text-green-600 flex-shrink-0" />
                    <p class="text-green-800">
                        {{ translated.success || texts.success }}
                    </p>
                </div>

                <!-- Inquiry Form -->
                <div class="bg-white border border-gray-200 rounded-lg p-8">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <Mail class="w-6 h-6 text-blue-600" />
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                {{ translated.inquiryForm || texts.inquiryForm }}
                            </h2>
                            <p class="text-sm text-gray-600 mt-1">
                                {{ translated.inquiryFormDesc || texts.inquiryFormDesc }}
                            </p>
                        </div>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ translated.name || texts.name }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                :placeholder="translated.namePlaceholder || texts.namePlaceholder"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.name }"
                            />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                                {{ form.errors.name }}
                            </p>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ translated.email || texts.email }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                :placeholder="translated.emailPlaceholder || texts.emailPlaceholder"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.email }"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ translated.phone || texts.phone }}
                            </label>
                            <input
                                id="phone"
                                v-model="form.phone"
                                type="tel"
                                :placeholder="translated.phonePlaceholder || texts.phonePlaceholder"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.phone }"
                            />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                                {{ form.errors.phone }}
                            </p>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ translated.subject || texts.subject }}
                                <span class="text-red-500">*</span>
                            </label>
                            <input
                                id="subject"
                                v-model="form.subject"
                                type="text"
                                required
                                :placeholder="translated.subjectPlaceholder || texts.subjectPlaceholder"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent"
                                :class="{ 'border-red-500': form.errors.subject }"
                            />
                            <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                                {{ form.errors.subject }}
                            </p>
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ translated.message || texts.message }}
                                <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                id="message"
                                v-model="form.message"
                                required
                                rows="6"
                                :placeholder="translated.messagePlaceholder || texts.messagePlaceholder"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent resize-none"
                                :class="{ 'border-red-500': form.errors.message }"
                            ></textarea>
                            <p v-if="form.errors.message" class="mt-1 text-sm text-red-600">
                                {{ form.errors.message }}
                            </p>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex items-center gap-2 px-6 py-3 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium"
                            >
                                <Send v-if="!form.processing" class="w-5 h-5" />
                                <span v-if="form.processing">{{ translated.submitting || texts.submitting }}</span>
                                <span v-else>{{ translated.submit || texts.submit }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
