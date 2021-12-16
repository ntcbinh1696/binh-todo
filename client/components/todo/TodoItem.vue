<template>
  <div
    class="todo-item field w-full flex justify-content-between mt-3"
    :class="todoCustomClass"
  >
    <div class="flex flex-order-0 align-items-center">
      <Checkbox v-model="isChecked" :binary="true"></Checkbox>
      <div v-if="isEditable" class="field p-inputgroup ml-3 mb-0">
        <InputText
          v-model="item.content"
          placeholder="Please input content of todo"
          type="text"
          :class="{ 'p-invalid': validationErrors.content }"
          @keydown.enter="updateTask(item)"
        />
        <Button
          class="p-button-success"
          label="Save"
          @click="updateTask(item)"
        />
      </div>
      <span v-else class="ml-3">{{ item.content }}</span>
    </div>
    <div class="flex-order-1">
      <SplitButton
        v-show="isChecked"
        :label="splitLabel"
        :icon="splitIcon"
        :model="getMenuItemsForItem(item)"
        @click="doneTask(item)"
      ></SplitButton>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TodoItem',
  props: {
    task: {
      type: Object,
      required: true,
    },
  },
  data() {
    return {
      isEditable: false,
      validationErrors: {},
      isChecked: this.task.isDone,
    }
  },
  computed: {
    splitLabel() {
      return this.task.isDone ? 'Reset Task' : 'Done'
    },
    splitIcon() {
      return this.task.isDone ? 'pi pi-replay' : 'pi pi-check'
    },
    todoCustomClass() {
      if (this.task.isPin && this.task.isDone)
        return 'todo-item__content--done-and-pin'
      if (this.task.isPin) return 'todo-item__content--pin'
      if (this.task.isDone) return 'todo-item__content--done'
      return ''
    },
    item() {
      return this.task
    },
  },
  methods: {
    // get items for split button
    getMenuItemsForItem(item) {
      const context = item
      return [
        {
          label: item.isPin ? 'UnPin' : 'Pin',
          icon: item.isPin ? 'pi pi-flag-fill' : 'pi pi-flag',
          command: () => {
            this.pinTask(context)
          },
        },
        {
          label: 'Delete',
          icon: 'pi pi-trash',
          command: () => {
            this.deleteTask(context)
          },
        },
        {
          label: 'Update',
          icon: 'pi pi-refresh',
          command: () => {
            this.isEditable = true
          },
        },
      ]
    },
    // check validation of todo-item
    validateTodo() {
      const errors = {}
      if (!this.item.content.trim()) {
        errors.content = true
        this.message.errors.content = `Todo's Content is required.`
      } else {
        delete errors.content
      }
      this.validationErrors = { ...errors }
      return !Object.keys(errors).length
    },
    // done task
    doneTask(item) {
      let updatedItem = {}
      updatedItem = {
        ...item,
        isDone: !item.isDone,
      }
      this.$emit('updateTask', updatedItem)
    },
    // pin task
    pinTask(context) {
      let updatedItem = {}
      updatedItem = {
        ...context,
        isPin: !context.isPin,
      }
      this.$emit('updateTask', updatedItem)
    },
    // delete task
    deleteTask(context) {
      let updatedItem = {}
      updatedItem = {
        ...context,
        isDeleted: true,
      }
      this.$emit('updateTask', updatedItem)
    },
    // update content of task
    updateTask(context) {
      if (!this.validateTodo()) return
      this.$emit('updateTask', context)
      this.isEditable = false
    },
  },
}
</script>

<style lang="scss" scoped>
.todo-item {
  &__content--done-and-pin span {
    text-decoration: line-through;
    color: palevioletred !important;
  }
  &__content--done span {
    text-decoration: line-through;
    color: teal !important;
  }
  &__content--pin span {
    color: palevioletred !important;
  }
}
/deep/ {
  .p-speeddial-button.p-button.p-button-icon-only {
    width: 2rem;
    height: 2rem;
    & .p-button-icon {
      font-size: 1rem;
    }
  }
  .speeddial-delay-demo {
    & .p-speeddial-direction-down-left {
      left: calc(100% - 3rem);
      top: 0.5rem;
    }
    & .p-speeddial-opened {
      z-index: 999;
    }
  }
}
</style>
