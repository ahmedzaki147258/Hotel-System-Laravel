<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
  room: Object,
  floors: Array,

});

// Form data
const form = reactive({
  
  capacity: props.room.capacity,
  price: props.room.price/100,
  floor_id: props.room.floor_id,

});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Rooms',
    href: '/rooms',
  },
  {
    title: 'Edit Room',
    href: `/rooms/${props.room.id}/edit`,
  }
];

// Methods
function updateRoom() {
  router.post(route('rooms.update', props.room.id), {
    _method: 'put',
    capacity: form.capacity,
    price: form.price,
    floor_id: form.floor_id, 
  });
}

</script>

<template>
  <Head title="Edit Room" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="max-w-md mx-auto space-y-6 p-6 bg-white rounded-2xl shadow-md">
        <h1 class="text-2xl font-semibold text-black">Edit Room</h1>

        <form @submit.prevent="updateRoom" class="space-y-4">
      <!-- Capacity -->
      <div class="space-y-1">
          <label for="name" class="block text-sm font-medium text-black">Room Capacity</label>
          <input
            id="capacity"
            type="number"
            v-model="form.capacity"
            required
            min="1" max="5"
            class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>

        <!-- Price -->
        <div class="space-y-1">
          <label for="name" class="block text-sm font-medium text-black">Room Price (in dollars)</label>
          <input
            id="price"
            type="number"
            v-model="form.price"
            required
            step="0.01" min="0.5"
            class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>
        
        <!-- Floor Name -->
        <div class="space-y-1">
          <label for="floor_id" class="block text-sm font-medium text-black">Floor</label>
          <select
            id="floor_id"
            v-model="form.floor_id"
            required
            class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
          >
            <option value="" disabled>Select a floor</option>
            <option v-for="floor in props.floors" :key="floor.id" :value="floor.id">{{ floor.name }}</option>
          </select>
        </div>

          <!-- Submit -->
          <div class="pt-4">
            <button
              type="submit"
              class="inline-flex items-center px-5 py-2.5 bg-primary text-black text-sm font-medium rounded-xl hover:bg-primary/90 transition"
            >
              Update Room
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>