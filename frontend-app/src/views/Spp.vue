<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>SPP &amp; Keuangan</h1>
          <p>Kelola tagihan bulanan/tahunan, riwayat pembayaran, tarif SPP, dan pemetaan santri</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="activeTab=t.key">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- STATS -->
      <div class="stats-grid">
        <div class="stat-card gold"><div class="stat-icon">💰</div><div><div class="stat-num">{{ formatRupiah(stats.total_terkumpul) }}</div><div class="stat-lbl">Total Terkumpul</div></div></div>
        <div class="stat-card red"><div class="stat-icon">⏳</div><div><div class="stat-num">{{ stats.belum_lunas }}</div><div class="stat-lbl">Belum Lunas</div></div></div>
        <div class="stat-card yellow"><div class="stat-icon">🔄</div><div><div class="stat-num">{{ stats.cicilan }}</div><div class="stat-lbl">Cicilan</div></div></div>
        <div class="stat-card green"><div class="stat-icon">✅</div><div><div class="stat-num">{{ stats.lunas }}</div><div class="stat-lbl">Lunas</div></div></div>
      </div>

      <!-- ========== TAB 1: TAGIHAN ========== -->
      <section v-if="activeTab==='tagihan'">
        <div class="card">
          <div class="card-header">
            <h3>Data Tagihan SPP</h3>
            <div class="header-actions">
              <button @click="showGenModal = true" class="btn-primary-purple">⚡ Generate Tagihan</button>
              <button @click="showBayarForm = true" class="btn-primary">+ Catat Pembayaran</button>
            </div>
          </div>
          <div class="search-bar">
            <input v-model="searchTagihan" placeholder="Cari nama santri..." class="search-input" />
            <select v-model="filterStatus" class="filter-select">
              <option value="">Semua Status</option>
              <option value="Belum Lunas">Belum Lunas</option>
              <option value="Cicilan">Cicilan</option>
              <option value="Lunas">Lunas</option>
            </select>
            <select v-model="filterBulan" class="filter-select">
              <option value="">Semua Bulan</option>
              <option v-for="(b, i) in bulanList" :key="i" :value="i">{{ b }}</option>
            </select>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Santri</th><th>Tagihan</th><th>Bulan</th><th>Tahun</th><th>Nominal</th><th>Diskon</th><th>Terbayar</th><th>Sisa</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="11" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="filteredTagihan.length===0"><td colspan="11" class="empty-cell">Tidak ada data tagihan</td></tr>
                <tr v-for="(t, i) in filteredTagihan" :key="t.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ t.nama_santri }}</td>
                  <td>{{ t.nama_tarif }}</td>
                  <td>{{ bulanList[t.bulan] || 'Tahunan' }}</td>
                  <td>{{ t.tahun }}</td>
                  <td>{{ formatRupiah(t.nominal_tagihan) }}</td>
                  <td class="discount-cell">{{ t.diskon > 0 ? formatRupiah(t.diskon) : '—' }}</td>
                  <td class="paid-cell">{{ formatRupiah(t.total_terbayar) }}</td>
                  <td :class="['sisa-cell', sisa(t) > 0 ? 'unpaid' : '']">{{ formatRupiah(sisa(t)) }}</td>
                  <td><span :class="['badge', statusClass(t.status)]">{{ t.status }}</span></td>
                  <td class="action-cell">
                    <button v-if="t.status !== 'Lunas'" @click="selectedTagihan = t; showBayarForm = true" class="btn-sm-green" title="Bayar">💳</button>
                    <button @click="deleteTagihan(t.id)" class="btn-danger-sm">🗑️</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 2: PEMBAYARAN ========== -->
      <section v-if="activeTab==='pembayaran'">
        <div class="card">
          <div class="card-header">
            <h3>Riwayat Pembayaran</h3>
          </div>
          <div class="search-bar">
            <input v-model="searchPembayaran" placeholder="Cari nama santri..." class="search-input" />
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Santri</th><th>Tagihan</th><th>Nominal Bayar</th><th>Tgl Bayar</th><th>Metode</th><th>No. Transaksi</th><th>Keterangan</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="filteredPembayaran.length===0"><td colspan="8" class="empty-cell">Tidak ada riwayat pembayaran</td></tr>
                <tr v-for="(p, i) in filteredPembayaran" :key="p.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ p.nama_santri }}</td>
                  <td>{{ p.nama_tarif }}</td>
                  <td class="paid-cell">{{ formatRupiah(p.nominal_bayar) }}</td>
                  <td>{{ formatDate(p.tanggal_bayar) }}</td>
                  <td><span class="badge badge-info">{{ p.metode_bayar }}</span></td>
                  <td><code>{{ p.nomor_transaksi || '-' }}</code></td>
                  <td class="muted">{{ p.keterangan || '—' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 3: TARIF SPP ========== -->
      <section v-if="activeTab==='tarif'">
        <div class="card">
          <div class="card-header">
            <h3>Master Tarif SPP</h3>
            <button @click="openAddTarif" class="btn-primary">+ Tambah Tarif</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Tarif</th><th>Tipe</th><th>Nominal</th><th>Tahun Ajaran</th><th>Keterangan</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="7" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="tarif.length===0"><td colspan="7" class="empty-cell">Belum ada master tarif</td></tr>
                <tr v-for="(tf, i) in tarif" :key="tf.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ tf.nama_tarif }}</td>
                  <td><span :class="['badge', tf.tipe==='Bulanan'?'badge-info':'badge-warning']">{{ tf.tipe }}</span></td>
                  <td class="paid-cell">{{ formatRupiah(tf.nominal) }}</td>
                  <td>{{ tf.nama_tahun || '—' }}</td>
                  <td class="muted">{{ tf.keterangan || '—' }}</td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditTarif(tf)" class="ab ab-blue" title="Edit">✏️</button>
                      <button @click="deleteTarif(tf.id, tf.nama_tarif)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 4: PEMETAAN TARIF ========== -->
      <section v-if="activeTab==='mapping'">
        <div class="card">
          <div class="card-header">
            <h3>Kesepakatan Bayaran Santri (Mapping Tarif)</h3>
            <input v-model="searchMapping" placeholder="Cari nama atau NISN santri..." class="search-input" />
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Santri</th><th>NISN</th><th>JK</th><th>Tarif yang Diikuti</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="6" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="filteredMapping.length===0"><td colspan="6" class="empty-cell">Tidak ada data santri</td></tr>
                <tr v-for="(m, i) in filteredMapping" :key="m.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ m.nama_lengkap }}</td>
                  <td><code>{{ m.nisn }}</code></td>
                  <td>{{ m.jenis_kelamin==='L'?'Laki-Laki':'Perempuan' }}</td>
                  <td>
                    <div class="mapping-chips-container">
                      <span v-for="c in m.mappings" :key="c.id" class="mchip" :title="c.keterangan_diskon">
                        {{ c.nama_tarif }} 
                        <strong v-if="c.nominal_diskon > 0" class="mchip-disc">(-{{ formatRupiah(c.nominal_diskon) }})</strong>
                      </span>
                      <span v-if="m.mappings.length===0" class="muted">Belum ada tarif</span>
                    </div>
                  </td>
                  <td>
                    <div class="action-group">
                      <button @click="openMappingModal(m)" class="ab ab-blue" title="Atur Pemetaan">⚙️ Atur Tarif</button>
                      <button @click="generateSantri(m.id)" class="ab ab-green" title="Generate Tagihan Bulan Ini">⚡ Tagih</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>

    <!-- ===== MODAL: GENERATE TAGIHAN MASSAL ===== -->
    <div v-if="showGenModal" class="modal-overlay" @click.self="showGenModal=false">
      <div class="modal-box">
        <div class="modal-head"><h3>⚡ Generate Tagihan SPP</h3><button @click="showGenModal=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Mode Pembuatan</label>
            <select v-model="genForm.mode" class="fi">
              <option value="mapping">Berdasarkan Kesepakatan (Mapping Tarif)</option>
              <option value="single">Satu Tarif untuk Semua Santri</option>
              <option value="yearly">Tahunan (Generate 12 Bulan Ajaran)</option>
            </select>
          </div>

          <!-- Jika Tahunan -->
          <div v-if="genForm.mode==='yearly'" class="fg full"><label>Tahun Akademik</label>
            <select v-model="genForm.id_tahun_akademik" class="fi">
              <option value="">— Pilih Tahun Akademik —</option>
              <option v-for="ta in tahunAkademik" :key="ta.id" :value="ta.id">{{ ta.nama_tahun }}</option>
            </select>
          </div>

          <!-- Jika Single -->
          <div v-if="genForm.mode==='single'" class="fg full"><label>Pilih Tarif SPP</label>
            <select v-model="genForm.tarif_id" class="fi">
              <option value="">— Pilih Tarif SPP —</option>
              <option v-for="t in tarif" :key="t.id" :value="t.id">{{ t.nama_tarif }} ({{ formatRupiah(t.nominal) }})</option>
            </select>
          </div>

          <!-- Bulan & Tahun (Kecuali Mode Yearly) -->
          <div v-if="genForm.mode!=='yearly'" class="fg"><label>Bulan Tagihan</label>
            <select v-model="genForm.bulan" class="fi">
              <option v-for="(b, i) in bulanList" :key="i" :value="i">{{ b }}</option>
            </select>
          </div>
          <div v-if="genForm.mode!=='yearly'" class="fg"><label>Tahun Tagihan</label>
            <input v-model="genForm.tahun" type="number" class="fi" placeholder="2026" />
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showGenModal=false" class="btn-secondary">Batal</button>
          <button @click="processGenerate" class="btn-primary" :disabled="saving">{{ saving?'Sedang Membuat...':'Generate Sekarang' }}</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: CATAT PEMBAYARAN ===== -->
    <div v-if="showBayarForm" class="modal-overlay" @click.self="closeBayarForm">
      <div class="modal-box">
        <div class="modal-head"><h3>💳 Catat Pembayaran SPP</h3><button @click="closeBayarForm" class="modal-close">✕</button></div>
        <div v-if="selectedTagihan" class="tagihan-info">
          <p>Santri: <strong>{{ selectedTagihan.nama_santri }}</strong></p>
          <p>Tagihan: {{ selectedTagihan.nama_tarif }} | Sisa: <strong class="unpaid">{{ formatRupiah(sisa(selectedTagihan)) }}</strong></p>
        </div>
        <div v-else class="form-group p20">
          <label>Pilih Tagihan</label>
          <select v-model="formBayar.tagihan_id" class="fi">
            <option value="">-- Pilih Tagihan --</option>
            <option v-for="t in tagihan.filter(x => x.status !== 'Lunas')" :key="t.id" :value="t.id">
              {{ t.nama_santri }} - {{ t.nama_tarif }} (Sisa: {{ formatRupiah(sisa(t)) }})
            </option>
          </select>
        </div>
        <div class="form-grid p20">
          <div class="fg"><label>Nominal Bayar *</label><input v-model="formBayar.nominal_bayar" type="number" class="fi" placeholder="0" /></div>
          <div class="fg"><label>Tanggal Bayar</label><input v-model="formBayar.tanggal_bayar" type="date" class="fi" /></div>
          <div class="fg"><label>Metode Pembayaran</label>
            <select v-model="formBayar.metode_bayar" class="fi">
              <option value="Tunai">Tunai</option><option value="Transfer">Transfer</option><option value="QRIS">QRIS</option>
            </select>
          </div>
          <div class="fg"><label>Keterangan</label><input v-model="formBayar.keterangan" class="fi" /></div>
        </div>
        <div class="modal-actions">
          <button @click="closeBayarForm" class="btn-secondary">Batal</button>
          <button @click="savePembayaran" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Pembayaran' }}</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: TARIF CRUD ===== -->
    <div v-if="showTarifForm" class="modal-overlay" @click.self="showTarifForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📋 {{ formTarif.id ? 'Edit Master Tarif' : 'Tambah Master Tarif' }}</h3><button @click="showTarifForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Nama Tarif *</label><input v-model="formTarif.nama_tarif" class="fi" placeholder="contoh: SPP Bulanan Kelas 7, Syahriah..." /></div>
          <div class="fg"><label>Tipe</label>
            <select v-model="formTarif.tipe" class="fi">
              <option value="Bulanan">Bulanan</option><option value="Tahunan">Tahunan</option>
            </select>
          </div>
          <div class="fg"><label>Nominal Tarif *</label><input v-model="formTarif.nominal" type="number" class="fi" placeholder="0" /></div>
          <div class="fg full"><label>Tahun Akademik</label>
            <select v-model="formTarif.id_tahun_akademik" class="fi">
              <option value="">— Pilih Tahun Akademik —</option>
              <option v-for="ta in tahunAkademik" :key="ta.id" :value="ta.id">{{ ta.nama_tahun }}</option>
            </select>
          </div>
          <div class="fg full"><label>Keterangan</label><input v-model="formTarif.keterangan" class="fi" placeholder="Keterangan opsional..." /></div>
        </div>
        <div class="modal-actions">
          <button @click="showTarifForm=false" class="btn-secondary">Batal</button>
          <button @click="saveTarif" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Tarif' }}</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: PEMETAAN TARIF DETAIL ===== -->
    <div v-if="mappingSantri" class="modal-overlay" @click.self="mappingSantri=null">
      <div class="modal-box modal-wide">
        <div class="modal-head">
          <h3>⚙️ Atur Tarif &amp; Diskon: {{ mappingSantri.nama_lengkap }}</h3>
          <button @click="mappingSantri=null" class="modal-close">✕</button>
        </div>
        <div class="p20">
          <div class="form-group full-width">
            <h4 style="margin-bottom: 12px; color: #a5b4fc">Pilih Tarif Kesepakatan</h4>
            <div v-if="tarif.length===0" class="empty-cell">Belum ada master tarif SPP</div>
            <div class="mapping-table-container">
              <table class="data-table">
                <thead><tr><th>Ikut?</th><th>Nama Tarif</th><th>Nominal Asli</th><th>Nominal Diskon</th><th>Keterangan Diskon</th></tr></thead>
                <tbody>
                  <tr v-for="t in tarif" :key="t.id">
                    <td>
                      <input type="checkbox" :value="t.id" v-model="formMapping.tarif_ids" style="width: 18px; height: 18px; cursor: pointer" />
                    </td>
                    <td class="name-cell">{{ t.nama_tarif }}</td>
                    <td>{{ formatRupiah(t.nominal) }}</td>
                    <td>
                      <input type="number" v-model="formMapping.nominal_diskon[t.id]" class="mini-input" placeholder="0" :disabled="!formMapping.tarif_ids.includes(t.id)" />
                    </td>
                    <td>
                      <input type="text" v-model="formMapping.keterangan_diskon[t.id]" class="mini-input-text" placeholder="misal: Beasiswa, Anak Yatim" :disabled="!formMapping.tarif_ids.includes(t.id)" />
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="mappingSantri=null" class="btn-secondary">Batal</button>
          <button @click="saveMapping" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Kesepakatan' }}</button>
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
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const router = useRouter()
const API    = 'http://127.0.0.1:8080/api'
const token  = localStorage.getItem('jwt_token')
const user   = JSON.parse(localStorage.getItem('user_info') || '{}')
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab       = ref('tagihan')
const loading         = ref(false)
const saving          = ref(false)
const showBayarForm   = ref(false)
const showGenModal    = ref(false)
const showTarifForm   = ref(false)
const selectedTagihan = ref(null)
const mappingSantri   = ref(null)

const tagihan         = ref([])
const pembayaran      = ref([])
const tarif           = ref([])
const mappingList     = ref([])
const tahunAkademik   = ref([])
const stats           = ref({ total_terkumpul: 0, belum_lunas: 0, cicilan: 0, lunas: 0 })

const searchTagihan   = ref('')
const searchPembayaran= ref('')
const searchMapping   = ref('')
const filterStatus    = ref('')
const filterBulan     = ref('')
const toast           = ref({ show: false, message: '', type: 'success' })

// Form states
const genForm = ref({ mode: 'mapping', tarif_id: '', id_tahun_akademik: '', bulan: dateMonth(), tahun: new Date().getFullYear() })
const formBayar = ref({ tagihan_id: '', nominal_bayar: '', tanggal_bayar: dateToday(), metode_bayar: 'Tunai', keterangan: '' })
const formTarif = ref({ id: '', nama_tarif: '', tipe: 'Bulanan', nominal: '', id_tahun_akademik: '', keterangan: '' })
const formMapping = ref({ tarif_ids: [], nominal_diskon: {}, keterangan_diskon: {} })

const tabs = [
  { key: 'tagihan', label: 'Tagihan SPP', icon: '🧾', color: 'gold' },
  { key: 'pembayaran', label: 'Riwayat Bayar', icon: '💳', color: 'green' },
  { key: 'tarif', label: 'Master Tarif', icon: '📋', color: 'blue' },
  { key: 'mapping', label: 'Mapping Tarif', icon: '⚙️', color: 'purple' },
]

const bulanList = ['Tahunan', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

// ===== COMPUTED =====
const filteredTagihan = computed(() => tagihan.value.filter(t => {
  const q  = !searchTagihan.value || t.nama_santri?.toLowerCase().includes(searchTagihan.value.toLowerCase())
  const st = !filterStatus.value || t.status === filterStatus.value
  const bl = filterBulan.value === '' || t.bulan == filterBulan.value
  return q && st && bl
}))

const filteredPembayaran = computed(() => pembayaran.value.filter(p =>
  !searchPembayaran.value || p.nama_santri?.toLowerCase().includes(searchPembayaran.value.toLowerCase())
))

const filteredMapping = computed(() => mappingList.value.filter(m =>
  !searchMapping.value || m.nama_lengkap?.toLowerCase().includes(searchMapping.value.toLowerCase()) || m.nisn?.includes(searchMapping.value)
))

// ===== HELPERS =====
function dateMonth() { return new Date().getMonth() + 1 }
function dateToday() { return new Date().toISOString().slice(0, 10) }
function sisa(t) { return Math.max(0, (t.nominal_tagihan - (t.diskon||0)) - (t.total_terbayar||0)) }
function statusClass(s) { return s==='Lunas'?'badge-success':s==='Cicilan'?'badge-warning':'badge-danger' }
function formatRupiah(n) { if(!n) return 'Rp 0'; return 'Rp ' + parseInt(n).toLocaleString('id-ID') }
function formatDate(d) { if(!d) return '—'; return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) }
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false,3000) }

