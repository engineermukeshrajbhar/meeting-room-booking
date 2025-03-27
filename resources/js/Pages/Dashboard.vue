<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { usePage, useForm } from '@inertiajs/vue3';

const props = defineProps({
    meetingRooms: Array,
    bookings: Object,
    upcomingBookings: Array,
    pastBookings: Array,
    userSubscription: Object,
});

const form = useForm({
    meeting_name: '',
    start_time: '',
    duration: 30,
    members: 1,
    meeting_room_id: null,
});

const availableRooms = ref([]);
const activeTab = ref('upcoming');
const loading = ref(false);

const user = computed(() => usePage().props.auth.user);

const subscriptionPlans = [
    { id: 1, name: 'Free Plan', bookings_per_day: 3, price: 0 },
    { id: 2, name: 'Basic Plan', bookings_per_day: 5, price: 9.99 },
    { id: 3, name: 'Advance Plan', bookings_per_day: 7, price: 14.99 },
    { id: 4, name: 'Premium Plan', bookings_per_day: 10, price: 19.99 }
];

const currentPlan = computed(() => {
    return props.userSubscription || subscriptionPlans.find(p => p.bookings_per_day === 3);
});

const bookingsToday = computed(() => {
    if (!props.bookings?.data) return 0;
    const today = new Date().toISOString().split('T')[0];
    return props.bookings.data.filter(booking => 
        new Date(booking.created_at).toISOString().split('T')[0] === today
    ).length;
});

const minDateTime = computed(() => {
    const now = new Date();
    now.setMinutes(now.getMinutes() + 30);
    return now.toISOString().slice(0, 16);
});

async function checkAvailability() {
    if (!form.start_time || !form.duration || !form.members) {
        alert('Please fill all required fields');
        return;
    }

    loading.value = true;
    
    try {
        await router.post(route('available-rooms'), {
            start_time: form.start_time,
            duration: form.duration,
            members: form.members
        }, {
            preserveState: true,
            onSuccess: (page) => {
                availableRooms.value = page.props.availableRooms || [];
            },
            onError: (errors) => {
                alert(errors.message || 'Error checking availability');
            }
        });
    } catch (error) {
        console.error('Error:', error);
        alert(error.message || 'Error checking availability');
    } finally {
        loading.value = false;
    }
}

function bookRoom(roomId) {
    form.meeting_room_id = roomId;
    form.post('/bookings', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            availableRooms.value = [];
        }
    });
}

function subscribe(planId) {
    const plan = subscriptionPlans.find(p => p.id === planId);
    router.post('/subscribe', { 
        plan: plan.name.toLowerCase().split(' ')[0] 
    }, {
        preserveScroll: true,
        onSuccess: () => router.reload({ only: ['userSubscription'] })
    });
}

