<!-- resources/js/components/App.vue -->
<template>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container-fluid">
                <router-link to="/" class="navbar-brand">Meeting Room Booking</router-link>
                <div class="navbar-nav">
                    <template v-if="!isAuthenticated">
                        <router-link to="/login" class="nav-link">Login</router-link>
                        <router-link to="/register" class="nav-link">Register</router-link>
                        <router-link to="/book-room" class="nav-link">Book Room</router-link>
                        <router-link to="/my-bookings" class="nav-link">My Bookings</router-link>
                    </template>
                    <template v-else>
                        <button @click="logout" class="nav-link btn btn-link">Logout</button>
                    </template>
                </div>
            </div>
        </nav>
        <router-view></router-view>
    </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState(['isAuthenticated'])
    },
    methods: {
        async logout() {
            try {
                await this.$store.dispatch('logout');
                this.$router.push('/login');
            } catch (error) {
                console.error(error);
            }
        }
    }
}
</script>