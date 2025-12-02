<template>
  <div class="container mx-auto space-y-8">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-8 rounded-2xl shadow-xl text-left">
      <div>
        <h1 class="text-3xl font-bold tracking-tight">Halo, {{ userName }}!</h1>
        
        <div class="mt-2 text-left">
            <p v-if="attendanceData?.status === 'terlambat'" class="text-orange-200 font-bold bg-white/10 px-3 py-1 rounded-lg inline-block">
               ⚠️ Anda terlambat hari ini. Besok lebih disiplin ya!
            </p>
            <p v-else-if="attendanceData?.status === 'pulang_cepat'" class="text-yellow-200 font-bold">
               Anda pulang lebih awal hari ini.
            </p>
            <p v-else class="text-blue-100">
               Selamat bekerja, jangan lupa istirahat.
            </p>
        </div>
      </div>
      
      <div class="mt-4 md:mt-0 text-right">
        <div class="text-5xl font-mono font-bold tracking-widest text-blue-200">{{ currentTime }}</div>
        <div class="text-sm font-medium opacity-80 mt-1 uppercase tracking-wide">{{ currentDate }}</div>
      </div>
    </div>

    <!-- PANEL UTAMA -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
      
      <!-- KIRI: KONTROL ABSENSI -->
      <div class="bg-white p-8 rounded-2xl shadow-lg border border-gray-100 flex flex-col items-center justify-center text-center relative overflow-hidden">
        <h3 class="text-lg font-bold text-gray-700 mb-6">Status Kehadiran</h3>
        
        <div class="mb-8 scale-110">
           <span v-if="attendanceStatus === 'not_present'" class="bg-gray-100 text-gray-600 px-6 py-2 rounded-full font-bold border border-gray-200">
             Belum Absen
           </span>
           <span v-else-if="attendanceStatus === 'checked_in' && attendanceData.status === 'hadir'" class="bg-emerald-50 text-emerald-600 px-6 py-2 rounded-full font-bold border border-emerald-200 flex items-center gap-2">
             <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span> Hadir Tepat Waktu
           </span>
           <span v-else-if="attendanceStatus === 'checked_in' && attendanceData.status === 'terlambat'" class="bg-orange-50 text-orange-600 px-6 py-2 rounded-full font-bold border border-orange-200 flex items-center gap-2">
             <span class="w-3 h-3 rounded-full bg-orange-500 animate-pulse"></span> Terlambat
           </span>
           <span v-else-if="attendanceStatus === 'checked_out' && attendanceData.status === 'hadir'" class="bg-blue-50 text-blue-600 px-6 py-2 rounded-full font-bold border border-blue-200">
             Selesai Bekerja
           </span>
           <span v-else-if="attendanceStatus === 'checked_out' && attendanceData.status === 'pulang_cepat'" class="bg-yellow-50 text-yellow-600 px-6 py-2 rounded-full font-bold border border-yellow-200">
             Pulang Lebih Awal
           </span>
           <span v-else-if="attendanceStatus === 'checked_out' && attendanceData.status === 'terlambat'" class="bg-red-50 text-red-600 px-6 py-2 rounded-full font-bold border border-red-200">
             Selesai (Terlambat)
           </span>
        </div>

        <div class="w-full">
          <button 
            v-if="attendanceStatus === 'not_present'"
            @click="handleClockIn"
            :disabled="isLoadingAbsen"
            class="w-full bg-emerald-500 hover:bg-emerald-600 active:bg-emerald-700 text-white font-bold py-5 px-6 rounded-xl shadow-lg shadow-emerald-200 transition transform hover:-translate-y-1 text-lg"
          >
            {{ isLoadingAbsen ? 'Memproses...' : 'ABSEN MASUK' }}
          </button>

          <button 
            v-else-if="attendanceStatus === 'checked_in'"
            @click="handleClockOut"
            :disabled="isLoadingAbsen"
            class="w-full bg-red-500 hover:bg-red-600 active:bg-red-700 text-white font-bold py-5 px-6 rounded-xl shadow-lg shadow-red-200 transition transform hover:-translate-y-1 text-lg"
          >
             {{ isLoadingAbsen ? 'Memproses...' : 'ABSEN PULANG' }}
          </button>
          
          <div v-else class="w-full bg-slate-100 text-slate-400 font-bold py-5 px-6 rounded-xl border border-slate-200 cursor-not-allowed text-lg">
            SUDAH PULANG
          </div>
        </div>

        <div class="mt-8 grid grid-cols-2 gap-4 w-full text-sm">
          <div class="bg-gray-50 p-3 rounded-lg">
            <p class="text-gray-400 mb-1">Jam Masuk</p>
            <span class="font-mono font-bold text-gray-800 text-lg">{{ attendanceData?.time_in || '--:--' }}</span>
          </div>
          <div class="bg-gray-50 p-3 rounded-lg">
             <p class="text-gray-400 mb-1">Jam Pulang</p>
            <span class="font-mono font-bold text-gray-800 text-lg">{{ attendanceData?.time_out || '--:--' }}</span>
          </div>
        </div>
      </div>

      <!-- KANAN: REKAP TIM HARI INI -->
      <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 flex flex-col h-full min-h-[400px] text-left">
         <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-bold text-gray-800">Rekan Kerja Hadir</h3>
            <span class="text-xs text-blue-600 bg-blue-50 px-2 py-1 rounded font-bold">{{ todaysAttendances.length }} Orang</span>
         </div>
         
         <div class="flex-1 overflow-y-auto pr-2 custom-scrollbar">
            <div v-if="todaysAttendances.length === 0" class="flex flex-col items-center justify-center h-48 text-gray-400 text-sm italic">
              <p>Belum ada yang absen hari ini.</p>
              <p>Jadilah yang pertama!</p>
            </div>

            <ul class="space-y-3">
              <li v-for="absen in todaysAttendances" :key="absen.id" class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 transition border border-transparent hover:border-gray-100">
                 <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 text-indigo-600 flex items-center justify-center font-bold mr-3 shadow-sm">
                      {{ absen.user?.nama.charAt(0) }}
                    </div>
                    <div>
                      <p class="text-sm font-bold text-gray-800">{{ absen.user?.nama }}</p>
                      <p class="text-xs text-gray-500">{{ absen.time_in }}</p>
                    </div>
                 </div>
                 
                 <span v-if="absen.status === 'hadir'" class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-emerald-100 text-emerald-600">
                    Hadir
                 </span>
                 <span v-else-if="absen.status === 'terlambat'" class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-orange-100 text-orange-600">
                    Terlambat
                 </span>
                 <span v-else-if="absen.status === 'pulang_cepat'" class="text-[10px] uppercase font-bold px-2 py-1 rounded bg-yellow-100 text-yellow-600">
                    Pulang Cepat
                 </span>
              </li>
            </ul>
         </div>
      </div>
    </div>
  </div>
