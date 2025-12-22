import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { createApp, h } from 'vue';
import { initializeTheme } from './composables/useAppearance.ts';

const appName = process.env.MIX_APP_NAME || 'Laravel';

// For Laravel Mix, we need to explicitly map page names to imports
// This allows webpack to statically analyze and bundle all pages
const pages = {
    // Auth pages
    'auth/Login': () => import('./pages/auth/Login.vue'),
    'auth/Register': () => import('./pages/auth/Register.vue'),
    'auth/ForgotPassword': () => import('./pages/auth/ForgotPassword.vue'),
    'auth/ResetPassword': () => import('./pages/auth/ResetPassword.vue'),
    'auth/ConfirmPassword': () => import('./pages/auth/ConfirmPassword.vue'),
    'auth/TwoFactorChallenge': () => import('./pages/auth/TwoFactorChallenge.vue'),
    'auth/VerifyEmail': () => import('./pages/auth/VerifyEmail.vue'),
    
    // Main pages
    'Home': () => import('./pages/Home.vue'),
    'Welcome': () => import('./pages/Welcome.vue'),
    'Dashboard': () => import('./pages/Dashboard.vue'),
    
    // Product pages
    'Products/Index': () => import('./pages/Products/Index.vue'),
    'Products/Show': () => import('./pages/Products/Show.vue'),
    
    // Cart pages
    'Cart/Index': () => import('./pages/Cart/Index.vue'),
    
    // Checkout
    'Checkout': () => import('./pages/Checkout.vue'),
    
    // Orders
    'Orders/Show': () => import('./pages/Orders/Show.vue'),
    
    // Admin pages
    'Admin/Products/Index': () => import('./pages/Admin/Products/Index.vue'),
    'Admin/Products/Create': () => import('./pages/Admin/Products/Create.vue'),
    'Admin/Products/Edit': () => import('./pages/Admin/Products/Edit.vue'),
    'Admin/Inventory/Index': () => import('./pages/Admin/Inventory/Index.vue'),
    'Admin/Orders/Index': () => import('./pages/Admin/Orders/Index.vue'),
    'Admin/Orders/Show': () => import('./pages/Admin/Orders/Show.vue'),
    'Admin/Settings/Index': () => import('./pages/Admin/Settings/Index.vue'),
    'Admin/Faqs/Index': () => import('./pages/Admin/Faqs/Index.vue'),
    'Admin/Faqs/Create': () => import('./pages/Admin/Faqs/Create.vue'),
    'Admin/Faqs/Edit': () => import('./pages/Admin/Faqs/Edit.vue'),
    'Admin/Brands/Index': () => import('./pages/Admin/Brands/Index.vue'),
    'Admin/Brands/Create': () => import('./pages/Admin/Brands/Create.vue'),
    'Admin/Brands/Edit': () => import('./pages/Admin/Brands/Edit.vue'),
    'Admin/Categories/Index': () => import('./pages/Admin/Categories/Index.vue'),
    'Admin/Categories/Create': () => import('./pages/Admin/Categories/Create.vue'),
    'Admin/Categories/Edit': () => import('./pages/Admin/Categories/Edit.vue'),
    'Admin/Carousel/Index': () => import('./pages/Admin/Carousel/Index.vue'),
    'Admin/Carousel/Create': () => import('./pages/Admin/Carousel/Create.vue'),
    'Admin/Carousel/Edit': () => import('./pages/Admin/Carousel/Edit.vue'),
    
    // Settings pages
    'settings/Profile': () => import('./pages/settings/Profile.vue'),
    'settings/Password': () => import('./pages/settings/Password.vue'),
    'settings/TwoFactor': () => import('./pages/settings/TwoFactor.vue'),
    'settings/Appearance': () => import('./pages/settings/Appearance.vue'),
};

function resolvePageComponent(name) {
    const pageLoader = pages[name];
    
    if (!pageLoader) {
        // Fallback: try dynamic import for pages not in the map
        console.warn(`Page "${name}" not found in pages map, trying dynamic import...`);
        return import(`./pages/${name}.vue`)
            .then(module => module.default || module)
            .catch(() => {
                throw new Error(`Page component "${name}" not found.`);
            });
    }
    
    return pageLoader().then(module => module.default || module);
}

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        return resolvePageComponent(name);
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// This will set light / dark mode on page load...
initializeTheme();