// ===== METHODS =====
async function fetchAll() {
  loading.value = true
  try {
    const [resT, resP, resS, resTf, resMap, resTa] = await Promise.all([
      axios.get(`${API}/spp/tagihan`, { headers }),
      axios.get(`${API}/spp/pembayaran`, { headers }),
      axios.get(`${API}/spp/stats`, { headers }),
      axios.get(`${API}/spp/tarif`, { headers }),
      axios.get(`${API}/spp/mapping`, { headers }),
      axios.get(`${API}/spp/tahun-akademik`, { headers }),
    ])
    tagihan.value       = resT.data.data || []
    pembayaran.value    = resP.data.data || []
    stats.value         = resS.data.data || {}
    tarif.value         = resTf.data.data || []
    mappingList.value   = resMap.data.data || []
    tahunAkademik.value = resTa.data.data || []
  } catch { showNotif('Gagal memuat data SPP', 'error') }
  finally { loading.value = false }
}

// === GENERATE TAGIHAN ===
async function processGenerate() {
  saving.value = true
  try {
    let r;
    if (genForm.value.mode === 'yearly') {
      r = await axios.post(`${API}/spp/tagihan/generate-tahunan`, { id_tahun_akademik: genForm.value.id_tahun_akademik }, { headers })
    } else {
      r = await axios.post(`${API}/spp/tagihan/generate-massal`, genForm.value, { headers })
    }
    showGenModal.value = false
    await fetchAll()
    showNotif(r.data.message || 'Tagihan berhasil digenerate!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal men-generate tagihan', 'error')
  } finally { saving.value = false }
}

