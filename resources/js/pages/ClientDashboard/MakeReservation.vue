<script setup lang="ts">
import 'swiper/css';
import { ref, onMounted, reactive, computed } from 'vue';
import axios from 'axios';
import { Alert, AlertTitle, AlertDescription } from '@/components/ui/alert';
import { Button } from '@/components/ui/button';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, DialogFooter } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Badge } from '@/components/ui/badge';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { Calendar, Users, DollarSign, Home } from 'lucide-vue-next';
import { Floor, Room } from '@/interfaces/model.interface';

const floors = ref<Floor[]>([]);
const roomsByFloor = ref<Record<number, Room[]>>({});
const loading = ref(true);
const currentFloorId = ref<number | null>(null);
const reservationDialog = ref(false);
const selectedRoom = ref<Room | null>(null);
const accompanyNumber = ref(1);
const errorMessage = ref('');
const successMessage = ref('');
const loadingMessage = ref('');

const form = reactive({
  room_id: '',
  accompany_number: 1,
  check_out_at: '',
});

onMounted(async () => {
  try {
    const response = await fetch('/client/floors');
    const res = await response.json();
    floors.value = res.data;

    if (floors.value.length > 0) {
      currentFloorId.value = floors.value[0].id;
      await loadRooms(currentFloorId.value);
    }
  } catch (error) {
    console.error('Error loading floors:', error);
  } finally {
    loading.value = false;
  }
});

const loadRooms = async (floorId: number) => {
  try {
    const response = await fetch(`/client/floors/${floorId}/rooms`);
    const res = await response.json();
    roomsByFloor.value[floorId] = res.data;
    currentFloorId.value = floorId;
  } catch (error) {
    console.error('Error loading rooms:', error);
  }
};

const openReservationDialog = (room: Room) => {
  if (room.status !== 'available') {
    errorMessage.value = 'This room is not available for reservation.';
    return;
  }

  selectedRoom.value = room;
  form.room_id = room.id.toString();
  form.accompany_number = 1;
  accompanyNumber.value = 1;

  // Set default checkout date to tomorrow
  const tomorrow = new Date();
  tomorrow.setDate(tomorrow.getDate() + 1);
  form.check_out_at = tomorrow.toISOString().split('T')[0];

  reservationDialog.value = true;
  errorMessage.value = '';
};

const validateAccompanyNumber = () => {
  if (!selectedRoom.value) return false;

  if (form.accompany_number > selectedRoom.value.capacity) {
    errorMessage.value = `Maximum allowed guests for this room is ${selectedRoom.value.capacity}.`;
    return false;
  }

  if (form.accompany_number < 1) {
    errorMessage.value = 'Number of guests must be at least 1.';
    return false;
  }

  errorMessage.value = '';
  return true;
};

const makeReservation = async () => {
  if (!validateAccompanyNumber()) return;

  try {
    // Close the dialog and show loading state
    reservationDialog.value = false;
    loading.value = true;
    loadingMessage.value = 'Processing reservation...';
    errorMessage.value = '';

    // Using axios which automatically handles CSRF tokens
    const reservationResponse = await axios.post('/client/reservations', {
      room_id: form.room_id,
      accompany_number: form.accompany_number,
      check_out_at: form.check_out_at
    });


    loadingMessage.value = 'Redirecting to payment...';

    // After successful form submission, request the Stripe payment URL
    const response = await fetch('/reservation/payment');
    const data = await response.json();

    loading.value = false;

    if (data.url) {
      // Direct browser navigation to Stripe checkout
      window.location.href = data.url;
    } else if (data.error) {
      errorMessage.value = data.error;
      reservationDialog.value = true;
    }
  } catch (error: any) {
    loading.value = false;
    errorMessage.value = error.response?.data?.message || 'An unexpected error occurred';
    reservationDialog.value = true;
    console.error(error);
  }
};

const roomsForCurrentFloor = computed(() => {
  return roomsByFloor.value[currentFloorId.value ?? 0] || [];
});

// Random room image URL - in a real app, this would be unique per room or category
const roomImageUrl = "https://images.unsplash.com/photo-1566665797739-1674de7a421a?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1074&q=80";
</script>

