<template>
  <!-- Container Utama: Flex Row & Full Height -->
  <div class="flex h-screen bg-gray-100 font-sans">
    
    <!-- SIDEBAR -->
    <aside class="w-64 bg-slate-800 text-white flex flex-col shadow-xl transition-all duration-300">
      <!-- Header Sidebar -->
      <div class="h-16 flex items-center justify-center bg-slate-900 border-b border-slate-700 shadow-md">
        <h3 class="text-xl font-bold tracking-wider text-white">HRIS APP</h3>
        <span class="ml-3 bg-emerald-500 text-white text-[10px] font-bold px-2 py-0.5 rounded uppercase tracking-wide">
          {{ role }}
        </span>
      </div>
      
      <!-- Navigasi Menu -->
      <nav class="flex-1 py-6 px-3 space-y-2 overflow-y-auto">
        <!-- Menu Dashboard (Untuk Semua User) -->
        <router-link 
          to="/dashboard" 
          class="flex items-center px-4 py-3 text-slate-300 rounded-lg transition-colors duration-200 hover:bg-slate-700 hover:text-white"
          active-class="bg-slate-700 text-white shadow-md border-l-4 border-emerald-500"
        >
          <span class="font-medium">Dashboard</span>
        </router-link>

        <!-- Jika Anda punya halaman lain khusus HRD, aktifkan ini -->
        <router-link 
          v-if="role === 'HRD'" 
          to="/rekap-absensi"
          class="flex items-center px-4 py-3 text-slate-300 rounded-lg transition-colors duration-200 hover:bg-slate-700 hover:text-white"
          active-class="bg-slate-700 text-white shadow-md border-l-4 border-emerald-500"
        >
          <span class="font-medium">Rekap Absensi</span>
        </router-link> 

        <router-link 
          v-if="role === 'HRD'" 
          to="/leave-approve"
          class="flex items-center px-4 py-3 text-slate-300 rounded-lg transition-colors duration-200 hover:bg-slate-700 hover:text-white"
          active-class="bg-slate-700 text-white shadow-md border-l-4 border-emerald-500"
        >
          <span class="font-medium">Rekap Perizinan</span>
        </router-link> 

        <router-link 
          v-if="role === 'Karyawan'" 
          to="/leave-request"
          class="flex items-center px-4 py-3 text-slate-300 rounded-lg transition-colors duration-200 hover:bg-slate-700 hover:text-white"
          active-class="bg-slate-700 text-white shadow-md border-l-4 border-emerald-500"
        >
          <span class="font-medium">Ajukan Izin</span>
        </router-link> 
      </nav>

      <!-- Footer Sidebar (Tombol Logout) -->
      <div class="p-4 border-t border-slate-700 bg-slate-800">
        <button 
          @click="handleLogout" 
          class="w-full flex items-center justify-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg shadow-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-opacity-50"
        >
          Logout
        </button>
      </div>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <main class="flex-1 flex flex-col overflow-hidden">
      <!-- Top Bar / Header Atas -->
      <header class="h-16 bg-white shadow-sm flex items-center justify-between px-8 z-10 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-800">Selamat Datang</h2>
        
        <div class="flex items-center text-sm text-gray-600 bg-gray-50 px-3 py-1.5 rounded-full border border-gray-200">
          <span class="mr-1">Login sebagai:</span>
          <strong class="text-gray-900">{{ role }}</strong>
        </div>
      </header>
      
      <!-- Content View (Isi Halaman akan muncul disini) -->
      <div class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-8">
        <router-view /> 
      </div>
    </main>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'MainLayout',
  data() {
    return {
      role: ''
    };
  },
  created() {
    this.role = localStorage.getItem('role') || 'Guest';
  },
  methods: {
    async handleLogout() {
      try {
        await api.post('/logout');
      } catch (error) {
        console.error("Logout error", error);
      } finally {
        localStorage.removeItem('token');
        localStorage.removeItem('role');
        this.$router.push('/login');
      }
    }
  }
};
</script>