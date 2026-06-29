<template>
  <div class="dashboard-container">
    <Sidebar />

    <!-- Main Content -->
    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Perizinan Santri</h1>
          <p>Kelola pengajuan izin, persetujuan, dan kepulangan santri</p>
        </div>
        <button @click="showForm = true" class="btn-primary">+ Ajukan Izin Baru</button>
      </header>

      <!-- Stats Cards -->
      <div class="stats-grid">
        <div class="stat-card orange">
          <div class="stat-icon">⏳</div>
          <div><div class="stat-num">{{ countByStatus('Pending') }}</div><div class="stat-lbl">Menunggu Persetujuan</div></div>
        </div>
        <div class="stat-card green">
          <div class="stat-icon">✅</div>
          <div><div class="stat-num">{{ countByStatus('Disetujui') }}</div><div class="stat-lbl">Disetujui</div></div>
        </div>
        <div class="stat-card red">
          <div class="stat-icon">🏃</div>
          <div><div class="stat-num">{{ countByStatus('Aktif') }}</div><div class="stat-lbl">Sedang Keluar</div></div>
        </div>
        <div class="stat-card blue">
          <div class="stat-icon">🏠</div>
          <div><div class="stat-num">{{ countByStatus('Kembali') }}</div><div class="stat-lbl">Sudah Kembali</div></div>
        </div>
        <div class="stat-card gray">
          <div class="stat-icon">❌</div>
          <div><div class="stat-num">{{ countByStatus('Ditolak') }}</div><div class="stat-lbl">Ditolak</div></div>
        </div>
      </div>

      <!-- Filter Chips -->
      <div class="filter-bar">
        <button
          v-for="f in filterOptions"
          :key="f.value"
          :class="['filter-chip', activeFilter === f.value ? 'chip-' + f.color : '']"
          @click="activeFilter = f.value"
        >{{ f.label }}</button>
      </div>

      <!-- Data Table -->
      <div class="card">
        <div class="card-header">
          <h3>
            Daftar Izin
            <span v-if="activeFilter" class="header-badge">{{ activeFilter }}</span>
          </h3>
          <div class="header-actions">
            <input v-model="searchQ" placeholder="Cari nama santri..." class="search-input" />
            <button @click="fetchIzinLogs" class="btn-refresh" :disabled="isLoading">
              <span :class="{ spinning: isLoading }">🔄</span>
            </button>
          </div>
        </div>

        <div v-if="isLoading" class="loading-state">
          <div class="loader"></div>
          <p>Mengambil data dari server...</p>
        </div>

        <div v-else-if="filteredList.length === 0" class="empty-state">
          <div class="empty-icon">📄</div>
          <h4>Tidak ada data izin</h4>
          <p>{{ activeFilter ? `Tidak ada izin dengan status "${activeFilter}"` : 'Belum ada pengajuan izin santri' }}</p>
        </div>

        <div v-else class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Santri</th>
                <th>Token</th>
                <th>Jenis Izin</th>
                <th>Alasan</th>
                <th>Mulai</th>
                <th>Selesai</th>
                <th>Status</th>
                <th>Waktu Kembali</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="(izin, i) in filteredList" :key="izin.id" :class="'row-' + izin.status.toLowerCase()">
                <td class="num-cell">{{ i + 1 }}</td>
                <td>
                  <div class="santri-info">
                    <div class="santri-avatar">{{ (izin.nama_lengkap || 'S').charAt(0) }}</div>
                    <div>
                      <div class="name-cell">{{ izin.nama_lengkap || '—' }}</div>
                      <div class="nisn-text">{{ izin.nisn }}</div>
                    </div>
                  </div>
                </td>
                <td><code class="token-code">{{ izin.token }}</code></td>
                <td>
                  <span :class="['badge-jenis', 'jenis-' + jenisClass(izin.jenis_izin)]">
                    {{ jenisEmoji(izin.jenis_izin) }} {{ izin.jenis_izin }}
                  </span>
                </td>
                <td class="alasan-cell" :title="izin.alasan">{{ truncate(izin.alasan) }}</td>
                <td class="time-cell">{{ formatDateTime(izin.tanggal_mulai) }}</td>
                <td class="time-cell">{{ formatDateTime(izin.tanggal_selesai) }}</td>
                <td>
                  <span :class="['status-pill', 'pill-' + izin.status.toLowerCase()]">
                    {{ statusEmoji(izin.status) }} {{ izin.status }}
                  </span>
                </td>
                <td class="time-cell muted">{{ izin.waktu_kembali ? formatDateTime(izin.waktu_kembali) : '—' }}</td>
                <td>
                  <div class="action-group">
                    <template v-if="izin.status === 'Pending'">
                      <button @click="handleApprove(izin.id)" class="act-btn act-approve" title="Setujui">✅</button>
                      <button @click="handleReject(izin.id)"  class="act-btn act-reject"  title="Tolak">❌</button>
                    </template>
                    <template v-if="izin.status === 'Disetujui'">
                      <button @click="handleAktifkan(izin.id)" class="act-btn act-keluar" title="Konfirmasi Keluar">🏃</button>
                    </template>
                    <template v-if="izin.status === 'Aktif'">
                      <button @click="handleKembali(izin.id)" class="act-btn act-kembali" title="Konfirmasi Kembali">🏠</button>
                    </template>
                    <button @click="handleDelete(izin.id)" class="act-btn act-delete" title="Hapus">🗑️</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- Modal Pengajuan Izin -->
    <div v-if="showForm" class="modal-overlay" @click.self="showForm = false">
      <div class="modal-box">
        <div class="modal-head">
          <h3>📄 Ajukan Izin Santri</h3>
          <button @click="showForm = false" class="modal-close">✕</button>
        </div>

        <div v-if="formError" class="alert-error">⚠️ {{ formError }}</div>
        <div v-if="formSuccess" class="alert-success">✅ {{ formSuccess }}</div>

        <div class="form-grid">
          <div class="form-group full-width">
            <label>Pilih Santri *</label>
            <div class="santri-input-row">
              <select v-model="newIzin.nisn" class="form-input">
                <option value="" disabled>— Pilih Santri —</option>
                <option v-for="s in santriList" :key="s.nisn" :value="s.nisn">
                  {{ s.nama_lengkap }} ({{ s.nisn }})
                </option>
              </select>
              <button type="button" @click="openScanner" class="btn-scan" title="Scan QR Code NISN">
                <span>📷</span> Scan QR
              </button>
            </div>
            <div v-if="scannedSantri" class="scan-result">
              ✅ Terdeteksi: <strong>{{ scannedSantri.nama_lengkap }}</strong> ({{ scannedSantri.nisn }})
            </div>
          </div>

          <div class="form-group">
            <label>Jenis Izin</label>
            <div class="jenis-selector">
              <button
                v-for="j in ['Sakit', 'Pulang', 'Keluar Lingkungan']"
                :key="j"
                type="button"
                :class="['jenis-btn', newIzin.jenis_izin === j ? 'jenis-active' : '']"
                @click="newIzin.jenis_izin = j"
              >{{ jenisEmoji(j) }} {{ j }}</button>
            </div>
          </div>

          <div class="form-group">
            <label>Tanggal Mulai *</label>
            <input v-model="newIzin.tanggal_mulai" type="datetime-local" class="form-input" />
          </div>

          <div class="form-group">
            <label>Tanggal Selesai *</label>
            <input v-model="newIzin.tanggal_selesai" type="datetime-local" class="form-input" />
          </div>

          <div class="form-group full-width">
            <label>Alasan / Keperluan *</label>
            <textarea v-model="newIzin.alasan" class="form-input" rows="3" placeholder="Tulis alasan izin secara detail..."></textarea>
          </div>
        </div>

        <div class="modal-actions">
          <button @click="showForm = false" class="btn-secondary">Batal</button>
          <button @click="handleSave" class="btn-primary" :disabled="isSaving">
            {{ isSaving ? '⏳ Menyimpan...' : '📤 Ajukan Izin' }}
          </button>
        </div>
      </div>
    </div>

    <!-- Modal QR Scanner -->
    <div v-if="showScanner" class="scanner-overlay" @click.self="closeScanner">
      <div class="scanner-box">
        <div class="scanner-head">
          <h3>📷 Scan QR Code NISN Santri</h3>
          <button @click="closeScanner" class="modal-close">✕</button>
        </div>
        <div class="scanner-body">
          <div class="scanner-frame">
            <div id="qr-reader"></div>
            <div v-if="!scannerStarted" class="scanner-hint">
              <div class="scanner-icon">📱</div>
              <p>Arahkan kamera ke QR Code NISN santri</p>
            </div>
          </div>
          <div v-if="scanError" class="scan-error">⚠️ {{ scanError }}</div>
          <div v-if="lastScanResult" class="scan-live-result">
            <span class="pulse-dot"></span>
            Terbaca: <strong>{{ lastScanResult }}</strong>
          </div>
        </div>
        <div class="scanner-footer">
          <p class="scanner-note">Pastikan QR Code NISN santri terlihat jelas dalam frame</p>
          <button @click="closeScanner" class="btn-secondary">Tutup</button>
        </div>
      </div>
    </div>

    <!-- Notification Toast -->
    <transition name="toast">
      <div v-if="toast.show" :class="['toast', 'toast-' + toast.type]">
        {{ toast.message }}
      </div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import { Html5Qrcode } from 'html5-qrcode'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const router     = useRouter()
