<script setup lang="ts">
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Menu, X, LogIn, User, LogOut, ShoppingCart } from 'lucide-vue-next';
import { ref } from 'vue';
// Routes are handled directly with URLs
import { useLanguage } from '@/composables/useLanguage';

const { language, setLanguage } = useLanguage();

const page = usePage();
const mobileMenuOpen = ref(false);
const cartSidebarOpen = ref(false);

const isAuthenticated = computed(() => !!page.props.auth?.user);
const cartCount = computed(() => {
    const cart = (page.props as any).cart;
    return cart?.count || 0;
});

const openCartSidebar = () => {
    cartSidebarOpen.value = true;
    // Emit event to open cart sidebar globally
    window.dispatchEvent(new CustomEvent('openCartSidebar'));
};

const closeCartSidebar = () => {
    cartSidebarOpen.value = false;
    window.dispatchEvent(new CustomEvent('closeCartSidebar'));
};

const isActive = (url: string) => {
    return page.url === url || page.url.startsWith(url + '/');
};

const navItems = [
    {
        title: 'Nantosha Import & Export Division',
        titleJa: '南東舎輸出入部',
        href: '/',
    },
    {
        title: 'How to Order',
        titleJa: '購入までの流れ',
        href: '/how-to-order',
    },
    {
        title: 'FAQ',
        titleJa: 'よくある質問',
        href: '/faq',
    },
];

const aboutUsDropdownOpen = ref(false);
const aboutUsDropdownTimeout = ref<NodeJS.Timeout | null>(null);

const openAboutUsDropdown = () => {
    if (aboutUsDropdownTimeout.value) {
        clearTimeout(aboutUsDropdownTimeout.value);
    }
    aboutUsDropdownOpen.value = true;
};

const closeAboutUsDropdown = () => {
    aboutUsDropdownTimeout.value = setTimeout(() => {
        aboutUsDropdownOpen.value = false;
    }, 200);
};

const keepAboutUsDropdownOpen = () => {
    if (aboutUsDropdownTimeout.value) {
        clearTimeout(aboutUsDropdownTimeout.value);
    }
};

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const handleLogout = () => {
    router.post('/logout');
};
</script>

