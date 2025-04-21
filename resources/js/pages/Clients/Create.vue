<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';
import { router, useForm } from '@inertiajs/vue3';

// Props
const props = defineProps({
  countries: Array,
});

// Form data
const form = useForm({
  name: '',
  email: '',
  mobile: '',
  gender: 'Male',
  country_id: null,
  avatar_image: null,
  password: '',
  confirm_password: '',
});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Clients',
    href: '/clients',
  },
  {
    title: 'Create Client',
    href: '/clients/create',
  }
];

// Methods
const createClient = () => {
  if (form.password !== form.confirm_password) {
    alert('Passwords do not match!');
    return;
  }

  form.post(route('clients.store'), {
    onFinish: () => form.reset(),
  });
};

function handleFileInput(event) {
  form.avatar_image = event.target.files[0];
}
</script>

<template>
  <Head title="Create Client" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="max-w-md mx-auto space-y-6 p-6 bg-white rounded-2xl shadow-md">
        <h1 class="text-2xl font-semibold text-black">Create Client</h1>

        <form @submit.prevent="createClient" class="space-y-4">
          <!-- Name -->
          <div class="space-y-1">
            <label for="name" class="block text-sm font-medium text-black">Name</label>
            <input
              id="name"
              type="text"
              required
              autofocus
              v-model="form.name"
              placeholder="Full name"
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            />
          </div>

          <!-- Email -->
          <div class="space-y-1">
            <label for="email" class="block text-sm font-medium text-black">Email</label>
            <input
              id="email"
              type="email"
              required
              v-model="form.email"
              placeholder="email@example.com"
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            />
          </div>

          <!-- Mobile -->
          <div class="space-y-1">
            <label for="mobile" class="block text-sm font-medium text-black">Mobile Number</label>
            <input
              id="mobile"
              type="tel"
              required
              v-model="form.mobile"
              placeholder="+20 123 456 7890"
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            />
          </div>

          <!-- Gender -->
          <div class="space-y-1">
            <label for="gender" class="block text-sm font-medium text-black">Gender</label>
            <select
              id="gender"
              v-model="form.gender"
              required
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            >
              <option value="Male">Male</option>
              <option value="Female">Female</option>
            </select>
          </div>

          <!-- Country -->
          <div class="space-y-1">
            <label for="country" class="block text-sm font-medium text-black">Country</label>
            <select
              id="country"
              v-model="form.country_id"
              required
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            >
              <option
                v-for="country in countries"
                :key="country.id"
                :value="country.id"
              >
                {{ country.name }} ({{ country.iso_alpha_2 }})
              </option>
            </select>
          </div>

          <!-- Avatar -->
          <div class="space-y-1">
            <label for="avatar_image" class="block text-sm font-medium text-black">Profile Picture</label>
            <input
              id="avatar_image"
              type="file"
              required
              accept="image/jpeg,image/png,image/jpg"
              @input="handleFileInput"
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            />
          </div>

          <!-- Password -->
          <div class="space-y-1">
            <label for="password" class="block text-sm font-medium text-black">Password</label>
            <input
              id="password"
              type="password"
              required
              v-model="form.password"
              placeholder="••••••••"
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            />
          </div>

          <!-- Confirm Password -->
          <div class="space-y-1">
            <label for="confirm_password" class="block text-sm font-medium text-black">Confirm Password</label>
            <input
              id="confirm_password"
              type="password"
              required
              v-model="form.confirm_password"
              placeholder="••••••••"
              class="w-full px-4 py-2 border text-black rounded-xl focus:outline-none focus:ring-2 focus:ring-primary"
            />
          </div>

          <!-- Submit -->
          <div class="pt-4">
            <button
              type="submit"
              class="inline-flex items-center px-5 py-2.5 bg-primary text-black text-sm font-medium rounded-xl hover:bg-primary/90 transition"
              :disabled="form.processing"
            >
              Create Client
            </button>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>