const API        = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8080'
const token      = localStorage.getItem('jwt_token')
const user       = JSON.parse(localStorage.getItem('user_info') || localStorage.getItem('user') || '{}')
const userInitial = computed(() => (user.nama_lengkap || 'U').charAt(0).toUpperCase())
const headers    = { Authorization: 'Bearer ' + token }

const izinList   = ref([])
const santriList = ref([])
const isLoading  = ref(false)
const isSaving   = ref(false)
const showForm   = ref(false)
const activeFilter = ref('')
const searchQ    = ref('')
const formError   = ref('')
const showScanner = ref(false)
const scannerStarted = ref(false)
const scanError   = ref('')
const lastScanResult = ref('')
const scannedSantri  = ref(null)
let html5QrCode = null
const formSuccess = ref('')
const toast      = ref({ show: false, message: '', type: 'success' })

const newIzin = ref({ nisn: '', jenis_izin: 'Keluar Lingkungan', tanggal_mulai: '', tanggal_selesai: '', alasan: '' })

const filterOptions = [
  { value: '',          label: '🔍 Semua',   color: 'all' },
  { value: 'Pending',   label: '⏳ Pending',  color: 'orange' },
  { value: 'Disetujui', label: '✅ Disetujui', color: 'green' },
  { value: 'Aktif',     label: '🏃 Keluar',   color: 'red' },
  { value: 'Kembali',   label: '🏠 Kembali',  color: 'blue' },
  { value: 'Ditolak',   label: '❌ Ditolak',  color: 'gray' },
]

