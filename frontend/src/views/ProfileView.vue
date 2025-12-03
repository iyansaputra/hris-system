<template>
  <div class="container mx-auto max-w-3xl">
    
    <h3 class="text-2xl font-bold text-gray-800 mb-6">Edit Profil Saya</h3>

    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
      <div class="p-8">
        <form @submit.prevent="updateProfile" class="space-y-8">
          
          <!-- BAGIAN 1: FOTO PROFIL -->
          <div class="flex flex-col items-center space-y-4">
            <div class="relative">
              <!-- Tampilkan Preview jika ada, jika tidak tampilkan Avatar Lama, jika tidak ada tampilkan Inisial -->
              <img 
                v-if="previewImage || user.avatar_url" 
                :src="previewImage || user.avatar_url" 
                class="w-32 h-32 rounded-full object-cover border-4 border-indigo-50 shadow-md"
              >
              <div v-else class="w-32 h-32 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-4xl font-bold border-4 border-indigo-50 shadow-md">
                {{ user.nama ? user.nama.charAt(0) : '?' }}
              </div>

              <!-- Tombol Kamera Kecil -->
              <label class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full cursor-pointer hover:bg-indigo-700 shadow-sm transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <input type="file" class="hidden" @change="handleFileUpload" accept="image/*">
              </label>
            </div>
            <p class="text-sm text-gray-500">Klik ikon kamera untuk mengganti foto. (Max 2MB)</p>
          </div>

          <hr class="border-gray-100">

          <!-- BAGIAN 2: INFO DATA DIRI -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
              <input type="text" :value="user.nama" disabled class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5 text-gray-500 cursor-not-allowed">
              <p class="text-xs text-gray-400 mt-1">*Nama tidak dapat diubah sendiri.</p>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input type="email" :value="user.email" disabled class="w-full bg-gray-50 border border-gray-300 rounded-lg p-2.5 text-gray-500 cursor-not-allowed">
            </div>
          </div>

          <hr class="border-gray-100">

          <!-- BAGIAN 3: GANTI PASSWORD (OPSIONAL) -->
          <div>
            <h4 class="text-lg font-semibold text-gray-800 mb-4">Ganti Password</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input v-model="form.password" type="password" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Kosongkan jika tidak diganti">
              </div>
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input v-model="form.password_confirmation" type="password" class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-2 focus:ring-indigo-500 outline-none" placeholder="Ulangi password baru">
              </div>
            </div>
          </div>

          <!-- TOMBOL SIMPAN -->
          <div class="flex justify-end pt-4">
            <button 
              type="submit" 
              :disabled="isLoading" 
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg shadow-md transition transform hover:-translate-y-0.5 disabled:bg-gray-400"
            >
              {{ isLoading ? 'Menyimpan...' : 'Simpan Perubahan' }}
            </button>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'ProfileView',
  data() {
    return {
      user: {},           // Data user saat ini
      previewImage: null, // URL gambar preview
      file: null,         // File object asli
      isLoading: false,
      
      form: {
        password: '',
        password_confirmation: ''
      }
    };
  },
  mounted() {
    this.fetchUserProfile();
  },
  methods: {
    // 1. Ambil data user saat ini (/me)
    async fetchUserProfile() {
      try {
        const response = await api.get('/me');
        this.user = response.data;
        
        // Cek apakah ada avatar URL dari backend?
        // Asumsikan backend mengirim field 'avatar' berisi path
        // Kita butuh URL lengkap. Di Controller ProfileController yang kita buat,
        // kita mengembalikan 'avatar_url' di response update, 
        // TAPI endpoint /me bawaan mungkin belum ada 'avatar_url'.
        
        // TIPS: Agar endpoint /me juga punya avatar_url, kita bisa modifikasi User Model (Accessor)
        // Tapi untuk sekarang kita handle sederhana dulu:
        if (this.user.avatar) {
            // Hardcode base URL storage jika belum ada accessor
            // Sesuaikan port backend Anda
            this.user.avatar_url = `http://127.0.0.1:8000/storage/${this.user.avatar}`;
        }
      } catch (error) {
        console.error("Gagal load user", error);
      }
    },

    // 2. Handle saat user pilih file
    handleFileUpload(event) {
      const file = event.target.files[0];
      if (!file) return;

      // Validasi ukuran (client side)
      if (file.size > 2 * 1024 * 1024) {
        alert("Ukuran file terlalu besar! Maksimal 2MB.");
        return;
      }

      this.file = file;
      // Buat URL sementara untuk preview
      this.previewImage = URL.createObjectURL(file);
    },

    // 3. Kirim Data ke Backend
    async updateProfile() {
      // Validasi password match
      if (this.form.password && this.form.password !== this.form.password_confirmation) {
        alert("Konfirmasi password tidak cocok!");
        return;
      }

      this.isLoading = true;

      try {
        // PENTING: Gunakan FormData untuk upload file
        const formData = new FormData();
        
        // Masukkan file jika ada
        if (this.file) {
          formData.append('avatar', this.file);
        }
        
        // Masukkan password jika ada
        if (this.form.password) {
          formData.append('password', this.form.password);
          formData.append('password_confirmation', this.form.password_confirmation);
        }

        // Kirim POST request
        const response = await api.post('/up-profile', formData, {
          headers: {
            'Content-Type': 'multipart/form-data' // Header wajib untuk file
          }
        });

        alert("Profil berhasil diperbarui!");
        
        // Update tampilan dengan data baru dari server
        this.user = response.data.data;
        this.user.avatar_url = response.data.avatar_url; // URL dari response controller
        
        // Reset form
        this.form.password = '';
        this.form.password_confirmation = '';
        this.file = null;
        this.previewImage = null; // Kembali gunakan gambar asli dari server

      } catch (error) {
        console.error(error);
        const msg = error.response?.data?.message || 'Gagal update profil.';
        alert(msg);
      } finally {
        this.isLoading = false;
      }
    }
  }
};
</script>