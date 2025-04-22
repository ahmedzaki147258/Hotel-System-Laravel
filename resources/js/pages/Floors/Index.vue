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
  floors: Array,
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
const floorToDelete = ref(null)

// Computed
const pageTitle = 'Manage Floors'

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: pageTitle,
    href: '/floors',
  }
];

// Methods
function goToCreate() {
  router.visit(route('floors.create'))
}

function editFloor(floorId) {
  router.get(route('floors.edit', { floor: floorId }))
}

function openDeleteModal(floorId) {
  isModalOpen.value = true
  floorToDelete.value = floorId
}

function closeModal() {
  isModalOpen.value = false
  floorToDelete.value = null
}

function confirmDelete() {
  if (floorToDelete.value) {
    router.delete(route('floors.destroy', floorToDelete.value))
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
            Create Floor
          </Button>
  
          <!-- Table -->
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
                    <TableRow v-for="(floor, index) in floors" :key="floor.id">
                        <TableCell>{{ floor.name }}</TableCell>
                        <TableCell>{{ floor.number }}</TableCell>
                        <TableCell v-if="isAdmin">{{ floor.manager_name }}</TableCell>
                        <TableCell v-if="currentId ==  floor.manager_id || isAdmin" class="space-x-2">
                            <Button @click="editFloor(floor.id)" size="sm" variant="secondary">Edit</Button>
                            <Button @click="openDeleteModal(floor.id)" size="sm" variant="destructive">Delete</Button>
                        </TableCell>
                        <TableCell v-else class="space-x-2">
                            <Badge variant="destructive">Not Allowed</Badge>
                        </TableCell>
                    </TableRow>
                    <TableRow v-if="floors.length === 0">
                        <TableCell :colspan="3" class="text-center py-8">No floors found. </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
            </div>
              
            
  
          <!-- Delete Modal -->
          <AlertDialog :open="isModalOpen" @update:open="closeModal">
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
  
