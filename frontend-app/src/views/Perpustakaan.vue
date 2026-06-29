<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Perpustakaan</h1>
          <p>Kelola koleksi buku fisik (asrama putra/putri) dan e-book perpustakaan digital</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="activeTab=t.key">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card purple"><div class="stat-icon">📚</div><div><div class="stat-num">{{ stats.total_buku }}</div><div class="stat-lbl">Total Koleksi</div></div></div>
        <div class="stat-card blue"><div class="stat-icon">👦</div><div><div class="stat-num">{{ stats.total_putra }}</div><div class="stat-lbl">Perpustakaan Putra</div></div></div>
        <div class="stat-card pink"><div class="stat-icon">👧</div><div><div class="stat-num">{{ stats.total_putri }}</div><div class="stat-lbl">Perpustakaan Putri</div></div></div>
        <div class="stat-card green"><div class="stat-icon">📱</div><div><div class="stat-num">{{ stats.total_digital }}</div><div class="stat-lbl">E-Book &amp; Digital</div></div></div>
      </div>

      <!-- ========== BUKU LIST ========== -->
      <div class="card">
        <div class="card-header">
          <h3>Koleksi Buku - {{ tabs.find(x => x.key === activeTab)?.label }}</h3>
          <div class="header-actions">
            <input v-model="searchQuery" placeholder="Cari judul, pengarang..." class="search-input" />
            <select v-model="filterKategori" class="filter-select">
              <option value="">Semua Kategori</option>
              <option v-for="k in kategoriList" :key="k" :value="k">{{ k }}</option>
            </select>
            <button @click="openAddBuku" class="btn-primary">+ Tambah Buku</button>
          </div>
        </div>
        <div class="table-wrapper">
          <table class="data-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Kode Buku</th>
                <th>Judul Buku</th>
                <th>Pengarang</th>
                <th>Penerbit</th>
                <th>Tahun</th>
                <th>Kategori</th>
                <th>Stok</th>
                <th>Berkas / Link</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <tr v-if="loading"><td colspan="10" class="loading-cell">Memuat data buku...</td></tr>
              <tr v-else-if="filteredBuku.length === 0"><td colspan="10" class="empty-cell">Tidak ada koleksi buku</td></tr>
              <tr v-for="(b, i) in filteredBuku" :key="b.id">
                <td>{{ i+1 }}</td>
                <td><code>{{ b.kode_buku }}</code></td>
                <td class="name-cell">{{ b.judul }}</td>
                <td>{{ b.pengarang || '—' }}</td>
                <td>{{ b.penerbit || '—' }}</td>
                <td>{{ b.tahun_terbit || '—' }}</td>
                <td><span class="badge badge-info">{{ b.kategori }}</span></td>
                <td>
                  <span :class="['badge', b.stok > 0 ? 'badge-success' : 'badge-danger']">
                    {{ b.stok }} Buku
                  </span>
                </td>
                <td>
                  <a v-if="b.link_eksternal" :href="b.link_eksternal" target="_blank" class="read-link">
                    {{ b.is_drive ? '📂 Google Drive' : '🔗 Buka Link' }}
                  </a>
                  <span v-else class="muted">—</span>
                </td>
                <td>
                  <div class="action-group">
                    <button @click="openEditBuku(b)" class="ab ab-blue" title="Edit">✏️</button>
                    <button @click="deleteBuku(b.id, b.judul)" class="ab ab-del" title="Hapus">🗑️</button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </main>

    <!-- ===== MODAL: BUKU FORM ===== -->
    <div v-if="showBukuForm" class="modal-overlay" @click.self="showBukuForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📘 {{ formBuku.id ? 'Edit Buku' : 'Tambah Buku Baru' }}</h3><button @click="showBukuForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg"><label>Kode Buku *</label><input v-model="formBuku.kode_buku" class="fi" placeholder="P001, D005..." /></div>
          <div class="fg"><label>Judul Buku *</label><input v-model="formBuku.judul" class="fi" placeholder="Judul lengkap..." /></div>
          <div class="fg"><label>Pengarang</label><input v-model="formBuku.pengarang" class="fi" /></div>
          <div class="fg"><label>Penerbit</label><input v-model="formBuku.penerbit" class="fi" /></div>
          <div class="fg"><label>Tahun Terbit</label><input v-model="formBuku.tahun_terbit" type="number" class="fi" placeholder="YYYY" /></div>
          <div class="fg"><label>Kategori</label>
            <select v-model="formBuku.kategori" class="fi">
              <option v-for="k in kategoriList" :key="k" :value="k">{{ k }}</option>
            </select>
          </div>
          <div class="fg"><label>Stok</label><input v-model.number="formBuku.stok" type="number" class="fi" placeholder="0" /></div>
          <div class="fg"><label>Lokasi Penyimpanan</label>
            <select v-model="formBuku.lokasi" class="fi">
              <option value="Putra">Putra (Fisik)</option>
              <option value="Putri">Putri (Fisik)</option>
              <option value="Digital">Digital / E-Book</option>
            </select>
          </div>
          <div class="fg full"><label>Link E-Book / Digital File (Google Drive dll)</label><input v-model="formBuku.link_eksternal" class="fi" placeholder="https://..." /></div>
          <div class="fg full"><label>Deskripsi Singkat</label><input v-model="formBuku.deskripsi" class="fi" /></div>
        </div>
        <div class="modal-actions">
          <button @click="showBukuForm=false" class="btn-secondary">Batal</button>
          <button @click="saveBuku" class="btn-primary" :disabled="saving">Simpan</button>
        </div>
      </div>
    </div>

    <!-- Notification Toast -->
    <transition name="toast">
      <div v-if="toast.show" :class="['toast', 'toast-' + toast.type]">{{ toast.message }}</div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const API    = 'http://127.0.0.1:8080/api'
