<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <!-- Header -->
      <header class="content-header">
        <div>
          <h1>Beranda Dashboard</h1>
          <p class="subtitle">Selamat datang kembali di SuperApp Sistem Informasi Pesantren</p>
        </div>
        <div class="date-badge">
          📅 {{ currentDate }}
        </div>
      </header>

      <div class="content-body">
        <!-- Welcome Banner -->
        <section class="welcome-banner">
          <div class="banner-overlay"></div>
          <div class="banner-content">
            <span class="welcome-tag">Sistem Manajemen Terpadu</span>
            <h2>Assalamualaikum, {{ user.nama_lengkap || 'Pengguna' }}!</h2>
            <p>Anda masuk sebagai <strong>{{ user.role || 'Staff' }}</strong>. Pantau kondisi pesantren secara berkala melalui data ringkas di bawah ini.</p>
          </div>
        </section>

        <!-- Stats Grid -->
        <div class="stats-grid">
          <!-- Card 1: Santri -->
          <div class="stat-card card-blue" @click="navigateTo('/akademik')">
            <div class="stat-icon">🎓</div>
            <div class="stat-info">
              <h3>{{ stats.total_santri }}</h3>
              <p>Total Santri Aktif</p>
            </div>
            <div class="stat-action">Lihat Detail →</div>
          </div>

          <!-- Card 2: Karyawan -->
          <div class="stat-card card-purple" @click="navigateTo('/kepegawaian')">
            <div class="stat-icon">👥</div>
            <div class="stat-info">
              <h3>{{ stats.total_karyawan }}</h3>
              <p>Asatidz &amp; Staff</p>
            </div>
            <div class="stat-action">Kelola Pegawai →</div>
          </div>

          <!-- Card 3: PPDB -->
          <div class="stat-card card-green" @click="navigateTo('/ppdb')">
            <div class="stat-icon">📋</div>
            <div class="stat-info">
              <h3>{{ stats.total_ppdb }}</h3>
              <p>Pendaftar PPDB</p>
            </div>
            <div class="stat-action">Data Pendaftar →</div>
          </div>

          <!-- Card 4: Kelas -->
          <div class="stat-card card-orange" @click="navigateTo('/akademik')">
            <div class="stat-icon">🏫</div>
            <div class="stat-info">
              <h3>{{ stats.total_kelas }}</h3>
              <p>Total Kelas</p>
            </div>
            <div class="stat-action">Lihat Kelas →</div>
          </div>
        </div>

        <div class="dashboard-grid">
          <!-- Recent Activity Logs -->
          <section class="card logs-card">
            <div class="card-title-bar">
              <h3>📜 Log Aktivitas Terbaru</h3>
              <button @click="navigateTo('/setting')" class="btn-text">Selengkapnya</button>
            </div>
            <div class="table-wrapper">
              <table class="data-table">
                <thead>
                  <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Modul</th>
                    <th>Aktivitas</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-if="loading">
                    <td colspan="4" class="text-center py20">Memuat data aktivitas...</td>
                  </tr>
                  <tr v-else-if="stats.latest_logs && stats.latest_logs.length === 0">
                    <td colspan="4" class="text-center py20">Belum ada riwayat aktivitas.</td>
                  </tr>
                  <tr v-for="log in stats.latest_logs" :key="log.id">
                    <td class="text-muted" style="white-space: nowrap;">{{ formatDate(log.created_at) }}</td>
                    <td><strong>{{ log.user }}</strong></td>
                    <td><span class="badge-mini">{{ log.module }}</span></td>
                    <td class="activity-text">{{ log.activity }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>

          <!-- Quick Access Navigation -->
          <section class="card shortcuts-card">
            <div class="card-title-bar">
              <h3>⚡ Akses Cepat Modul</h3>
            </div>
            <div class="shortcuts-grid">
              <button v-if="hasPermission('akademik')" @click="navigateTo('/akademik')" class="shortcut-btn">
                <span class="shortcut-icon">🎓</span>
                <span class="shortcut-label">Siakad</span>
              </button>
              <button v-if="hasPermission('spp')" @click="navigateTo('/spp')" class="shortcut-btn">
                <span class="shortcut-icon">💰</span>
                <span class="shortcut-label">Pembayaran SPP</span>
              </button>
              <button v-if="hasPermission('keuangan')" @click="navigateTo('/keuangan')" class="shortcut-btn">
                <span class="shortcut-icon">📊</span>
                <span class="shortcut-label">Keuangan</span>
              </button>
              <button v-if="hasPermission('sarpras')" @click="navigateTo('/sarpras')" class="shortcut-btn">
                <span class="shortcut-icon">📦</span>
                <span class="shortcut-label">Sarpras</span>
              </button>
              <button v-if="hasPermission('perijinan')" @click="navigateTo('/perijinan')" class="shortcut-btn">
                <span class="shortcut-icon">📄</span>
                <span class="shortcut-label">Perizinan</span>
              </button>
              <button v-if="hasPermission('poskestren')" @click="navigateTo('/poskestren')" class="shortcut-btn">
                <span class="shortcut-icon">🏥</span>
                <span class="shortcut-label">Poskestren</span>
              </button>
              <button v-if="hasPermission('perpustakaan')" @click="navigateTo('/perpustakaan')" class="shortcut-btn">
                <span class="shortcut-icon">📚</span>
                <span class="shortcut-label">Perpustakaan</span>
              </button>
              <button v-if="hasPermission('setting')" @click="navigateTo('/setting')" class="shortcut-btn">
                <span class="shortcut-icon">⚙️</span>
                <span class="shortcut-label">Pengaturan</span>
              </button>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const router = useRouter()

const API = 'http://127.0.0.1:8080/api'
const token = localStorage.getItem('jwt_token')
const headers = { Authorization: 'Bearer ' + token }

const user = ref({})
const permissions = ref([])
const loading = ref(false)
const stats = ref({
  total_santri: 0,
  total_karyawan: 0,
  total_kelas: 0,
  total_users: 0,
  total_ppdb: 0,
  latest_logs: []
})

const currentDate = computed(() => {
  return new Date().toLocaleDateString('id-ID', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
})

function hasPermission(perm) {
  if (permissions.value.includes('*')) return true
  return permissions.value.includes(perm)
}

function navigateTo(path) {
  router.push(path)
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  try {
    return new Date(dateStr).toLocaleString('id-ID', {
      hour: '2-digit',
      minute: '2-digit'
    })
  } catch {
    return dateStr
  }
}

async function fetchStats() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/dashboard/stats`, { headers })
    if (res.data && res.data.status === 200) {
      stats.value = res.data.data
    }
  } catch (err) {
    console.error('Error fetching dashboard stats:', err)
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  const userData = localStorage.getItem('user_info') || localStorage.getItem('user')
  if (userData) {
    const parsed = JSON.parse(userData)
    user.value = parsed
    permissions.value = parsed.permissions || []
  }
  fetchStats()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

.dashboard-container {
  display: flex;
  height: 100vh;
  background: #0f1117;
  font-family: 'Inter', sans-serif;
  color: #e2e8f0;
  overflow: hidden;
}

/* Main Content */
.main-content {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.content-header {
  padding: 28px 32px 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  background: rgba(26, 29, 46, 0.5);
  gap: 16px;
  flex-wrap: wrap;
}

.content-header h1 {
  font-size: 22px;
  font-weight: 700;
  color: #ffffff;
}

.subtitle {
  font-size: 13px;
  color: #64748b;
  margin-top: 2px;
}

.date-badge {
  background: rgba(99, 102, 241, 0.1);
  border: 1px solid rgba(99, 102, 241, 0.2);
  color: #818cf8;
  padding: 8px 16px;
  border-radius: 20px;
  font-size: 12px;
  font-weight: 600;
}

.content-body {
  padding: 24px 32px 32px;
  display: flex;
  flex-direction: column;
  gap: 24px;
}

/* Welcome Banner */
.welcome-banner {
  position: relative;
  background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%);
  border: 1px solid rgba(124, 58, 237, 0.15);
  border-radius: 16px;
  padding: 28px 32px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
}

.banner-overlay {
  position: absolute;
  top: 0;
  right: 0;
  width: 40%;
  height: 100%;
  background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
  pointer-events: none;
}

.welcome-tag {
  display: inline-block;
  background: rgba(167, 139, 250, 0.15);
  color: #c084fc;
  padding: 4px 10px;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.8px;
  margin-bottom: 12px;
}

.welcome-banner h2 {
  font-size: 24px;
  font-weight: 700;
  color: #ffffff;
  margin-bottom: 8px;
}

.welcome-banner p {
  font-size: 14px;
  color: #94a3b8;
  line-height: 1.5;
}

.welcome-banner strong {
  color: #c4b5fd;
}

/* Stats Cards */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}

.stat-card {
  background: rgba(255, 255, 255, 0.02);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 12px;
  cursor: pointer;
  transition: all 0.25s ease;
  position: relative;
  overflow: hidden;
}

.stat-card:hover {
  transform: translateY(-4px);
  background: rgba(255, 255, 255, 0.04);
}

.stat-icon {
  font-size: 24px;
  width: 42px;
  height: 42px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.card-blue .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
.card-purple .stat-icon { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }
.card-green .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.card-orange .stat-icon { background: rgba(249, 115, 22, 0.1); color: #f97106; }

.stat-info h3 {
  font-size: 28px;
  font-weight: 700;
  color: #ffffff;
}

.stat-info p {
  font-size: 13px;
  color: #94a3b8;
  margin-top: 2px;
}

.stat-action {
  font-size: 11px;
  font-weight: 600;
  margin-top: auto;
  transition: color 0.2s;
}

.card-blue .stat-action { color: #60a5fa; }
.card-purple .stat-action { color: #a78bfa; }
.card-green .stat-action { color: #34d399; }
.card-orange .stat-action { color: #fb923c; }

.card-blue:hover { border-color: rgba(59, 130, 246, 0.25); }
.card-purple:hover { border-color: rgba(139, 92, 246, 0.25); }
.card-green:hover { border-color: rgba(16, 185, 129, 0.25); }
.card-orange:hover { border-color: rgba(249, 115, 22, 0.25); }

/* Dashboard Layout */
.dashboard-grid {
  display: grid;
  grid-template-columns: 1.6fr 1fr;
  gap: 24px;
}

@media (max-width: 1024px) {
  .dashboard-grid {
    grid-template-columns: 1fr;
  }
}

.card {
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.07);
  border-radius: 16px;
  padding: 20px;
}

.card-title-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.card-title-bar h3 {
  font-size: 15px;
  font-weight: 600;
  color: #ffffff;
}

.btn-text {
  background: none;
  border: none;
  color: #818cf8;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
}

.btn-text:hover {
  color: #a5b4fc;
  text-decoration: underline;
}

/* Tables inside card */
.table-wrapper {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12.5px;
}

.data-table th {
  padding: 10px 14px;
  text-align: left;
  font-size: 11px;
  color: #64748b;
  text-transform: uppercase;
  font-weight: 600;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
}

.data-table td {
  padding: 11px 14px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.04);
  vertical-align: middle;
}

.data-table tr:last-child td {
  border-bottom: none;
}

.text-muted {
  color: #64748b;
}

.badge-mini {
  background: rgba(99, 102, 241, 0.1);
  color: #818cf8;
  padding: 2px 6px;
  border-radius: 4px;
  font-size: 10px;
  font-weight: 600;
}

.activity-text {
  max-width: 250px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.text-center {
  text-align: center;
}

.py20 {
  padding-top: 20px;
  padding-bottom: 20px;
}

/* Shortcuts */
.shortcuts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 12px;
}

.shortcut-btn {
  background: rgba(255, 255, 255, 0.01);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 12px;
  padding: 16px 10px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  transition: all 0.2s;
  color: #cbd5e1;
}

.shortcut-btn:hover {
  background: rgba(99, 102, 241, 0.06);
  border-color: rgba(99, 102, 241, 0.2);
  color: #a5b4fc;
  transform: scale(1.03);
}

.shortcut-icon {
  font-size: 22px;
}

.shortcut-label {
  font-size: 11px;
  font-weight: 500;
  text-align: center;
}
</style>
