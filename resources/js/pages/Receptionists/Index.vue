<template>
  <div class="space-y-6 p-6">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-semibold">Receptionists</h1>
      <Button @click="goToCreateReceptionist">Create Receptionist</Button>
    </div>

    <Card>
      <CardContent class="p-0">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>Name</TableHead>
              <TableHead>Email</TableHead>
              <TableHead>National ID</TableHead>
              <TableHead>Avatar</TableHead>
              <TableHead class="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow
              v-for="receptionist in receptionists"
              :key="receptionist.id"
              class="hover:bg-muted transition"
            >
              <TableCell>{{ receptionist.name }}</TableCell>
              <TableCell>{{ receptionist.email }}</TableCell>
              <TableCell>{{ receptionist.national_id }}</TableCell>
              <TableCell>
                <img
                  :src="receptionist.avatar"
                  alt="Avatar"
                  class="w-10 h-10 rounded-full object-cover"
                />
              </TableCell>
              <TableCell class="text-right space-x-2">
                <Button variant="outline" size="sm" @click="editReceptionist(receptionist.id)">
                  Edit
                </Button>
                <Button variant="destructive" size="sm" @click="openDeleteModal(receptionist.id)">
                  Delete
                </Button>
                <Button
                  :variant="receptionist.is_banned ? 'success' : 'secondary'"
                  size="sm"
                  @click="toggleBan(receptionist.id, receptionist.is_banned)"
                >
                  {{ receptionist.is_banned ? 'Unban' : 'Ban' }}
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </CardContent>
    </Card>

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
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';

defineProps({
  receptionists: Array,
})

const isModalOpen = ref(false)
const receptionistToDelete = ref(null)

const goToCreateReceptionist = () => {
  router.visit(route('receptionists.create'))
}

const editReceptionist = (id) => {
  router.visit(route('receptionists.edit', { receptionist: id }))
}

const openDeleteModal = (id) => {
  receptionistToDelete.value = id
  isModalOpen.value = true
}

const closeModal = () => {
  isModalOpen.value = false
  receptionistToDelete.value = null
}

const confirmDelete = () => {
  router.delete(route('receptionists.destroy', receptionistToDelete.value))
  closeModal()
}

const toggleBan = (id, isBanned) => {
  console.log(isBanned);
  const routeName = isBanned ? 'receptionists.unban' : 'receptionists.ban'
  router.post(route(routeName, { receptionist: id }))
}
</script>