const filteredList = computed(() => izinList.value.filter(izin => {
  const matchStatus = !activeFilter.value || izin.status === activeFilter.value
  const matchQ      = !searchQ.value || (izin.nama_lengkap || '').toLowerCase().includes(searchQ.value.toLowerCase())
  return matchStatus && matchQ
}))

function countByStatus(status) { return izinList.value.filter(i => i.status === status).length }

function statusEmoji(s) {
  const map = { Pending:'⏳', Disetujui:'✅', Aktif:'🏃', Kembali:'🏠', Ditolak:'❌' }
  return map[s] || '•'
}
function jenisEmoji(j) {
  const map = { Sakit:'🤒', Pulang:'🏡', 'Keluar Lingkungan':'🚶' }
  return map[j] || '📄'
}
function jenisClass(j) {
  const map = { Sakit:'sakit', Pulang:'pulang', 'Keluar Lingkungan':'keluar' }
  return map[j] || 'default'
}
function formatDateTime(dt) {
  if (!dt) return '—'
  const d = new Date(dt)
  return d.toLocaleDateString('id-ID', { day:'2-digit', month:'short', year:'numeric' }) +
         ' ' + d.toLocaleTimeString('id-ID', { hour:'2-digit', minute:'2-digit' })
}
function truncate(str, max = 40) {
  if (!str) return '—'
  return str.length > max ? str.slice(0, max) + '…' : str
}

function showToast(message, type = 'success') {
  toast.value = { show: true, message, type }
  setTimeout(() => toast.value.show = false, 3500)
}