async function generateSantri(santri_id) {
  if (!confirm('Buat tagihan bulan ini untuk santri ini?')) return
  try {
    const r = await axios.post(`${API}/spp/tagihan/generate-santri/${santri_id}`, {}, { headers })
    await fetchAll()
    showNotif(r.data.message || 'Tagihan berhasil dibuat!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal men-generate tagihan', 'error')
  }
}

async function deleteTagihan(id) {
  if (!confirm('Hapus tagihan ini?')) return
  try {
    await axios.delete(`${API}/spp/tagihan/delete/${id}`, { headers })
    await fetchAll()
    showNotif('Tagihan berhasil dihapus!')
  } catch { showNotif('Gagal menghapus tagihan', 'error') }
}

// === CATAT BAYAR ===
function closeBayarForm() {
  showBayarForm.value = false
  selectedTagihan.value = null
  formBayar.value = { tagihan_id: '', nominal_bayar: '', tanggal_bayar: dateToday(), metode_bayar: 'Tunai', keterangan: '' }
}

async function savePembayaran() {
  const id = selectedTagihan.value?.id || formBayar.value.tagihan_id
  if (!id || !formBayar.value.nominal_bayar) return showNotif('Tagihan dan nominal bayar wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/spp/pembayaran/save`, { tagihan_id: id, ...formBayar.value }, { headers })
    closeBayarForm()
    await fetchAll()
    showNotif('Pembayaran berhasil dicatat!')
  } catch { showNotif('Gagal menyimpan pembayaran', 'error') }
  finally { saving.value = false }
}

