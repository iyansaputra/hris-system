<template>
  <div class="container mx-auto">
    
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
      <h3 class="text-2xl font-bold text-gray-800 tracking-tight">
        Daftar Karyawan
      </h3>
      
      <!-- Tombol Tambah (Hanya Admin/HRD) -->
      <button 
        v-if="isAdmin" 
        @click="openModal('add')" 
        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2 px-6 rounded-lg shadow-md transition duration-200 flex items-center gap-2 transform hover:scale-105"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Karyawan
      </button>
    </div>

    <!-- TABLE KARYAWAN -->
    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100">
      <div class="overflow-x-auto">
        <table class="min-w-full leading-normal">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama & Email</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Divisi</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
              <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
              <th v-if="isAdmin" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(karyawan, index) in karyawans" :key="karyawan.id" class="hover:bg-gray-50 transition duration-150">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-left">{{ index + 1 }}</td>
              
              <td class="px-6 py-4 text-left">
                <div class="flex flex-col">
                  <span class="text-sm font-semibold text-gray-900 ">{{ karyawan.nama }}</span>
                  <span class="text-xs text-gray-500">{{ karyawan.user?.email }}</span>
                </div>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-left">
                {{ karyawan.divisions?.nama || '-' }}
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span 
                  class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                  :class="karyawan.user?.role === 'HRD' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'"
                >
                  {{ karyawan.user?.role }}
                </span>
              </td>
              
              <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="capitalize text-sm text-gray-700 bg-gray-100 px-3 py-1 rounded-md border border-gray-200">
                  {{ karyawan.jenis_karyawan }}
                </span>
              </td>
              
              <td v-if="isAdmin" class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                <button 
                  @click="openModal('edit', karyawan)" 
                  class="text-amber-500 hover:text-amber-700 bg-amber-50 hover:bg-amber-100 px-3 py-1.5 rounded transition duration-200"
                >
                  Edit
                </button>
                <button 
                  @click="deleteKaryawan(karyawan.id)" 
                  class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded transition duration-200"
                >
                  Hapus
                </button>
              </td>
            </tr>
            
            <!-- Empty State -->
            <tr v-if="karyawans.length === 0">
              <td colspan="6" class="px-6 py-10 text-center text-gray-500 italic">
                Belum ada data karyawan. Silakan tambah data baru.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- MODAL POP-UP (Dengan Backdrop Blur) -->
    <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <!-- Background Overlay -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity backdrop-blur-sm" aria-hidden="true" @click="closeModal"></div>

        <!-- Center Modal Trick -->
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <!-- Modal Panel -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
          
          <!-- Header Modal -->
          <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-lg leading-6 font-bold text-gray-800" id="modal-title">
              {{ isEditMode ? 'Edit Data Karyawan' : 'Tambah Karyawan Baru' }}
            </h3>
            <button @click="closeModal" class="text-gray-400 hover:text-gray-600 transition">
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <!-- Form Content -->
          <div class="bg-white px-6 pt-5 pb-4 sm:p-6 sm:pb-4">
            <form @submit.prevent="saveData" class="space-y-4">
              
              <!-- Input Nama -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                <input v-model="form.nama" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition duration-200">
              </div>

              <!-- Input Email & HP (Grid 2 Kolom) -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                  <input v-model="form.email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition duration-200">
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">No HP</label>
                  <input v-model="form.no_hp" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition duration-200">
                </div>
              </div>

              <!-- Input Password -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Password <span v-if="isEditMode" class="text-xs text-gray-400 font-normal">(Kosongkan jika tidak diganti)</span>
                </label>
                <input v-model="form.password" type="password" :required="!isEditMode" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition duration-200">
              </div>

              <!-- Dropdown Role & Divisi -->
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                  <select v-model="form.role" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                    <option value="Karyawan">Karyawan</option>
                    <option value="HRD">HRD</option>
                  </select>
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Divisi</label>
                  <select v-model="form.divisi_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                    <option value="1">IT Department</option>
                    <option value="2">HR Department</option>
                    <option value="3">Finance</option>
                  </select>
                </div>
              </div>

              <!-- Dropdown Jenis Karyawan -->
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status Karyawan</label>
                <select v-model="form.jenis_karyawan" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                  <option value="tetap">Tetap</option>
                  <option value="kontrak">Kontrak</option>
                  <option value="magang">Magang</option>
                  <option value="pkl">PKL</option>
                </select>
              </div>

              <!-- Footer Modal (Buttons) -->
              <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse -mx-6 -mb-4 mt-6 rounded-b-2xl">
                <button type="submit" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                  {{ isEditMode ? 'Simpan Perubahan' : 'Tambah Data' }}
                </button>
                <button type="button" @click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-200">
                  Batal
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'HomeView',
  data() {
    return {
      karyawans: [],
      isAdmin: false,
      
      // Modal State
      showModal: false,
      isEditMode: false,
      editId: null,

      // Form Data
      form: {
        nama: '',
        email: '',
        password: '',
        role: 'Karyawan',
        divisi_id: 1,
        no_hp: '',
        jenis_karyawan: 'kontrak'
      }
    };
  },
  mounted() {
    this.checkRole();
    this.fetchKaryawans();
  },
  methods: {
    checkRole() {
      const role = localStorage.getItem('role');
      this.isAdmin = (role === 'HRD');
    },

    async fetchKaryawans() {
      try {
        const response = await api.get('/karyawans');
        this.karyawans = response.data.data.data || response.data.data; 
      } catch (error) {
        console.error('Gagal ambil data:', error);
      }
    },

    openModal(type, data = null) {
      this.showModal = true;
      if (type === 'edit' && data) {
        this.isEditMode = true;
        this.editId = data.id;
        this.form = {
          nama: data.nama, 
          email: data.user.email,
          password: '', 
          role: data.user.role,
          divisi_id: data.divisi_id,
          no_hp: data.no_hp,
          jenis_karyawan: data.jenis_karyawan
        };
      } else {
        this.isEditMode = false;
        this.resetForm();
      }
    },

    closeModal() {
      this.showModal = false;
      this.resetForm();
    },

    resetForm() {
      this.form = {
        nama: '', email: '', password: '', role: 'Karyawan',
        divisi_id: 1, no_hp: '', jenis_karyawan: 'kontrak'
      };
    },

    async saveData() {
      try {
        if (this.isEditMode) {
          // UPDATE
          await api.put(`/karyawans/${this.editId}`, this.form);
          alert('Data berhasil diperbarui!');
        } else {
          // CREATE
          await api.post('/karyawans', this.form);
          alert('Karyawan berhasil ditambahkan!');
        }
        this.closeModal();
        this.fetchKaryawans(); 
      } catch (error) {
        alert('Gagal menyimpan: ' + (error.response?.data?.message || error.message));
      }
    },

    async deleteKaryawan(id) {
      if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
        try {
          await api.delete(`/karyawans/${id}`);
          this.fetchKaryawans();
        } catch (error) {
          alert('Gagal menghapus data.');
        }
      }
    }
  }
};
</script>