const token  = localStorage.getItem('jwt_token')
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab     = ref('putra')
const loading       = ref(false)
const saving        = ref(false)
const toast         = ref({ show: false, message: '', type: 'success' })

const buku          = ref([])
const stats         = ref({ total_buku: 0, total_putra: 0, total_putri: 0, total_digital: 0 })

const searchQuery   = ref('')
const filterKategori = ref('')
const showBukuForm  = ref(false)

const formBuku      = ref({ id: '', kode_buku: '', judul: '', pengarang: '', penerbit: '', tahun_terbit: '', kategori: 'Agama', stok: 1, lokasi: 'Putra', deskripsi: '', link_eksternal: '' })

const tabs = [
  { key: 'putra', label: 'Perpustakaan Putra', icon: '👦', color: 'blue' },
  { key: 'putri', label: 'Perpustakaan Putri', icon: '👧', color: 'pink' },
  { key: 'digital', label: 'E-Book / Digital', icon: '📱', color: 'green' },
]

const kategoriList = ['Agama', 'Bahasa', 'Sains', 'Sejarah', 'Sosial', 'Umum', 'Novel/Fiksi']

// ===== COMPUTED =====
const filteredBuku = computed(() => {
  return buku.value.filter(b => {
    const mapTab = { putra: 'Putra', putri: 'Putri', digital: 'Digital' }
    const matchTab  = b.lokasi === mapTab[activeTab.value]
    const matchCat  = !filterKategori.value || b.kategori === filterKategori.value
    const matchSearch = !searchQuery.value || 
                        b.judul?.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                        b.pengarang?.toLowerCase().includes(searchQuery.value.toLowerCase()) || 
                        b.kode_buku?.toLowerCase().includes(searchQuery.value.toLowerCase())
    return matchTab && matchCat && matchSearch
  })
})

// ===== HELPERS =====
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }

// ===== METHODS =====
async function fetchAll() {
  loading.value = true
  try {
    const [resB, resS] = await Promise.all([
      axios.get(`${API}/perpustakaan/buku`, { headers }),
      axios.get(`${API}/perpustakaan/stats`, { headers })
    ])
    buku.value  = resB.data.data || []
    stats.value = resS.data.data || {}
  } catch { showNotif('Gagal memuat perpustakaan', 'error') }
  finally { loading.value = false }
}

function openAddBuku() {
  const mapTab = { putra: 'Putra', putri: 'Putri', digital: 'Digital' }
  formBuku.value = { id: '', kode_buku: '', judul: '', pengarang: '', penerbit: '', tahun_terbit: new Date().getFullYear(), kategori: 'Agama', stok: 1, lokasi: mapTab[activeTab.value], deskripsi: '', link_eksternal: '' }
  showBukuForm.value = true
}

function openEditBuku(b) {
  formBuku.value = { ...b }
  showBukuForm.value = true
}

async function saveBuku() {
  if (!formBuku.value.judul || !formBuku.value.kode_buku) return showNotif('Judul & kode buku wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/perpustakaan/buku/save`, formBuku.value, { headers })
    showBukuForm.value = false
    await fetchAll()
    showNotif('Buku berhasil disimpan!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan buku', 'error')
  } finally { saving.value = false }
}

