<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { reactive } from 'vue';
import { router } from '@inertiajs/vue3';

// Props
const props = defineProps({
floor: Object,
});

// Form data
const form = reactive({
  ...props.floor,

});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Floors',
    href: '/floors',
  },
  {
    title: 'Edit Floor',
    href: `/floors/${props.floor.id}/edit`,
  }
];

// Methods
function updateFloor() {
  router.post(route('floors.update', props.floor.id), {
    _method: 'put',
      name: form.name, 
  });
}

</script>

<template>
  <Head title="Edit Floor" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="max-w-md mx-auto space-y-6 p-6 bg-white rounded-2xl shadow-md">
        <h1 class="text-2xl font-semibold text-black">Edit Floor</h1>

        <form @submit.prevent="updateFloor" class="space-y-4">
          <!-- Name -->
          <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-black">Name</label>
            <input
              id="name"
              type="text"
              required
              v-model="form.name"
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            />
          </div>

          
          <!-- Submit -->
          <div class="pt-4">
            <button
              type="submit"
              class="inline-flex items-center px-5 py-2.5 bg-primary text-black text-sm font-medium rounded-xl hover:bg-primary/90 transition"
            >
              Update Floor
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>