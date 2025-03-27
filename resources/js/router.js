// resources/js/router.js
import { createRouter, createWebHistory } from 'vue-router';
import Login from './components/auth/Login.vue';
import Register from './components/auth/Register.vue';
import Home from './components/Home.vue';
import BookingForm from './components/booking/BookingForm.vue';
import MyBookings from './components/booking/MyBookings.vue';

const routes = [
    { path: '/', component: Home, meta: { requiresAuth: true } },
    { path: '/login', component: Login, meta: { requiresGuest: true } },
    { path: '/register', component: Register, meta: { requiresGuest: true } },
    { path: '/book-room', component: BookingForm, meta: { requiresAuth: true } },
    { path: '/my-bookings', component: MyBookings, meta: { requiresAuth: true } },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

router.beforeEach((to, from, next) => {
    const isAuthenticated = localStorage.getItem('isAuthenticated') === 'true';
    
    if (to.meta.requiresAuth && !isAuthenticated) {
        next('/login');
    } else if (to.meta.requiresGuest && isAuthenticated) {
        next('/');
    } else {
        next();
    }
});

export default router;