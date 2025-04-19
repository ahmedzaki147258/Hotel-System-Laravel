<template>
  <div>
    <h1>Create Client</h1>

    <form @submit.prevent="createClient">
      <div>
        <label for="name">Name</label>
        <input type="text" v-model="form.name" required />
      </div>

      <div>
        <label for="email">Email</label>
        <input type="email" v-model="form.email" required />
      </div>

      <button type="submit" class="btn btn-primary">Create</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
      },
    };
  },

  methods: {
    createClient() {
      this.$inertia.post(route('clients.store'), this.form);
    },
  },
};
</script>