// ===== QR SCANNER =====
async function openScanner() {
  showScanner.value = true
  scanError.value   = ''
  lastScanResult.value = ''
  scannerStarted.value = false

  await nextTick()

  try {
    html5QrCode = new Html5Qrcode('qr-reader')
    await html5QrCode.start(
      { facingMode: 'environment' },
      { fps: 12, qrbox: { width: 240, height: 240 } },
      (decodedText) => onScanSuccess(decodedText),
      () => {} // error callback silent
    )
    scannerStarted.value = true
  } catch (err) {
    scanError.value = 'Kamera tidak dapat diakses. Pastikan izin kamera diberikan.'
    console.error('QR Scanner error:', err)
  }
}

async function closeScanner() {
  try {
    if (html5QrCode && scannerStarted.value) {
      await html5QrCode.stop()
      html5QrCode.clear()
    }
  } catch {}
  html5QrCode = null
  scannerStarted.value = false
  showScanner.value = false
  scanError.value = ''
}

async function onScanSuccess(decodedText) {
  const nisn = decodedText.trim()
  lastScanResult.value = nisn

  // Cari santri berdasarkan NISN dari hasil scan
  const found = santriList.value.find(s => s.nisn === nisn || s.id == nisn)
  if (found) {
    newIzin.value.nisn = found.nisn
    scannedSantri.value = found
    showToast(`✅ Santri ditemukan: ${found.nama_lengkap}`, 'success')
    await closeScanner()
  } else {
    // Coba set langsung jika format valid (hanya angka)
    if (/^\d{5,20}$/.test(nisn)) {
      newIzin.value.nisn = nisn
      scannedSantri.value = { nisn, nama_lengkap: 'NISN: ' + nisn }
      showToast(`QR terbaca: ${nisn} — Santri tidak ditemukan di daftar lokal`, 'error')
      await closeScanner()
    } else {
      scanError.value = `Format tidak dikenali: "${nisn.slice(0, 30)}". Pastikan QR Code berisi NISN.`
    }
  }
}

async function fetchSantri() {
  try {
    const res = await axios.get(`${API}/api/perijinan/santri`, { headers })
    santriList.value = res.data.data || []
  } catch {}
}

async function fetchIzinLogs() {
  isLoading.value = true
  try {
    const res = await axios.get(`${API}/api/perijinan/`, { headers })
    izinList.value = res.data.data || []
  } catch (e) {
    if (e.response?.status === 401) handleLogout()
  } finally {
    isLoading.value = false
  }
}

async function handleSave() {
  formError.value   = ''
  formSuccess.value = ''
  if (!newIzin.value.nisn || !newIzin.value.alasan || !newIzin.value.tanggal_mulai || !newIzin.value.tanggal_selesai) {
    formError.value = 'Semua field wajib diisi!'
    return
  }
  isSaving.value = true
  try {
    await axios.post(`${API}/api/perijinan/save`, newIzin.value, { headers })
    formSuccess.value = 'Pengajuan izin berhasil diajukan!'
    newIzin.value = { nisn: '', jenis_izin: 'Keluar Lingkungan', tanggal_mulai: '', tanggal_selesai: '', alasan: '' }
    await fetchIzinLogs()
    setTimeout(() => { showForm.value = false }, 1500)
    showToast('Izin berhasil diajukan!')
  } catch (e) {
    formError.value = e.response?.data?.message || 'Gagal menyimpan izin.'
  } finally {
    isSaving.value = false
  }
}

async function handleApprove(id) {
  if (!confirm('Setujui pengajuan izin ini?')) return
  try {
    await axios.post(`${API}/api/perijinan/approve/${id}`, {}, { headers })
    await fetchIzinLogs()
    showToast('Izin telah disetujui ✅')
  } catch { showToast('Gagal menyetujui izin', 'error') }
}

async function handleReject(id) {
  const catatan = prompt('Masukkan alasan penolakan:')
  if (catatan === null) return
  try {
    await axios.post(`${API}/api/perijinan/reject/${id}`, { catatan }, { headers })
    await fetchIzinLogs()
    showToast('Izin ditolak')
  } catch { showToast('Gagal menolak izin', 'error') }
}

