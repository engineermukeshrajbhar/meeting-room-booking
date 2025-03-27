<!-- resources/js/components/Home.vue -->
<template>
    <div class="container mt-5">
        <h1>Welcome, {{ user.name }}</h1>
        <p>This is the meeting room booking system.</p>
    </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
    computed: {
        ...mapState(['user'])
    }
}
</script>