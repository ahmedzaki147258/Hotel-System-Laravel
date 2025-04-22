<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
  managerId: Number,
    floors: Array,
});

// Form data
const form = reactive({
  capacity: 0,
  price: 0,
  floor_number: null,
        manager_id: props.managerId,

});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Rooms',
    href: '/rooms',
  },
  {
    title: 'Create Room',
    href: `/rooms/create`,
  }
];

// Methods
function createRoom() {
  router.post(route('rooms.store'), {
    _method: 'post',
    ...form,
  });
}

</script>
<template>
      <Head title="Create Room" />
      <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

    <div class="max-w-md mx-auto space-y-6 p-6 bg-white rounded-2xl shadow-md">
      <h1 class="text-2xl font-semibold text-black">Create Room</h1>
  
      <form @submit.prevent="createRoom" class="space-y-4">
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
            v-model="form.floor_number"
            required
            class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
          >
            <option value="" disabled>Select a floor</option>
            <option v-for="floor in props.floors" :key="floor.id" :value="floor.number">{{ floor.name }}</option>
          </select>
        </div>
  
        <!-- Submit Button -->
        <div class="pt-4">
          <button
            type="submit"
            class="inline-flex items-center px-5 py-2.5 bg-primary text-black text-sm font-medium rounded-xl hover:bg-primary/90 transition"
          >
            Create Room
          </button>
        </div>
      </form>
    </div>
</div>


</AppLayout>

  </template>
  
  
  