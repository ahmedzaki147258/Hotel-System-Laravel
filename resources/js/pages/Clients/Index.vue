<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { 
  createTable,
  getCoreRowModel,
  PaginationState,
} from '@tanstack/vue-table';

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
  pagination: Object,
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
const pagination = ref<PaginationState>({
  pageIndex: props.pagination?.pageIndex || 0,
  pageSize: props.pagination?.pageSize || 5, 
});

// Column definitions
const columns = [
  {
    accessorKey: 'name',
    header: 'Name',
  },
  {
    accessorKey: 'email',
    header: 'Email',
  },
  {
    accessorKey: 'mobile',
    header: 'Mobile',
  },
  {
    accessorKey: 'country',
    header: 'Country',
  },
  {
    accessorKey: 'gender',
    header: 'Gender',
  },
  {
    accessorKey: 'status',
    header: 'Status',
    cell: (info) => (info.row.original.approved_by ? 'Approved' : 'Pending'),
  },
];

// Create table instance
const table = createTable({
  get data() {
    return props.clients || [];
  },
  columns,
  state: {
    get pagination() {
      return pagination.value;
    },
  },
  manualPagination: true,
  pageCount: props.pagination?.pageCount || 0,
  getCoreRowModel: getCoreRowModel(),
  onPaginationChange: (updaterOrValue) => {
    if (typeof updaterOrValue === 'function') {
      pagination.value = updaterOrValue(pagination.value);
    } else {
      pagination.value = updaterOrValue;
    }
    fetchData();
  },
});

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
function fetchData() {
  router.get(route('clients.index'), {
    pageIndex: pagination.value.pageIndex,
    pageSize: pagination.value.pageSize,
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['clients', 'pagination'],
  });
}

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
  isModalOpen.value = false;
}

function confirmDelete() {
  if (clientToDelete.value) {
    router.delete(route('clients.destroy', { client: clientToDelete.value }), {
      onSuccess: () => {
        isModalOpen.value = false;
        clientToDelete.value = null;
      },
      onError: () => {
        console.error('Failed to delete client');
      },
    });
  }
}

// Pagination actions
function goToFirstPage() {
  table.setPageIndex(0);
}

function goToPreviousPage() {
  table.previousPage();
}

function goToNextPage() {
  table.nextPage();
}

function goToLastPage() {
  table.setPageIndex(table.getPageCount() - 1);
}
</script>

<template>
  <Head :title="pageTitle" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
          <h1 class="text-2xl font-semibold">{{ pageTitle }}</h1>
          <!-- Create Client Button -->
          <Button v-if="canCreate" @click="goToCreateClient" variant="default">
            Create Client
          </Button>
        </div>
        
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
                v-for="client in props.clients"
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

                  <!-- Approve Button (for unapproved clients) -->
                  <Button 
                    v-if="!client.approved_by" 
                    @click="approveClient(client.id)"
                    variant="success"
                    size="sm"
                  >
                    Approve
                  </Button>
                </TableCell>
              </TableRow>
              
              <!-- No clients message -->
              <TableRow v-if="props.clients.length === 0">
                <TableCell :colspan="shouldShowActions ? 7 : 6" class="text-center py-8">
                  No clients found
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>

        <!-- Pagination Controls -->
        <div class="flex items-center justify-end">
          <div class="flex items-center gap-1">
            <span class="text-sm text-gray-600 mr-2">
              Page {{ pagination.pageIndex + 1 }} of {{ props.pagination?.pageCount || 1 }}
            </span>
            <Button
              variant="outline"
              size="sm"
              @click="goToFirstPage"
              :disabled="!table.getCanPreviousPage()"
            >
              First
            </Button>
            <Button
              variant="outline"
              size="sm"
              @click="goToPreviousPage"
              :disabled="!table.getCanPreviousPage()"
            >
              Previous
            </Button>
            <Button
              variant="outline"
              size="sm"
              @click="goToNextPage"
              :disabled="!table.getCanNextPage()"
            >
              Next
            </Button>
            <Button
              variant="outline"
              size="sm"
              @click="goToLastPage"
              :disabled="!table.getCanNextPage()"
            >
              Last
            </Button>
          </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <AlertDialog :open="isModalOpen">
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