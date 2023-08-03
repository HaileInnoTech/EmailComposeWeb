<script setup>
import navigation from './navigation.vue'
</script>
<template>
  <navigation></navigation>
  <div class="tab-content d-flex align-items-center justify-content-center">
    <div
      class="tab-pane fade show active"
      id="pills-login"
      role="tabpanel"
      aria-labelledby="tab-login"
    >
      <div>
        <div class="text-center">
          <p>Create an Account</p>
        </div>
        <div class="form-outline mb-4">
          <input type="email" id="email" v-model="email" class="form-control" />
          <label class="form-label" for="email">Email</label>
        </div>
        <div class="form-outline mb-4">
          <input type="password" id="password" class="form-control" v-model="password" />
          <label class="form-label" for="password">Password</label>
        </div>

        <button type="submit" class="btn btn-primary btn-block mb-4" @click="handleRegister">
          Register
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import { getAuth, createUserWithEmailAndPassword } from 'firebase/auth'
import router from '../router'

export default {
  data() {
    return {
      email: '',
      password: ''
    }
  },
  methods: {
    handleRegister() {
      // Use getAuth() to get the Auth object
      let auth = getAuth()
      // Now you can call createUserWithEmailAndPassword method
      createUserWithEmailAndPassword(auth, this.email, this.password)
        .then((data) => {
          console.log('Successfully registered')
          // Optionally, you can navigate to a different page after successful registration
          router.push('/login')
        })
        .catch((error) => {
          console.log(error.code)
          alert(error.message)
        })
    }
  }
}
</script>

<style></style>
