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
  reservations: Array,
  pagination: Object,
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
    accessorKey: 'client_name',
    header: 'Client Name',
  },
  {
    accessorKey: 'room_number',
    header: 'Room Number',
  },
  {
    accessorKey: 'floor_number',
    header: 'Floor Number',
  },
  {
    accessorKey: 'accompany_number',
    header: 'Accompany Number',
  },
  {
    accessorKey: 'paid_price',
    header: 'Paid Price',
    cell: (info) => formatCurrency(info.getValue()),
  },
  {
    accessorKey: 'check_out_at',
    header: 'Check Out Date',
    cell: (info) => formatDate(info.getValue()),
  },
  {
    accessorKey: 'created_at',
    header: 'Reservation Date',
    cell: (info) => formatDate(info.getValue()),
  },
];

// Create table instance
const table = createTable({
  get data() {
    return props.reservations || [];
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
    title: 'Client Reservations',
    href: '/reservations',
  }
];

// Methods
function fetchData() {
  router.get(route('reservations.index'), {
    pageIndex: pagination.value.pageIndex,
    pageSize: pagination.value.pageSize,
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['reservations', 'pagination'],
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

function formatCurrency(amount) {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD' // Change to your currency code if needed
  }).format(amount);
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
  <Head title="Client Reservations" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
      <div class="p-6 space-y-6">
        <h1 class="text-2xl font-semibold">
          {{ userRole === 'receptionist' ? 'My Approved Clients Reservations' : 'All Client Reservations' }}
        </h1>
        
        <!-- Reservations Table -->
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
          <Table>
            <TableHeader>
              <TableRow>
                <TableHead>Client Name</TableHead>
                <TableHead>Room Number</TableHead>
                <TableHead>Floor Number</TableHead>
                <TableHead>Accompany Number</TableHead>
                <TableHead>Paid Price</TableHead>
                <TableHead>Check Out Date</TableHead>
                <TableHead>Reservation Date</TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow
                v-for="reservation in props.reservations"
                :key="reservation.id"
                class="hover:bg-gray-50 transition"
              >
                <TableCell class="font-medium">{{ reservation.client_name }}</TableCell>
                <TableCell>{{ reservation.room_number }}</TableCell>
                <TableCell>{{ reservation.floor_number }}</TableCell>
                <TableCell>{{ reservation.accompany_number }}</TableCell>
                <TableCell>{{ formatCurrency(reservation.paid_price) }}</TableCell>
                <TableCell>{{ formatDate(reservation.check_out_at) }}</TableCell>
                <TableCell>{{ formatDate(reservation.created_at) }}</TableCell>
              </TableRow>
              
              <!-- No reservations message -->
              <TableRow v-if="props.reservations.length === 0">
                <TableCell colspan="7" class="text-center py-8">
                  {{ userRole === 'receptionist' ?
                    "Your approved clients don't have any reservations yet" :
                    "No reservations found" }}
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
      </div>
    </div>
  </AppLayout>
</template>