<template>
  <div class="space-y-6">
    <h1 class="text-2xl font-bold mb-6">Make Reservation</h1>

    <!-- Success message -->
    <Alert v-if="successMessage" class="bg-green-50 border-green-500 mb-4 dark:bg-green-900/20 dark:border-green-600 dark:text-green-400">
      <Calendar class="h-4 w-4 text-green-500 dark:text-green-400" />
      <AlertTitle class="text-green-700 dark:text-green-400">Success!</AlertTitle>
      <AlertDescription class="text-green-600 dark:text-green-400">{{ successMessage }}</AlertDescription>
    </Alert>

    <!-- Loading state for initial fetch -->
    <div v-if="loading" class="flex flex-col items-center justify-center h-64">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ loadingMessage || 'Loading...' }}</p>
    </div>

    <div v-else-if="floors.length === 0" class="text-center p-8">
      <Alert>
        <AlertTitle>No Floors Available</AlertTitle>
        <AlertDescription>
          There are currently no floors available in the system.
        </AlertDescription>
      </Alert>
    </div>

    <div v-else class="space-y-8">
      <!-- Floors Slider -->
      <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
        <h2 class="text-lg font-semibold mb-3">Select Floor</h2>
        <Swiper
          :slides-per-view="3.5"
          :space-between="10"
          :grab-cursor="true"
          :breakpoints="{
            '640': { slidesPerView: 4.5 },
            '768': { slidesPerView: 5.5 },
            '1024': { slidesPerView: 6.5 }
          }"
        >
          <SwiperSlide
            v-for="floor in floors"
            :key="floor.id"
            class="cursor-pointer"
          >
            <div
              class="p-4 text-center rounded-lg hover:bg-primary/10 transition-colors"
              :class="{ 'bg-primary/20 font-bold': currentFloorId === floor.id }"
              @click="loadRooms(floor.id)"
            >
              <Home class="h-5 w-5 mx-auto mb-1" />
              <p>{{ floor.number }}</p>
              <p class="text-xs text-gray-500 dark:text-gray-400">{{ floor.name }}</p>
            </div>
          </SwiperSlide>
        </Swiper>
      </div>

      <!-- Rooms Container -->
      <div class="mb-4">
        <h2 class="text-lg font-semibold mb-3">Available Rooms</h2>
        <div class="overflow-y-auto max-h-[500px] pr-2">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div
              v-for="room in roomsForCurrentFloor"
              :key="room.id"
              class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden border dark:border-gray-700"
            >
              <!-- Room Image with Status Badge -->
              <div class="relative">
                <img :src="roomImageUrl" alt="Room" class="w-full h-48 object-cover" />
                <Badge
                  class="absolute top-2 right-2"
                  :class="room.status === 'available' ? 'bg-green-500' : 'bg-red-500'"
                >
                  {{ room.status === 'available' ? 'Available' : 'Unavailable' }}
                </Badge>
              </div>

              <!-- Room Details -->
              <div class="p-4">
                <h3 class="font-semibold text-lg">Room: {{ room.number }}</h3>
                <div class="mt-2 space-y-2">
                  <div class="flex items-center text-gray-600 dark:text-gray-300">
                    <DollarSign class="h-4 w-4 mr-2" />
                    <span>{{ room.price }} per night</span>
                  </div>
                  <div class="flex items-center text-gray-600 dark:text-gray-300">
                    <Users class="h-4 w-4 mr-2" />
                    <span>Capacity: {{ room.capacity }} guests</span>
                  </div>
                </div>

                <!-- Reservation Button -->
                <Button
                  class="w-full mt-4"
                  :class="room.status !== 'available' ? 'opacity-50 cursor-not-allowed' : ''"
                  :disabled="room.status !== 'available'"
                  @click="openReservationDialog(room)"
                >
                  Make Reservation
                </Button>
              </div>
            </div>
          </div>

          <!-- Empty state when no rooms -->
          <div v-if="roomsForCurrentFloor.length === 0" class="text-center p-8 bg-gray-50 dark:bg-gray-800/50 rounded-lg">
            <p class="text-gray-500 dark:text-gray-400">No rooms available on this floor</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Reservation Dialog -->
    <Dialog :open="reservationDialog" @update:open="reservationDialog = $event">
      <DialogContent>
        <DialogHeader>
          <DialogTitle>Make Reservation</DialogTitle>
          <DialogDescription v-if="selectedRoom">
            You are about to reserve Room {{ selectedRoom.number }} on Floor {{ selectedRoom.floor.number }}
          </DialogDescription>
        </DialogHeader>

        <div class="space-y-4 py-2">
          <!-- Error message -->
          <Alert v-if="errorMessage" class="bg-red-50 border-red-500 dark:bg-red-900/20 dark:border-red-600 dark:text-red-400">
            <AlertTitle class="text-red-700 dark:text-red-400">Error</AlertTitle>
            <AlertDescription class="text-red-600 dark:text-red-400">{{ errorMessage }}</AlertDescription>
          </Alert>

          <div class="space-y-2">
            <label for="accompany_number" class="text-sm font-medium">
              Number of Guests
            </label>
            <Input
              id="accompany_number"
              v-model.number="form.accompany_number"
              type="number"
              min="1"
              :max="selectedRoom?.capacity"
              @input="validateAccompanyNumber"
            />
            <p v-if="selectedRoom" class="text-xs text-gray-500 dark:text-gray-400">
              This room has a maximum capacity of {{ selectedRoom.capacity }} guests.
            </p>
          </div>

          <div class="space-y-2">
            <label for="check_out_at" class="text-sm font-medium">
              Check-out Date
            </label>
            <Input
              id="check_out_at"
              v-model="form.check_out_at"
              type="date"
              :min="new Date().toISOString().split('T')[0]"
            />
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="reservationDialog = false">
            Cancel
          </Button>
          <Button @click="makeReservation">
            Confirm Reservation
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  </div>
</template>

<style scoped>
.swiper {
  width: 100%;
}
</style>
