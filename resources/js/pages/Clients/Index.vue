<template>
  <div class="p-6 space-y-6">
    <h1 class="text-2xl font-semibold">Clients</h1>

    <!-- Create Client Button -->
    <button
      @click="goToCreateClient"
      class="inline-flex items-center px-4 py-2 bg-primary text-black rounded-xl hover:bg-primary/90 transition"
    >
      Create Client
    </button>

    <!-- Clients Table -->
    <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-sm">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Name</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Email</th>
            <th class="px-6 py-3 text-left text-sm font-medium text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          <tr
            v-for="client in clients"
            :key="client.id"
            class="hover:bg-gray-50 transition"
          >
            <td class="px-6 py-4 text-sm font-medium text-gray-900">
              {{ client.name }}
            </td>
            <td class="px-6 py-4 text-sm text-gray-700">
              {{ client.email }}
            </td>
            <td class="px-6 py-4 space-x-2">
              <!-- Edit Button -->
              <button
                @click="editClient(client.id)"
                class="inline-flex items-center px-3 py-1 text-sm text-white bg-yellow-500 hover:bg-yellow-600 rounded-lg"
              >
                Edit
              </button>

              <!-- Delete Button -->
              <button
                @click="openDeleteModal(client.id)"
                class="inline-flex items-center px-3 py-1 text-sm text-white bg-red-500 hover:bg-red-600 rounded-lg"
              >
                Delete
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Delete Confirmation Modal -->
    <div 
      v-if="isModalOpen" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
      <div class="bg-black p-6 rounded-lg shadow-lg w-96">
        <h3 class="text-lg font-semibold mb-4">Are you sure you want to delete this client?</h3>
        <div class="flex justify-end space-x-4">
          <!-- Cancel Button -->
          <button
            @click="closeModal"
            class="px-4 py-2 text-sm text-gray-600 bg-gray-200 rounded-lg hover:bg-gray-300"
          >
            Cancel
          </button>
          <!-- Confirm Button -->
          <button
            @click="confirmDelete"
            class="px-4 py-2 text-sm text-white bg-red-500 rounded-lg hover:bg-red-600"
          >
            Confirm
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    clients: Array,
    canCreate: Boolean,
  },
  data() {
    return {
      isModalOpen: false,
      clientToDelete: null, // Store the ID of the client to delete
    };
  },
  methods: {
    goToCreateClient() {
      this.$inertia.visit(route('clients.create'));
    },
    editClient(clientId) {
      this.$inertia.get(route('clients.edit', { client: clientId }));
    },
    openDeleteModal(clientId) {
      // Open modal and set client to delete
      this.clientToDelete = clientId;
      this.isModalOpen = true;
    },
    closeModal() {
      // Close modal and clear client to delete
      this.clientToDelete = null;
      this.isModalOpen = false;
    },
    confirmDelete() {
      // Proceed with deletion
      this.$inertia.delete(route('clients.destroy', this.clientToDelete));
      this.closeModal(); // Close the modal after deletion
    }
  }
}
</script>

