<template>
  <Head :title="pageTitle" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Managers</h1>
        <Button @click="goToCreateManager">Create Manager</Button>
      </div>

      <Card>
        <CardContent class="p-0">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Image</TableHead>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>National ID</TableHead>
                <TableHead>Created At</TableHead>
                <TableHead class="text-left">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="manager in managers.data"
                :key="manager.id"
                class="hover:bg-muted transition"
              >
                <TableCell>
                  <img 
                    :src="manager.avatar" 
                    :alt="manager.name"
                    class="w-10 h-10 rounded-full object-cover"
                  />
                </TableCell>
                <TableCell>{{ manager.name }}</TableCell>
                <TableCell>{{ manager.email }}</TableCell>
                <TableCell>{{ manager.national_id }}</TableCell>
                <TableCell>{{ formatDate(manager.created_at) }}</TableCell>
                <TableCell class="text-left space-x-2">
                  <Button variant="outline" size="sm" @click="editManager(manager.id)">
                    Edit
                  </Button>
                  <Button variant="destructive" size="sm" @click="openDeleteModal(manager.id)">
                    Delete
                  </Button>
                  <Button
                    :variant="manager.is_banned ? 'success' : 'secondary'"
                    size="sm"
                    @click="toggleBan(manager.id, manager.is_banned)"
                  >
                    {{ manager.is_banned ? 'Unban' : 'Ban' }}
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <div class="flex justify-end items-center space-x-2 mt-4">
        <Button
          variant="outline"
          size="sm"
          :disabled="!managers.prev_page_url"
          @click="changePage(managers.current_page - 1)"
        >
          Previous
        </Button>
        <span class="text-sm text-muted-foreground">
          Page {{ managers.current_page }} of {{ managers.last_page }}
        </span>
        <Button
          variant="outline"
          size="sm"
          :disabled="!managers.next_page_url"
          @click="changePage(managers.current_page + 1)"
        >
          Next
        </Button>
      </div>

      <!-- Delete Confirmation Dialog -->
      <AlertDialog :open="isDeleteModalOpen" @update:open="isDeleteModalOpen = $event">
        <AlertDialogContent>
          <AlertDialogHeader>
            <AlertDialogTitle>Are you sure?</AlertDialogTitle>
            <AlertDialogDescription>
              This action cannot be undone. This will permanently delete the manager's account.
            </AlertDialogDescription>
          </AlertDialogHeader>
          <AlertDialogFooter>
            <AlertDialogCancel @click="closeDeleteModal">Cancel</AlertDialogCancel>
            <AlertDialogAction @click="confirmDelete">Continue</AlertDialogAction>
          </AlertDialogFooter>
        </AlertDialogContent>
      </AlertDialog>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';

// Import shadcn-vue components
import { 
  Table, 
  TableHeader, 
  TableBody, 
  TableHead, 
  TableRow, 
  TableCell 
} from '@/components/ui/table';
import { 
  AlertDialog, 
  AlertDialogAction, 
  AlertDialogCancel, 
  AlertDialogContent, 
  AlertDialogDescription, 
  AlertDialogFooter, 
  AlertDialogHeader, 
  AlertDialogTitle 
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';

// Props
defineProps({
  managers: {
    type: Object,
    required: true
  }
});

// State
const isDeleteModalOpen = ref(false);
const managerToDelete = ref(null);

// Breadcrumbs
const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Manage Managers',
    href: '/managers',
  }
];

const pageTitle = 'Manage Managers';

// Methods
function goToCreateManager() {
  router.visit(route('managers.create'));
}

function editManager(managerId) {
  router.visit(route('managers.edit', managerId));
}

function openDeleteModal(managerId) {
  managerToDelete.value = managerId;
  isDeleteModalOpen.value = true;
}

function closeDeleteModal() {
  isDeleteModalOpen.value = false;
  managerToDelete.value = null;
}

function confirmDelete() {
  if (managerToDelete.value) {
    router.delete(route('managers.destroy', managerToDelete.value), {
      onSuccess: () => {
        closeDeleteModal();
      },
    });
  }
}

function toggleBan(managerId, isBanned) {
  const route = isBanned ? 'managers.unban' : 'managers.ban';
  router.post(route(route, managerId));
}

function changePage(page) {
  router.get(route('managers.index', { page }), {
    preserveScroll: true,
    only: ['managers'],
  });
}

// Format date to a readable string
function formatDate(timestamp) {
  if (!timestamp) return '';
  const date = new Date(timestamp);
  return date.toLocaleString('en-US', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
}
</script> 