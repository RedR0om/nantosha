<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import PublicNav from '@/components/PublicNav.vue';
import { AlertTriangle, FileText, CheckCircle, ShoppingCart } from 'lucide-vue-next';

const props = defineProps<{
    cartItems: any[];
    subtotal: number;
    tax: number;
    shipping: number;
    total: number;
}>();

const form = ref({
    email: '',
    first_name: '',
    last_name: '',
    phone: '',
    address_line_1: '',
    address_line_2: '',
    city: '',
    state: '',
    postal_code: '',
    country: 'JP',
    payment_method: 'bank_transfer',
    
    // Health Check Sheet
    health_check: {
        age: '',
        weight: '',
        height: '',
        current_medications: '',
        allergies: '',
        medical_conditions: '',
        previous_ivermectin_use: false,
        pregnancy_status: '',
        reason_for_use: '',
    },
    
    // Risk Acknowledgment
    risk_acknowledged: false,
    resale_prohibited: false,
});

const currentStep = ref(1);
const totalSteps = 5;

const countries = [
    { code: 'AF', name: 'Afghanistan' },
    { code: 'AX', name: 'Åland Islands' },
    { code: 'AL', name: 'Albania' },
    { code: 'DZ', name: 'Algeria' },
    { code: 'AS', name: 'American Samoa' },
    { code: 'AD', name: 'Andorra' },
    { code: 'AO', name: 'Angola' },
    { code: 'AI', name: 'Anguilla' },
    { code: 'AQ', name: 'Antarctica' },
    { code: 'AG', name: 'Antigua and Barbuda' },
    { code: 'AR', name: 'Argentina' },
    { code: 'AM', name: 'Armenia' },
    { code: 'AW', name: 'Aruba' },
    { code: 'AU', name: 'Australia' },
    { code: 'AT', name: 'Austria' },
    { code: 'AZ', name: 'Azerbaijan' },
    { code: 'BS', name: 'Bahamas' },
    { code: 'BH', name: 'Bahrain' },
    { code: 'BD', name: 'Bangladesh' },
    { code: 'BB', name: 'Barbados' },
    { code: 'BY', name: 'Belarus' },
    { code: 'BE', name: 'Belgium' },
    { code: 'BZ', name: 'Belize' },
    { code: 'BJ', name: 'Benin' },
    { code: 'BM', name: 'Bermuda' },
    { code: 'BT', name: 'Bhutan' },
    { code: 'BO', name: 'Bolivia' },
    { code: 'BQ', name: 'Bonaire, Sint Eustatius and Saba' },
    { code: 'BA', name: 'Bosnia and Herzegovina' },
    { code: 'BW', name: 'Botswana' },
    { code: 'BV', name: 'Bouvet Island' },
    { code: 'BR', name: 'Brazil' },
    { code: 'IO', name: 'British Indian Ocean Territory' },
    { code: 'BN', name: 'Brunei Darussalam' },
    { code: 'BG', name: 'Bulgaria' },
    { code: 'BF', name: 'Burkina Faso' },
    { code: 'BI', name: 'Burundi' },
    { code: 'CV', name: 'Cabo Verde' },
    { code: 'KH', name: 'Cambodia' },
    { code: 'CM', name: 'Cameroon' },
    { code: 'CA', name: 'Canada' },
    { code: 'KY', name: 'Cayman Islands' },
    { code: 'CF', name: 'Central African Republic' },
    { code: 'TD', name: 'Chad' },
    { code: 'CL', name: 'Chile' },
    { code: 'CN', name: 'China' },
    { code: 'CX', name: 'Christmas Island' },
    { code: 'CC', name: 'Cocos (Keeling) Islands' },
    { code: 'CO', name: 'Colombia' },
    { code: 'KM', name: 'Comoros' },
    { code: 'CG', name: 'Congo' },
    { code: 'CD', name: 'Congo, Democratic Republic of the' },
    { code: 'CK', name: 'Cook Islands' },
    { code: 'CR', name: 'Costa Rica' },
    { code: 'CI', name: 'Côte d\'Ivoire' },
    { code: 'HR', name: 'Croatia' },
    { code: 'CU', name: 'Cuba' },
    { code: 'CW', name: 'Curaçao' },
    { code: 'CY', name: 'Cyprus' },
    { code: 'CZ', name: 'Czechia' },
    { code: 'DK', name: 'Denmark' },
    { code: 'DJ', name: 'Djibouti' },
    { code: 'DM', name: 'Dominica' },
    { code: 'DO', name: 'Dominican Republic' },
    { code: 'EC', name: 'Ecuador' },
    { code: 'EG', name: 'Egypt' },
    { code: 'SV', name: 'El Salvador' },
    { code: 'GQ', name: 'Equatorial Guinea' },
    { code: 'ER', name: 'Eritrea' },
    { code: 'EE', name: 'Estonia' },
    { code: 'SZ', name: 'Eswatini' },
    { code: 'ET', name: 'Ethiopia' },
    { code: 'FK', name: 'Falkland Islands (Malvinas)' },
    { code: 'FO', name: 'Faroe Islands' },
    { code: 'FJ', name: 'Fiji' },
    { code: 'FI', name: 'Finland' },
    { code: 'FR', name: 'France' },
    { code: 'GF', name: 'French Guiana' },
    { code: 'PF', name: 'French Polynesia' },
    { code: 'TF', name: 'French Southern Territories' },
    { code: 'GA', name: 'Gabon' },
    { code: 'GM', name: 'Gambia' },
    { code: 'GE', name: 'Georgia' },
    { code: 'DE', name: 'Germany' },
    { code: 'GH', name: 'Ghana' },
    { code: 'GI', name: 'Gibraltar' },
    { code: 'GR', name: 'Greece' },
    { code: 'GL', name: 'Greenland' },
    { code: 'GD', name: 'Grenada' },
    { code: 'GP', name: 'Guadeloupe' },
    { code: 'GU', name: 'Guam' },
    { code: 'GT', name: 'Guatemala' },
    { code: 'GG', name: 'Guernsey' },
    { code: 'GN', name: 'Guinea' },
    { code: 'GW', name: 'Guinea-Bissau' },
    { code: 'GY', name: 'Guyana' },
    { code: 'HT', name: 'Haiti' },
    { code: 'HM', name: 'Heard Island and McDonald Islands' },
    { code: 'VA', name: 'Holy See' },
    { code: 'HN', name: 'Honduras' },
    { code: 'HK', name: 'Hong Kong' },
    { code: 'HU', name: 'Hungary' },
    { code: 'IS', name: 'Iceland' },
    { code: 'IN', name: 'India' },
    { code: 'ID', name: 'Indonesia' },
    { code: 'IR', name: 'Iran, Islamic Republic of' },
    { code: 'IQ', name: 'Iraq' },
    { code: 'IE', name: 'Ireland' },
    { code: 'IM', name: 'Isle of Man' },
    { code: 'IL', name: 'Israel' },
    { code: 'IT', name: 'Italy' },
    { code: 'JM', name: 'Jamaica' },
    { code: 'JP', name: 'Japan' },
    { code: 'JE', name: 'Jersey' },
    { code: 'JO', name: 'Jordan' },
    { code: 'KZ', name: 'Kazakhstan' },
    { code: 'KE', name: 'Kenya' },
    { code: 'KI', name: 'Kiribati' },
    { code: 'KP', name: 'Korea, Democratic People\'s Republic of' },
    { code: 'KR', name: 'Korea, Republic of' },
    { code: 'KW', name: 'Kuwait' },
    { code: 'KG', name: 'Kyrgyzstan' },
    { code: 'LA', name: 'Lao People\'s Democratic Republic' },
    { code: 'LV', name: 'Latvia' },
    { code: 'LB', name: 'Lebanon' },
    { code: 'LS', name: 'Lesotho' },
    { code: 'LR', name: 'Liberia' },
    { code: 'LY', name: 'Libya' },
    { code: 'LI', name: 'Liechtenstein' },
    { code: 'LT', name: 'Lithuania' },
    { code: 'LU', name: 'Luxembourg' },
    { code: 'MO', name: 'Macao' },
    { code: 'MG', name: 'Madagascar' },
    { code: 'MW', name: 'Malawi' },
    { code: 'MY', name: 'Malaysia' },
    { code: 'MV', name: 'Maldives' },
    { code: 'ML', name: 'Mali' },
    { code: 'MT', name: 'Malta' },
    { code: 'MH', name: 'Marshall Islands' },
    { code: 'MQ', name: 'Martinique' },
    { code: 'MR', name: 'Mauritania' },
    { code: 'MU', name: 'Mauritius' },
    { code: 'YT', name: 'Mayotte' },
    { code: 'MX', name: 'Mexico' },
    { code: 'FM', name: 'Micronesia, Federated States of' },
    { code: 'MD', name: 'Moldova, Republic of' },
    { code: 'MC', name: 'Monaco' },
    { code: 'MN', name: 'Mongolia' },
    { code: 'ME', name: 'Montenegro' },
    { code: 'MS', name: 'Montserrat' },
    { code: 'MA', name: 'Morocco' },
    { code: 'MZ', name: 'Mozambique' },
    { code: 'MM', name: 'Myanmar' },
    { code: 'NA', name: 'Namibia' },
    { code: 'NR', name: 'Nauru' },
    { code: 'NP', name: 'Nepal' },
    { code: 'NL', name: 'Netherlands' },
    { code: 'NC', name: 'New Caledonia' },
    { code: 'NZ', name: 'New Zealand' },
    { code: 'NI', name: 'Nicaragua' },
    { code: 'NE', name: 'Niger' },
    { code: 'NG', name: 'Nigeria' },
    { code: 'NU', name: 'Niue' },
    { code: 'NF', name: 'Norfolk Island' },
    { code: 'MK', name: 'North Macedonia' },
    { code: 'MP', name: 'Northern Mariana Islands' },
    { code: 'NO', name: 'Norway' },
    { code: 'OM', name: 'Oman' },
    { code: 'PK', name: 'Pakistan' },
    { code: 'PW', name: 'Palau' },
    { code: 'PS', name: 'Palestine, State of' },
    { code: 'PA', name: 'Panama' },
    { code: 'PG', name: 'Papua New Guinea' },
    { code: 'PY', name: 'Paraguay' },
    { code: 'PE', name: 'Peru' },
    { code: 'PH', name: 'Philippines' },
    { code: 'PN', name: 'Pitcairn' },
    { code: 'PL', name: 'Poland' },
    { code: 'PT', name: 'Portugal' },
    { code: 'PR', name: 'Puerto Rico' },
    { code: 'QA', name: 'Qatar' },
    { code: 'RE', name: 'Réunion' },
    { code: 'RO', name: 'Romania' },
    { code: 'RU', name: 'Russian Federation' },
    { code: 'RW', name: 'Rwanda' },
    { code: 'BL', name: 'Saint Barthélemy' },
    { code: 'SH', name: 'Saint Helena, Ascension and Tristan da Cunha' },
    { code: 'KN', name: 'Saint Kitts and Nevis' },
    { code: 'LC', name: 'Saint Lucia' },
    { code: 'MF', name: 'Saint Martin (French part)' },
    { code: 'PM', name: 'Saint Pierre and Miquelon' },
    { code: 'VC', name: 'Saint Vincent and the Grenadines' },
    { code: 'WS', name: 'Samoa' },
    { code: 'SM', name: 'San Marino' },
    { code: 'ST', name: 'Sao Tome and Principe' },
    { code: 'SA', name: 'Saudi Arabia' },
    { code: 'SN', name: 'Senegal' },
    { code: 'RS', name: 'Serbia' },
    { code: 'SC', name: 'Seychelles' },
    { code: 'SL', name: 'Sierra Leone' },
    { code: 'SG', name: 'Singapore' },
    { code: 'SX', name: 'Sint Maarten (Dutch part)' },
    { code: 'SK', name: 'Slovakia' },
    { code: 'SI', name: 'Slovenia' },
    { code: 'SB', name: 'Solomon Islands' },
    { code: 'SO', name: 'Somalia' },
    { code: 'ZA', name: 'South Africa' },
    { code: 'GS', name: 'South Georgia and the South Sandwich Islands' },
    { code: 'SS', name: 'South Sudan' },
    { code: 'ES', name: 'Spain' },
    { code: 'LK', name: 'Sri Lanka' },
    { code: 'SD', name: 'Sudan' },
    { code: 'SR', name: 'Suriname' },
    { code: 'SJ', name: 'Svalbard and Jan Mayen' },
    { code: 'SE', name: 'Sweden' },
    { code: 'CH', name: 'Switzerland' },
    { code: 'SY', name: 'Syrian Arab Republic' },
    { code: 'TW', name: 'Taiwan, Province of China' },
    { code: 'TJ', name: 'Tajikistan' },
    { code: 'TZ', name: 'Tanzania, United Republic of' },
    { code: 'TH', name: 'Thailand' },
    { code: 'TL', name: 'Timor-Leste' },
    { code: 'TG', name: 'Togo' },
    { code: 'TK', name: 'Tokelau' },
    { code: 'TO', name: 'Tonga' },
    { code: 'TT', name: 'Trinidad and Tobago' },
    { code: 'TN', name: 'Tunisia' },
    { code: 'TR', name: 'Turkey' },
    { code: 'TM', name: 'Turkmenistan' },
    { code: 'TC', name: 'Turks and Caicos Islands' },
    { code: 'TV', name: 'Tuvalu' },
    { code: 'UG', name: 'Uganda' },
    { code: 'UA', name: 'Ukraine' },
    { code: 'AE', name: 'United Arab Emirates' },
    { code: 'GB', name: 'United Kingdom' },
    { code: 'US', name: 'United States' },
    { code: 'UM', name: 'United States Minor Outlying Islands' },
    { code: 'UY', name: 'Uruguay' },
    { code: 'UZ', name: 'Uzbekistan' },
    { code: 'VU', name: 'Vanuatu' },
    { code: 'VE', name: 'Venezuela, Bolivarian Republic of' },
    { code: 'VN', name: 'Viet Nam' },
    { code: 'VG', name: 'Virgin Islands, British' },
    { code: 'VI', name: 'Virgin Islands, U.S.' },
    { code: 'WF', name: 'Wallis and Futuna' },
    { code: 'EH', name: 'Western Sahara' },
    { code: 'YE', name: 'Yemen' },
    { code: 'ZM', name: 'Zambia' },
    { code: 'ZW', name: 'Zimbabwe' },
];

