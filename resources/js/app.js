import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers'
import { ZiggyVue } from '../../vendor/tightenco/ziggy'

createInertiaApp({
    title: (title) => title ? `${title} — MainYuk` : 'MainYuk',
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })

        // Global error handler for debugging
        app.config.errorHandler = (err, instance, info) => {
            console.error('[MainYuk Vue Error]', err, info)
        }

        app
            .use(plugin)
            .use(ZiggyVue, Ziggy) // Pass Ziggy config from @routes blade directive
            .mount(el)
    },
    progress: {
        color: '#00754A',
    },
})

// Initialize Flowbite after each Inertia page navigation (optional)
document.addEventListener('inertia:finish', () => {
    try {
        import('flowbite').then(({ initFlowbite }) => initFlowbite())
    } catch (e) { /* Flowbite optional */ }
})