// === TARIF CRUD ===
function openAddTarif() {
  formTarif.value = { id: '', nama_tarif: '', tipe: 'Bulanan', nominal: '', id_tahun_akademik: '', keterangan: '' }
  showTarifForm.value = true
}

function openEditTarif(tf) {
  formTarif.value = { ...tf }
  showTarifForm.value = true
}

async function saveTarif() {
  if (!formTarif.value.nama_tarif || !formTarif.value.nominal) return showNotif('Nama dan nominal wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/spp/tarif/save`, formTarif.value, { headers })
    showTarifForm.value = false
    await fetchAll()
    showNotif('Tarif master berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan tarif', 'error') }
  finally { saving.value = false }
}

async function deleteTarif(id, nama) {
  if (!confirm(`Hapus tarif master "${nama}"? Semua pemetaan santri dengan tarif ini juga akan terhapus.`)) return
  try {
    await axios.delete(`${API}/spp/tarif/delete/${id}`, { headers })
    await fetchAll()
    showNotif('Tarif berhasil dihapus!')
  } catch { showNotif('Gagal menghapus tarif', 'error') }
}

// === MAPPING SPP ===
async function openMappingModal(m) {
  mappingSantri.value = m
  formMapping.value = { tarif_ids: [], nominal_diskon: {}, keterangan_diskon: {} }
  try {
    const res = await axios.get(`${API}/spp/mapping/santri/${m.id}`, { headers })
    const data = res.data.data
    formMapping.value.tarif_ids = data.current.map(c => c.tarif_id)
    
    // Set diskon & ket
    tarif.value.forEach(t => {
      const match = data.current.find(c => c.tarif_id === t.id)
      formMapping.value.nominal_diskon[t.id] = match ? match.nominal_diskon : 0
      formMapping.value.keterangan_diskon[t.id] = match ? match.keterangan_diskon : ''
    })
  } catch {}
}