async function deleteBuku(id, judul) {
  if (!confirm(`Hapus buku "${judul}"?`)) return
  try {
    await axios.delete(`${API}/perpustakaan/buku/delete/${id}`, { headers })
    await fetchAll()
    showNotif('Buku berhasil dihapus!')
  } catch { showNotif('Gagal menghapus buku', 'error') }
}

// Watch tab to reset search/filters
watch(activeTab, () => {
  searchQuery.value = ''
  filterKategori.value = ''
})

onMounted(fetchAll)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-container { display: flex; height: 100vh; background: #0f1117; font-family: 'Inter', sans-serif; color: #e2e8f0; overflow: hidden; }

/* Main Content */
.main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }
.content-header { height: 80px; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(26,29,46,0.5); gap: 16px; flex-shrink: 0; }
.content-header h1 { font-size: 22px; font-weight: 700; }
.content-header p { font-size: 13px; color: #64748b; margin-top: 2px; }
.header-tabs { display: flex; gap: 6px; }
.tab-btn { padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 12px; cursor: pointer; transition: all 0.2s; white-space: nowrap; }

.active-blue { background: rgba(96,165,250,0.15); color: #60a5fa; border-color: rgba(96,165,250,0.3); font-weight: 600; }
.active-pink { background: rgba(244,114,182,0.15); color: #f472b6; border-color: rgba(244,114,182,0.3); font-weight: 600; }
.active-green { background: rgba(52,211,153,0.15); color: #34d399; border-color: rgba(52,211,153,0.3); font-weight: 600; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; padding: 20px 32px 0; }
.stat-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 12px; padding: 16px 20px; display: flex; gap: 16px; align-items: center; }
.stat-icon { font-size: 28px; }
.stat-num { font-size: 20px; font-weight: 700; }
.stat-lbl { font-size: 12px; color: #64748b; }
.stat-card.purple .stat-num { color: #a78bfa; }
.stat-card.blue .stat-num { color: #60a5fa; }
.stat-card.pink .stat-num { color: #f472b6; }
.stat-card.green .stat-num { color: #34d399; }

/* Card */
.card { margin: 20px 32px 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
.card-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 12px; }
.card-header h3 { font-size: 16px; font-weight: 600; }
.header-actions { display: flex; gap: 10px; align-items: center; }
.search-input, .filter-select { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 14px; color: #e2e8f0; font-size: 13px; outline: none; }
.filter-select option { background: #1a1d2e; }

/* Table */
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.data-table td { padding: 13px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.name-cell { font-weight: 500; color: #c4b5fd; }
.loading-cell, .empty-cell { text-align: center; color: #64748b; padding: 40px !important; }
code { background: rgba(255,255,255,0.07); padding: 2px 6px; border-radius: 4px; font-size: 11px; }

/* Read Link */
.read-link { color: #34d399; text-decoration: none; font-weight: 500; }
.read-link:hover { text-decoration: underline; }

/* Badges */
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
.badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }
.badge-info { background: rgba(99,102,241,0.15); color: #818cf8; }

/* Action buttons */
.action-group { display: flex; gap: 4px; align-items: center; }
.ab { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 7px; border: 1px solid transparent; cursor: pointer; font-size: 13px; background: rgba(255,255,255,0.04); transition: all 0.2s; }
.ab:hover { transform: scale(1.1); }
.ab-blue:hover { background: rgba(96,165,250,0.15); }
.ab-del:hover { background: rgba(248,113,113,0.1); }

/* Buttons */
.btn-primary { padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; font-size: 13px; cursor: pointer; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal-box { background: linear-gradient(135deg, #1a1d2e, #1e2235); border: 1px solid rgba(124,58,237,0.3); border-radius: 20px; padding: 0; width: 560px; max-width: 95vw; max-height: 85vh; overflow-y: auto; }
.modal-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.07); }
.modal-head h3 { font-size: 17px; font-weight: 700; color: #c084fc; }
.modal-close { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; width: 28px; height: 28px; color: #94a3b8; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; }
.modal-close:hover { background: rgba(248,113,113,0.15); color: #f87171; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.p20 { padding: 20px 24px; }
.fg { display: flex; flex-direction: column; gap: 6px; }
.full { grid-column: 1 / -1; }
.fg label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.fi { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: #e2e8f0; font-size: 13px; outline: none; width: 100%; font-family: 'Inter', sans-serif; }
.fi:focus { border-color: #7c3aed; }
.fi option { background: #1a1d2e; }
.modal-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.06); }
.muted { color: #64748b; font-size: 12px; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
