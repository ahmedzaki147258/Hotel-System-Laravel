<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';

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
import { Badge } from '@/components/ui/badge';
// Props
const props = defineProps({
  rooms: Array,
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

// State
const isModalOpen = ref(false)
const roomToDelete = ref(null)

// Computed
const pageTitle = 'Manage Rooms'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: pageTitle,
    href: '/rooms',
  }
];

// Methods
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
  isModalOpen.value = false
  roomToDelete.value = null
}

function confirmDelete() {
  if (roomToDelete.value) {
    router.delete(route('rooms.destroy', roomToDelete.value))
  }
  closeModal()
}
</script>

<template>
    <Head :title="pageTitle" />
  
    <AppLayout :breadcrumbs="breadcrumbs">
      <div class="flex h-full flex-col gap-4 rounded-xl p-4">
        <div class="p-6 space-y-6">
          <h1 class="text-2xl font-semibold">{{ pageTitle }}</h1>
  
          <!-- Create Button -->
          <Button @click="goToCreate" variant="default">
            Create Room
          </Button>
  
          <!-- Table -->
          <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm mt-4">
            <Table>
                <TableHeader>
                    <TableRow>
                        <TableHead>Number</TableHead>
                        <TableHead>Capacity</TableHead>
                        <TableHead>Price</TableHead>
                        <TableHead>Floor Number</TableHead>

                        <TableHead v-if="isAdmin">Manager Name</TableHead>
                        <TableHead>Actions</TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(room, index) in rooms" :key="room.id">
                        <TableCell>{{ room.number }}</TableCell>
                        <TableCell>{{ room.capacity }}</TableCell>
                        <TableCell>{{ room.price }}</TableCell>

                        <TableCell v-if="isAdmin">{{ room.manager_name }}</TableCell>
                        <TableCell v-if="currentId ==  room.manager_id || isAdmin" class="space-x-2">
                            <Button @click="editRoom(room.id)" size="sm" variant="secondary">Edit</Button>
                            <Button @click="openDeleteModal(room.id)" size="sm" variant="destructive">Delete</Button>
                        </TableCell>
                        <TableCell v-else class="space-x-2">
                            <Badge variant="destructive">Not Allowed</Badge>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="rooms.length === 0">
                        <TableCell :colspan="3" class="text-center py-8">No rooms found. </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            </div>
              
            
  
          <!-- Delete Modal -->
          <AlertDialog :open="isModalOpen" @update:open="closeModal">
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
  
