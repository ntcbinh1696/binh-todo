<template>
  <div>
    <div class="field w-full flex justify-content-center m-0">
      <Message
        v-if="message.todo.type && message.todo.content"
        :severity="message.todo.type"
        :life="3000"
        :sticky="false"
        class="field col-6 md-6"
      >
        {{ message.todo.content }}
      </Message>
    </div>
    <div class="field w-full flex justify-content-center">
      <div class="field col-6 md-6">
        <div class="p-inputgroup">
          <InputText
            v-model="todo"
            placeholder="Please input content of todo"
            type="text"
            :class="{ 'p-invalid': validationErrors.todo }"
            @keydown.enter="addTodo"
          />
          <Button
            icon="pi pi-plus-circle"
            class="p-button-success"
            @click="addTodo"
          />
        </div>
        <small v-show="validationErrors.todo" class="p-error">
          {{ message.errors.todo }}
        </small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  methods: {
    
  }
}
</script>

<style lang="scss" scoped></style>
