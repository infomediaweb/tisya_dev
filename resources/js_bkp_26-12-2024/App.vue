<template>

  <!-- Login Page -->
  <template v-if="!route.meta.isHeader">
    <router-view></router-view>
  </template>

  
  <!-- Show Dashboard -->
  <template v-else>
    <Header />
  </template>
  
  <Toast />

</template>

<script setup>
  import Header from '@components/header.vue'
  import Toast from '@components/toast.vue';
  import { useRoute } from 'vue-router'
  const route = useRoute()
</script>
