<template>
  <div class="todo-app">
    <Toolbar>
      <template #start>
        <div class="title">{{ title }}</div>
      </template>
      <template #end>
        <Button
          v-if="$auth.loggedIn"
          label="Logout"
          icon="pi pi-user"
          class="p-button-rounded p-button-outlined"
          @click="logout"
        />
      </template>
    </Toolbar>
    <Nuxt />
  </div>
</template>

<script>
export default {
  computed: {
    title() {
      return `${
        this.$auth.user?.name ? this.$auth.user?.name + `'s` : 'My'
      } Todo List`
    },
  },
  methods: {
    logout() {
      this.$auth.logout()
    },
  },
}
</script>
<style scoped lang="scss">
.todo-app {
  background-color: var(--cyan-50) !important;
  overflow-y: hidden;
}
/deep/ {
  .p-toolbar {
    height: 90px;
    background-color: #99f6e4;
    & .title {
      font-family: cursive;
      font-size: 45px;
      margin: 10px;
      text-shadow: 3px 3px 5px #fff;
      font-weight: bold;
      color: var(--primary-color);
    }
  }
}
</style>
