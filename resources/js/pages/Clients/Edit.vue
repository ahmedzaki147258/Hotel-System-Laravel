<template>
  <div>
    <h1>Edit Client</h1>

    <form @submit.prevent="updateClient">
      <div>
        <label for="name">Name</label>
        <input type="text" v-model="form.name" required />
      </div>

      <div>
        <label for="email">Email</label>
        <input type="email" v-model="form.email" required />
      </div>

      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</template>

<script>
export default {
  props: {
    client: Object,
  },

  data() {
    return {
      form: { ...this.client },
    };
  },

  methods: {
    updateClient() {
      this.$inertia.put(route('clients.update', this.client.id), this.form);
    },
  },
};
</script>
