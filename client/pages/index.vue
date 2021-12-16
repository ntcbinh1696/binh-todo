<template>
  <div v-if="$auth.loggedIn" class="card p-5" style="height: 85vh">
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
    <div class="field w-full flex justify-content-center">
      <div class="field col-6 md-6">
        <div class="flex justify-content-between">
          <h2 class="text-h4 success--text pl-4">
            Tasks:&nbsp;
            <span :key="`tasks-${tasks.length}`">
              {{ tasks.length + deleted.length }}
            </span>
          </h2>
          <Knob v-model="progress" value-template="{value}%" />
        </div>

        <Divider class="m-0" />

        <div class="field w-full flex justify-content-center">
          <strong class="mx-4 info--text text--darken-2 mt-4">
            Remaining: {{ remainingTasks }}
          </strong>

          <Divider layout="vertical" class="mt-3"></Divider>

          <strong class="mx-4 info--text text--darken-2 mt-4">
            Completed: {{ completedTasks }}
          </strong>

          <Divider layout="vertical" class="mt-3"></Divider>

          <strong class="mx-4 success--text text--darken-2 mt-4">
            Deleted: {{ deletedTasks }}
          </strong>
        </div>

        <Divider class="m-0" />

        <ScrollPanel style="height: 46%">
          <div v-for="(task, i) in tasks" :key="i">
            <todo-item :task="task" @updateTask="updateTodo"></todo-item>
          </div>
        </ScrollPanel>
      </div>
    </div>
  </div>
</template>

<script>
import TodoItem from '../components/todo/TodoItem.vue'
export default {
  components: { TodoItem },
  data() {
    return {
      todo: '',
      validationErrors: {},
      message: {
        todo: {
          content: '',
          type: '',
        },
        errors: {
          todo: '',
        },
      },
      tasks: [],
      deleted: [],
    }
  },
  computed: {
    completedTasks() {
      return this.tasks.filter((task) => task.isDone).length
    },
    deletedTasks() {
      return this.deleted.length
    },
    progress() {
      return Math.round((this.completedTasks / this.tasks.length) * 100)
    },
    remainingTasks() {
      return this.tasks.length - this.completedTasks
    },
  },
  created() {
    this.getTasks()
  },
  methods: {
    // logout
    logout() {
      this.$auth.logout()
    },
    // set success message
    success(mode) {
      this.setMessage('success', `${mode} Task successfully!`)
    },
    // set failure message
    failure(mode) {
      this.setMessage('error', `${mode} Task failure!`)
    },
    // set message to be displayed
    setMessage(type, message) {
      this.message.todo = { type, content: message }
      setTimeout(() => {
        this.message.todo = {}
      }, 3000)
      this.getTasks()
    },
    // get all tasks of user
    async getTasks() {
      await this.$axios
        .get(`api/tasks?userId=${this.$auth.user.id}`)
        .then((res) => {
          this.deleted = res?.data
            ?.map((item) => {
              const task = {
                ...item,
                id: item.id,
                content: item.content,
                isDone: Boolean(item.isDone),
                isDeleted: Boolean(item.isDeleted),
                isPin: Boolean(item.isPin),
                createdAt: item.createdAt,
                updatedAt: item.updatedAt,
              }
              return task
            })
            ?.filter((task) => task.isDeleted)
          this.tasks = res?.data
            ?.map((item) => {
              const task = {
                ...item,
                id: item.id,
                content: item.content,
                isDone: Boolean(item.isDone),
                isDeleted: Boolean(item.isDeleted),
                isPin: Boolean(item.isPin),
                createdAt: item.createdAt,
                updatedAt: item.updatedAt,
              }
              return task
            })
            ?.filter((task) => !task.isDeleted)
        })
    },
    // add new todo
    async addTodo() {
      if (!this.validateForm()) return
      const todoObject = {
        content: this.todo,
        userId: this.$auth.user.id,
        isDone: false,
        isPin: false,
        isDeleted: false,
      }
      await this.$axios
        .post('api/tasks', todoObject)
        .then((res) => {
          if (res?.data.success) {
            this.success('Add')
            this.todo = ''
          } else {
            this.failure('Add')
          }
        })
        .catch(() => {
          this.failure('Add')
        })
    },
    // update todo (pin, done, update, delete)
    async updateTodo(updatedItem) {
      const id = updatedItem.id
      const data = { ...updatedItem, id: undefined }
      await this.$axios
        .put(`api/tasks/${id}`, data)
        .then((res) => {
          if (res?.data.success) {
            this.success('Update')
            this.todo = ''
          } else {
            this.failure('Update')
          }
        })
        .catch(() => {
          this.failure('Update')
        })
    },
    // check validation form
    validateForm() {
      const errors = {}
      if (!this.todo.trim()) {
        errors.todo = true
        this.message.errors.todo = `Todo's Content is required.`
      } else {
        delete errors.todo
      }
      this.validationErrors = { ...errors }
      return !Object.keys(errors).length
    },
  },
}
</script>
<style scoped lang="scss">
@import 'primeflex/primeflex.scss';

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