const formatPrice = (price: number) => {
    return new Intl.NumberFormat('ja-JP', {
        style: 'currency',
        currency: 'JPY',
        minimumFractionDigits: 0,
    }).format(price);
};

const nextStep = () => {
    if (currentStep.value < totalSteps) {
        currentStep.value++;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const submit = () => {
    if (!form.value.risk_acknowledged || !form.value.resale_prohibited) {
        alert('Please acknowledge all risks and prohibitions before proceeding.');
        return;
    }
    
    if (!form.value.payment_method) {
        alert('Please select a payment method.');
        return;
    }
    
    // Ensure checkboxes are sent as accepted values
    const submitData = {
        ...form.value,
        risk_acknowledged: form.value.risk_acknowledged ? '1' : '0',
        resale_prohibited: form.value.resale_prohibited ? '1' : '0',
    };
    
    router.post('/orders', submitData, {
        onSuccess: () => {
            // Success - will redirect to order page
        },
        onError: (errors) => {
            console.error('Order submission errors:', errors);
            if (errors.risk_acknowledged || errors.resale_prohibited) {
                alert('Please acknowledge all risks and prohibitions.');
            }
        },
    });
};
</script>

<template>
    <Head title="Checkout - Nantosha Import & Export Division" />
    
    <div class="min-h-screen bg-gray-50">
        <PublicNav />
        <div class="py-8">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
            
            <!-- Progress Steps -->
            <div class="mb-8">
                <div class="flex items-start justify-between">
                    <div
                        v-for="step in totalSteps"
                        :key="step"
                        class="flex flex-col items-center flex-1"
                    >
                        <div class="flex items-center w-full">
                            <div
                                class="w-10 h-10 rounded-full flex items-center justify-center font-semibold transition-colors flex-shrink-0"
                                :class="currentStep >= step 
                                    ? 'bg-gray-900 text-white' 
                                    : 'bg-gray-200 text-gray-600'"
                            >
                                {{ step }}
                            </div>
                            <div
                                v-if="step < totalSteps"
                                class="h-1 flex-1 mx-2 transition-colors"
                                :class="currentStep > step ? 'bg-gray-900' : 'bg-gray-200'"
                            ></div>
                        </div>
                        <div class="mt-2 text-xs text-center text-gray-600">
                            <span v-if="step === 1">Confirm Amount</span>
                            <span v-else-if="step === 2">Customer Info</span>
                            <span v-else-if="step === 3">Shipping</span>
                            <span v-else-if="step === 4">Risks</span>
                            <span v-else-if="step === 5">Review</span>
                        </div>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <!-- Step 1: Confirm Amount -->
                <div
                    v-if="currentStep === 1"
                    class="bg-white rounded-lg shadow-md p-6 mb-6"
                >
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <ShoppingCart class="w-6 h-6" />
                        Confirm Amount
                    </h2>
                    <p class="text-gray-600 mb-6">
                        Please review your cart items and amounts before proceeding.
                    </p>
                    
                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6">
                        <div
                            v-for="item in cartItems"
                            :key="item.id"
                            class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg"
                        >
                            <div class="w-20 h-20 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                <img
                                    v-if="item.product?.image"
                                    :src="item.product.image"
                                    :alt="item.product.name"
                                    class="w-full h-full object-cover"
                                />
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-gray-900 mb-1">
                                    {{ item.product?.name }}
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Quantity: {{ item.quantity }} × {{ formatPrice(item.price) }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-gray-900">
                                    {{ formatPrice(item.price * item.quantity) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <h3 class="font-semibold text-gray-900 mb-4">Order Summary</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal:</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping:</span>
                                <span>{{ shipping === 0 ? 'Free' : formatPrice(shipping) }}</span>
                            </div>
                            <div class="border-t pt-2 mt-2 flex justify-between text-lg font-bold text-gray-900">
                                <span>Total:</span>
                                <span>{{ formatPrice(total) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                        <p class="text-sm text-gray-700">
                            You can modify quantities in your <Link href="/cart" class="text-blue-600 hover:underline">cart</Link> before proceeding to checkout.
                        </p>
                    </div>
                </div>

                <!-- Step 2: Customer Info & Health Check Sheet -->
                <div
                    v-if="currentStep === 2"
                    class="bg-white rounded-lg shadow-md p-6 mb-6"
                >
                    <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <FileText class="w-6 h-6" />
                        Customer Information & Health Check Sheet
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Email *
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Phone *
                            </label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                First Name *
                            </label>
                            <input
                                v-model="form.first_name"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Last Name *
                            </label>
                            <input
                                v-model="form.last_name"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                    </div>

                    <!-- Health Check Sheet -->
                    <div class="border-t pt-6 mt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Health Check Sheet
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">
                            This information helps the manufacturing doctor provide minimal advice based on your health information.
                        </p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Age *
                                </label>
                                <input
                                    v-model="form.health_check.age"
                                    type="number"
                                    required
                                    min="1"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Weight (kg) *
                                </label>
                                <input
                                    v-model="form.health_check.weight"
                                    type="number"
                                    required
                                    min="1"
                                    step="0.1"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Height (cm)
                                </label>
                                <input
                                    v-model="form.health_check.height"
                                    type="number"
                                    min="1"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                />
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Pregnancy Status
                                </label>
                                <select
                                    v-model="form.health_check.pregnancy_status"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                >
                                    <option value="">Select...</option>
                                    <option value="not_applicable">Not Applicable</option>
                                    <option value="not_pregnant">Not Pregnant</option>
                                    <option value="pregnant">Pregnant</option>
                                    <option value="breastfeeding">Breastfeeding</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Current Medications
                                </label>
                                <textarea
                                    v-model="form.health_check.current_medications"
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    placeholder="List any medications you are currently taking"
                                ></textarea>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Allergies
                                </label>
                                <textarea
                                    v-model="form.health_check.allergies"
                                    rows="2"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    placeholder="List any known allergies"
                                ></textarea>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Medical Conditions
                                </label>
                                <textarea
                                    v-model="form.health_check.medical_conditions"
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    placeholder="List any medical conditions"
                                ></textarea>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Reason for Use
                                </label>
                                <textarea
                                    v-model="form.health_check.reason_for_use"
                                    rows="3"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                                    placeholder="Please describe your reason for using Ivermectin"
                                ></textarea>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="flex items-center gap-2">
                                    <input
                                        v-model="form.health_check.previous_ivermectin_use"
                                        type="checkbox"
                                        class="rounded"
                                    />
                                    <span class="text-sm text-gray-700">
                                        Have you used Ivermectin before?
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Shipping Address -->
                <div
                    v-if="currentStep === 3"
                    class="bg-white rounded-lg shadow-md p-6 mb-6"
                >
                    <h2 class="text-xl font-bold text-gray-900 mb-6">
                        Shipping Address
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 1 *
                            </label>
                            <input
                                v-model="form.address_line_1"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 2
                            </label>
                            <input
                                v-model="form.address_line_2"
                                type="text"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                City *
                            </label>
                            <input
                                v-model="form.city"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                State/Prefecture *
                            </label>
                            <input
                                v-model="form.state"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Postal Code *
                            </label>
                            <input
                                v-model="form.postal_code"
                                type="text"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            />
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Country *
                            </label>
                            <select
                                v-model="form.country"
                                required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 bg-white focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-gray-900"
                            >
                                <option
                                    v-for="country in countries"
                                    :key="country.code"
                                    :value="country.code"
                                >
                                    {{ country.name }}
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Risk Acknowledgment -->
                <div
                    v-if="currentStep === 4"
                    class="bg-white rounded-lg shadow-md p-6 mb-6"
                >
                    <!-- Risk Acknowledgment -->
                    <div class="bg-red-50 border-l-4 border-red-500 rounded-lg p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <AlertTriangle class="w-6 h-6 text-red-600" />
                            Risks & Prohibitions
                        </h2>
                        
                        <div class="space-y-4 mb-6">
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Risks:</h3>
                                <ul class="list-disc list-inside space-y-1 text-gray-700">
                                    <li>Potential for wrong medication</li>
                                    <li>Incorrect dosage without a doctor</li>
                                    <li>Customs delays</li>
                                </ul>
                            </div>
                            
                            <div>
                                <h3 class="font-semibold text-gray-900 mb-2">Prohibitions:</h3>
                                <ul class="list-disc list-inside space-y-1 text-gray-700">
                                    <li>Resale or transfer to third parties is strictly prohibited</li>
                                    <li>Exception: Doctors with proper "Yakkan Shoumei" permits can prescribe to others</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input
                                    v-model="form.risk_acknowledged"
                                    type="checkbox"
                                    required
                                    class="mt-1 rounded"
                                    true-value="1"
                                    false-value="0"
                                />
                                <span class="text-sm text-gray-700">
                                    I acknowledge the risks of self-medication and understand that I am responsible for my personal import. *
                                </span>
                            </label>
                            
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input
                                    v-model="form.resale_prohibited"
                                    type="checkbox"
                                    required
                                    class="mt-1 rounded"
                                    true-value="1"
                                    false-value="0"
                                />
                                <span class="text-sm text-gray-700">
                                    I understand that resale or transfer to third parties is strictly prohibited. *
                                </span>
                            </label>
                        </div>
                    </div>

                </div>

                <!-- Step 5: Final Review & Confirm Order -->
                <div
                    v-if="currentStep === 5"
                    class="space-y-6"
                >
                    <!-- Order Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-6">
                            Order Summary
                        </h2>
                        
                        <!-- Cart Items -->
                        <div class="space-y-4 mb-6">
                            <div
                                v-for="item in cartItems"
                                :key="item.id"
                                class="flex items-center gap-4 p-4 border border-gray-200 rounded-lg"
                            >
                                <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    <img
                                        v-if="item.product?.image"
                                        :src="item.product.image"
                                        :alt="item.product.name"
                                        class="w-full h-full object-cover"
                                    />
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900 mb-1">
                                        {{ item.product?.name }}
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        Quantity: {{ item.quantity }} × {{ formatPrice(item.price) }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">
                                        {{ formatPrice(item.price * item.quantity) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t pt-4 space-y-2 text-sm">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal:</span>
                                <span>{{ formatPrice(subtotal) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-600">
                                <span>Shipping:</span>
                                <span>{{ shipping === 0 ? 'Free' : formatPrice(shipping) }}</span>
                            </div>
                            <div class="border-t pt-2 mt-2 flex justify-between text-lg font-bold text-gray-900">
                                <span>Total:</span>
                                <span>{{ formatPrice(total) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address Summary -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            Shipping Address
                        </h2>
                        <div class="text-gray-700">
                            <p class="font-semibold">{{ form.first_name }} {{ form.last_name }}</p>
                            <p>{{ form.address_line_1 }}</p>
                            <p v-if="form.address_line_2">{{ form.address_line_2 }}</p>
                            <p>{{ form.city }}, {{ form.state }} {{ form.postal_code }}</p>
                            <p>{{ form.country }}</p>
                            <p class="mt-2">Phone: {{ form.phone }}</p>
                            <p>Email: {{ form.email }}</p>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">
                            Payment Method
                        </h2>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                :class="form.payment_method === 'bank_transfer' ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300'">
                                <input
                                    v-model="form.payment_method"
                                    type="radio"
                                    value="bank_transfer"
                                    class="mt-1"
                                />
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Bank Transfer</div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Transfer funds directly to our bank account. You will receive payment instructions after confirming your order.
                                    </p>
                                </div>
                            </label>
                            
                            <label class="flex items-start gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors"
                                :class="form.payment_method === 'credit_card' ? 'border-gray-900 bg-gray-50' : 'border-gray-200 hover:border-gray-300'">
                                <input
                                    v-model="form.payment_method"
                                    type="radio"
                                    value="credit_card"
                                    class="mt-1"
                                />
                                <div class="flex-1">
                                    <div class="font-semibold text-gray-900">Credit Card</div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Pay securely with your credit card. You will be redirected to our secure payment gateway.
                                    </p>
                                </div>
                            </label>
                        </div>
                        <p class="text-sm text-gray-600 mt-4">
                            Payment instructions will be provided after order confirmation.
                        </p>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between">
                    <button
                        v-if="currentStep > 1"
                        type="button"
                        @click="prevStep"
                        class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition"
                    >
                        Previous
                    </button>
                    <div v-else></div>
                    
                    <div class="flex gap-4">
                        <button
                            v-if="currentStep < totalSteps"
                            type="button"
                            @click="nextStep"
                            class="bg-gray-900 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition uppercase tracking-wide"
                        >
                            Next Step
                        </button>
                        <button
                            v-else
                            type="submit"
                            :disabled="!form.risk_acknowledged || !form.resale_prohibited"
                            class="bg-gray-900 text-white px-8 py-3 rounded-lg font-semibold hover:bg-gray-800 transition disabled:bg-gray-300 disabled:cursor-not-allowed flex items-center gap-2 uppercase tracking-wide"
                        >
                            <CheckCircle class="w-5 h-5" />
                            Confirm Order
                        </button>
                    </div>
                </div>
            </form>
        </div>
        </div>
    </div>
</template>
