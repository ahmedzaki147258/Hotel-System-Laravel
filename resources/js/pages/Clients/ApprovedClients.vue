<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
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
import { Button } from '@/components/ui/button';

// Props
const props = defineProps({
  clients: Array,
  pagination: Object,
  canEdit: {
    type: Boolean,
    default: false
  },
  userRole: String
});

// Pagination state
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
    accessorKey: 'approved_at',
    header: 'Approved Date',
    cell: (info) => formatDate(info.getValue()),
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

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'My Approved Clients',
    href: '/my-approved-clients',
  }
];

// Methods
function fetchData() {
  router.get(route('clients.approved'), {
    pageIndex: pagination.value.pageIndex,
    pageSize: pagination.value.pageSize,
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['clients', 'pagination'],
  });
}

function formatDate(dateString) {
  if (!dateString) return '';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
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
  <Head title="My Approved Clients" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">My Approved Clients</h1>
        
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
                <TableHead>Approved Date</TableHead>
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
                <TableCell>{{ formatDate(client.approved_at) }}</TableCell>
              </TableRow>
              
              <!-- No clients message -->
              <TableRow v-if="props.clients.length === 0">
                <TableCell :colspan="6" class="text-center py-8">
                  You haven't approved any clients yet
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
        
        <!-- Pagination Controls-->
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
      </div>
    </div>
  </AppLayout>
</template>