async function saveMapping() {
  saving.value = true
  try {
    await axios.post(`${API}/spp/mapping/save`, {
      nisn: mappingSantri.value.nisn,
      tarif_ids: formMapping.value.tarif_ids,
      nominal_diskon: formMapping.value.nominal_diskon,
      keterangan_diskon: formMapping.value.keterangan_diskon
    }, { headers })
    mappingSantri.value = null
    await fetchAll()
    showNotif('Pemetaan tarif santri disimpan!')
  } catch { showNotif('Gagal menyimpan pemetaan', 'error') }
  finally { saving.value = false }
}

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
.header-tabs { display: flex; gap: 8px; }
.tab-btn { padding: 8px 16px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 13px; cursor: pointer; transition: all 0.2s; }
.active-gold { background: rgba(251,191,36,0.15); color: #fbbf24; border-color: rgba(251,191,36,0.3); font-weight: 600; }
.active-green { background: rgba(52,211,153,0.15); color: #34d399; border-color: rgba(52,211,153,0.3); font-weight: 600; }
.active-blue { background: rgba(96,165,250,0.15); color: #60a5fa; border-color: rgba(96,165,250,0.3); font-weight: 600; }
.active-purple { background: rgba(167,139,250,0.15); color: #a78bfa; border-color: rgba(167,139,250,0.3); font-weight: 600; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; padding: 20px 32px 0; }
.stat-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 12px; padding: 16px 20px; display: flex; gap: 16px; align-items: center; }
.stat-icon { font-size: 28px; }
.stat-num { font-size: 20px; font-weight: 700; }
.stat-lbl { font-size: 12px; color: #64748b; }
.stat-card.gold .stat-num { color: #fbbf24; }
.stat-card.red .stat-num { color: #f87171; }
.stat-card.yellow .stat-num { color: #fb923c; }
.stat-card.green .stat-num { color: #34d399; }

/* Card */
.card { margin: 20px 32px 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
.card-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); }
.card-header h3 { font-size: 16px; font-weight: 600; }
.header-actions { display: flex; gap: 10px; }
.search-bar { display: flex; gap: 10px; padding: 16px 24px; border-bottom: 1px solid rgba(255,255,255,0.05); flex-wrap: wrap; }
.search-input, .filter-select { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 14px; color: #e2e8f0; font-size: 13px; outline: none; }
.search-input { flex: 1; min-width: 180px; }
.filter-select option { background: #1a1d2e; }

/* Table */
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.data-table td { padding: 13px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.name-cell { font-weight: 500; color: #c4b5fd; }
.paid-cell { color: #34d399; font-weight: 600; }
.discount-cell { color: #fb923c; }
.sisa-cell { color: #94a3b8; }
.sisa-cell.unpaid { color: #f87171; font-weight: 600; }
.loading-cell, .empty-cell { text-align: center; color: #64748b; padding: 40px !important; }
.action-cell { white-space: nowrap; display: flex; gap: 4px; }
code { background: rgba(255,255,255,0.07); padding: 2px 6px; border-radius: 4px; font-size: 11px; }

/* Mapping chips */
.mapping-chips-container { display: flex; gap: 6px; flex-wrap: wrap; }
.mchip { background: rgba(99,102,241,0.1); border: 1px solid rgba(99,102,241,0.25); color: #c4b5fd; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 500; }
.mchip-disc { color: #fb923c; }

/* Badges */
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
.badge-warning { background: rgba(251,146,60,0.15); color: #fb923c; }
.badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }
.badge-info { background: rgba(99,102,241,0.15); color: #818cf8; }

/* Action buttons */
.action-group { display: flex; gap: 4px; align-items: center; }
.ab { padding: 5px 10px; border-radius: 6px; border: 1px solid transparent; cursor: pointer; font-size: 12px; background: rgba(255,255,255,0.04); color: #e2e8f0; font-weight: 500; transition: all 0.2s; }
.ab:hover { transform: scale(1.05); }
.ab-blue { border-color: rgba(96,165,250,0.3); color: #60a5fa; }
.ab-blue:hover { background: rgba(96,165,250,0.15); }
.ab-green { border-color: rgba(52,211,153,0.3); color: #34d399; }
.ab-green:hover { background: rgba(52,211,153,0.15); }
.ab-del { border-color: rgba(248,113,113,0.2); color: #f87171; }
.ab-del:hover { background: rgba(248,113,113,0.15); }

/* Buttons */
.btn-primary { padding: 9px 18px; background: linear-gradient(135deg, #d97706, #f59e0b); border: none; border-radius: 8px; color: #0f1117; font-size: 13px; font-weight: 700; cursor: pointer; }
.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary:disabled, .btn-primary-purple:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; font-size: 13px; cursor: pointer; }
.btn-sm-green { padding: 5px 10px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); border-radius: 6px; color: #34d399; cursor: pointer; font-size: 12px; }
.btn-danger-sm { padding: 5px 10px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; color: #f87171; cursor: pointer; font-size: 12px; }

/* Modal */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 100; }
.modal-box { background: linear-gradient(135deg, #1a1d2e, #1e2235); border: 1px solid rgba(245,158,11,0.3); border-radius: 20px; padding: 0; width: 560px; max-width: 95vw; max-height: 85vh; overflow-y: auto; }
.modal-wide { width: 780px; }
.modal-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.07); }
.modal-head h3 { font-size: 17px; font-weight: 700; color: #fbbf24; }
.modal-close { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; width: 28px; height: 28px; color: #94a3b8; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; }
.modal-close:hover { background: rgba(248,113,113,0.15); color: #f87171; }
.tagihan-info { background: rgba(255,255,255,0.05); border-radius: 10px; padding: 14px 18px; margin: 20px 24px 0; font-size: 13px; line-height: 1.8; }
.tagihan-info strong { color: #c4b5fd; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.p20 { padding: 20px 24px; }
.fg { display: flex; flex-direction: column; gap: 6px; }
.full { grid-column: 1 / -1; }
.fg label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.fi { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: #e2e8f0; font-size: 13px; outline: none; width: 100%; font-family: 'Inter', sans-serif; }
.fi:focus { border-color: #f59e0b; }
.fi option { background: #1a1d2e; }
.modal-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.06); }
.muted { color: #64748b; font-size: 12px; }

/* Sub-inputs */
.mini-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 5px 10px; color: #e2e8f0; font-size: 12px; outline: none; width: 100px; }
.mini-input-text { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 5px 10px; color: #e2e8f0; font-size: 12px; outline: none; width: 100%; }
.mapping-table-container { max-height: 300px; overflow-y: auto; margin-top: 10px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05); }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
