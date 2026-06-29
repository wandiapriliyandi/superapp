<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Kepegawaian</h1>
          <p>Kelola data pegawai/asatidz, pangkat/jabatan, absensi, pengajuan cuti, dan penggajian bulanan</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- ========== TAB 1: PEGAWAI ========== -->
      <section v-if="activeTab==='pegawai'">
        <div class="card">
          <div class="card-header">
            <h3>Daftar Pegawai &amp; Asatidz</h3>
            <div class="header-actions">
              <input v-model="searchPegawai" placeholder="Cari nama pegawai..." class="search-input" />
              <select v-model="filterDept" class="filter-select">
                <option value="">Semua Departemen</option>
                <option v-for="d in departemen" :key="d.id" :value="d.id">{{ d.nama_departemen }}</option>
              </select>
              <button @click="openAddPegawai" class="btn-primary">+ Tambah Pegawai</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Lengkap</th><th>NIK</th><th>Jenis Kelamin</th><th>Jabatan</th><th>Departemen</th><th>No. HP</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="9" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="filteredPegawai.length===0"><td colspan="9" class="empty-cell">Tidak ada data pegawai</td></tr>
                <tr v-for="(p, i) in filteredPegawai" :key="p.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ p.nama_lengkap }}</td>
                  <td><code>{{ p.nik || '—' }}</code></td>
                  <td>{{ p.jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                  <td><span class="badge badge-info">{{ p.nama_jabatan || '—' }}</span></td>
                  <td>{{ p.nama_departemen || '—' }}</td>
                  <td>{{ p.no_hp || '—' }}</td>
                  <td><span :class="['badge', p.status_pegawai==='Aktif'?'badge-success':'badge-danger']">{{ p.status_pegawai }}</span></td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditPegawai(p)" class="ab ab-blue">✏️</button>
                      <button @click="deletePegawai(p.id, p.nama_lengkap)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 2: JABATAN ========== -->
      <section v-if="activeTab==='jabatan'">
        <div class="card">
          <div class="card-header">
            <h3>Daftar Jabatan &amp; Pangkat</h3>
            <button @click="openAddJabatan" class="btn-primary">+ Tambah Jabatan</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Jabatan</th><th>Gaji Pokok</th><th>Tunjangan Makan</th><th>Tunjangan Transport</th><th>Total Gaji + Tunjangan</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="7" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="jabatan.length===0"><td colspan="7" class="empty-cell">Belum ada data jabatan</td></tr>
                <tr v-for="(j, i) in jabatan" :key="j.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ j.nama_jabatan }}</td>
                  <td class="paid-cell">{{ formatRupiah(j.gaji_pokok) }}</td>
                  <td>{{ formatRupiah(j.tunjangan_makan) }}</td>
                  <td>{{ formatRupiah(j.tunjangan_transport) }}</td>
                  <td class="paid-cell" style="color: #60a5fa">{{ formatRupiah(calcTotalSalary(j)) }}</td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditJabatan(j)" class="ab ab-blue">✏️</button>
                      <button @click="deleteJabatan(j.id, j.nama_jabatan)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 3: ABSENSI PEGAWAI ========== -->
      <section v-if="activeTab==='absensi'">
        <div class="card">
          <div class="card-header">
            <h3>Pencatatan Absensi Harian Pegawai</h3>
            <div class="header-actions">
              <input v-model="absensiTanggal" type="date" class="search-input" />
              <button @click="fetchAbsensi" class="btn-primary-purple">Cari Tanggal</button>
            </div>
          </div>
          <div v-if="absensiList.length > 0" class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Pegawai</th><th>NIK</th><th>Status</th><th>Jam Masuk</th><th>Jam Pulang</th><th>Keterangan</th></tr></thead>
              <tbody>
                <tr v-for="(a, i) in absensiList" :key="a.pegawai_id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ a.nama_lengkap }}</td>
                  <td><code>{{ a.nik }}</code></td>
                  <td>
                    <select v-model="a.status" class="filter-select mini-select">
                      <option value="Hadir">🟢 Hadir</option>
                      <option value="Sakit">🟡 Sakit</option>
                      <option value="Izin">🔵 Izin</option>
                      <option value="Alpa">🔴 Alpa</option>
                    </select>
                  </td>
                  <td><input v-model="a.jam_masuk" type="time" class="mini-input" :disabled="a.status!=='Hadir'" /></td>
                  <td><input v-model="a.jam_pulang" type="time" class="mini-input" :disabled="a.status!=='Hadir'" /></td>
                  <td><input v-model="a.keterangan" type="text" class="fi mini-input-text" placeholder="Keterangan opsional" /></td>
                </tr>
              </tbody>
            </table>
            <div class="p20" style="text-align: right">
              <button @click="saveAbsensi" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Absensi' }}</button>
            </div>
          </div>
          <div v-else class="empty-cell">Pilih tanggal dan klik Cari Tanggal untuk mulai mengisi absensi</div>
        </div>
      </section>

      <!-- ========== TAB 4: CUTI ========== -->
      <section v-if="activeTab==='cuti'">
        <div class="card">
          <div class="card-header">
            <h3>Daftar Pengajuan Cuti Pegawai</h3>
            <button @click="showCutiForm=true" class="btn-primary">+ Ajukan Cuti</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Pegawai</th><th>NIK</th><th>Jenis Cuti</th><th>Mulai</th><th>Selesai</th><th>Alasan</th><th>Status</th><th>Disetujui Oleh</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="10" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="cutiList.length===0"><td colspan="10" class="empty-cell">Tidak ada pengajuan cuti</td></tr>
                <tr v-for="(c, i) in cutiList" :key="c.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ c.nama_lengkap }}</td>
                  <td><code>{{ c.nik }}</code></td>
                  <td><span class="badge badge-info">{{ c.jenis_cuti }}</span></td>
                  <td>{{ formatDate(c.tanggal_mulai) }}</td>
                  <td>{{ formatDate(c.tanggal_selesai) }}</td>
                  <td>{{ c.alasan || '—' }}</td>
                  <td>
                    <span :class="['badge', c.status==='Disetujui'?'badge-success':c.status==='Ditolak'?'badge-danger':'badge-warning']">
                      {{ c.status }}
                    </span>
                  </td>
                  <td>{{ c.disetujui_oleh || '—' }}</td>
                  <td>
                    <div v-if="c.status==='Pending'" class="action-group">
                      <button @click="setCutiStatus(c.id, 'approve')" class="ab ab-green" title="Setujui">✅</button>
                      <button @click="setCutiStatus(c.id, 'reject')" class="ab ab-del" title="Tolak">❌</button>
                    </div>
                    <span v-else>—</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 5: PAYROLL & GAJI ========== -->
      <section v-if="activeTab==='payroll'">
        <div class="card">
          <div class="card-header">
            <h3>Payroll &amp; Slip Gaji Pegawai</h3>
            <div class="header-actions">
              <select v-model="payrollParams.bulan" class="filter-select">
                <option v-for="(b, i) in bulanList" :key="i" :value="i+1">{{ b }}</option>
              </select>
              <input v-model="payrollParams.tahun" type="number" class="search-input" style="width: 100px" />
              <button @click="fetchPayroll" class="btn-primary-purple">Tampilkan</button>
              <button @click="generatePayroll" class="btn-primary" :disabled="saving">⚡ Generate Slip Gaji</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Pegawai</th><th>Jabatan</th><th>Gaji Pokok</th><th>Total Tunjangan</th><th>Potongan</th><th>Gaji Bersih</th><th>Status</th><th>Tanggal Bayar</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="10" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="payrollList.length===0"><td colspan="10" class="empty-cell">Belum ada slip gaji digenerate untuk bulan ini</td></tr>
                <tr v-for="(p, i) in payrollList" :key="p.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ p.nama_lengkap }}</td>
                  <td>{{ p.nama_jabatan || '—' }}</td>
                  <td>{{ formatRupiah(p.gaji_pokok) }}</td>
                  <td>{{ formatRupiah(p.total_tunjangan) }}</td>
                  <td>{{ formatRupiah(p.potongan) }}</td>
                  <td class="paid-cell">{{ formatRupiah(p.gaji_bersih) }}</td>
                  <td>
                    <span :class="['badge', p.status_bayar==='Dibayar'?'badge-success':'badge-danger']">
                      {{ p.status_bayar }}
                    </span>
                  </td>
                  <td>{{ formatDate(p.tanggal_bayar) }}</td>
                  <td>
                    <button v-if="p.status_bayar==='Belum Dibayar'" @click="bayarPayroll(p.id)" class="btn-sm-green">💳 Bayar</button>
                    <span v-else>✅</span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>
    </main>

    <!-- ===== MODAL: PEGAWAI FORM ===== -->
    <div v-if="showPegawaiForm" class="modal-overlay" @click.self="showPegawaiForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📋 {{ formPegawai.id?'Edit Data Pegawai':'Tambah Pegawai Baru' }}</h3><button @click="showPegawaiForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Nama Lengkap *</label><input v-model="formPegawai.nama_lengkap" class="fi" /></div>
          <div class="fg"><label>NIK</label><input v-model="formPegawai.nik" class="fi" /></div>
          <div class="fg"><label>Jenis Kelamin</label>
            <select v-model="formPegawai.jenis_kelamin" class="fi">
              <option value="L">Laki-Laki</option><option value="P">Perempuan</option>
            </select>
          </div>
          <div class="fg"><label>Tempat Lahir</label><input v-model="formPegawai.tempat_lahir" class="fi" /></div>
          <div class="fg"><label>Tanggal Lahir</label><input v-model="formPegawai.tanggal_lahir" type="date" class="fi" /></div>
          <div class="fg"><label>No. HP</label><input v-model="formPegawai.no_hp" class="fi" /></div>
          <div class="fg"><label>Email</label><input v-model="formPegawai.email" type="email" class="fi" /></div>
          <div class="fg"><label>Jabatan</label>
            <select v-model="formPegawai.jabatan_id" class="fi">
              <option value="">-- Pilih --</option>
              <option v-for="j in jabatan" :key="j.id" :value="j.id">{{ j.nama_jabatan }}</option>
            </select>
          </div>
          <div class="fg"><label>Departemen</label>
            <select v-model="formPegawai.departemen_id" class="fi">
              <option value="">-- Pilih --</option>
              <option v-for="d in departemen" :key="d.id" :value="d.id">{{ d.nama_departemen }}</option>
            </select>
          </div>
          <div class="fg"><label>Status Pegawai</label>
            <select v-model="formPegawai.status_pegawai" class="fi">
              <option value="Aktif">Aktif</option><option value="Nonaktif">Nonaktif</option>
            </select>
          </div>
          <div class="fg"><label>Tanggal Masuk Kerja</label><input v-model="formPegawai.tanggal_masuk" type="date" class="fi" /></div>
        </div>
        <div class="modal-actions"><button @click="showPegawaiForm=false" class="btn-secondary">Batal</button><button @click="savePegawai" class="btn-primary" :disabled="saving">Simpan</button></div>
      </div>
    </div>

    <!-- ===== MODAL: JABATAN FORM ===== -->
    <div v-if="showJabatanForm" class="modal-overlay" @click.self="showJabatanForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>👥 {{ formJabatan.id?'Edit Jabatan':'Tambah Jabatan' }}</h3><button @click="showJabatanForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Nama Jabatan *</label><input v-model="formJabatan.nama_jabatan" class="fi" /></div>
          <div class="fg"><label>Gaji Pokok *</label><input v-model="formJabatan.gaji_pokok" type="number" class="fi" /></div>
          <div class="fg"><label>Tunjangan Makan</label><input v-model="formJabatan.tunjangan_makan" type="number" class="fi" /></div>
          <div class="fg full"><label>Tunjangan Transport</label><input v-model="formJabatan.tunjangan_transport" type="number" class="fi" /></div>
        </div>
        <div class="modal-actions"><button @click="showJabatanForm=false" class="btn-secondary">Batal</button><button @click="saveJabatan" class="btn-primary" :disabled="saving">Simpan</button></div>
      </div>
    </div>

    <!-- ===== MODAL: CUTI FORM ===== -->
    <div v-if="showCutiForm" class="modal-overlay" @click.self="showCutiForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>🏖️ Pengajuan Cuti Pegawai</h3><button @click="showCutiForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Pegawai *</label>
            <select v-model="formCuti.pegawai_id" class="fi">
              <option value="">-- Pilih Pegawai --</option>
              <option v-for="p in pegawai" :key="p.id" :value="p.id">{{ p.nama_lengkap }} ({{ p.nik }})</option>
            </select>
          </div>
          <div class="fg full"><label>Jenis Cuti</label>
            <select v-model="formCuti.jenis_cuti" class="fi">
              <option value="Tahunan">Tahunan</option><option value="Sakit">Sakit</option><option value="Melahirkan">Melahirkan</option><option value="Penting">Alasan Penting</option>
            </select>
          </div>
          <div class="fg"><label>Tanggal Mulai *</label><input v-model="formCuti.tanggal_mulai" type="date" class="fi" /></div>
          <div class="fg"><label>Tanggal Selesai *</label><input v-model="formCuti.tanggal_selesai" type="date" class="fi" /></div>
          <div class="fg full"><label>Alasan Cuti</label><input v-model="formCuti.alasan" class="fi" placeholder="Keterangan keperluan..." /></div>
        </div>
        <div class="modal-actions"><button @click="showCutiForm=false" class="btn-secondary">Batal</button><button @click="saveCuti" class="btn-primary" :disabled="saving">Ajukan</button></div>
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
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab       = ref('pegawai')
const loading         = ref(false)
const saving          = ref(false)
const toast           = ref({ show: false, message: '', type: 'success' })

const pegawai         = ref([])
const jabatan         = ref([])
const departemen      = ref([])
const absensiList     = ref([])
const cutiList        = ref([])
const payrollList     = ref([])

const searchPegawai   = ref('')
const filterDept      = ref('')
const absensiTanggal  = ref(new Date().toISOString().slice(0, 10))

const showPegawaiForm = ref(false)
const showJabatanForm = ref(false)
const showCutiForm    = ref(false)

const formPegawai     = ref({ id: '', nama_lengkap: '', nik: '', jenis_kelamin: 'L', tempat_lahir: '', tanggal_lahir: '', no_hp: '', email: '', jabatan_id: '', departemen_id: '', status_pegawai: 'Aktif', tanggal_masuk: '' })
const formJabatan     = ref({ id: '', nama_jabatan: '', gaji_pokok: '', tunjangan_makan: '', tunjangan_transport: '' })
const formCuti        = ref({ pegawai_id: '', jenis_cuti: 'Tahunan', tanggal_mulai: '', tanggal_selesai: '', alasan: '' })

const payrollParams   = ref({ bulan: new Date().getMonth() + 1, tahun: new Date().getFullYear() })

const tabs = [
  { key: 'pegawai', label: 'Pegawai & Asatidz', icon: '👥', color: 'blue' },
  { key: 'jabatan', label: 'Jabatan', icon: '🎓', color: 'purple' },
  { key: 'absensi', label: 'Absensi', icon: '📝', color: 'teal' },
  { key: 'cuti', label: 'Cuti', icon: '🏖️', color: 'green' },
  { key: 'payroll', label: 'Slip Gaji', icon: '💰', color: 'gold' },
]

const bulanList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember']

// ===== COMPUTED =====
const filteredPegawai = computed(() => pegawai.value.filter(p => {
  const matchSearch = !searchPegawai.value || p.nama_lengkap.toLowerCase().includes(searchPegawai.value.toLowerCase())
  const matchDept   = !filterDept.value || p.departemen_id == filterDept.value
  return matchSearch && matchDept
}))

// ===== HELPERS =====
function calcTotalSalary(j) {
  return (parseFloat(j.gaji_pokok)||0) + (parseFloat(j.tunjangan_makan)||0) + (parseFloat(j.tunjangan_transport)||0)
}
function formatRupiah(n) { if(!n) return 'Rp 0'; return 'Rp ' + parseInt(n).toLocaleString('id-ID') }
function formatDate(d) { if(!d) return '—'; return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) }
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }

// ===== METHODS =====
async function switchTab(key) {
  activeTab.value = key
  if (key === 'pegawai') await fetchPegawai()
  if (key === 'jabatan') await fetchJabatan()
  if (key === 'cuti') await fetchCuti()
  if (key === 'payroll') await fetchPayroll()
}

// === FETCH DATA ===
async function fetchPegawai() {
  loading.value = true
  try { pegawai.value = (await axios.get(`${API}/kepegawaian/pegawai`, { headers })).data.data || [] }
  catch { showNotif('Gagal memuat data pegawai', 'error') }
  finally { loading.value = false }
}

async function fetchJabatan() {
  try { jabatan.value = (await axios.get(`${API}/kepegawaian/jabatan`, { headers })).data.data || [] }
  catch {}
}

async function fetchDepartemen() {
  try { departemen.value = (await axios.get(`${API}/kepegawaian/departemen`, { headers })).data.data || [] }
  catch {}
}

// === PEGAWAI CRUD ===
function openAddPegawai() {
  formPegawai.value = { id: '', nama_lengkap: '', nik: '', jenis_kelamin: 'L', tempat_lahir: '', tanggal_lahir: '', no_hp: '', email: '', jabatan_id: '', departemen_id: '', status_pegawai: 'Aktif', tanggal_masuk: '' }
  showPegawaiForm.value = true
}

