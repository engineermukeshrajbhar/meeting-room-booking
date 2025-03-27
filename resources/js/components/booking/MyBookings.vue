<!-- resources/js/components/booking/MyBookings.vue -->
<template>
    <div class="container mt-4">
      <h2>My Bookings</h2>
      
      <div class="mb-3">
        <button @click="filter = 'upcoming'" :class="{'btn-primary': filter === 'upcoming', 'btn-outline-primary': filter !== 'upcoming'}" class="btn me-2">
          Upcoming
        </button>
        <button @click="filter = 'past'" :class="{'btn-primary': filter === 'past', 'btn-outline-primary': filter !== 'past'}" class="btn">
          Past
        </button>
      </div>
      
      <div v-if="loading" class="text-center">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
      
      <div v-else>
        <div v-if="bookings.data.length === 0" class="alert alert-info">
          No bookings found
        </div>
        
        <div v-else class="list-group">
          <div v-for="booking in bookings.data" :key="booking.id" class="list-group-item">
            <h5>{{ booking.meeting_name }}</h5>
            <p>
              <strong>Room:</strong> {{ booking.meeting_room.name }} (Capacity: {{ booking.meeting_room.capacity }})<br>
              <strong>Time:</strong> {{ formatDateTime(booking.start_time) }}<br>
              <strong>Duration:</strong> {{ booking.duration }} minutes<br>
              <strong>Members:</strong> {{ booking.members }}
            </p>
          </div>
        </div>
        
        <nav class="mt-4">
          <ul class="pagination">
            <li v-for="link in bookings.links" :key="link.label" class="page-item" :class="{active: link.active, disabled: !link.url}">
              <button @click="changePage(link.url)" class="page-link" v-html="link.label"></button>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        bookings: { data: [], links: [] },
        filter: 'upcoming',
        loading: false
      }
    },
    watch: {
      filter() {
        this.fetchBookings();
      }
    },
    created() {
      this.fetchBookings();
    },
    methods: {
      async fetchBookings(page = 1) {
        this.loading = true;
        try {
          const response = await axios.get(`/api/my-bookings?filter=${this.filter}&page=${page}`);
          this.bookings = response.data;
        } catch (error) {
          console.error(error);
        } finally {
          this.loading = false;
        }
      },
      changePage(url) {
        if (!url) return;
        const page = new URL(url).searchParams.get('page');
        this.fetchBookings(page);
      },
      formatDateTime(dateTime) {
        return new Date(dateTime).toLocaleString();
      }
    }
  }
  </script>