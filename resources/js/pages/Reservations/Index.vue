<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

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
  userRole: String
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Client Reservations',
    href: '/reservations',
  }
];

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
                v-for="reservation in reservations"
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
              <TableRow v-if="reservations.length === 0">
                <TableCell colspan="7" class="text-center py-8">
                  {{ userRole === 'receptionist' ? 
                    "Your approved clients don't have any reservations yet" : 
                    "No reservations found" }}
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>