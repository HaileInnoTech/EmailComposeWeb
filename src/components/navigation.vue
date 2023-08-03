<template>
  <header>
    <div class="d-flex align-items-center justify-content-center">
      <ul class="nav nav-pills nav-justified mb-3" id="ex1" role="tablist">
        <li class="nav-item" role="presentation">
          <router-link to="/login" class="nav-link" :class="{ active: $route.path === '/login' }"
            >Login</router-link
          >
        </li>
        <li class="nav-item" role="presentation">
          <router-link
            to="/register"
            class="nav-link"
            :class="{ active: $route.path === '/register' }"
            >Register</router-link
          >
        </li>
      </ul>
    </div>
  </header>
</template>

<script setup>
import { useRoute } from 'vue-router'
</script>
