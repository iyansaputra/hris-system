<template>
  <div class="container mx-auto space-y-6">
    <div class="flex justify-between items-center text-left">
      <div>
        <h3 class="text-2xl font-bold text-gray-800">Persetujuan Cuti</h3>
        <p class="text-gray-500 text-sm">Daftar pengajuan cuti masuk dari karyawan.</p>
      </div>
      <button @click="fetchAllLeaves" class="text-indigo-600 hover:underline text-sm font-semibold">Refresh Data</button>
    </div>

    <!-- TABLE APPROVAL -->
    <div class="bg-white rounded-xl shadow border border-gray-100 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full text-left text-sm">
          <thead class="bg-gray-50 border-b">
            <tr>
              <th class="px-6 py-3 font-bold text-gray-700">Karyawan</th>
              <th class="px-6 py-3 font-bold text-gray-700">Tipe & Tanggal</th>
              <th class="px-6 py-3 font-bold text-gray-700">Alasan</th>
              <th class="px-6 py-3 font-bold text-gray-700 text-center">Status</th>
              <th class="px-6 py-3 font-bold text-gray-700 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="leave in leaves" :key="leave.id" class="hover:bg-gray-50">
              <!-- Kolom Karyawan -->
              <td class="px-6 py-4">
                <div class="font-bold text-gray-900">{{ leave.user?.nama }}</div>
                <div class="text-xs text-gray-500">{{ leave.user?.email }}</div>
              </td>
              
              <!-- Kolom Tipe & Tgl -->
              <td class="px-6 py-4">
                <span class="capitalize font-semibold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded text-xs">{{ leave.type }}</span>
                <div class="text-xs text-gray-600 mt-1">
                  {{ formatDate(leave.start_date) }} - {{ formatDate(leave.end_date) }}
                </div>
              </td>

              <!-- Kolom Alasan -->
              <td class="px-6 py-4 max-w-xs">
                <p class="truncate" :title="leave.reason">{{ leave.reason }}</p>
                <p v-if="leave.status === 'rejected'" class="text-xs text-red-500 mt-1 italic">Note: {{ leave.rejection_note }}</p>
              </td>

              <!-- Kolom Status -->
              <td class="px-6 py-4 text-center">
                 <span v-if="leave.status === 'pending'" class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs font-bold">PENDING</span>
                 <span v-else-if="leave.status === 'approved'" class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold">APPROVED</span>
                 <span v-else class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold">REJECTED</span>
              </td>

              <!-- Kolom Aksi -->
              <td class="px-6 py-4 text-center">
                <div v-if="leave.status === 'pending'" class="flex justify-center gap-2">
                  <button @click="processLeave(leave.id, 'approved')" class="bg-emerald-500 hover:bg-emerald-600 text-white p-1.5 rounded shadow tooltip" title="Setujui">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                  </button>
                  <button @click="promptReject(leave)" class="bg-red-500 hover:bg-red-600 text-white p-1.5 rounded shadow tooltip" title="Tolak">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                  </button>
                </div>
                <span v-else class="text-gray-400 text-xs">- Selesai -</span>
              </td>
            </tr>
            <tr v-if="leaves.length === 0">
               <td colspan="5" class="px-6 py-10 text-center text-gray-400">Tidak ada data pengajuan.</td>
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
  name: 'LeaveApprovalView',
  data() {
    return {
      leaves: []
    };
  },
  mounted() {
    this.fetchAllLeaves();
  },
  methods: {
    async fetchAllLeaves() {
      try {
        const res = await api.get('/leaves/all');
        this.leaves = res.data.data;
      } catch (e) { console.error(e); }
    },
    async processLeave(id, status, note = null) {
      try {
        await api.put(`/leaves/${id}/status`, {
          status: status,
          rejection_note: note
        });
        alert('Status berhasil diperbarui!');
        this.fetchAllLeaves();
      } catch (e) {
        alert(e.response?.data?.message || 'Gagal memproses data.');
      }
    },
    promptReject(leave) {
      const note = prompt("Masukkan alasan penolakan untuk " + leave.user?.nama + ":");
      if (note !== null) { // Jika tidak cancel
         if(!note.trim()) return alert("Alasan penolakan wajib diisi!");
         this.processLeave(leave.id, 'rejected', note);
      }
    },
    formatDate(dateStr) {
      if(!dateStr) return '-';
      const date = new Date(dateStr);
      return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    }
  }
};
</script>