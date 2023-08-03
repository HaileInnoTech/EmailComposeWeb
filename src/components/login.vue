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
          <p>Sign in with:</p>
          <button type=" button" class="btn btn-link btn-floating mx-1" @click="facebookSignIn">
            <i class="fab fa-facebook-f"></i>
          </button>
          <button type="button" class="btn btn-link btn-floating mx-1" @click="googleSignIn">
            <i class="fab fa-google"></i>
          </button>
        </div>
        <p class="text-center">or:</p>
        <div class="form-outline mb-4">
          <input type="email" id="email" class="form-control" v-model="email" />
          <label class="form-label" for="email">Email</label>
        </div>
        <div class="form-outline mb-4">
          <input type="password" id="password" class="form-control" v-model="password" />
          <label class="form-label" for="password">Password</label>
        </div>
        <button type="submit" class="btn btn-primary btn-block mb-4" @click="handleLoggin">
          Log in
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import {
  signInWithPopup,
  GoogleAuthProvider,
  FacebookAuthProvider,
  signInWithEmailAndPassword,
  getAuth
} from 'firebase/auth'
import auth from '../firebase'
import router from '../router'

export default {
  data() {
    return {
      email: '',
      password: ''
    }
  },
  methods: {
    googleSignIn() {
      const provider = new GoogleAuthProvider()
      signInWithPopup(auth, provider)
        .then((result) => {
          // Handle successful sign-in
          console.log('Signed-in user:', result.user)
          router.push('/dashboard')
        })
        .catch((error) => {
          // Handle sign-in error
          console.error('Sign-in error:', error)
        })
    },
    facebookSignIn() {
      const provider = new FacebookAuthProvider()

      signInWithPopup(auth, provider)
        .then((result) => {
          // Handle successful sign-in
          console.log('Signed-in user:', result.user)
          router.push('/dashboard')
        })
        .catch((error) => {
          // Handle sign-in error
          console.error('Sign-in error:', error)
        })
    },
    handleLoggin() {
      signInWithEmailAndPassword(auth, this.email, this.password)
        .then((data) => {
          console.log('Login Succesfully')
          router.push('/dashboard')
        })
        .catch((error) => {
          console.log(error.code)
          alert(error.message)
        })
    }
  }
}
</script>

<style>
/* Your custom styles here */
</style>
