<template>
  <div>
    <h1>Clients</h1>

    <!-- Display a button to create a client if the user is authorized -->
    <div v-if="canCreate">
      <inertia-link :href="route('clients.create')" class="btn btn-primary">Create Client</inertia-link>
    </div>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="client in clients" :key="client.id">
          <td>{{ client.name }}</td>
          <td>{{ client.email }}</td>
          <td>
            <inertia-link :href="route('clients.show', client.id)" class="btn btn-info">View</inertia-link>
            <inertia-link :href="route('clients.edit', client.id)" class="btn btn-warning">Edit</inertia-link>
            <button @click="deleteClient(client.id)" class="btn btn-danger">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: {
    clients: Array,
    canCreate: Boolean,
  },

  methods: {
    deleteClient(clientId) {
      if (confirm('Are you sure you want to delete this client?')) {
        this.$inertia.delete(route('clients.destroy', clientId));
      }
    }
  }
}
</script>