async function handleAktifkan(id) {
  if (!confirm('Konfirmasi santri keluar dari pesantren?')) return
  try {
    await axios.post(`${API}/api/perijinan/aktifkan/${id}`, {}, { headers })
    await fetchIzinLogs()
    showToast('Status diperbarui: Santri keluar 🏃')
  } catch { showToast('Gagal memperbarui status', 'error') }
}

async function handleKembali(id) {
  if (!confirm('Konfirmasi santri telah kembali ke pesantren?')) return
  try {
    await axios.post(`${API}/api/perijinan/kembali/${id}`, {}, { headers })
    await fetchIzinLogs()
    showToast('Santri telah kembali ke pesantren 🏠')
  } catch { showToast('Gagal memperbarui status', 'error') }
}

async function handleDelete(id) {
  if (!confirm('Hapus data izin ini secara permanen?')) return
  try {
    await axios.delete(`${API}/api/perijinan/delete/${id}`, { headers })
    await fetchIzinLogs()
    showToast('Data izin berhasil dihapus')
  } catch { showToast('Gagal menghapus data', 'error') }
}

function handleLogout() {
  localStorage.removeItem('jwt_token')
  localStorage.removeItem('user_info')
  localStorage.removeItem('user')
  router.push('/login')
}

// Bersihkan scanner jika komponen unmount
import { onUnmounted } from 'vue'
onUnmounted(async () => { if (html5QrCode && scannerStarted.value) { try { await html5QrCode.stop(); html5QrCode.clear() } catch {} } })