function openEditPegawai(p) {
  formPegawai.value = { ...p }
  showPegawaiForm.value = true
}

async function savePegawai() {
  if (!formPegawai.value.nama_lengkap) return showNotif('Nama lengkap wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/kepegawaian/pegawai/save`, formPegawai.value, { headers })
    showPegawaiForm.value = false
    await fetchPegawai()
    showNotif('Data pegawai berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan data pegawai', 'error') }
  finally { saving.value = false }
}

async function deletePegawai(id, nama) {
  if (!confirm(`Hapus pegawai "${nama}"?`)) return
  try {
    await axios.delete(`${API}/kepegawaian/pegawai/delete/${id}`, { headers })
    await fetchPegawai()
    showNotif('Pegawai berhasil dihapus!')
  } catch { showNotif('Gagal menghapus pegawai', 'error') }
}

// === JABATAN CRUD ===
function openAddJabatan() {
  formJabatan.value = { id: '', nama_jabatan: '', gaji_pokok: '', tunjangan_makan: '', tunjangan_transport: '' }
  showJabatanForm.value = true
}

function openEditJabatan(j) {
  formJabatan.value = { ...j }
  showJabatanForm.value = true
}

async function saveJabatan() {
  if (!formJabatan.value.nama_jabatan) return showNotif('Nama jabatan wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/kepegawaian/jabatan/save`, formJabatan.value, { headers })
    showJabatanForm.value = false
    await fetchJabatan()
    showNotif('Data jabatan berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan jabatan', 'error') }
  finally { saving.value = false }
}

