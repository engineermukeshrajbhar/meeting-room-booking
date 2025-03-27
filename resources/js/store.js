// resources/js/store.js
import { createStore } from 'vuex';
import axios from 'axios';

export default createStore({
    state: {
        isAuthenticated: localStorage.getItem('isAuthenticated') === 'true',
        user: JSON.parse(localStorage.getItem('user') || 'null'),
        meetingRooms: [],
        bookings: [],
        subscriptionPlans: [
            { id: 1, name: 'Free Plan', bookings_per_day: 3, price: 0 },
            { id: 2, name: 'Basic Plan', bookings_per_day: 5, price: 9.99 },
            { id: 3, name: 'Advance Plan', bookings_per_day: 7, price: 14.99 },
            { id: 4, name: 'Premium Plan', bookings_per_day: 10, price: 19.99 }
        ]
    },
    mutations: {
        setAuthenticated(state, value) {
            state.isAuthenticated = value;
            localStorage.setItem('isAuthenticated', value);
        },
        setUser(state, user) {
            state.user = user;
            localStorage.setItem('user', JSON.stringify(user));
        },
        setMeetingRooms(state, rooms) {
            state.meetingRooms = rooms;
        },
        addBooking(state, booking) {
            state.bookings.push(booking);
        },
        setSubscription(state, subscription) {
            if (!state.user) {
                state.user = {};
            }
            state.user.subscription = subscription;
        }
    },
    actions: {
        async register({ commit }, form) {
            try {
                const response = await axios.post('/api/register', form);
                commit('setAuthenticated', true);
                commit('setUser', response.data.user);
                return response;
            } catch (error) {
                throw error;
            }
        },
        async login({ commit }, credentials) {
            try {
                const response = await axios.post('/api/login', credentials);
                commit('setAuthenticated', true);
                commit('setUser', response.data.user);
                return response;
            } catch (error) {
                throw error;
            }
        },
        async logout({ commit }) {
            try {
                await axios.post('/api/logout');
                commit('setAuthenticated', false);
                commit('setUser', null);
                localStorage.removeItem('isAuthenticated');
                localStorage.removeItem('user');
            } catch (error) {
                throw error;
            }
        },
        async fetchMeetingRooms({ commit }) {
            try {
                const response = await axios.get('/api/meeting-rooms');
                commit('setMeetingRooms', response.data.meeting_rooms);
                return response;
            } catch (error) {
                throw error;
            }
        },
        async bookRoom({ commit }, bookingData) {
            try {
                const response = await axios.post('/api/bookings', bookingData);
                commit('addBooking', response.data);
                return response;
            } catch (error) {
                throw error;
            }
        },
        async subscribe({ commit }, plan) {
            try {
                const response = await axios.post('/api/subscription/subscribe', { plan });
                commit('setSubscription', response.data);
                return response;
            } catch (error) {
                throw error;
            }
        }
    },
    getters: {
        availablePlans: (state) => state.subscriptionPlans,
        userSubscription: (state) => state.user?.subscription || null,
        dailyBookingLimit: (state) => {
            return state.user?.subscription?.bookings_per_day || 3;
        },
        bookingsToday: (state) => {
            if (!state.user) return 0;
            const today = new Date().toISOString().split('T')[0];
            return state.bookings.filter(booking => 
                new Date(booking.created_at).toISOString().split('T')[0] === today
            ).length;
        }
    }
});