function formatDateTime(dateTime) {
    return new Date(dateTime).toLocaleString();
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- User Stats Section -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- User Info Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium">Welcome, {{ user.name }}</h3>
                            <div class="mt-4">
                                <p><strong>Plan:</strong> {{ currentPlan.name }}</p>
                                <p><strong>Daily Limit:</strong> {{ currentPlan.bookings_per_day }} bookings</p>
                                <p><strong>Bookings Today:</strong> {{ bookingsToday }} / {{ currentPlan.bookings_per_day }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium">Quick Stats</h3>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <p class="text-sm text-gray-500">Upcoming</p>
                                    <p class="text-2xl font-bold">{{ upcomingBookings.length }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Past</p>
                                    <p class="text-2xl font-bold">{{ pastBookings.length }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Subscription Plans Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div class="p-6 bg-white border-b border-gray-200">
                            <h3 class="text-lg font-medium">Subscription Plans</h3>
                            <div class="mt-4 space-y-2">
                                <div v-for="plan in subscriptionPlans" :key="plan.id" class="flex items-center justify-between">
                                    <div>
                                        <p class="font-medium">{{ plan.name }}</p>
                                        <p class="text-sm text-gray-500">{{ plan.bookings_per_day }} bookings/day</p>
                                    </div>
                                    <button
                                        @click="subscribe(plan.id)"
                                        class="px-3 py-1 text-sm rounded"
                                        :class="{
                                            'bg-blue-500 text-white': currentPlan.id !== plan.id,
                                            'bg-gray-200 text-gray-700': currentPlan.id === plan.id
                                        }"
                                        :disabled="currentPlan.id === plan.id"
                                    >
                                        {{ currentPlan.id === plan.id ? 'Current' : 'Subscribe' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Booking Form Section -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-medium">Book a Meeting Room</h3>
                        <form @submit.prevent="checkAvailability" class="mt-4 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Meeting Name</label>
                                <input v-model="form.meeting_name" type="text" class="block w-full mt-1 rounded-md shadow-sm" required>
                                <p v-if="form.errors.meeting_name" class="mt-1 text-sm text-red-600">{{ form.errors.meeting_name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Date and Time</label>
                                <input v-model="form.start_time" type="datetime-local" :min="minDateTime" class="block w-full mt-1 rounded-md shadow-sm" required>
                                <p v-if="form.errors.start_time" class="mt-1 text-sm text-red-600">{{ form.errors.start_time }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Duration</label>
                                <select v-model="form.duration" class="block w-full mt-1 rounded-md shadow-sm" required>
                                    <option value="30">30 minutes</option>
                                    <option value="60">60 minutes</option>
                                    <option value="90">90 minutes</option>
                                </select>
                                <p v-if="form.errors.duration" class="mt-1 text-sm text-red-600">{{ form.errors.duration }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Number of Members</label>
                                <input v-model.number="form.members" type="number" min="1" class="block w-full mt-1 rounded-md shadow-sm" required>
                                <p v-if="form.errors.members" class="mt-1 text-sm text-red-600">{{ form.errors.members }}</p>
                            </div>
                            
                            <button type="submit" :disabled="loading" class="px-4 py-2 text-white bg-blue-500 rounded-md hover:bg-blue-600 disabled:opacity-50">
                                <span v-if="loading">Checking...</span>
                                <span v-else>Check Availability</span>
                            </button>
                        </form>

                        <div v-if="availableRooms.length" class="mt-6">
                            <h4 class="mb-2 text-lg font-medium">Available Rooms</h4>
                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                                <div v-for="room in availableRooms" :key="room.id" class="p-4 border rounded-md">
                                    <h5 class="font-medium">{{ room.name }}</h5>
                                    <p class="text-sm text-gray-500">Capacity: {{ room.capacity }} members</p>
                                    <button 
                                        @click="bookRoom(room.id)"
                                        class="w-full px-4 py-2 mt-2 text-white bg-green-500 rounded-md hover:bg-green-600"
                                    >
                                        Book This Room
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings Section -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium">My Bookings</h3>
                            <div class="flex space-x-2">
                                <button
                                    @click="activeTab = 'upcoming'"
                                    class="px-4 py-2 rounded-md"
                                    :class="{
                                        'bg-blue-500 text-white': activeTab === 'upcoming',
                                        'bg-gray-200': activeTab !== 'upcoming'
                                    }"
                                >
                                    Upcoming
                                </button>
                                <button
                                    @click="activeTab = 'past'"
                                    class="px-4 py-2 rounded-md"
                                    :class="{
                                        'bg-blue-500 text-white': activeTab === 'past',
                                        'bg-gray-200': activeTab !== 'past'
                                    }"
                                >
                                    Past
                                </button>
                            </div>
                        </div>

                        <div v-if="loading" class="flex justify-center mt-4">
                            <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                        </div>

                        <div v-else class="mt-4">
                            <div v-if="activeTab === 'upcoming' && upcomingBookings.length === 0" class="p-4 text-center text-gray-500">
                                No upcoming bookings found
                            </div>
                            <div v-if="activeTab === 'past' && pastBookings.length === 0" class="p-4 text-center text-gray-500">
                                No past bookings found
                            </div>

                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Meeting</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Room</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Time</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Duration</th>
                                            <th class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase">Members</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <tr v-for="booking in activeTab === 'upcoming' ? upcomingBookings : pastBookings" :key="booking.id">
                                            <td class="px-6 py-4 whitespace-nowrap">{{ booking.meeting_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ booking.meeting_room.name }} ({{ booking.meeting_room.capacity }})</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ formatDateTime(booking.start_time) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ booking.duration }} mins</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ booking.members }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>