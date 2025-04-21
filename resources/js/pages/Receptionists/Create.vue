<template>
 <Head :title="pageTitle" />

<AppLayout :breadcrumbs="breadcrumbs">
  <div class="max-w-md mx-auto space-y-6 p-6 bg-white rounded-2xl shadow-md">
    <h1 class="text-2xl font-semibold text-black">Create Receptionist</h1>

    <form @submit.prevent="createReceptionist" class="space-y-4">
      <!-- Email (required) -->
      <div class="space-y-1">
        <Label for="email">Email</Label>
        <Input id="email" v-model="form.email" type="email"  placeholder="email@example.com" />
        <div v-if="form.errors.email" class="text-red-600 text-sm">
          {{ form.errors.email }}
        </div>
      </div>

      <!-- National ID (required) -->
      <div class="space-y-1">
        <Label for="national_id">National ID</Label>
        <Input id="national_id" v-model="form.national_id"  placeholder="Enter national ID" />
        <div v-if="form.errors.national_id" class="text-red-600 text-sm">
          {{ form.errors.national_id }}
        </div>
      </div>

      <!-- Password (required) -->
      <div class="space-y-1">
        <Label for="password">Password</Label>
        <Input
          id="password"
          v-model="form.password"
          type="password"
          placeholder="••••••••"
        />
        <div v-if="form.errors.password" class="text-red-600 text-sm">
          {{ form.errors.password }}
        </div>
      </div>

      <!-- Confirm Password (required) -->
      <div class="space-y-1">
        <Label for="confirm_password">Confirm Password</Label>
        <Input
          id="confirm_password"
          v-model="form.confirm_password"
          type="password"
         
          placeholder="••••••••"
        />
        <div v-if="form.errors.confirm_password" class="text-red-600 text-sm">
          {{ form.errors.confirm_password }}
        </div>
      </div>

      <!-- Name (optional) -->
      <div class="space-y-1">
        <Label for="name">Name</Label>
        <Input id="name" v-model="form.name" placeholder="Full name (optional)" />
      </div>

      <!-- Avatar (optional) -->
      <div class="space-y-1">
        <Label for="avatar_image">Profile Picture</Label>
        <Input
          id="avatar_image"
          type="file"
          accept="image/*"
          @input="form.avatar_image = $event.target.files[0]"
        />
      </div>

      <!-- Submit -->
      <div class="pt-4">
        <Button type="submit" :disabled="form.processing">Create Receptionist</Button>
      </div>
    </form>
  </div>
</AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Button } from '@/components/ui/button';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Receptionists',
    href: '/receptionists',
  },
  {
    title: 'Create Receptionists',
    href: '/receptionists/create',
  }
];

const form = useForm({
  name: '',
  email: '',
  national_id: '',
  avatar_image: null,
  password: '',
  confirm_password: '',
});

// Function to validate password confirmation and submit form
const createReceptionist = () => {
  form.clearErrors(); // Clear previous errors before submitting

  // Validate password match
  if (form.password !== form.confirm_password) {
    form.setError('confirm_password', 'Passwords do not match');
    return;
  }

  // Submit form
  form.post(route('receptionists.store'), {
    onFinish: () => form.reset(),
  });
};
</script>
