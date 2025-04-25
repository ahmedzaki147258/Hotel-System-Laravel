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
  rooms: {
    type: Object,
    required: true,
  },
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

const isModalOpen = ref(false)
const roomToDelete = ref(null)

const pageTitle = 'Manage Rooms'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: pageTitle,
    href: '/rooms',
  }
];

function goToCreate() {
  router.visit(route('rooms.create'))
}

function editRoom(roomId) {
  router.get(route('rooms.edit', { room: roomId }))
}

function openDeleteModal(roomId) {
  isModalOpen.value = true
  roomToDelete.value = roomId
}

function closeModal() {
  console.log(`Closing modal for room ID: ${roomToDelete.value}`);
  isModalOpen.value = false
  roomToDelete.value = null
}

function confirmDelete() {
  if (roomToDelete.value) {
    console.log(`Deleting room with ID: ${roomToDelete.value}`);
    
    router.delete(route('rooms.destroy', roomToDelete.value))
      .then(() => {
        console.log(`Room with ID ${roomToDelete.value} has been deleted`);

        closeModal();

        fetchUpdatedData();
      })
      .catch((error) => {
        console.error('Error deleting room:', error);
        alert('Error deleting room. Please try again.');
      });
  } else {
    closeModal();
  }
}

function fetchUpdatedData() {
  router.get(route('rooms.index'), {
    preserveScroll: true,
    only: ['rooms'],  
  });
}


function changePage(page: number) {
  router.get(route('rooms.index', { page }), {
    preserveScroll: true,
    only: ['rooms'],
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
          Create Room
        </Button>

        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm mt-4">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Number</TableHead>
                <TableHead>Capacity</TableHead>
                <TableHead>Price</TableHead>
                <TableHead>Floor Name</TableHead>

                <TableHead v-if="isAdmin">Manager Name</TableHead>
                <TableHead>Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="(room, index) in rooms.data" :key="room.id">
                <TableCell>{{ room.number }}</TableCell>
                <TableCell>{{ room.capacity }}</TableCell>
                <TableCell>${{ (room.price / 100).toFixed(2) }}</TableCell>
                <TableCell>{{ room.floor_name }}</TableCell>

                <TableCell v-if="isAdmin">{{ room.manager_name }}</TableCell>
                <TableCell v-if="currentId == room.manager_id || isAdmin" class="space-x-2">
                  <Button @click="editRoom(room.id)" size="sm" variant="secondary">Edit</Button>
                  <div class="relative group inline-block">
                    <Button :disabled="room.status == 'unavailable'" @click="openDeleteModal(room.id)" size="sm"
                      variant="destructive">
                      Delete
                    </Button>
                    <div v-if="room.status == 'unavailable'"
                      class="absolute bottom-full mb-1 left-1/2 -translate-x-1/2 bg-gray-700 text-white text-xs rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-10">
                      Currently Unavailable, can't delete
                    </div>
                  </div>
                </TableCell>
                <TableCell v-else class="space-x-2">
                  <Badge variant="destructive">Not Allowed</Badge>
                </TableCell>
              </TableRow>
              <TableRow v-if="rooms.data.length === 0">
                <TableCell :colspan="3" class="text-center py-8">No rooms found. </TableCell>
              </TableRow>
            </TableBody>

          </Table>
        </div>

        <div class="flex justify-end items-center space-x-2 mt-4">
          <Button variant="outline" size="sm" :disabled="rooms.current_page === 1"
            @click="changePage(rooms.current_page - 1)">
            Previous
          </Button>
          <span class="text-sm text-muted-foreground">
            Page {{ rooms.current_page }} of {{ rooms.last_page }}
          </span>
          <Button variant="outline" size="sm" :disabled="rooms.current_page === rooms.last_page"
            @click="changePage(rooms.current_page + 1)">
            Next
          </Button>
        </div>

        <AlertDialog v-model:open="isModalOpen">
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Delete Room</AlertDialogTitle>
              <AlertDialogDescription>
                Are you sure you want to delete this Room? This action cannot be undone.
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
