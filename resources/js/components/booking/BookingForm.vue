<!-- resources/js/components/booking/BookingForm.vue -->
<template>
    <div class="container mt-4">
      <h2>Book a Meeting Room</h2>
      <form @submit.prevent="checkAvailability">
        <div class="mb-3">
          <label class="form-label">Meeting Name</label>
          <input v-model="form.meeting_name" type="text" class="form-control" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Date and Time</label>
          <input v-model="form.start_time" type="datetime-local" class="form-control" :min="minDateTime" required>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Duration</label>
          <select v-model="form.duration" class="form-select" required>
            <option value="30">30 minutes</option>
            <option value="60">60 minutes</option>
            <option value="90">90 minutes</option>
          </select>
        </div>
        
        <div class="mb-3">
          <label class="form-label">Number of Members</label>
          <input v-model.number="form.members" type="number" class="form-control" min="1" required>
        </div>
        
        <button type="submit" class="btn btn-primary">Check Availability</button>
      </form>
  
      <div v-if="availableRooms.length" class="mt-4">
        <h3>Available Rooms</h3>
        <div class="list-group">
          <div v-for="room in availableRooms" :key="room.id" class="list-group-item">
            <h5>{{ room.name }} (Capacity: {{ room.capacity }})</h5>
            <button @click="bookRoom(room.id)" class="btn btn-success">Book This Room</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
      return {
        form: {
          meeting_name: '',
          start_time: '',
          duration: 30,
          members: 1,
          meeting_room_id: null
        },
        availableRooms: []
      }
    },
    computed: {
      minDateTime() {
        const now = new Date();
        now.setMinutes(now.getMinutes() + 30);
        return now.toISOString().slice(0, 16);
      }
    },
    methods: {
      async checkAvailability() {
        try {
          const response = await axios.post('/api/available-rooms', this.form);
          this.availableRooms = response.data;
        } catch (error) {
          console.error(error);
          alert('Error checking availability');
        }
      },
      async bookRoom(roomId) {
        try {
          this.form.meeting_room_id = roomId;
          await axios.post('/api/bookings', this.form);
          alert('Booking successful!');
          this.$router.push('/my-bookings');
        } catch (error) {
          console.error(error);
          alert(error.response?.data?.message || 'Booking failed');
        }
      }
    }
  }
  </script>