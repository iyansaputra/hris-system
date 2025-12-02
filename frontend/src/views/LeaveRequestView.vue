<template>
  <div class="container mx-auto space-y-8">
    
    <!-- HEADER PAGE -->
    <div class="flex justify-between items-center">
      <div>
        <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Pengajuan Cuti & Izin</h3>
        <p class="text-gray-500 text-sm">Ajukan permohonan cuti atau sakit di sini.</p>
      </div>
      <button @click="showForm = true" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow transition flex items-center gap-2">
        <span>+ Buat Pengajuan</span>
      </button>
    </div>

    <!-- FORM MODAL -->
    <div v-if="showForm" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900 bg-opacity-50 backdrop-blur-sm">
      <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
          <h3 class="font-bold text-gray-700">Form Pengajuan</h3>
          <button @click="closeForm" class="text-gray-400 hover:text-red-500">&times;</button>
        </div>
        
        <form @submit.prevent="submitLeave" class="p-6 space-y-4">
          <!-- Tipe Cuti -->
          <div>
            <label class="block text-sm font-medium mb-1">Jenis Pengajuan</label>
            <select v-model="form.type" required class="w-full border rounded-lg p-2 bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
              <option value="cuti">Cuti Tahunan</option>
              <option value="sakit">Sakit</option>
              <option value="izin">Izin Lainnya</option>
            </select>
          </div>

          <!-- Tanggal -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium mb-1">Mulai Tanggal</label>
              <input v-model="form.start_date" type="date" required class="w-full border rounded-lg p-2">
            </div>
            <div>
              <label class="block text-sm font-medium mb-1">Sampai Tanggal</label>
              <input v-model="form.end_date" type="date" required class="w-full border rounded-lg p-2">
            </div>
          </div>

          <!-- Alasan -->
          <div>
            <label class="block text-sm font-medium mb-1">Alasan / Keterangan</label>
            <textarea v-model="form.reason" rows="3" required class="w-full border rounded-lg p-2" placeholder="Contoh: Demam tinggi, butuh istirahat..."></textarea>
          </div>

          <div class="pt-4 flex justify-end gap-3">
            <button type="button" @click="closeForm" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg">Batal</button>
            <button type="submit" :disabled="isLoading" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:bg-gray-400">
              {{ isLoading ? 'Mengirim...' : 'Kirim Pengajuan' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- TABLE RIWAYAT -->
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
      <div class="p-4 bg-gray-50 border-b border-gray-100">
        <h4 class="font-bold text-gray-700">Riwayat Pengajuan Saya</h4>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-white border-b">
            <tr>
              <th class="px-6 py-3 font-semibold text-gray-600">Tanggal</th>
              <th class="px-6 py-3 font-semibold text-gray-600">Tipe</th>
              <th class="px-6 py-3 font-semibold text-gray-600">Alasan</th>
              <th class="px-6 py-3 font-semibold text-gray-600 text-center">Status</th>
              <th class="px-6 py-3 font-semibold text-gray-600">Catatan HRD</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
            <tr v-for="item in myLeaves" :key="item.id" class="hover:bg-gray-50">
              <td class="px-6 py-4">
                <div class="text-gray-900 font-medium">{{ formatDate(item.start_date) }}</div>
                <div class="text-xs text-gray-500">s/d {{ formatDate(item.end_date) }}</div>
              </td>
              <td class="px-6 py-4 capitalize">{{ item.type }}</td>
              <td class="px-6 py-4 truncate max-w-xs" :title="item.reason">{{ item.reason }}</td>
              <td class="px-6 py-4 text-center">
                <span v-if="item.status === 'pending'" class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded text-xs font-bold uppercase">Menunggu</span>
                <span v-else-if="item.status === 'approved'" class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-xs font-bold uppercase">Disetujui</span>
                <span v-else class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold uppercase">Ditolak</span>
              </td>
              <td class="px-6 py-4 text-gray-500 italic">{{ item.rejection_note || '-' }}</td>
            </tr>
            <tr v-if="myLeaves.length === 0">
              <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat pengajuan.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'LeaveRequestView',
  data() {
    return {
      myLeaves: [],
      showForm: false,
      isLoading: false,
      form: {
        type: 'cuti',
        start_date: '',
        end_date: '',
        reason: ''
      }
    };
  },
  mounted() {
    this.fetchMyLeaves();
  },
  methods: {
    async fetchMyLeaves() {
      try {
        const res = await api.get('/leaves/my');
        this.myLeaves = res.data.data;
      } catch (e) { console.error(e); }
    },
    async submitLeave() {
      this.isLoading = true;
      try {
        await api.post('/leaves', this.form);
        alert('Berhasil diajukan! Menunggu persetujuan HRD.');
        this.closeForm();
        this.fetchMyLeaves();
      } catch (e) {
        alert(e.response?.data?.message || 'Gagal mengirim pengajuan.');
      } finally {
        this.isLoading = false;
      }
    },
    closeForm() {
      this.showForm = false;
      this.form = { type: 'cuti', start_date: '', end_date: '', reason: '' };
    },
    formatDate(dateStr) {
      if(!dateStr) return '-';
      const date = new Date(dateStr);
      return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }
  }
};
</script>