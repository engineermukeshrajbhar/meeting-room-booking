import '../css/app.css';
import './bootstrap';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import App from './components/App.vue';
import router from './router';
import store from './store';


const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App: InertiaApp, props, plugin }) {
        const app = createApp({ render: () => h(InertiaApp, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(router) // Vue Router
            .use(store); // Vuex or Pinia

        app.component('App', App); // Register main App component

        return app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
