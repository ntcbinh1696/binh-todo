<template>
  <div>
    <Message
      v-if="backendResponse && backendResponse.error"
      severity="error"
      :life="5000"
      :sticky="false"
      >{{ backendResponse.error }}</Message
    >
    <Dialog
      :visible.sync="display"
      :closable="false"
      :content-style="{ overflow: 'visible' }"
    >
      <template #header>
        <h3>{{ message.buttonLabel }}</h3>
      </template>

      <div v-if="isRegister" class="field col-12 md-12">
        <div class="p-inputgroup">
          <span class="p-inputgroup-addon">
            <i class="pi pi-user"></i>
          </span>
          <span class="p-float-label">
            <InputText
              id="name"
              v-model="inputForm.name"
              type="text"
              :class="{ 'p-invalid': validationErrors.name }"
            />
            <label for="email">Name</label>
          </span>
        </div>
        <small v-show="validationErrors.name" class="p-error">
          {{ message.errors.name }}
        </small>
      </div>
      <div class="field col-12 md-12 mt-3">
        <div class="p-inputgroup">
          <span class="p-inputgroup-addon">
            <i class="pi pi-envelope"></i>
          </span>
          <span class="p-float-label">
            <InputText
              id="email"
              v-model="inputForm.email"
              type="email"
              :class="{ 'p-invalid': validationErrors.email }"
            />
            <label for="email">Email</label>
          </span>
        </div>
        <small v-show="validationErrors.email" class="p-error">
          {{ message.errors.email }}
        </small>
      </div>
      <div class="field col-12 md-12 mt-3">
        <div class="p-inputgroup">
          <span class="p-inputgroup-addon">
            <i class="pi pi-lock"></i>
          </span>
          <span class="p-float-label">
            <Password
              id="password"
              v-model="inputForm.password"
              :class="{ 'p-invalid': validationErrors.password }"
              toggle-mask
            ></Password>
            <label for="password">Password</label>
          </span>
        </div>
        <small v-show="validationErrors.password" class="p-error">
          {{ message.errors.password }}
        </small>
      </div>
      <div v-if="isRegister" class="field col-12 md-12 mt-3">
        <div class="p-inputgroup">
          <span class="p-inputgroup-addon">
            <i class="pi pi-lock"></i>
          </span>
          <span class="p-float-label">
            <Password
              id="password_confirmation"
              v-model="inputForm.password_confirmation"
              :class="{ 'p-invalid': validationErrors.password_confirmation }"
              toggle-mask
            ></Password>
            <label for="password">Password Confirmation</label>
          </span>
        </div>
        <small v-show="validationErrors.password_confirmation" class="p-error">
          {{ message.errors.password_confirmation }}
        </small>
      </div>

      <template #footer>
        <div class="inline-flex align-items-center flex-wrap">
          <span class="mr-1">{{ message.status }}</span>
          <nuxt-link
            to=""
            class="mr-3"
            @click.native="isRegister = !isRegister"
          >
            {{ message.linkLabel }}
          </nuxt-link>
          <Button
            class="p-button-raised p-button-success"
            :label="message.buttonLabel"
            @click="submit(inputForm)"
          />
        </div>
      </template>
    </Dialog>
  </div>
</template>
<script>
export default {
  data() {
    return {
      inputForm: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
      },
      display: true,
      validationErrors: {},
      message: {
        status: `Don't have an account?`,
        linkLabel: 'Sign up',
        buttonLabel: 'Login',
        errors: {
          name: '',
          email: '',
          password: '',
          password_confirmation: '',
        },
      },
      backendResponse: {},
      isRegister: false,
    }
  },
  computed: {
    reg() {
      return /\S+@\S+\.\S+/
    },
  },
  watch: {
    isRegister(val) {
      switch (val) {
        case false:
          this.message.status = `Don't have an account?`
          this.message.linkLabel = 'Sign up'
          this.message.buttonLabel = 'Login'
          break
        default:
          this.message.status = 'Already have an account?'
          this.message.linkLabel = 'Login'
          this.message.buttonLabel = 'Sign up'
      }
    },
  },
  methods: {
    async submit(data) {
      if (!this.validateForm()) return
      if (this.isRegister) {
        this.backendResponse = null
        try {
          await this.$axios.post('/api/auth/register', data)
          await this.$auth.loginWith('local', {
            data: {
              email: data.email,
              password: data.password,
            },
          })
        } catch (err) {
          this.backendResponse = err.response?.data
        }
        return
      }
      this.backendResponse = null
      try {
        await this.$auth.loginWith('local', {
          data: {
            email: data.email,
            password: data.password,
          },
        })
      } catch (err) {
        this.backendResponse = err.response?.data
      }
    },
    validateEmail(email) {
      if (!this.reg.test(email)) {
        this.message.errors.email = 'Please enter a valid email address'
        return false
      } else {
        this.message.errors.email = ''
        return true
      }
    },
    validateForm() {
      const errors = {}
      if (this.isRegister && !this.inputForm.name.trim()) {
        errors.name = true
        this.message.errors.name = 'Name is required.'
      } else {
        delete errors.name
      }
      if (
        !this.inputForm.email.trim() ||
        !this.validateEmail(this.inputForm.email)
      ) {
        errors.email = true
        if (!this.inputForm.email.trim())
          this.message.errors.email = 'Email is required.'
      } else {
        delete errors.email
      }
      if (!this.inputForm.password.trim()) {
        errors.password = true
        this.message.errors.password = 'Password is required.'
      } else {
        delete errors.password
      }
      if (this.isRegister && !this.inputForm.password_confirmation.trim()) {
        errors.password_confirmation = true
        this.message.errors.password_confirmation =
          'Password confirmation is required.'
      } else {
        delete errors.password_confirmation
      }
      this.validationErrors = { ...errors }
      return !Object.keys(errors).length
    },
  },
}
</script>
<style lang="scss" scoped>
/deep/ {
  .p-dialog {
    width: 600px;
    & > .p-dialog-header {
      padding: 24px 24px 0 24px;
      height: 54px;
      & > h3 {
        color: var(--green-500);
        font-size: 24px;
      }
    }
    & > .p-dialog-content {
      padding: 24px;
    }
  }
  .p-field {
    padding: 8px;
  }
  .p-password-input {
    border-top-left-radius: 0 !important;
    border-bottom-left-radius: 0 !important;
  }
}
</style>
