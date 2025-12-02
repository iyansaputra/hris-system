<template>
  <HomeView v-if="role === 'HRD'" />
  <HomeViewKaryawan v-else />
</template>

<script>
import HomeView from './HomeView.vue';
import HomeViewKaryawan from './HomeViewKaryawan.vue';

export default {
  name: 'DashboardManager',
  components: {
    HomeView,
    HomeViewKaryawan
  },
  data() {
    return {
      role: ''
    };
  },
  created() {
    this.role = localStorage.getItem('role');
  }
};
</script>