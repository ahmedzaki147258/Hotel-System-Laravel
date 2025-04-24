<template>
  <Head title="Edit Manager" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Edit Manager</h1>
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
                <Label for="national_id">National ID</Label>
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
                <Label for="avatar">Profile Image</Label>
                <div class="flex items-center space-x-4">
                  <div v-if="form.avatar_image || props.manager.avatar" class="relative w-20 h-20">
                    <img
                      :src="form.avatar_image ? window.URL.createObjectURL(form.avatar_image) : props.manager.avatar"
                      alt="Profile preview"
                      class="w-full h-full object-cover rounded-full"
                    />
                  </div>
                  <Input
                    id="avatar"
                    type="file"
                    accept="image/*"
                    @change="(e) => form.avatar_image = e.target.files[0]"
                    :disabled="form.processing"
                    class="flex-1"
                  />
                </div>
                <p class="text-sm text-muted-foreground">
                  Upload a new profile image (optional)
                </p>
              </div>

              <div class="space-y-2">
                <Label for="password">Password</Label>
                <Input
                  id="password"
                  v-model="form.password"
                  type="password"
                  placeholder="Leave blank to keep current password"
                  :disabled="form.processing"
                />
                <p class="text-sm text-muted-foreground">
                  Leave blank to keep the current password
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
                Update Manager
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

const props = defineProps<{
  manager: {
    id: number;
    name: string;
    email: string;
    national_id: string;
    avatar: string;
  }
}>();

const form = useForm({
  name: props.manager.name,
  email: props.manager.email,
  national_id: props.manager.national_id,
  password: '',
  avatar_image: null,
});

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Managers',
    href: '/managers',
  },
  {
    title: 'Edit Manager',
    href: `/managers/${props.manager.id}/edit`,
  }
];

const submit = () => {
  router.post(route('managers.update', props.manager.id), {
    _method: 'put',
    ...form,
  });
};
</script> 