async function deleteJabatan(id, nama) {
  if (!confirm(`Hapus jabatan "${nama}"?`)) return
  try {
    await axios.delete(`${API}/kepegawaian/jabatan/delete/${id}`, { headers })
    await fetchJabatan()
    showNotif('Jabatan berhasil dihapus!')
  } catch { showNotif('Gagal menghapus jabatan', 'error') }
}

// === ABSENSI PEGAWAI ===
async function fetchAbsensi() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/kepegawaian/absensi?tanggal=${absensiTanggal.value}`, { headers })
    absensiList.value = res.data.data || []
  } catch { showNotif('Gagal memuat absensi', 'error') }
  finally { loading.value = false }
}

async function saveAbsensi() {
  saving.value = true
  try {
    await axios.post(`${API}/kepegawaian/absensi/save`, {
      tanggal: absensiTanggal.value,
      records: absensiList.value
    }, { headers })
    showNotif('Absensi pegawai berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan absensi', 'error') }
  finally { saving.value = false }
}

// === CUTI PEGAWAI ===
async function fetchCuti() {
  loading.value = true
  try { cutiList.value = (await axios.get(`${API}/kepegawaian/cuti`, { headers })).data.data || [] }
  catch { showNotif('Gagal memuat data cuti', 'error') }
  finally { loading.value = false }
}

async function saveCuti() {
  if (!formCuti.value.pegawai_id || !formCuti.value.tanggal_mulai || !formCuti.value.tanggal_selesai) {
    return showNotif('Field bertanda * wajib diisi', 'error')
  }
  saving.value = true
  try {
    await axios.post(`${API}/kepegawaian/cuti/save`, formCuti.value, { headers })
    showCutiForm.value = false
    await fetchCuti()
    showNotif('Cuti berhasil diajukan!')
  } catch { showNotif('Gagal mengajukan cuti', 'error') }
  finally { saving.value = false }
}

async function setCutiStatus(id, status) {
  if (!confirm(`Apakah Anda yakin ingin memproses status cuti ini?`)) return
  try {
    await axios.post(`${API}/kepegawaian/cuti/status/${id}/${status}`, {}, { headers })
    await fetchCuti()
    showNotif('Status cuti diperbarui!')
  } catch { showNotif('Gagal memproses cuti', 'error') }
}

// === PAYROLL ===
async function fetchPayroll() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/kepegawaian/payroll?bulan=${payrollParams.value.bulan}&tahun=${payrollParams.value.tahun}`, { headers })
    payrollList.value = res.data.data || []
  } catch { showNotif('Gagal memuat slip gaji', 'error') }
  finally { loading.value = false }
}

