<template>
  <Head title="Create Manager" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Create Manager</h1>
      </div>

      <Card>
        <CardContent class="pt-6">
          <form @submit.prevent="submit" class="space-y-6">
            <div class="space-y-4">
              <div class="space-y-2">
                <Label for="name">Name</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  type="text"
                  placeholder="Enter manager name"
                  :disabled="form.processing"
                  required
                  :class="{ 'border-red-500': form.errors.name }"
                />
                <p v-if="form.errors.name" class="text-sm text-red-500 mt-1">
                  {{ form.errors.name }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="email">Email</Label>
                <Input
                  id="email"
                  v-model="form.email"
                  type="email"
                  placeholder="Enter manager email"
                  :disabled="form.processing"
                  required
                  :class="{ 'border-red-500': form.errors.email }"
                />
                <p v-if="form.errors.email" class="text-sm text-red-500 mt-1">
                  {{ form.errors.email }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="name">National ID</Label>
                <Input
                  id="national_id"
                  v-model="form.national_id"
                  type="text"
                  placeholder="Enter manager national ID"
                  :disabled="form.processing"
                  required
                  :class="{ 'border-red-500': form.errors.national_id }"
                />
                <p v-if="form.errors.national_id" class="text-sm text-red-500 mt-1">
                  {{ form.errors.national_id }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="password">Password</Label>
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  placeholder="Enter manager password"
                  :disabled="form.processing"
                  required
                  :class="{ 'border-red-500': form.errors.password }"
                />
                <p v-if="form.errors.password" class="text-sm text-red-500 mt-1">
                  {{ form.errors.password }}
                </p>
              </div>

              <div class="space-y-2">
                <Label for="avatar_image">Profile Image</Label>
                <Input
                  id="avatar_image"
                  type="file"
                  accept="image/*"
                  @change="handleImageUpload"
                  :disabled="form.processing"
                  :class="{ 'border-red-500': form.errors.avatar_image }"
                />
                <p class="text-sm text-muted-foreground">
                  Accepted formats: JPG, PNG, JPEG. Max size: 2MB
                </p>
                <p v-if="form.errors.avatar_image" class="text-sm text-red-500 mt-1">
                  {{ form.errors.avatar_image }}
                </p>
              </div>
            </div>

            <div class="flex justify-end space-x-4">
              <Button
                variant="outline"
                :disabled="form.processing"
                @click="router.visit(route('managers.index'))"
              >
                Cancel
              </Button>
              <Button type="submit" :disabled="form.processing">
                Create Manager
              </Button>
            </div>
          </form>
        </CardContent>
      </Card>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { useForm } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Import shadcn-vue components
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const form = useForm({
  name: '',
  email: '',
  password: '',
  national_id: '',
  avatar_image: null,
});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Managers',
    href: '/managers',
  },
  {
    title: 'Create Manager',
    href: '/managers/create',
  }
];

const submit = () => {
  form.post(route('managers.store'), {
    onError: (errors) => {
      console.log('Validation errors:', errors);
    },
    preserveScroll: true
  });
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      form.errors.avatar_image = 'Image size must be less than 2MB';
      event.target.value = ''; // Clear the file input
      return;
    }
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!validTypes.includes(file.type)) {
      form.errors.avatar_image = 'Only JPG, PNG, and JPEG formats are allowed';
      event.target.value = ''; // Clear the file input
      return;
    }
    
    form.avatar_image = file;
    form.errors.avatar_image = null; // Clear any previous errors
  }
};
</script>