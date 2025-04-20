<template>
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
            <TableHead>Actions</TableHead>
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
            <TableCell class="space-x-2">
              <!-- Approve Button (for receptionists and unapproved clients) -->
              <Button 
                v-if="isReceptionist && !client.approved_by" 
                @click="approveClient(client.id)"
                variant="success"
                size="sm"
              >
                Approve
              </Button>
              
              <!-- View Button -->
              <Button
                @click="viewClient(client.id)"
                variant="outline"
                size="sm"
              >
                View
              </Button>
              
              <!-- Edit Button -->
              <Button
                v-if="canEdit(client)"
                @click="editClient(client.id)"
                variant="secondary"
                size="sm"
              >
                Edit
              </Button>
              
              <!-- Delete Button -->
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
            <TableCell colspan="7" class="text-center py-8">
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
</template>

<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
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
});

// State
const isModalOpen = ref(false);
const clientToDelete = ref(null);

// Computed properties
const isManager = computed(() => props.userRole === 'manager' || props.userRole === 'admin');
const isReceptionist = computed(() => props.userRole === 'receptionist');
const canCreate = computed(() => true); // Everyone can create clients as per policy
const canDelete = computed(() => isManager.value);

const pageTitle = computed(() => {
  if (isReceptionist.value) {
    return 'Pending Clients';
  } else {
    return 'Manage Clients';
  }
});

// Methods
function goToCreateClient() {
  router.visit(route('clients.create'));
}

function viewClient(clientId) {
  router.get(route('clients.show', { client: clientId }));
}

function editClient(clientId) {
  router.get(route('clients.edit', { client: clientId }));
}

function approveClient(clientId) {
  router.post(route('clients.approve', { client: clientId }));
}

function canEdit(client) {
  if (isManager.value) {
    return true;
  }
  
  if (isReceptionist.value && client.approved_by) {
    return true;
  }
  
  return false;
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
  router.delete(route('clients.destroy', clientToDelete.value));
  closeModal();
}
</script>