</template>

<script>
import api from '@/services/api';

export default {
  name: 'HomeViewKaryawan',
  data() {
    return {
      userName: 'Karyawan',
      currentTime: '',
      currentDate: '',
      
      attendanceStatus: 'not_present',
      attendanceData: null,
      isLoadingAbsen: false,
      todaysAttendances: [],
    };
  },
  mounted() {
    this.startClock();
    this.fetchUserProfile();
    this.fetchAttendanceStatus();
    this.fetchTodaysRecap();
  },
  methods: {
    startClock() {
      const updateTime = () => {
        const now = new Date();
        this.currentTime = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        this.currentDate = now.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
      };
      updateTime();
      setInterval(updateTime, 1000);
    },

    async fetchUserProfile() {
        try {
            const res = await api.get('/me');
            this.userName = res.data.nama; 
        } catch (e) { console.log(e); }
    },

    async fetchAttendanceStatus() {
      try {
        const response = await api.get('/attendance/status');
        this.attendanceStatus = response.data.status;
        this.attendanceData = response.data.data;
      } catch (error) { console.error(error); }
    },

    async fetchTodaysRecap() {
        try {
            const response = await api.get('/attendance/today');
            this.todaysAttendances = response.data.data;
        } catch (error) { console.error(error); }
    },

    async handleClockIn() {
      this.isLoadingAbsen = true;
      try {
        const res = await api.post('/attendance/clock-in');
        alert(res.data.message); 
        await this.fetchAttendanceStatus(); 
        await this.fetchTodaysRecap();
      } catch (error) {
        alert(error.response?.data?.message || 'Gagal absen masuk');
      } finally {
        this.isLoadingAbsen = false;
      }
    },

    async handleClockOut() {
       if(!confirm('Apakah Anda yakin ingin mengakhiri pekerjaan hari ini?')) return;
       this.isLoadingAbsen = true;
       try {
        const res = await api.post('/attendance/clock-out');
        alert(res.data.message); 
        await this.fetchAttendanceStatus();
        await this.fetchTodaysRecap();
      } catch (error) {
        alert(error.response?.data?.message || 'Gagal absen pulang');
      } finally {
        this.isLoadingAbsen = false;
      }
    }
  }
};
</script>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1;
  border-radius: 3px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8;
}
</style>