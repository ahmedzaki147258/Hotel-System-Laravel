<template>
  <Head :title="pageTitle" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6 p-6">
      <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold">Receptionists</h1>
        <Button @click="goToCreateReceptionist">Create Receptionist</Button>
      </div>

      <Card>
        <CardContent class="p-0">
          <Table>
            <TableHeader>
              <TableRow v-for="headerGroup in table.getHeaderGroups()" :key="headerGroup.id">
                <TableHead v-for="header in headerGroup.headers" :key="header.id">
                  <FlexRender v-if="!header.isPlaceholder" :render="header.column.columnDef.header"
                    :props="header.getContext()" />
                </TableHead>
              </TableRow>
            </TableHeader>
            <TableBody>
              <TableRow v-for="row in table.getRowModel().rows" :key="row.id">
                <TableCell v-for="cell in row.getVisibleCells()" :key="cell.id">
                  <FlexRender :render="cell.column.columnDef.cell" :props="cell.getContext()" />
                </TableCell>
                <TableCell class="text-right space-x-2">
                  <Button variant="outline" size="sm" @click="editReceptionist(row.original.id)">
                    Edit
                  </Button>
                  <Button variant="destructive" size="sm" @click="openDeleteModal(row.original.id)">
                    Delete
                  </Button>
                  <Button
                    :variant="row.original.is_banned ? 'success' : 'secondary'"
                    size="sm"
                    @click="toggleBan(row.original.id, row.original.is_banned)"
                  >
                    {{ row.original.is_banned ? 'Unban' : 'Ban' }}
                  </Button>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </CardContent>
      </Card>

      <div class="flex justify-end items-center space-x-2 mt-4">
        <Button
          variant="outline"
          size="sm"
          :disabled="!receptionists.prev_page_url"
          @click="changePage(receptionists.current_page - 1)"
        >
          Previous
        </Button>
        <span class="text-sm text-muted-foreground">
          Page {{ receptionists.current_page }} of {{ receptionists.last_page }}
        </span>
        <Button
          variant="outline"
          size="sm"
          :disabled="!receptionists.next_page_url"
          @click="changePage(receptionists.current_page + 1)"
        >
          Next
        </Button>
      </div>

      <!-- Delete Confirmation Modal -->
      <Dialog v-model:open="isModalOpen">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete Receptionist</DialogTitle>
            <DialogDescription>
              Are you sure you want to delete this receptionist? This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          <div class="flex justify-end gap-2">
            <Button variant="outline" @click="closeModal">Cancel</Button>
            <Button variant="destructive" @click="confirmDelete">Confirm</Button>
          </div>
        </DialogContent>
      </Dialog>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { type BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { FlexRender } from '@tanstack/vue-table';
import { Receptionist, ReceptionistResponse } from '@/interfaces/model.interface';
import {
  createColumnHelper,
  getCoreRowModel,
  useVueTable,
  type ColumnDef
} from '@tanstack/vue-table'




const props = defineProps<{
  receptionists: ReceptionistResponse;
  isAdmin: boolean;
}>();

const pageTitle = ref('Receptionists');
const isModalOpen = ref(false);
const receptionistToDelete = ref<number | null>(null);

const columnHelper = createColumnHelper<Receptionist>();

const columns = computed<ColumnDef<Receptionist>[]>(() => {
  const baseColumns = [
    columnHelper.accessor('name', {
      header: 'Name',
      cell: info => info.getValue(),
    }),
    columnHelper.accessor('email', {
      header: 'Email',
      cell: info => info.getValue(),
    }),
    columnHelper.accessor('created_at', {
      header: 'Created At',
      cell: info => new Date(info.getValue()).toLocaleDateString(),
    }),
  ];

  // Conditionally add Manager column for admins
  if (props.isAdmin) {
    baseColumns.push(
      columnHelper.accessor('manager', {
        header: 'Manager',
        cell: info => info.getValue() ?? 'No Manager',
      })
    );
  }

  return baseColumns;
});



const table = ref(
  useVueTable({
    data: props.receptionists.data,
    columns: columns.value,
    getCoreRowModel: getCoreRowModel(),
  })
);

watch(() => props.receptionists.data, (newData) => {
  table.value = useVueTable({
    data: newData,
    columns: columns.value,
    getCoreRowModel: getCoreRowModel(),
  });
});

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Receptionists',
    href: '/receptionists',
  }
];

const goToCreateReceptionist = () => {
  router.visit(route('receptionists.create'));
};

const editReceptionist = (id: number) => {
  router.visit(route('receptionists.edit', { receptionist: id }));
};

const openDeleteModal = (id: number) => {
  receptionistToDelete.value = id;
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  receptionistToDelete.value = null;
};

const confirmDelete = () => {
  if (receptionistToDelete.value) {
    router.delete(route('receptionists.destroy', { receptionist: receptionistToDelete.value }), {
      onSuccess: () => closeModal()
    });
  }
};

const toggleBan = (id: number, isBanned: boolean) => {
  const routeName = isBanned ? 'receptionists.unban' : 'receptionists.ban';
  router.post(route(routeName, { receptionist: id }), {
    preserveScroll: true,
    preserveState: true,
  });
};

const changePage = (page: number) => {
  router.get(route('receptionists.index', { page }), {
    preserveScroll: true,
    only: ['receptionists'],
  });
};
</script>