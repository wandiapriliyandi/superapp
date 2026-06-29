<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Sarana &amp; Prasarana (Sarpras)</h1>
          <p>Manajemen aset inventaris pondok pesantren, pencatatan mutasi stok, dan pelacakan peminjaman barang</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- ==================== TAB 1: DAFTAR BARANG (MASTER INVENTORY) ==================== -->
      <section v-if="activeTab==='barang'" class="content-body">
        <div class="card">
          <div class="card-header">
            <h3>Persediaan Aset &amp; Barang Inventaris</h3>
            <div class="header-actions">
              <select v-model="barangLimit" class="fi" style="width: 100px; height: 36px; padding: 4px 8px; font-size: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; outline: none;">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
              </select>
              <input v-model="searchBarang" @input="debounceBarangSearch" placeholder="Cari nama atau kode barang..." class="search-input" />
              <button @click="openAddBarang" class="btn-primary">+ Daftarkan Barang</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>Kode Barang</th><th>Nama Barang</th><th>Kategori</th><th>Stok Tersedia</th><th>Satuan</th><th>Lokasi Penyimpanan</th><th>Kondisi</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat data inventaris...</td></tr>
                <tr v-else-if="barangList.length===0"><td colspan="8" class="empty-cell">Tidak ada data barang inventaris</td></tr>
                <tr v-for="b in barangList" :key="b.id">
                  <td><code>{{ b.kode_barang || '—' }}</code></td>
                  <td class="name-cell">{{ b.nama_barang }}</td>
                  <td><span class="badge badge-info">{{ b.kategori || 'Umum' }}</span></td>
                  <td><strong>{{ b.stok }}</strong></td>
                  <td>{{ b.satuan }}</td>
                  <td>{{ b.lokasi || '—' }}</td>
                  <td>
                    <span :class="['badge', b.kondisi==='Baik'?'badge-success':(b.kondisi==='Rusak Ringan'?'badge-warning':'badge-danger')]">
                      {{ b.kondisi }}
                    </span>
                  </td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditBarang(b)" class="ab ab-blue" title="Edit">✏️</button>
                      <button @click="deleteBarang(b.id, b.nama_barang)" class="ab ab-del" title="Hapus">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Paginasi Barang -->
          <div v-if="barangTotalPages > 1" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 10px; padding: 14px 20px;">
            <span style="font-size: 12px; color: #64748b;">
              Menampilkan <strong>{{ (barangPage - 1) * barangLimit + 1 }}</strong> – 
              <strong>{{ Math.min(barangPage * barangLimit, barangTotal) }}</strong> dari 
              <strong>{{ barangTotal }}</strong> barang
            </span>
            <div style="display: flex; gap: 5px;">
              <button @click="changeBarangPage(1)" :disabled="barangPage===1" class="tab-btn" style="padding:5px 9px;">«</button>
              <button @click="changeBarangPage(barangPage - 1)" :disabled="barangPage===1" class="tab-btn" style="padding:5px 10px;">‹</button>
              <button v-for="p in barangPaginationRange" :key="p" @click="changeBarangPage(p)" :class="['tab-btn', barangPage===p ? 'active-indigo' : '']" style="padding:5px 10px;">{{ p }}</button>
              <button @click="changeBarangPage(barangPage + 1)" :disabled="barangPage===barangTotalPages" class="tab-btn" style="padding:5px 10px;">›</button>
              <button @click="changeBarangPage(barangTotalPages)" :disabled="barangPage===barangTotalPages" class="tab-btn" style="padding:5px 9px;">»</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== TAB 2: PEMINJAMAN BARANG ==================== -->
      <section v-if="activeTab==='peminjaman'" class="content-body">
        <div class="card">
          <div class="card-header">
            <h3>Daftar Transaksi Peminjaman Aset</h3>
            <div style="display:flex; align-items:center; gap:10px;">
              <select v-model="peminjamanLimit" class="fi" style="width: 100px; height: 36px; padding: 4px 8px; font-size: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; outline: none;">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
              </select>
              <input v-model="searchPeminjaman" @input="debouncePeminjamanSearch" placeholder="Cari nama peminjam..." class="search-input" />
              <button @click="openAddPeminjaman" class="btn-primary">+ Catat Peminjaman</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>Nama Barang</th><th>Peminjam</th><th>Tipe</th><th>Jumlah</th><th>Tgl Pinjam</th><th>Tgl Rencana Kembali</th><th>Tgl Realisasi</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="9" class="loading-cell">Memuat data peminjaman...</td></tr>
                <tr v-else-if="peminjamanList.length===0"><td colspan="9" class="empty-cell">Belum ada transaksi peminjaman barang saat ini</td></tr>
                <tr v-for="p in peminjamanList" :key="p.id">
                  <td class="name-cell">{{ p.nama_barang }}</td>
                  <td><strong>{{ p.peminjam_nama }}</strong></td>
                  <td><span class="badge badge-info">{{ p.peminjam_tipe }}</span></td>
                  <td><strong>{{ p.jumlah }} {{ p.satuan }}</strong></td>
                  <td>{{ formatDate(p.tgl_pinjam) }}</td>
                  <td>{{ formatDate(p.tgl_kembali_rencana) }}</td>
                  <td>{{ formatDate(p.tgl_kembali_realisasi) }}</td>
                  <td>
                    <span v-if="p.status==='Dipinjam'" class="badge badge-warning">Dipinjam</span>
                    <span v-else-if="p.status==='Kembali'" class="badge badge-success">Kembali</span>
                    <span v-else class="badge badge-danger">Terlambat</span>
                  </td>
                  <td>
                    <div class="action-group">
                      <button v-if="p.status==='Dipinjam'" @click="kembalikanBarang(p.id, p.peminjam_nama)" class="btn-primary" style="padding: 4px 8px; font-size: 11px; background: linear-gradient(135deg, #10b981, #059669)">Kembalikan</button>
                      <button @click="deletePeminjaman(p.id)" class="ab ab-del" title="Hapus Rekam">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Paginasi Peminjaman -->
          <div v-if="peminjamanTotalPages > 1" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 10px; padding: 14px 20px;">
            <span style="font-size: 12px; color: #64748b;">
              Menampilkan <strong>{{ (peminjamanPage - 1) * peminjamanLimit + 1 }}</strong> – 
              <strong>{{ Math.min(peminjamanPage * peminjamanLimit, peminjamanTotal) }}</strong> dari 
              <strong>{{ peminjamanTotal }}</strong> peminjaman
            </span>
            <div style="display: flex; gap: 5px;">
              <button @click="changePeminjamanPage(1)" :disabled="peminjamanPage===1" class="tab-btn" style="padding:5px 9px;">«</button>
              <button @click="changePeminjamanPage(peminjamanPage - 1)" :disabled="peminjamanPage===1" class="tab-btn" style="padding:5px 10px;">‹</button>
              <button v-for="p in peminjamanPaginationRange" :key="p" @click="changePeminjamanPage(p)" :class="['tab-btn', peminjamanPage===p ? 'active-indigo' : '']" style="padding:5px 10px;">{{ p }}</button>
              <button @click="changePeminjamanPage(peminjamanPage + 1)" :disabled="peminjamanPage===peminjamanTotalPages" class="tab-btn" style="padding:5px 10px;">›</button>
              <button @click="changePeminjamanPage(peminjamanTotalPages)" :disabled="peminjamanPage===peminjamanTotalPages" class="tab-btn" style="padding:5px 9px;">»</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== TAB 3: MUTASI STOK BARANG ==================== -->
      <section v-if="activeTab==='mutasi'" class="content-body">
        <div class="mutasi-grid">
          <!-- Form Mutasi -->
          <div class="card">
            <div class="card-header">
              <h3>📦 Catat Penyesuaian Stok (Mutasi)</h3>
            </div>
            <div class="p20" style="display: flex; flex-direction: column; gap: 16px">
              <div class="fg">
                <label>Pilih Barang Inventaris *</label>
                <select v-model="formMutasi.barang_id" class="fi">
                  <option value="">-- Pilih Barang --</option>
                  <option v-for="b in barangList" :key="b.id" :value="b.id">{{ b.nama_barang }} (Stok: {{ b.stok }} {{ b.satuan }})</option>
                </select>
              </div>
              <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px">
                <div class="fg">
                  <label>Jenis Mutasi</label>
                  <select v-model="formMutasi.tipe" class="fi">
                    <option value="masuk">Stok Masuk (Pengadaan / Donasi)</option>
                    <option value="keluar">Stok Keluar (Rusak / Dibuang)</option>
                  </select>
                </div>
                <div class="fg">
                  <label>Jumlah Kuantitas *</label>
                  <input type="number" v-model.number="formMutasi.jumlah" class="fi" min="1" />
                </div>
              </div>
              <div class="fg">
                <label>Keterangan / Alasan Mutasi</label>
                <input v-model="formMutasi.keterangan" class="fi" placeholder="Sebutkan alasan penyesuaian..." />
              </div>
              <div style="text-align: right; margin-top: 8px">
                <button @click="saveMutasi" class="btn-primary" :disabled="saving">Simpan Mutasi</button>
              </div>
            </div>
          </div>

          <!-- Log Riwayat Mutasi -->
          <div class="card">
            <div class="card-header">
              <h3>📜 Log Histori Mutasi Barang</h3>
            </div>
            <div class="table-wrapper" style="max-height: 500px">
              <table class="data-table">
                <thead><tr><th>Waktu</th><th>Nama Barang</th><th>Jenis</th><th>Jumlah</th><th>Stok Awal</th><th>Stok Akhir</th><th>Keterangan</th></tr></thead>
                <tbody>
                  <tr v-if="riwayatMutasi.length===0"><td colspan="7" class="empty-cell">Belum ada catatan log mutasi barang</td></tr>
                  <tr v-for="m in riwayatMutasi" :key="m.id">
                    <td>{{ formatDateTime(m.created_at) }}</td>
                    <td class="name-cell">{{ m.nama_barang }}</td>
                    <td>
                      <span :class="['badge', m.tipe==='masuk'?'badge-success':'badge-danger']">{{ m.tipe }}</span>
                    </td>
                    <td><strong>{{ m.jumlah }} {{ m.satuan }}</strong></td>
                    <td>{{ m.stok_sebelum }}</td>
                    <td><strong>{{ m.stok_sesudah }}</strong></td>
                    <td style="font-size: 11px">{{ m.keterangan || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- Paginasi Mutasi -->
            <div v-if="mutasiTotalPages > 1" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 10px; padding: 14px 20px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <select v-model="mutasiLimit" class="fi" style="width: 100px; height: 36px; padding: 4px 8px; font-size: 12px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; outline: none;">
                  <option :value="10">10 baris</option>
                  <option :value="25">25 baris</option>
                  <option :value="50">50 baris</option>
                </select>
                <span style="font-size: 12px; color: #64748b;">
                  Menampilkan <strong>{{ (mutasiPage - 1) * mutasiLimit + 1 }}</strong> – 
                  <strong>{{ Math.min(mutasiPage * mutasiLimit, mutasiTotal) }}</strong> dari 
                  <strong>{{ mutasiTotal }}</strong> mutasi
                </span>
              </div>
              <div style="display: flex; gap: 5px;">
                <button @click="changeMutasiPage(1)" :disabled="mutasiPage===1" class="tab-btn" style="padding:5px 9px;">«</button>
                <button @click="changeMutasiPage(mutasiPage - 1)" :disabled="mutasiPage===1" class="tab-btn" style="padding:5px 10px;">‹</button>
                <button v-for="p in mutasiPaginationRange" :key="p" @click="changeMutasiPage(p)" :class="['tab-btn', mutasiPage===p ? 'active-indigo' : '']" style="padding:5px 10px;">{{ p }}</button>
                <button @click="changeMutasiPage(mutasiPage + 1)" :disabled="mutasiPage===mutasiTotalPages" class="tab-btn" style="padding:5px 10px;">›</button>
                <button @click="changeMutasiPage(mutasiTotalPages)" :disabled="mutasiPage===mutasiTotalPages" class="tab-btn" style="padding:5px 9px;">»</button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- ===== MODAL: FORM BARANG ===== -->
    <div v-if="showBarangForm" class="modal-overlay" @click.self="showBarangForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📦 {{ formBarang.id ? 'Edit Aset Barang' : 'Daftarkan Barang Baru' }}</h3><button @click="showBarangForm=false" class="modal-close">✕</button></div>
        <div class="p20" style="display: flex; flex-direction: column; gap: 16px">
          <div class="fg"><label>Nama Barang *</label><input v-model="formBarang.nama_barang" class="fi" placeholder="Contoh: Proyektor LCD Epson, Kursi Kuliah, dll." /></div>
          <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px">
            <div class="fg"><label>Kode Barang (Opsional)</label><input v-model="formBarang.kode_barang" class="fi" placeholder="Contoh: PRJ-001" /></div>
            <div class="fg"><label>Kategori Aset</label>
              <select v-model="formBarang.kategori" class="fi">
                <option value="Elektronik">Elektronik</option>
                <option value="Mebel &amp; Furnitur">Mebel &amp; Furnitur</option>
                <option value="Alat Olahraga">Alat Olahraga</option>
                <option value="Buku / Literatur">Buku / Literatur</option>
                <option value="Alat Tulis / ATK">Alat Tulis / ATK</option>
                <option value="Lainnya">Lainnya</option>
              </select>
            </div>
          </div>
          <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px">
            <div class="fg"><label>Satuan Takar</label><input v-model="formBarang.satuan" class="fi" placeholder="Contoh: Pcs, Unit, Set, Lembar" /></div>
            <div class="fg" v-if="!formBarang.id"><label>Stok Awal Masuk</label><input type="number" v-model.number="formBarang.stok_awal" class="fi" min="0" /></div>
          </div>
          <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px">
            <div class="fg"><label>Lokasi Penyimpanan</label><input v-model="formBarang.lokasi" class="fi" placeholder="Contoh: Ruang Lab, Kantor TU" /></div>
            <div class="fg"><label>Kondisi Barang</label>
              <select v-model="formBarang.kondisi" class="fi">
                <option value="Baik">Baik &amp; Layak Pakai</option>
                <option value="Rusak Ringan">Rusak Ringan</option>
                <option value="Rusak Berat">Rusak Berat</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showBarangForm=false" class="btn-secondary">Batal</button>
          <button @click="saveBarang" class="btn-primary" :disabled="saving">Simpan Aset</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: FORM PEMINJAMAN ===== -->
    <div v-if="showPeminjamanForm" class="modal-overlay" @click.self="showPeminjamanForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>🪪 Catat Peminjaman Barang Baru</h3><button @click="showPeminjamanForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full">
            <label>Pilih Barang Aset *</label>
            <select v-model="formPeminjaman.barang_id" class="fi">
              <option value="">-- Pilih Barang --</option>
              <option v-for="b in barangActiveList" :key="b.id" :value="b.id">{{ b.nama_barang }} (Stok Tersedia: {{ b.stok }} {{ b.satuan }})</option>
            </select>
          </div>
          <div class="fg full"><label>Nama Lengkap Peminjam *</label><input v-model="formPeminjaman.peminjam_nama" class="fi" placeholder="Contoh: Muhammad Ali, Ustadz Rahman" /></div>
          <div class="fg"><label>Tipe Peminjam</label>
            <select v-model="formPeminjaman.peminjam_tipe" class="fi">
              <option value="Santri">Santri</option>
              <option value="Karyawan">Karyawan / Guru</option>
              <option value="Luar">Pihak Luar</option>
            </select>
          </div>
          <div class="fg"><label>Jumlah Kuantitas Pinjam *</label><input type="number" v-model.number="formPeminjaman.jumlah" class="fi" min="1" /></div>
          <div class="fg"><label>Tanggal Pinjam *</label><input type="datetime-local" v-model="formPeminjaman.tgl_pinjam" class="fi" /></div>
          <div class="fg"><label>Estimasi Tanggal Kembali</label><input type="datetime-local" v-model="formPeminjaman.tgl_kembali_rencana" class="fi" /></div>
          <div class="fg full"><label>Keperluan / Keterangan</label><input v-model="formPeminjaman.keterangan" class="fi" placeholder="Contoh: Keperluan acara gebyar muharram di aula" /></div>
        </div>
        <div class="modal-actions">
          <button @click="showPeminjamanForm=false" class="btn-secondary">Batal</button>
          <button @click="savePeminjaman" class="btn-primary" :disabled="saving">Catat Peminjaman</button>
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

const API     = 'http://127.0.0.1:8080/api/sarpras'
const token   = localStorage.getItem('jwt_token')
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab          = ref('barang')
const loading            = ref(false)
const saving             = ref(false)
const toast              = ref({ show: false, message: '', type: 'success' })

const searchBarang       = ref('')
const barangList         = ref([])
const peminjamanList     = ref([])
const riwayatMutasi      = ref([])

// Paginasi Barang
const barangPage         = ref(1)
const barangLimit        = ref(10)
const barangTotal        = ref(0)
const barangTotalPages   = ref(0)

// Paginasi Peminjaman
const peminjamanPage     = ref(1)
const peminjamanLimit    = ref(10)
const peminjamanTotal    = ref(0)
const peminjamanTotalPages = ref(0)
const searchPeminjaman   = ref('')

// Paginasi Mutasi
const mutasiPage         = ref(1)
const mutasiLimit        = ref(10)
const mutasiTotal        = ref(0)
const mutasiTotalPages   = ref(0)
const filterMutasiBarangId = ref('')

const showBarangForm     = ref(false)
const showPeminjamanForm = ref(false)

const formBarang         = ref({ id: '', kode_barang: '', nama_barang: '', kategori: 'Elektronik', satuan: 'Pcs', stok_awal: 0, lokasi: '', kondisi: 'Baik' })
const formPeminjaman     = ref({ barang_id: '', peminjam_nama: '', peminjam_tipe: 'Santri', jumlah: 1, tgl_pinjam: '', tgl_kembali_rencana: '', keterangan: '' })
const formMutasi         = ref({ barang_id: '', tipe: 'masuk', jumlah: 1, keterangan: '' })

const tabs = [
  { key: 'barang', label: 'Inventaris Aset', icon: '🏢', color: 'blue' },
  { key: 'peminjaman', label: 'Peminjaman Barang', icon: '🪪', color: 'purple' },
  { key: 'mutasi', label: 'Log Mutasi Stok', icon: '📦', color: 'green' },
]

// ===== COMPUTED =====
const filteredBarang = computed(() => {
  return barangList.value.filter(b => 
    !searchBarang.value || 
    b.nama_barang.toLowerCase().includes(searchBarang.value.toLowerCase()) || 
    (b.kode_barang && b.kode_barang.toLowerCase().includes(searchBarang.value.toLowerCase()))
  )
})

const barangActiveList = computed(() => {
  return barangList.value.filter(b => b.stok > 0)
})

const barangPaginationRange = computed(() => {
  const c = barangPage.value, t = barangTotalPages.value, d = 2
  const r = []
  for (let i = Math.max(1, c - d); i <= Math.min(t, c + d); i++) r.push(i)
  return r
})

const peminjamanPaginationRange = computed(() => {
  const c = peminjamanPage.value, t = peminjamanTotalPages.value, d = 2
  const r = []
  for (let i = Math.max(1, c - d); i <= Math.min(t, c + d); i++) r.push(i)
  return r
})

const mutasiPaginationRange = computed(() => {
  const c = mutasiPage.value, t = mutasiTotalPages.value, d = 2
  const r = []
  for (let i = Math.max(1, c - d); i <= Math.min(t, c + d); i++) r.push(i)
  return r
})

// ===== HELPERS =====
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }
function formatDate(d) { if(!d) return '-'; const date = new Date(d); return date.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'}) }
function formatDateTime(d) { if(!d) return '-'; const date = new Date(d); return date.toLocaleString('id-ID', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) }

// ===== METHODS =====
async function switchTab(key) {
  activeTab.value = key
  if (key === 'barang') await fetchBarang(1)
  if (key === 'peminjaman') await fetchPeminjaman(1)
  if (key === 'mutasi') {
    await fetchBarang(1)
    await fetchMutasi(1)
  }
}

// === BARANG CRUD ===
async function fetchBarang(page = 1) {
  loading.value = true
  barangPage.value = page
  try {
    const res = await axios.get(`${API}/barang`, {
      params: { page: barangPage.value, limit: barangLimit.value, q: searchBarang.value },
      headers
    })
    barangList.value = res.data.data || []
    if (res.data.pagination) {
      barangTotal.value = res.data.pagination.total || 0
      barangTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch { showNotif('Gagal memuat barang inventaris', 'error') }
  finally { loading.value = false }
}

watch(barangLimit, () => { barangPage.value = 1; fetchBarang(1) })

let barangSearchTimeout = null
function debounceBarangSearch() {
  if (barangSearchTimeout) clearTimeout(barangSearchTimeout)
  barangSearchTimeout = setTimeout(() => fetchBarang(1), 400)
}

function changeBarangPage(p) {
  if (p < 1 || p > barangTotalPages.value) return
  barangPage.value = p; fetchBarang(p)
}

function openAddBarang() {
  formBarang.value = { id: '', kode_barang: '', nama_barang: '', kategori: 'Elektronik', satuan: 'Pcs', stok_awal: 0, lokasi: '', kondisi: 'Baik' }
  showBarangForm.value = true
}

function openEditBarang(b) {
  formBarang.value = { ...b }
  showBarangForm.value = true
}

async function saveBarang() {
  if (!formBarang.value.nama_barang) return showNotif('Nama barang wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/barang/save`, formBarang.value, { headers })
    showBarangForm.value = false
    await fetchBarang(1)
    showNotif('Barang inventaris berhasil disimpan!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan barang inventaris', 'error')
  } finally { saving.value = false }
}

async function deleteBarang(id, nama) {
  if (!confirm(`Hapus barang "${nama}" dari inventaris?`)) return
  try {
    await axios.delete(`${API}/barang/delete/${id}`, { headers })
    await fetchBarang(barangPage.value)
    showNotif('Barang inventaris berhasil dihapus!')
  } catch { showNotif('Gagal menghapus barang inventaris', 'error') }
}

// === PEMINJAMAN CRUD ===
async function fetchPeminjaman(page = 1) {
  loading.value = true
  peminjamanPage.value = page
  try {
    const res = await axios.get(`${API}/peminjaman`, {
      params: { page: peminjamanPage.value, limit: peminjamanLimit.value, q: searchPeminjaman.value },
      headers
    })
    peminjamanList.value = res.data.data || []
    if (res.data.pagination) {
      peminjamanTotal.value = res.data.pagination.total || 0
      peminjamanTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch { showNotif('Gagal memuat transaksi peminjaman', 'error') }
  finally { loading.value = false }
}

watch(peminjamanLimit, () => { peminjamanPage.value = 1; fetchPeminjaman(1) })

let peminjamanSearchTimeout = null
function debouncePeminjamanSearch() {
  if (peminjamanSearchTimeout) clearTimeout(peminjamanSearchTimeout)
  peminjamanSearchTimeout = setTimeout(() => fetchPeminjaman(1), 400)
}

function changePeminjamanPage(p) {
  if (p < 1 || p > peminjamanTotalPages.value) return
  peminjamanPage.value = p; fetchPeminjaman(p)
}

async function openAddPeminjaman() {
  formPeminjaman.value = {
    barang_id: '',
    peminjam_nama: '',
    peminjam_tipe: 'Santri',
    jumlah: 1,
    tgl_pinjam: new Date().toISOString().slice(0, 16),
    tgl_kembali_rencana: '',
    keterangan: ''
  }
  // Load latest active items list
  await fetchBarang()
  showPeminjamanForm.value = true
}

async function savePeminjaman() {
  const f = formPeminjaman.value
  if (!f.barang_id || !f.peminjam_nama || !f.tgl_pinjam) {
    return showNotif('Barang, nama peminjam, dan tanggal pinjam wajib diisi', 'error')
  }
  saving.value = true
  try {
    await axios.post(`${API}/peminjaman/save`, f, { headers })
    showPeminjamanForm.value = false
    await fetchPeminjaman(1)
    showNotif('Peminjaman barang berhasil dicatat!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan peminjaman', 'error')
  } finally { saving.value = false }
}

async function kembalikanBarang(id, nama) {
  if (!confirm(`Tandai pengembalian barang dari "${nama}"?`)) return
  try {
    await axios.post(`${API}/peminjaman/kembalikan/${id}`, {}, { headers })
    await fetchPeminjaman(peminjamanPage.value)
    showNotif('Barang berhasil dikembalikan ke gudang!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal melakukan pengembalian', 'error')
  }
}

async function deletePeminjaman(id) {
  if (!confirm('Hapus rekam peminjaman ini? Jika status masih dipinjam, stok barang akan otomatis dikembalikan.')) return
  try {
    await axios.delete(`${API}/peminjaman/delete/${id}`, { headers })
    await fetchPeminjaman(peminjamanPage.value)
    showNotif('Rekam peminjaman berhasil dihapus!')
  } catch { showNotif('Gagal menghapus rekam peminjaman', 'error') }
}

// === MUTASI STOK ===
async function fetchMutasi(page = 1) {
  mutasiPage.value = page
  try {
    const res = await axios.get(`${API}/mutasi`, {
      params: { page: mutasiPage.value, limit: mutasiLimit.value, barang_id: filterMutasiBarangId.value },
      headers
    })
    riwayatMutasi.value = res.data.data || []
    if (res.data.pagination) {
      mutasiTotal.value = res.data.pagination.total || 0
      mutasiTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch { showNotif('Gagal memuat log mutasi', 'error') }
}

watch(mutasiLimit, () => { mutasiPage.value = 1; fetchMutasi(1) })

function changeMutasiPage(p) {
  if (p < 1 || p > mutasiTotalPages.value) return
  mutasiPage.value = p; fetchMutasi(p)
}

async function saveMutasi() {
  const f = formMutasi.value
  if (!f.barang_id || f.jumlah <= 0 || !f.tipe) {
    return showNotif('Lengkapi semua field mutasi dengan benar', 'error')
  }
  saving.value = true
  try {
    await axios.post(`${API}/mutasi/save`, f, { headers })
    formMutasi.value = { barang_id: '', tipe: 'masuk', jumlah: 1, keterangan: '' }
    await Promise.all([
      fetchBarang(1),
      fetchMutasi(1)
    ])
    showNotif('Mutasi stok berhasil dicatat!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal mencatat mutasi', 'error')
  } finally { saving.value = false }
}

// === INIT ===
onMounted(fetchBarang)
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
* { margin: 0; padding: 0; box-sizing: border-box; }
.dashboard-container { display: flex; height: 100vh; background: #0f1117; font-family: 'Inter', sans-serif; color: #e2e8f0; overflow: hidden; }

/* Main Content */
.main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }
.content-header { padding: 28px 32px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(26,29,46,0.5); gap: 16px; flex-wrap: wrap; }
.content-header h1 { font-size: 22px; font-weight: 700; }
.content-header p { font-size: 13px; color: #64748b; margin-top: 2px; }
.header-tabs { display: flex; gap: 6px; }
.tab-btn { padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 12px; cursor: pointer; transition: all 0.2s; white-space: nowrap; }

.active-blue { background: rgba(96,165,250,0.15); color: #60a5fa; border-color: rgba(96,165,250,0.3); font-weight: 600; }
.active-purple { background: rgba(167,139,250,0.15); color: #a78bfa; border-color: rgba(167,139,250,0.3); font-weight: 600; }
.active-green { background: rgba(52,211,153,0.15); color: #34d399; border-color: rgba(52,211,153,0.3); font-weight: 600; }
.active-indigo { background: rgba(99,102,241,0.2); color: #818cf8; border-color: rgba(99,102,241,0.4); font-weight: 700; }
.tab-btn:disabled { opacity: 0.4; cursor: not-allowed; }

.content-body { padding: 24px 32px; display: flex; flex-direction: column; gap: 24px; }

/* Card */
.card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
.card-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 12px; }
.card-header h3 { font-size: 15px; font-weight: 600; }

.header-actions { display: flex; gap: 12px; align-items: center; }
.search-input { padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.05); color: #e2e8f0; font-size: 12.5px; width: 220px; outline: none; }
.search-input:focus { border-color: #6366f1; }

/* Tables */
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.data-table th { padding: 12px 16px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.data-table td { padding: 12px 16px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
.data-table tr:last-child td { border-bottom: none; }
.data-table tr:hover td { background: rgba(255,255,255,0.02); }
.name-cell { font-weight: 500; color: #c4b5fd; }
.loading-cell, .empty-cell { text-align: center; color: #64748b; padding: 40px !important; }
code { background: rgba(255,255,255,0.07); padding: 2px 6px; border-radius: 4px; font-size: 11px; }

/* Badges */
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
.badge-warning { background: rgba(245,158,11,0.15); color: #fb923c; }
.badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }
.badge-info { background: rgba(99,102,241,0.15); color: #818cf8; }

/* Mutasi Grid */
.mutasi-grid { display: grid; grid-template-columns: 1fr 1.3fr; gap: 24px; align-items: start; }

/* Buttons */
.btn-primary { padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity 0.2s; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; font-size: 13px; cursor: pointer; }

/* Action Buttons */
.action-group { display: flex; gap: 4px; align-items: center; }
.ab { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 7px; border: 1px solid transparent; cursor: pointer; font-size: 13px; background: rgba(255,255,255,0.04); transition: all 0.2s; }
.ab:hover { transform: scale(1.1); }
.ab-blue:hover { background: rgba(96,165,250,0.15); }
.ab-del:hover { background: rgba(248,113,113,0.1); }

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

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
