<script setup lang="ts">
import { ref, computed, watch, h } from 'vue';
import { Card, CardContent } from '@/components/ui/card';
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    getPaginationRowModel,
    type PaginationState
} from '@tanstack/vue-table';
import { Button } from '@/components/ui/button';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Reservation } from '@/interfaces/model.interface';
import { router } from '@inertiajs/vue3';

const loading = ref(false);
const data = ref<Reservation[]>([]);
const pagination = ref<PaginationState>({
    pageIndex: 0,
    pageSize: 10,
});
const totalItems = ref(0);
const totalPages = ref(1);

// Get current pagination from URL
const initPaginationFromUrl = () => {
    const urlParams = new URLSearchParams(window.location.search);
    const pageParam = urlParams.get('page');
    const limitParam = urlParams.get('limit');

    if (pageParam) {
        pagination.value.pageIndex = parseInt(pageParam) - 1;
    }

    if (limitParam) {
        pagination.value.pageSize = parseInt(limitParam);
    }
};

// Initialize from URL on component mount
initPaginationFromUrl();

const columns = [
    {
        accessorKey: 'id',
        header: '#ID',
    },
    {
        accessorKey: 'room.floor.number',
        header: 'Floor',
    },
    {
        accessorKey: 'room.number',
        header: 'Room',
    },
    {
        accessorKey: 'check_out_at',
        id: 'status',
        header: 'Status',
        cell: ({ row }: { row: any }) => {
            const checkOutDate = new Date(row.original.check_out_at);
            if (checkOutDate < new Date()) {
                return h('span', { class: 'text-blue-600 font-medium' }, 'Completed');
            } else {
                return h('span', { class: 'text-green-600 font-medium' }, 'Ongoing');
            }
        },
    },
    {
        accessorKey: 'accompany_number',
        header: 'Guests',
    },
    {
        accessorKey: 'paid_price_in_dollars',
        header: 'Paid Price',
    },
    {
        accessorKey: 'check_in_at',
        header: 'Check In At',
        cell: (info: any) => formatDate(info.getValue()),
    },
    {
        accessorKey: 'check_out_at',
        header: 'Check Out At',
        cell: (info: any) => formatDate(info.getValue()),
    },
];

// Create table instance
const table = useVueTable({
    get data() {
        return data.value;
    },
    columns,
    get pageCount() {
        return totalPages.value;
    },
    state: {
        get pagination() {
            return pagination.value;
        },
    },
    onPaginationChange: (updater) => {
        const newPagination = typeof updater === 'function' ? updater(pagination.value) : updater;
        pagination.value = newPagination;
    },
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    getPaginationRowModel: getPaginationRowModel(),
});

type AcceptableValue = string | number | null | Record<string, any>;

const fetchReservations = async () => {
    loading.value = true;
    try {
        const page = pagination.value.pageIndex + 1;
        const limit = pagination.value.pageSize;

        // Update URL with page and limit
        updateUrl(page, limit);

        const response = await fetch(`/client/reservations?page=${page}&limit=${limit}`);
        const result = await response.json();

        data.value = result.data;
        totalItems.value = result.meta.total;
        totalPages.value = result.meta.last_page;
    } catch (error) {
        console.error('Error fetching reservations:', error);
    } finally {
        loading.value = false;
    }
};

// Update URL with pagination parameters
const updateUrl = (page: number, limit: number) => {
    router.visit(`/client/my-reservations?page=${page}&limit=${limit}`, {
        preserveState: true,
        preserveScroll: true,
        only: [],
        replace: true
    });
};

const onPageSizeChange = (value: AcceptableValue) => {
    if (value !== null) {
        const sizeValue = typeof value === 'object' && value !== null
            ? String(Object.values(value)[0] || '')
            : String(value);
        table.setPageSize(Number(sizeValue));
    }
};

const handlePageChange = (newPageIndex: number) => {
    table.setPageIndex(newPageIndex);
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

// Watch for pagination changes
watch(
    () => pagination.value,
    () => {
        fetchReservations();
    },
    { deep: true }
);

// Initial fetch
fetchReservations();

// Computed properties for pagination
const currentPageIndex = computed(() => table.getState().pagination.pageIndex);
const canPreviousPage = computed(() => currentPageIndex.value > 0);
const canNextPage = computed(() => currentPageIndex.value < totalPages.value - 1);
</script>

<template>
    <div class="space-y-4">
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold">My Reservations</h1>
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-600">Rows per page:</span>
                <Select :model-value="pagination.pageSize.toString()" @update:model-value="onPageSizeChange">
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
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b bg-muted/50">
                                <th v-for="header in table.getFlatHeaders()" :key="header.id"
                                    class="px-4 py-3 text-left font-medium text-muted-foreground">
                                    <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                                        :props="header.getContext()" />
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="loading">
                                <td :colspan="columns.length" class="text-center py-8">
                                    Loading reservations...
                                </td>
                            </tr>
                            <tr v-else-if="table.getRowModel().rows.length === 0">
                                <td :colspan="columns.length" class="text-center py-8">
                                    No reservations found
                                </td>
                            </tr>
                            <template v-else>
                                <tr v-for="row in table.getRowModel().rows" :key="row.id"
                                    class="border-b hover:bg-muted/50">
                                    <td v-for="cell in row.getVisibleCells()" :key="cell.id" class="px-4 py-3">
                                        <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>

        <div v-if="!loading && data.length > 0" class="flex items-center justify-between">
            <div class="text-sm text-gray-600 whitespace-nowrap">
                Showing {{ (currentPageIndex * pagination.pageSize) + 1 }} to
                {{ Math.min((currentPageIndex + 1) * pagination.pageSize, totalItems) }} of
                {{ totalItems }} entries
            </div>

            <div class="flex items-center gap-2">
                <Button variant="outline" size="sm" @click="handlePageChange(0)" :disabled="!canPreviousPage">
                    First
                </Button>
                <Button variant="outline" size="sm" @click="() => table.previousPage()" :disabled="!canPreviousPage">
                    Previous
                </Button>
                <div class="flex items-center gap-1">
                    <span class="text-sm text-gray-600">
                        Page {{ currentPageIndex + 1 }} of {{ totalPages }}
                    </span>
                </div>
                <Button variant="outline" size="sm" @click="() => table.nextPage()" :disabled="!canNextPage">
                    Next
                </Button>
                <Button variant="outline" size="sm" @click="handlePageChange(totalPages - 1)" :disabled="!canNextPage">
                    Last
                </Button>
            </div>
        </div>
    </div>
</template>
