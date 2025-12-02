<template>
  <!-- Container Utama: Full Screen & Center -->
  <div class="min-h-screen flex items-center justify-center bg-gray-100 px-4">
    
    <!-- Card Login -->
    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
      
      <!-- Header -->
      <div class="text-center mb-6">
        <h2 class="text-3xl font-bold text-gray-800">HRIS SYSTEM</h2>
        <p class="text-gray-500 text-sm mt-1">Silakan login untuk masuk</p>
      </div>

      <form @submit.prevent="handleLogin">
        <!-- Input Email -->
        <div class="mb-4 text-left">
          <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
          <input 
            type="email" 
            v-model="email" 
            required 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
            placeholder="hrd@kantor.com"
          />
        </div>

        <!-- Input Password -->
        <div class="mb-6 text-left">
          <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
          <input 
            type="password" 
            v-model="password" 
            required 
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
            placeholder="******"
          />
        </div>

        <!-- Tombol Login -->
        <button 
          type="submit" 
          :disabled="isLoading"
          class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 disabled:bg-gray-400 transition duration-200"
        >
          <span v-if="isLoading">Loading...</span>
          <span v-else>LOGIN</span>
        </button>
      </form>

      <!-- Pesan Error -->
      <div v-if="errorMessage" class="mt-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded text-sm text-center">
        {{ errorMessage }}
      </div>
      
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'LoginView',
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
      isLoading: false
    };
  },
  methods: {
    async handleLogin() {
      this.isLoading = true;
      this.errorMessage = '';

      try {
        const response = await api.post('/login', {
          email: this.email,
          password: this.password
        });

        console.log('Login Berhasil:', response.data);

        const token = response.data.data.access_token;
        const role = response.data.data.user.role;

        localStorage.setItem('token', token);
        localStorage.setItem('role', role);

        // KOREKSI PENTING:
        // Gunakan '/' karena di router.js dashboard kita ada di path root '/'
        this.$router.push('/dashboard'); 
        
      } catch (error) {
        console.error('Login Gagal:', error);
        
        if (error.response && error.response.data) {
          this.errorMessage = error.response.data.message || 'Login gagal';
        } else {
          this.errorMessage = 'Terjadi kesalahan jaringan / server mati.';
        }
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>