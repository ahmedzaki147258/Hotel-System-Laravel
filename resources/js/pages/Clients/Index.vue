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
  clients: Array,
  userRole: String,
  canCreate: {
    type: Boolean,
    default: false
  },
  canDelete: {
    type: Boolean,
    default: false
  },
  isReceptionist: {
    type: Boolean,
    default: false
  },
  isAdmin: {
    type: Boolean,
    default: false
  },
  isManager: {
    type: Boolean,
    default: false
  }
});

// State
const isModalOpen = ref(false);
const clientToDelete = ref(null);

// Computed properties
const isAdminOrManager = computed(() => props.isAdmin || props.isManager);
const shouldShowActions = computed(() => isAdminOrManager.value || props.isReceptionist);

const pageTitle = computed(() => {
  if (props.isReceptionist) {
    return 'Pending Clients';
  } else {
    return 'Manage Clients';
  }
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: pageTitle.value,
    href: '/clients',
  }
];

// Methods
function goToCreateClient() {
  router.visit(route('clients.create'));
}

function editClient(clientId) {
  router.get(route('clients.edit', { client: clientId }));
}

function approveClient(clientId) {
  router.post(route('clients.approve', { client: clientId }));
}

function openDeleteModal(clientId) {
  clientToDelete.value = clientId;
  isModalOpen.value = true;
}

function closeModal() {
  clientToDelete.value = null;
  isModalOpen.value = false;
}

function confirmDelete() {
  if (clientToDelete.value) {
    router.delete(route('clients.destroy', clientToDelete.value));
  }
  closeModal();
}
</script>

<template>
  <Head :title="pageTitle" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">{{ pageTitle }}</h1>

        <!-- Create Client Button -->
        <Button v-if="canCreate" @click="goToCreateClient" variant="default">
          Create Client
        </Button>

        <!-- Clients Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Name</TableHead>
                <TableHead>Email</TableHead>
                <TableHead>Mobile</TableHead>
                <TableHead>Country</TableHead>
                <TableHead>Gender</TableHead>
                <TableHead>Status</TableHead>
                <TableHead v-if="shouldShowActions">Actions</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="client in clients"
                :key="client.id"
                class="hover:bg-gray-50 transition"
              >
                <TableCell class="font-medium">{{ client.name }}</TableCell>
                <TableCell>{{ client.email }}</TableCell>
                <TableCell>{{ client.mobile }}</TableCell>
                <TableCell>{{ client.country }}</TableCell>
                <TableCell>{{ client.gender }}</TableCell>
                <TableCell>
                  <Badge v-if="client.approved_by" variant="success">Approved</Badge>
                  <Badge v-else variant="secondary">Pending</Badge>
                </TableCell>
                <TableCell v-if="shouldShowActions" class="space-x-2">
                  <!-- Approve Button (for unapproved clients) -->
                  <Button 
                    v-if="!client.approved_by" 
                    @click="approveClient(client.id)"
                    variant="success"
                    size="sm"
                  >
                    Approve
                  </Button>
                  
                  <!-- Edit Button (for admin/manager only) -->
                  <Button
                    v-if="isAdminOrManager"
                    @click="editClient(client.id)"
                    variant="secondary"
                    size="sm"
                  >
                    Edit
                  </Button>
                  
                  <!-- Delete Button (for admin/manager only) -->
                  <Button
                    v-if="canDelete"
                    @click="openDeleteModal(client.id)"
                    variant="destructive"
                    size="sm"
                  >
                    Delete
                  </Button>
                </TableCell>
              </TableRow>
              
              <!-- No clients message -->
              <TableRow v-if="clients.length === 0">
                <TableCell :colspan="shouldShowActions ? 7 : 6" class="text-center py-8">
                  No clients found
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Delete Confirmation Modal -->
        <AlertDialog :open="isModalOpen" @update:open="closeModal">
          <AlertDialogContent>
            <AlertDialogHeader>
              <AlertDialogTitle>Delete Client</AlertDialogTitle>
              <AlertDialogDescription>
                Are you sure you want to delete this client? This action cannot be undone.
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