<template>
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                <!-- Logo/Brand -->
                <Link href="/" class="flex items-center space-x-2">
                    <span class="text-lg font-semibold text-gray-900 tracking-tight">Nantosha</span>
                    <span class="text-xs text-gray-600">南東舎</span>
                </Link>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        :class="[
                            'text-sm font-medium transition-colors py-2',
                            isActive(item.href)
                                ? 'text-gray-900 border-b-2 border-gray-900'
                                : 'text-gray-600 hover:text-gray-900'
                        ]"
                    >
                        <div class="flex flex-col items-start">
                            <span>{{ item.title }}</span>
                            <span class="text-xs text-gray-400 mt-0.5">{{ item.titleJa }}</span>
                        </div>
                    </Link>
                    
                    <!-- About Us Dropdown -->
                    <div 
                        class="relative"
                        @mouseenter="openAboutUsDropdown"
                        @mouseleave="closeAboutUsDropdown"
                    >
                        <div
                            :class="[
                                'text-sm font-medium transition-colors py-2 cursor-pointer',
                                (isActive('/dr-landrito-profile') || isActive('/corporate-profile'))
                                    ? 'text-gray-900 border-b-2 border-gray-900'
                                    : 'text-gray-600 hover:text-gray-900'
                            ]"
                        >
                            <div class="flex flex-col items-start">
                                <span>About Us</span>
                                <span class="text-xs text-gray-400 mt-0.5">会社について</span>
                            </div>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div
                            v-if="aboutUsDropdownOpen"
                            @mouseenter="keepAboutUsDropdownOpen"
                            @mouseleave="closeAboutUsDropdown"
                            class="absolute top-full left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-50"
                        >
                            <Link
                                href="/dr-landrito-profile"
                                :class="[
                                    'block px-4 py-3 text-sm font-medium transition-colors',
                                    isActive('/dr-landrito-profile')
                                        ? 'text-gray-900 bg-gray-50'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                                ]"
                            >
                                Dr. Allan Landrito
                            </Link>
                            <Link
                                href="/corporate-profile"
                                :class="[
                                    'block px-4 py-3 text-sm font-medium transition-colors border-t border-gray-200',
                                    isActive('/corporate-profile')
                                        ? 'text-gray-900 bg-gray-50'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                                ]"
                            >
                                Corporate Profile
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Contact Us Link -->
                    <Link
                        href="/contact"
                        :class="[
                            'text-sm font-medium transition-colors py-2',
                            isActive('/contact')
                                ? 'text-gray-900 border-b-2 border-gray-900'
                                : 'text-gray-600 hover:text-gray-900'
                        ]"
                    >
                        <div class="flex flex-col items-start">
                            <span>Contact Us</span>
                            <span class="text-xs text-gray-400 mt-0.5">お問い合わせ</span>
                        </div>
                    </Link>
                    
                    <!-- Language Switcher -->
                    <div class="flex items-center gap-1 border border-gray-300 rounded-lg overflow-hidden">
                        <button
                            @click="setLanguage('en')"
                            :class="[
                                'px-3 py-1.5 text-xs font-medium transition-colors',
                                language === 'en'
                                    ? 'bg-gray-900 text-white'
                                    : 'bg-white text-gray-600 hover:bg-gray-50'
                            ]"
                        >
                            EN
                        </button>
                        <button
                            @click="setLanguage('ja')"
                            :class="[
                                'px-3 py-1.5 text-xs font-medium transition-colors',
                                language === 'ja'
                                    ? 'bg-gray-900 text-white'
                                    : 'bg-white text-gray-600 hover:bg-gray-50'
                            ]"
                        >
                            日本語
                        </button>
                    </div>
                    
                    <!-- Cart Icon -->
                    <button
                        @click="openCartSidebar"
                        class="relative p-2 text-gray-600 hover:text-gray-900 transition-colors"
                        aria-label="Open cart"
                    >
                        <ShoppingCart class="w-5 h-5" />
                        <span
                            v-if="cartCount > 0"
                            class="absolute -top-1 -right-1 bg-gray-900 text-white text-xs font-semibold rounded-full w-5 h-5 flex items-center justify-center"
                        >
                            {{ cartCount > 99 ? '99+' : cartCount }}
                        </span>
                    </button>
                    
                    <!-- Auth Buttons -->
                    <div class="flex items-center space-x-3 ml-4 pl-4 border-l border-gray-200">
                        <template v-if="!isAuthenticated">
                            <Link
                                href="/login"
                                class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                            >
                                <LogIn class="w-4 h-4" />
                                <span>Login</span>
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/dashboard"
                                class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                            >
                                <User class="w-4 h-4" />
                                <span>Dashboard</span>
                            </Link>
                            <button
                                @click="handleLogout"
                                class="flex items-center gap-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                            >
                                <LogOut class="w-4 h-4" />
                                <span>Logout</span>
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <button
                    @click="toggleMobileMenu"
                    class="md:hidden p-2 text-gray-700 hover:text-gray-900"
                    aria-label="Toggle menu"
                >
                    <Menu v-if="!mobileMenuOpen" class="w-5 h-5" />
                    <X v-else class="w-5 h-5" />
                </button>
            </div>

            <!-- Mobile Navigation -->
            <div
                v-if="mobileMenuOpen"
                class="md:hidden border-t border-gray-200 py-3"
            >
                <div class="flex flex-col space-y-1">
                    <Link
                        v-for="item in navItems"
                        :key="item.href"
                        :href="item.href"
                        @click="mobileMenuOpen = false"
                        :class="[
                            'px-3 py-2 text-sm font-medium transition-colors',
                            isActive(item.href)
                                ? 'text-gray-900 bg-gray-50'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                        ]"
                    >
                        <div class="flex flex-col">
                            <span>{{ item.title }}</span>
                            <span class="text-xs text-gray-400 mt-0.5">{{ item.titleJa }}</span>
                        </div>
                    </Link>
                    
                    <!-- Mobile About Us -->
                    <div class="px-3 py-2">
                        <div class="text-sm font-medium text-gray-700 mb-2">About Us / 会社について</div>
                        <div class="flex flex-col space-y-1 ml-4">
                            <Link
                                href="/dr-landrito-profile"
                                @click="mobileMenuOpen = false"
                                :class="[
                                    'px-3 py-2 text-sm transition-colors rounded',
                                    isActive('/dr-landrito-profile')
                                        ? 'text-gray-900 bg-gray-50'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                                ]"
                            >
                                Dr. Allan Landrito
                            </Link>
                            <Link
                                href="/corporate-profile"
                                @click="mobileMenuOpen = false"
                                :class="[
                                    'px-3 py-2 text-sm transition-colors rounded',
                                    isActive('/corporate-profile')
                                        ? 'text-gray-900 bg-gray-50'
                                        : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                                ]"
                            >
                                Corporate Profile
                            </Link>
                        </div>
                    </div>
                    
                    <!-- Mobile Contact Us -->
                    <Link
                        href="/contact"
                        @click="mobileMenuOpen = false"
                        :class="[
                            'px-3 py-2 text-sm font-medium transition-colors',
                            isActive('/contact')
                                ? 'text-gray-900 bg-gray-50'
                                : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                        ]"
                    >
                        <div class="flex flex-col">
                            <span>Contact Us</span>
                            <span class="text-xs text-gray-400 mt-0.5">お問い合わせ</span>
                        </div>
                    </Link>
                    
                    <!-- Mobile Language Switcher -->
                    <div class="border-t border-gray-200 mt-2 pt-2 px-3 py-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-700">Language</span>
                            <div class="flex items-center gap-1 border border-gray-300 rounded-lg overflow-hidden">
                                <button
                                    @click="setLanguage('en')"
                                    :class="[
                                        'px-3 py-1.5 text-xs font-medium transition-colors',
                                        language === 'en'
                                            ? 'bg-gray-900 text-white'
                                            : 'bg-white text-gray-600 hover:bg-gray-50'
                                    ]"
                                >
                                    EN
                                </button>
                                <button
                                    @click="setLanguage('ja')"
                                    :class="[
                                        'px-3 py-1.5 text-xs font-medium transition-colors',
                                        language === 'ja'
                                            ? 'bg-gray-900 text-white'
                                            : 'bg-white text-gray-600 hover:bg-gray-50'
                                    ]"
                                >
                                    日本語
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Mobile Cart Button -->
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <button
                            @click="openCartSidebar; mobileMenuOpen = false"
                            class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors w-full text-left"
                        >
                            <ShoppingCart class="w-4 h-4" />
                            <span>Cart</span>
                            <span
                                v-if="cartCount > 0"
                                class="ml-auto bg-gray-900 text-white text-xs font-semibold rounded-full w-5 h-5 flex items-center justify-center"
                            >
                                {{ cartCount > 99 ? '99+' : cartCount }}
                            </span>
                        </button>
                    </div>
                    
                    <!-- Mobile Auth Buttons -->
                    <div class="border-t border-gray-200 mt-2 pt-2">
                        <template v-if="!isAuthenticated">
                            <Link
                                href="/login"
                                @click="mobileMenuOpen = false"
                                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors"
                            >
                                <LogIn class="w-4 h-4" />
                                <span>Login</span>
                            </Link>
                        </template>
                        <template v-else>
                            <Link
                                href="/dashboard"
                                @click="mobileMenuOpen = false"
                                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors"
                            >
                                <User class="w-4 h-4" />
                                <span>Dashboard</span>
                            </Link>
                            <button
                                @click="() => { handleLogout(); mobileMenuOpen = false; }"
                                class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors w-full text-left mt-1"
                            >
                                <LogOut class="w-4 h-4" />
                                <span>Logout</span>
                            </button>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