onMounted(() => {
  fetchSantri()
  fetchIzinLogs()
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
* { margin: 0; padding: 0; box-sizing: border-box; }

/* Layout */
.dashboard-container { display: flex; height: 100vh; background: #0f1117; font-family: 'Inter', sans-serif; color: #e2e8f0; overflow: hidden; }

/* Main */
.main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }
.content-header { height: 80px; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(26,29,46,0.5); gap: 16px; flex-shrink: 0; }
.content-header h1 { font-size: 22px; font-weight: 700; }
.content-header p { font-size: 13px; color: #64748b; margin-top: 4px; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; padding: 20px 32px 0; }
.stat-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; gap: 14px; transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-2px); }
.stat-icon { font-size: 26px; }
.stat-num { font-size: 24px; font-weight: 700; line-height: 1; }
.stat-lbl { font-size: 11px; color: #64748b; margin-top: 4px; }
.stat-card.orange { border-color: rgba(251,146,60,0.2); background: rgba(251,146,60,0.05); }
.stat-card.orange .stat-num { color: #fb923c; }
.stat-card.green { border-color: rgba(52,211,153,0.2); background: rgba(52,211,153,0.05); }
.stat-card.green .stat-num { color: #34d399; }
.stat-card.red { border-color: rgba(248,113,113,0.2); background: rgba(248,113,113,0.05); }
.stat-card.red .stat-num { color: #f87171; }
.stat-card.blue { border-color: rgba(96,165,250,0.2); background: rgba(96,165,250,0.05); }
.stat-card.blue .stat-num { color: #60a5fa; }
.stat-card.gray { border-color: rgba(148,163,184,0.2); background: rgba(148,163,184,0.05); }
.stat-card.gray .stat-num { color: #94a3b8; }

/* Filter Chips */
.filter-bar { display: flex; gap: 8px; padding: 16px 32px 0; flex-wrap: wrap; }
.filter-chip { padding: 6px 16px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 12px; font-family: 'Inter', sans-serif; cursor: pointer; transition: all 0.2s; }
.filter-chip:hover { color: #e2e8f0; border-color: rgba(255,255,255,0.2); }
.chip-all    { background: rgba(255,255,255,0.08); color: #e2e8f0; border-color: rgba(255,255,255,0.2); }
.chip-orange { background: rgba(251,146,60,0.15);  color: #fb923c; border-color: rgba(251,146,60,0.3); }
.chip-green  { background: rgba(52,211,153,0.15);  color: #34d399; border-color: rgba(52,211,153,0.3); }
.chip-red    { background: rgba(248,113,113,0.15); color: #f87171; border-color: rgba(248,113,113,0.3); }
.chip-blue   { background: rgba(96,165,250,0.15);  color: #60a5fa; border-color: rgba(96,165,250,0.3); }
.chip-gray   { background: rgba(148,163,184,0.15); color: #94a3b8; border-color: rgba(148,163,184,0.3); }

/* Card */
.card { margin: 16px 32px 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
.card-header { padding: 18px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 12px; }
.card-header h3 { font-size: 16px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
.header-badge { background: rgba(124,58,237,0.2); color: #c084fc; font-size: 12px; padding: 2px 10px; border-radius: 12px; font-weight: 500; }
.header-actions { display: flex; gap: 10px; align-items: center; }
.search-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 14px; color: #e2e8f0; font-size: 13px; outline: none; width: 220px; font-family: 'Inter', sans-serif; }
.btn-refresh { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 12px; color: #94a3b8; cursor: pointer; font-size: 14px; transition: all 0.2s; }
.btn-refresh:hover { background: rgba(255,255,255,0.1); }
.spinning { display: inline-block; animation: spin 0.8s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }

/* Loading & Empty */
.loading-state { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 16px; padding: 60px; color: #64748b; }
.loader { width: 36px; height: 36px; border: 3px solid rgba(124,58,237,0.2); border-top-color: #7c3aed; border-radius: 50%; animation: spin 0.8s linear infinite; }
.empty-state { text-align: center; padding: 60px; color: #64748b; }
.empty-icon { font-size: 48px; margin-bottom: 12px; }
.empty-state h4 { color: #94a3b8; margin-bottom: 6px; font-size: 16px; }
.empty-state p { font-size: 13px; }

/* Table */
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table th { padding: 12px 14px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); white-space: nowrap; }
.data-table td { padding: 13px 14px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.row-aktif td { background: rgba(248,113,113,0.03); }
.row-pending td { background: rgba(251,146,60,0.03); }

/* Cell types */
.num-cell { color: #475569; width: 32px; }
.santri-info { display: flex; align-items: center; gap: 10px; }
.santri-avatar { width: 30px; height: 30px; background: linear-gradient(135deg, #7c3aed, #a855f7); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 12px; flex-shrink: 0; }
.name-cell { font-weight: 600; color: #c4b5fd; white-space: nowrap; }
.nisn-text { font-size: 11px; color: #64748b; margin-top: 2px; }
.alasan-cell { max-width: 160px; color: #94a3b8; font-size: 12px; }
.time-cell { white-space: nowrap; font-size: 12px; }
.muted { color: #64748b; }
.token-code { background: rgba(124,58,237,0.15); color: #c084fc; padding: 3px 8px; border-radius: 6px; font-size: 11px; font-family: monospace; white-space: nowrap; }

/* Jenis Badge */
.badge-jenis { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
.jenis-sakit  { background: rgba(251,146,60,0.15); color: #fb923c; }
.jenis-pulang { background: rgba(96,165,250,0.15);  color: #60a5fa; }
.jenis-keluar { background: rgba(52,211,153,0.15);  color: #34d399; }
.jenis-default{ background: rgba(148,163,184,0.15); color: #94a3b8; }

/* Status Pills */
.status-pill { display: inline-flex; align-items: center; gap: 5px; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; }
.pill-pending   { background: rgba(251,146,60,0.15); color: #fb923c; border: 1px solid rgba(251,146,60,0.3); }
.pill-disetujui { background: rgba(52,211,153,0.15); color: #34d399; border: 1px solid rgba(52,211,153,0.3); }
.pill-aktif     { background: rgba(248,113,113,0.2); color: #f87171; border: 1px solid rgba(248,113,113,0.4); animation: pulse-red 2s infinite; }
.pill-kembali   { background: rgba(96,165,250,0.15); color: #60a5fa; border: 1px solid rgba(96,165,250,0.3); }
.pill-ditolak   { background: rgba(148,163,184,0.1); color: #94a3b8; border: 1px solid rgba(148,163,184,0.2); }
@keyframes pulse-red { 0%, 100% { opacity: 1; } 50% { opacity: 0.65; } }

/* Action Buttons */
.action-group { display: flex; gap: 4px; flex-wrap: wrap; }
.act-btn { width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid transparent; cursor: pointer; font-size: 14px; transition: all 0.2s; background: rgba(255,255,255,0.04); }
.act-btn:hover { transform: scale(1.1); }
.act-approve { border-color: rgba(52,211,153,0.3); }
.act-approve:hover { background: rgba(52,211,153,0.15); }
.act-reject  { border-color: rgba(248,113,113,0.3); }
.act-reject:hover  { background: rgba(248,113,113,0.15); }
.act-keluar  { border-color: rgba(251,146,60,0.3); }
.act-keluar:hover  { background: rgba(251,146,60,0.15); }
.act-kembali { border-color: rgba(96,165,250,0.3); }
.act-kembali:hover { background: rgba(96,165,250,0.15); }
.act-delete  { border-color: rgba(148,163,184,0.2); }
.act-delete:hover  { background: rgba(248,113,113,0.1); border-color: rgba(248,113,113,0.3); }

/* Buttons */
.btn-primary { padding: 10px 20px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Inter', sans-serif; transition: opacity 0.2s; }
.btn-primary:hover { opacity: 0.85; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 10px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; color: #e2e8f0; font-size: 13px; cursor: pointer; font-family: 'Inter', sans-serif; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.65); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal-box { background: linear-gradient(145deg, #1a1d2e, #1e2338); border: 1px solid rgba(124,58,237,0.3); border-radius: 20px; padding: 0; width: 580px; max-width: 95vw; max-height: 88vh; overflow-y: auto; }
.modal-head { display: flex; align-items: center; justify-content: space-between; padding: 24px 28px 20px; border-bottom: 1px solid rgba(255,255,255,0.07); }
.modal-head h3 { font-size: 18px; font-weight: 700; color: #c084fc; }
.modal-close { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; width: 30px; height: 30px; color: #94a3b8; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; }
.modal-close:hover { background: rgba(248,113,113,0.15); color: #f87171; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 20px 28px; }
.form-group { display: flex; flex-direction: column; gap: 7px; }
.full-width { grid-column: 1 / -1; }
.form-group label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.6px; }
.form-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: #e2e8f0; font-size: 13px; outline: none; font-family: 'Inter', sans-serif; transition: border-color 0.2s; width: 100%; resize: vertical; }
.form-input:focus { border-color: #7c3aed; background: rgba(124,58,237,0.05); }
.form-input option { background: #1a1d2e; }

/* Jenis Selector */
.jenis-selector { display: flex; gap: 8px; flex-wrap: wrap; }
.jenis-btn { padding: 7px 14px; border-radius: 20px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 12px; cursor: pointer; font-family: 'Inter', sans-serif; transition: all 0.2s; }
.jenis-active { background: rgba(124,58,237,0.2); color: #c084fc; border-color: rgba(124,58,237,0.4); }

/* Alerts */
.alert-error   { margin: 0 28px; padding: 10px 14px; background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.3); border-radius: 10px; color: #f87171; font-size: 13px; }
.alert-success { margin: 0 28px; padding: 10px 14px; background: rgba(52,211,153,0.1); border: 1px solid rgba(52,211,153,0.3); border-radius: 10px; color: #34d399; font-size: 13px; }

.modal-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 20px 28px; border-top: 1px solid rgba(255,255,255,0.06); }

/* Toast */
.toast { position: fixed; bottom: 28px; right: 28px; padding: 13px 22px; border-radius: 12px; font-size: 13px; font-weight: 600; z-index: 200; pointer-events: none; }
.toast-success { background: rgba(52,211,153,0.95); color: #0f1117; }
.toast-error   { background: rgba(248,113,113,0.95); color: #fff; }
.toast-enter-active, .toast-leave-active { transition: all 0.35s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(12px); }

/* ===== QR SCANNER ===== */
.santri-input-row { display: flex; gap: 10px; align-items: stretch; }
.santri-input-row .form-input { flex: 1; }

.btn-scan {
  display: flex; align-items: center; gap: 8px;
  padding: 10px 16px;
  background: linear-gradient(135deg, #7c3aed, #a855f7);
  border: none; border-radius: 10px;
  color: white; font-size: 13px; font-weight: 600;
  cursor: pointer; white-space: nowrap;
  font-family: 'Inter', sans-serif;
  transition: all 0.2s; flex-shrink: 0;
}
.btn-scan:hover { opacity: 0.85; transform: scale(1.02); }

.scan-result {
  margin-top: 8px; padding: 8px 14px;
  background: rgba(52,211,153,0.1);
  border: 1px solid rgba(52,211,153,0.3);
  border-radius: 8px; font-size: 13px; color: #34d399;
  animation: fadeIn 0.3s ease;
}
.scan-result strong { color: #a7f3d0; }

/* Scanner Modal */
.scanner-overlay {
  position: fixed; inset: 0;
  background: rgba(0,0,0,0.8);
  backdrop-filter: blur(8px);
  display: flex; align-items: center; justify-content: center;
  z-index: 300;
}

.scanner-box {
  background: linear-gradient(145deg, #1a1d2e, #1e2338);
  border: 1px solid rgba(124,58,237,0.4);
  border-radius: 20px;
  width: 400px; max-width: 95vw;
  overflow: hidden;
  box-shadow: 0 25px 60px rgba(0,0,0,0.5), 0 0 0 1px rgba(124,58,237,0.15);
}

.scanner-head {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px;
  border-bottom: 1px solid rgba(255,255,255,0.07);
  background: rgba(124,58,237,0.1);
}
.scanner-head h3 { font-size: 16px; font-weight: 700; color: #c084fc; }

.scanner-body { padding: 20px; }

.scanner-frame {
  position: relative;
  background: #000;
  border-radius: 14px;
  overflow: hidden;
  min-height: 280px;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid rgba(124,58,237,0.4);
  box-shadow: inset 0 0 30px rgba(124,58,237,0.1);
}

/* Animasi sudut scanner */
.scanner-frame::before, .scanner-frame::after {
  content: '';
  position: absolute;
  width: 30px; height: 30px;
  border-color: #a855f7;
  border-style: solid;
  z-index: 10;
}
.scanner-frame::before { top: 12px; left: 12px; border-width: 3px 0 0 3px; border-radius: 4px 0 0 0; }
.scanner-frame::after  { top: 12px; right: 12px; border-width: 3px 3px 0 0; border-radius: 0 4px 0 0; }

/* QR reader container dari html5-qrcode */
#qr-reader { width: 100%; }
#qr-reader video { width: 100% !important; border-radius: 12px; }
#qr-reader__scan_region { border-radius: 12px; }
#qr-reader__dashboard { display: none !important; } /* Sembunyikan UI bawaan */

.scanner-hint {
  position: absolute; inset: 0;
  display: flex; flex-direction: column;
  align-items: center; justify-content: center; gap: 12px;
  color: #64748b; pointer-events: none;
}
.scanner-icon { font-size: 48px; animation: bounce 2s ease infinite; }
.scanner-hint p { font-size: 13px; text-align: center; }

/* Scan error & live result */
.scan-error {
  margin-top: 12px; padding: 10px 14px;
  background: rgba(248,113,113,0.1);
  border: 1px solid rgba(248,113,113,0.3);
  border-radius: 8px; font-size: 13px; color: #f87171;
}
.scan-live-result {
  margin-top: 12px; padding: 10px 14px;
  background: rgba(52,211,153,0.08);
  border: 1px solid rgba(52,211,153,0.25);
  border-radius: 8px; font-size: 13px; color: #34d399;
  display: flex; align-items: center; gap: 10px;
}
.scan-live-result strong { color: #a7f3d0; }

.pulse-dot {
  width: 10px; height: 10px; border-radius: 50%;
  background: #34d399; flex-shrink: 0;
  animation: pulse-green 1.2s ease infinite;
}

.scanner-footer {
  padding: 16px 24px;
  border-top: 1px solid rgba(255,255,255,0.06);
  display: flex; align-items: center; justify-content: space-between; gap: 12px;
  background: rgba(0,0,0,0.2);
}
.scanner-note { font-size: 12px; color: #64748b; flex: 1; }

/* Scanning line animation */
.scanner-frame::after {
  animation: corner-anim 3s ease-in-out infinite;
}
@keyframes corner-anim {
  0%, 100% { opacity: 0.6; }
  50% { opacity: 1; }
}

@keyframes bounce {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-8px); }
}
@keyframes pulse-green {
  0%, 100% { box-shadow: 0 0 0 0 rgba(52,211,153,0.6); }
  50% { box-shadow: 0 0 0 8px rgba(52,211,153,0); }
}
@keyframes fadeIn { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }
</style>

