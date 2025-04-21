<template>
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
            v-for="client in clients"
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
          <TableRow v-if="clients.length === 0">
            <TableCell :colspan="canEdit ? 7 : 6" class="text-center py-8">
              You haven't approved any clients yet
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </div>
</template>

<script setup>
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
import { Button } from '@/components/ui/button';

// Props
const props = defineProps({
  clients: Array,
  canEdit: {
    type: Boolean,
    default: false
  },
  userRole: String
});

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
</script>