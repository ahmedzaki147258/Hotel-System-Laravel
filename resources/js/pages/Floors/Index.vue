<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

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
import { Badge } from '@/components/ui/badge';

const props = defineProps({
  floors: Object,
  userRole: String,
  isAdmin: {
    type: Boolean,
    default: false
  },
  isManager: {
    type: Boolean,
    default: false
  },
  currentId: {
    type: Number,
    default: null
  },
});

const isModalOpen = ref(false);
const floorToDelete = ref(null);

const pageTitle = 'Manage Floors';

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: pageTitle,
    href: '/floors',
  }
];

function goToCreate() {
  router.visit(route('floors.create'));
}

function editFloor(floorId) {
  router.get(route('floors.edit', { floor: floorId }));
}

function openDeleteModal(floorId) {
  isModalOpen.value = true;
  floorToDelete.value = floorId;
}

function closeModal() {
  isModalOpen.value = false;
  floorToDelete.value = null;
}

function confirmDelete() {
  if (floorToDelete.value) {
    console.log(`Deleting floor with ID: ${floorToDelete.value}`);
    
    router.delete(route('floors.destroy', floorToDelete.value))
      .then(() => {
        console.log(`Floor with ID ${floorToDelete.value} has been deleted`);

        closeModal();

        fetchUpdatedData();
      })
      .catch((error) => {
        console.error('Error deleting floor:', error);
        alert('Error deleting floor. Please try again.');
      });
  } else {
    closeModal();
  }
}

function fetchUpdatedData() {
  router.get(route('floors.index'), {
    preserveScroll: true,
    only: ['floors'],  
  });
}

function changePage(page: number) {
  router.get(route('floors.index', { page }), {
    preserveScroll: true,
    only: ['floors'],
  });
}
</script>

<template>
  <Head :title="pageTitle" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-col gap-4 rounded-xl p-4">
      <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">{{ pageTitle }}</h1>

        <Button @click="goToCreate" variant="default">
          Create Floor
        </Button>

        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm mt-4">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Number</TableHead>
                <TableHead v-if="isAdmin">Manager Name</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="(floor, index) in floors.data" :key="floor.id">
                <TableCell>{{ floor.name }}</TableCell>
                <TableCell>{{ floor.number }}</TableCell>
                <TableCell v-if="isAdmin">{{ floor.manager_name }}</TableCell>
                <TableCell v-if="currentId == floor.manager_id || isAdmin" class="space-x-2">
                  <Button @click="editFloor(floor.id)" size="sm" variant="secondary">Edit</Button>
                  <div class="relative group inline-block">
                    <Button :disabled="floor.room_count != 0" @click="openDeleteModal(floor.id)" size="sm"
                      variant="destructive">
                      Delete
                    </Button>
                    <div v-if="floor.room_count != 0"
                      class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-gray-700 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                      Floor cannot be deleted because it has rooms assigned to it.
                    </div>
                  </div>
                </TableCell>
                <TableCell v-else class="space-x-2">
                  <Badge variant="destructive">Not Allowed</Badge>
                </TableCell>
              </TableRow>
              <TableRow v-if="floors.data.length === 0">
                <TableCell :colspan="4" class="text-center py-8">No floors found.</TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <div class="flex justify-end items-center space-x-2 mt-4">
          <Button variant="outline" size="sm" :disabled="!floors.prev_page_url"
            @click="changePage(floors.current_page - 1)">
            Previous
          </Button>
          <span class="text-sm text-muted-foreground">
            Page {{ floors.current_page }} of {{ floors.last_page }}
          </span>
          <Button variant="outline" size="sm" :disabled="!floors.next_page_url"
            @click="changePage(floors.current_page + 1)">
            Next
          </Button>
        </div>

        <AlertDialog v-model:open="isModalOpen">
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Delete Floor</AlertDialogTitle>
              <AlertDialogDescription>
                Are you sure you want to delete this Floor? This action cannot be undone.
              </AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
              <AlertDialogCancel @click="closeModal">Cancel</AlertDialogCancel>
              <AlertDialogAction @click="confirmDelete">Delete</AlertDialogAction>
            </AlertDialogFooter>
          </AlertDialogContent>
        </AlertDialog>
      </div>
    </div>
  </AppLayout>
</template>
