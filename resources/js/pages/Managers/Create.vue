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
                />
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
                />
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
                />
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
                />
              </div>

              <div class="space-y-2">
                <Label for="avatar_image">Profile Image</Label>
                <Input
                  id="avatar_image"
                  type="file"
                  accept="image/*"
                  @change="handleImageUpload"
                  :disabled="form.processing"
                />
                <p class="text-sm text-muted-foreground">
                  Accepted formats: JPG, PNG, JPEG. Max size: 2MB
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
  form.post(route('managers.store'));
};

const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.avatar_image = file;
  }
};
</script> 