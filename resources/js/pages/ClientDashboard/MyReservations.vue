<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow
} from '@/components/ui/table';
import { Button } from '@/components/ui/button';
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select';
import {
  Pagination,
  PaginationEllipsis,
  PaginationItem,
} from '@/components/ui/pagination';

interface Reservation {
  id: number;
  client_id: number;
  accompany_number: number;
  paid_price_in_cents: number;
  paid_price_in_dollars: string;
  payment_id: string;
  created_at: string;
  check_out_at: string;
}

const reservations = ref<Reservation[]>([]);
const loading = ref(false);
const pagination = ref({
  currentPage: 1,
  lastPage: 1,
  perPage: 10,
  total: 0
});

const fetchReservations = async (page: number = 1, limit: number = 10) => {
  loading.value = true;
  try {
    const response = await fetch(`/client/reservations?page=${page}&limit=${limit}`);
    const data = await response.json();
    reservations.value = data.data;
    pagination.value = {
      currentPage: data.meta.current_page,
      lastPage: data.meta.last_page,
      perPage: data.meta.per_page,
      total: data.meta.total
    };
  } catch (error) {
    console.error('Error fetching reservations:', error);
  } finally {
    loading.value = false;
  }
};

const onPageChange = (page: number) => {
  fetchReservations(page, pagination.value.perPage);
};

const onPerPageChange = (value: any) => {
  if (value) {
    const perPage = typeof value === 'number' ? value : parseInt(value);
    pagination.value.perPage = perPage;
    fetchReservations(1, perPage);
  }
};

const formatDate = (dateString: string) => {
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('en-US', {
    month: 'short',
    day: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false
  }).format(date);
};

const shouldShowPage = (page: number): boolean => {
  const { currentPage, lastPage } = pagination.value;
  if (page === 1 || page === lastPage) return true;
  if (Math.abs(page - currentPage) <= 1) return true;
  return false;
};

const shouldShowEllipsis = (page: number): boolean => {
  const { currentPage, lastPage } = pagination.value;
  if (page === 2 && currentPage > 3) return true;
  if (page === lastPage - 1 && currentPage < lastPage - 2) return true;
  return false;
};

onMounted(() => {
  fetchReservations();
});
</script>

<template>
  <div class="space-y-4">
    <div class="flex justify-between items-center">
      <h1 class="text-2xl font-bold">My Reservations</h1>
      <div class="flex items-center gap-2">
        <span class="text-sm text-gray-600">Rows per page:</span>
        <Select
          :modelValue="pagination.perPage.toString()"
          @update:modelValue="onPerPageChange"
        >
          <SelectTrigger class="w-[70px]">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="5">5</SelectItem>
            <SelectItem value="10">10</SelectItem>
            <SelectItem value="20">20</SelectItem>
            <SelectItem value="50">50</SelectItem>
          </SelectContent>
        </Select>
      </div>
    </div>

    <Card>
      <CardContent class="p-0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>ID</TableHead>
              <TableHead>Created At</TableHead>
              <TableHead>Check Out At</TableHead>
              <TableHead>Guests</TableHead>
              <TableHead>Paid Price</TableHead>
              <TableHead>Payment ID</TableHead>
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="loading">
              <TableRow>
                <TableCell colspan="7" class="text-center py-8">
                  Loading reservations...
                </TableCell>
              </TableRow>
            </template>
            <template v-else-if="reservations.length === 0">
              <TableRow>
                <TableCell colspan="7" class="text-center py-8">
                  No reservations found
                </TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-for="reservation in reservations" :key="reservation.id">
                <TableCell>{{ reservation.id }}</TableCell>
                <TableCell>{{ formatDate(reservation.created_at) }}</TableCell>
                <TableCell>{{ formatDate(reservation.check_out_at) }}</TableCell>
                <TableCell>{{ reservation.accompany_number }}</TableCell>
                <TableCell>{{ reservation.paid_price_in_dollars }}</TableCell>
                <TableCell class="font-mono text-sm">
                  {{ reservation.payment_id.slice(0, 8) }}...
                </TableCell>
                <TableCell>
                  <Button variant="ghost" size="sm">View</Button>
                </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>
      </CardContent>
    </Card>

    <div v-if="!loading && reservations.length > 0" class="flex items-center justify-between">
      <div class="text-sm text-gray-600 whitespace-nowrap">
        Showing {{ ((pagination.currentPage - 1) * pagination.perPage) + 1 }} to
        {{ Math.min(pagination.currentPage * pagination.perPage, pagination.total) }} of {{ pagination.total }} entries
      </div>

      <Pagination
        v-if="pagination.lastPage > 1"
        :total="pagination.total"
        :sibling-count="1"
        :items-per-page="pagination.perPage"
        :current-page="pagination.currentPage"
        @update:page="onPageChange"
      >

        <template v-for="page in Array.from({length: pagination.lastPage}, (_, i) => i + 1)" :key="page">
          <PaginationItem
            v-if="shouldShowPage(page)"
            :value="page"
            @click="onPageChange(page)"
            :class="[
                'cursor-pointer',
                page === pagination.currentPage
                ? 'bg-blue-500 text-white hover:bg-blue-600'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ page }}
          </PaginationItem>
          <PaginationEllipsis v-else-if="shouldShowEllipsis(page)" />
        </template>
      </Pagination>
    </div>
  </div>
</template>