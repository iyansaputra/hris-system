<template>
  <div class="container mx-auto">
    
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
      <div>
        <h3 class="text-2xl font-bold text-gray-800 tracking-tight">Riwayat Absensi</h3>
        <p class="text-gray-500 text-sm">Pantau log kehadiran seluruh karyawan.</p>
      </div>
      
      <button 
        @click="fetchAbsensi" 
        class="text-indigo-600 hover:text-indigo-800 font-semibold text-sm flex items-center gap-1"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
        Refresh Data
      </button>
    </div>

    <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-100 text-left">
       <div class="overflow-x-auto">
        <table class="min-w-full leading-normal">
          <thead>
            <tr class="bg-gray-50 border-b border-gray-200">
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Karyawan</th>
              <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Masuk</th>
              <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Jam Pulang</th>
              <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50">
             <tr v-for="absen in historyAbsensi" :key="absen.id" class="hover:bg-gray-50 transition duration-150">
                 <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">{{ absen.date }}</td>
                 <td class="px-6 py-4 whitespace-nowrap">
                     <div class="flex items-center">
                        <div class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3">
                            {{ absen.user?.nama ? absen.user.nama.charAt(0) : '?' }}
                        </div>
                        <span class="font-bold text-gray-800 text-sm">{{ absen.user?.nama }}</span>
                     </div>
                 </td>
                 <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono text-emerald-600 font-bold">
                     {{ absen.time_in }}
                 </td>
                 <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono text-red-600 font-bold">
                     {{ absen.time_out || '--:--:--' }}
                 </td>
                 <td class="px-6 py-4 whitespace-nowrap text-center">
                    <span v-if="absen.status === 'hadir'" class="px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase tracking-wide">TEPAT WAKTU</span>
                    <span v-else-if="absen.status === 'terlambat'" class="px-3 py-1 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 uppercase tracking-wide">TERLAMBAT</span>
                    <span v-else-if="absen.status === 'pulang_cepat'" class="px-3 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700 uppercase tracking-wide">PULANG CEPAT</span>
                 </td>
             </tr>
             <tr v-if="historyAbsensi.length === 0">
                 <td colspan="5" class="px-6 py-10 text-center text-gray-400 italic">
                    Belum ada riwayat absensi.
                 </td>
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
  name: 'RekapAbsensiView',
  data() {
    return {
      historyAbsensi: []
    };
  },
  mounted() {
    this.fetchAbsensi();
  },
  methods: {
    async fetchAbsensi() {
        try {
            const response = await api.get('/attendance/history');
            this.historyAbsensi = response.data.data;
        } catch (error) {
            console.error('Gagal ambil history absen:', error);
        }
    }
  }
};
</script>