async function generatePayroll() {
  saving.value = true
  try {
    const res = await axios.post(`${API}/kepegawaian/payroll/generate`, {
      bulan: payrollParams.value.bulan,
      tahun: payrollParams.value.tahun
    }, { headers })
    await fetchPayroll()
    showNotif(res.data.message || 'Slip gaji berhasil digenerate!')
  } catch { showNotif('Gagal generate slip gaji', 'error') }
  finally { saving.value = false }
}

async function bayarPayroll(id) {
  if (!confirm('Tandai slip gaji ini sudah dibayarkan secara tunai/transfer?')) return
  try {
    await axios.post(`${API}/kepegawaian/payroll/bayar/${id}`, {}, { headers })
    await fetchPayroll()
    showNotif('Pembayaran gaji berhasil dikonfirmasi!')
  } catch { showNotif('Gagal memproses pembayaran gaji', 'error') }
}

// === INIT ===
async function init() {
  loading.value = true
  try {
    await Promise.all([
      fetchPegawai(),
      fetchJabatan(),
      fetchDepartemen()
    ])
  } catch {}
  finally { loading.value = false }
}

onMounted(init)
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
.header-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
.tab-btn { padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 12px; cursor: pointer; transition: all 0.2s; white-space: nowrap; }

.active-blue { background: rgba(96,165,250,0.15); color: #60a5fa; border-color: rgba(96,165,250,0.3); font-weight: 600; }
.active-purple { background: rgba(167,139,250,0.15); color: #a78bfa; border-color: rgba(167,139,250,0.3); font-weight: 600; }
.active-teal { background: rgba(45,212,191,0.15); color: #2dd4bf; border-color: rgba(45,212,191,0.3); font-weight: 600; }
.active-green { background: rgba(52,211,153,0.15); color: #34d399; border-color: rgba(52,211,153,0.3); font-weight: 600; }
.active-gold { background: rgba(251,191,36,0.15); color: #fbbf24; border-color: rgba(251,191,36,0.3); font-weight: 600; }

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
.paid-cell { color: #34d399; font-weight: 600; }
.loading-cell, .empty-cell { text-align: center; color: #64748b; padding: 40px !important; }
code { background: rgba(255,255,255,0.07); padding: 2px 6px; border-radius: 4px; font-size: 11px; }

/* Badges */
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
.badge-warning { background: rgba(251,146,60,0.15); color: #fb923c; }
.badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }
.badge-info { background: rgba(99,102,241,0.15); color: #818cf8; }

/* Action buttons */
.action-group { display: flex; gap: 4px; align-items: center; }
.ab { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 7px; border: 1px solid transparent; cursor: pointer; font-size: 13px; background: rgba(255,255,255,0.04); transition: all 0.2s; }
.ab:hover { transform: scale(1.1); }
.ab-blue:hover { background: rgba(96,165,250,0.15); }
.ab-green:hover { background: rgba(52,211,153,0.15); }
.ab-del:hover { background: rgba(248,113,113,0.1); }

/* Buttons */
.btn-primary { padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary:disabled, .btn-primary-purple:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; font-size: 13px; cursor: pointer; }
.btn-sm-green { padding: 5px 10px; background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); border-radius: 6px; color: #34d399; cursor: pointer; font-size: 12px; }

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

/* Sub-inputs */
.mini-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 5px 10px; color: #e2e8f0; font-size: 12px; outline: none; width: 80px; }
.mini-input-text { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 6px; padding: 5px 10px; color: #e2e8f0; font-size: 12px; outline: none; width: 100%; }
.mini-select { padding: 5px 10px; font-size: 12px; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
