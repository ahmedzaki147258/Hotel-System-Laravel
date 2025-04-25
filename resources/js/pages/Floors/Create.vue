<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
    managerId: Number,
    nextFloorNumber: Number,
});

// Form data
const form = reactive({
        name: '',
        number: props.nextFloorNumber,
        manager_id: props.managerId,

});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Floors',
    href: '/floors',
  },
  {
    title: 'Create Floor',
    href: `/floors/create`,
  }
];

// Methods
function createFloor() {
  router.post(route('floors.store'), {
    _method: 'post',
    ...form,
  });
}

</script>
<template>
      <Head title="Create Floor" />
      <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">

    <div class="max-w-md mx-auto space-y-6 p-6 bg-white rounded-2xl shadow-md">
      <h1 class="text-2xl font-semibold text-black">Create Floor</h1>
  
      <form @submit.prevent="createFloor" class="space-y-4">
        <!-- Floor Name -->
        <div class="space-y-1">
          <label for="name" class="block text-sm font-medium text-black">Floor Name</label>
          <input
            id="name"
            type="text"
            v-model="form.name"
            required
            min="3"
            class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
          />
        </div>
  
        <!-- Submit Button -->
        <div class="pt-4">
          <button
            type="submit"
            class="inline-flex items-center px-5 py-2.5 bg-primary text-black text-sm font-medium rounded-xl hover:bg-primary/90 transition"
          >
            Create Floor
          </button>
        </div>
      </form>
    </div>
</div>


</AppLayout>

  </template>
  
  
  