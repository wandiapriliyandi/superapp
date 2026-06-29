<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Keuangan &amp; Akuntansi</h1>
          <p>Kelola daftar akun (COA), input jurnal umum double-entry, buku besar, dan laporan keuangan (laba rugi &amp; neraca)</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- ========== TAB 1: COA / DAFTAR AKUN ========== -->
      <section v-if="activeTab==='akun'">
        <div class="card">
          <div class="card-header">
            <h3>Daftar Akun (Chart of Accounts)</h3>
            <button @click="openAddAkun" class="btn-primary">+ Tambah Akun</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>Kode Akun</th><th>Nama Akun</th><th>Kategori</th><th>Saldo Normal</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="6" class="loading-cell">Memuat data akun...</td></tr>
                <tr v-else-if="akun.length===0"><td colspan="6" class="empty-cell">Belum ada akun terdaftar</td></tr>
                <tr v-for="a in akun" :key="a.id">
                  <td><code>{{ a.kode_akun }}</code></td>
                  <td class="name-cell" style="padding-left: 20px">{{ a.nama_akun }}</td>
                  <td><span class="badge badge-info">{{ a.kategori }}</span></td>
                  <td>{{ a.saldo_normal }}</td>
                  <td><span :class="['badge', a.is_aktif?'badge-success':'badge-danger']">{{ a.is_aktif?'Aktif':'Nonaktif' }}</span></td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditAkun(a)" class="ab ab-blue">✏️</button>
                      <button @click="deleteAkun(a.id, a.nama_akun)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 2: JURNAL UMUM ========== -->
      <section v-if="activeTab==='jurnal'">
        <div class="card">
          <div class="card-header">
            <h3>Jurnal Umum &amp; Transaksi</h3>
            <button @click="openAddJurnal" class="btn-primary">+ Buat Jurnal Baru</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nomor Jurnal</th><th>Tanggal</th><th>Keterangan</th><th>Referensi</th><th>Total Debit/Kredit</th><th>Item Details</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat data jurnal...</td></tr>
                <tr v-else-if="jurnals.length===0"><td colspan="8" class="empty-cell">Belum ada transaksi jurnal dicatat</td></tr>
                <tr v-for="(j, idx) in jurnals" :key="j.id">
                  <td>{{ idx+1 }}</td>
                  <td><span class="badge badge-info">📄 {{ j.nomor_jurnal }}</span></td>
                  <td>{{ formatDate(j.tanggal) }}</td>
                  <td class="name-cell">{{ j.keterangan }}</td>
                  <td><code>{{ j.referensi || '—' }}</code></td>
                  <td class="paid-cell" style="color: #60a5fa">{{ formatRupiah(calcJurnalTotal(j)) }}</td>
                  <td>
                    <table class="sub-table">
                      <tr v-for="d in j.details" :key="d.id">
                        <td style="width: 140px; color: #a78bfa">{{ d.kode_akun }} - {{ d.nama_akun }}</td>
                        <td style="width: 80px; text-align: right">{{ d.debit > 0 ? formatRupiah(d.debit) : '—' }}</td>
                        <td style="width: 80px; text-align: right">{{ d.kredit > 0 ? formatRupiah(d.kredit) : '—' }}</td>
                      </tr>
                    </table>
                  </td>
                  <td>
                    <button @click="deleteJurnal(j.id)" class="ab ab-del">🗑️</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 3: BUKU BESAR ========== -->
      <section v-if="activeTab==='buku_besar'">
        <div class="card">
          <div class="card-header">
            <h3>Buku Besar (General Ledger)</h3>
            <div class="header-actions">
              <select v-model="glParams.akun_id" class="filter-select">
                <option value="">-- Pilih Akun --</option>
                <option v-for="a in akun" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
              </select>
              <input v-model="glParams.tgl_mulai" type="date" class="search-input" />
              <input v-model="glParams.tgl_selesai" type="date" class="search-input" />
              <button @click="fetchBukuBesar" class="btn-primary-purple">Tampilkan</button>
            </div>
          </div>
          <div v-if="glData" class="gl-summary p20">
            <div class="gl-sum-box"><span>Akun:</span><strong>{{ glData.akun?.kode_akun }} - {{ glData.akun?.nama_akun }}</strong></div>
            <div class="gl-sum-box"><span>Saldo Normal:</span><strong>{{ glData.akun?.saldo_normal }}</strong></div>
            <div class="gl-sum-box"><span>Saldo Awal Periode:</span><strong class="paid-cell">{{ formatRupiah(glData.saldo_awal) }}</strong></div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>Tanggal</th><th>Nomor Jurnal</th><th>Keterangan Jurnal</th><th>Debit</th><th>Kredit</th><th>Saldo Berjalan</th></tr></thead>
              <tbody>
                <tr v-if="loadingGL"><td colspan="6" class="loading-cell">Memuat buku besar...</td></tr>
                <tr v-else-if="!glData"><td colspan="6" class="empty-cell">Pilih akun dan rentang tanggal untuk melihat buku besar</td></tr>
                <tr v-else-if="glData.transaksi.length===0">
                  <td colspan="6" class="empty-cell">Tidak ada transaksi dalam periode ini. Saldo Akhir: {{ formatRupiah(glData.saldo_awal) }}</td>
                </tr>
                <template v-else>
                  <tr v-for="(t, index) in computedGLTransactions" :key="t.id">
                    <td>{{ formatDate(t.tanggal) }}</td>
                    <td><span class="badge badge-info">{{ t.nomor_jurnal }}</span></td>
                    <td>{{ t.ket_jurnal }} <span v-if="t.keterangan_item" class="muted">({{ t.keterangan_item }})</span></td>
                    <td style="color: #34d399">{{ t.debit > 0 ? formatRupiah(t.debit) : '—' }}</td>
                    <td style="color: #f87171">{{ t.kredit > 0 ? formatRupiah(t.kredit) : '—' }}</td>
                    <td class="paid-cell" style="color: #60a5fa">{{ formatRupiah(t.running_balance) }}</td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 4: LAPORAN KEUANGAN ========== -->
      <section v-if="activeTab==='laporan'">
        <div class="sub-header-tabs p20">
          <button :class="['sub-tab-btn', subTab==='laba_rugi'?'active':'']" @click="subTab='laba_rugi'">📈 Laporan Laba Rugi</button>
          <button :class="['sub-tab-btn', subTab==='neraca'?'active':'']" @click="subTab='neraca'">⚖️ Laporan Neraca</button>
        </div>

        <!-- LABA RUGI -->
        <div v-if="subTab==='laba_rugi'" class="card">
          <div class="card-header">
            <h3>Laporan Laba Rugi</h3>
            <div class="header-actions">
              <input v-model="lrParams.tgl_mulai" type="date" class="search-input" />
              <input v-model="lrParams.tgl_selesai" type="date" class="search-input" />
              <button @click="fetchLabaRugi" class="btn-primary-purple">Kalkulasi</button>
            </div>
          </div>
          <div class="laporan-wrapper p20" v-if="lrData">
            <!-- Pendapatan -->
            <div class="lap-section">
              <h4 class="lap-section-title">Pendapatan Operasional</h4>
              <div class="lap-row" v-for="p in lrData.pendapatan" :key="p.kode_akun">
                <span>{{ p.kode_akun }} - {{ p.nama_akun }}</span><strong>{{ formatRupiah(p.saldo) }}</strong>
              </div>
              <div class="lap-row total"><span>Total Pendapatan</span><strong>{{ formatRupiah(lrData.total_pendapatan) }}</strong></div>
            </div>
            <!-- Beban -->
            <div class="lap-section">
              <h4 class="lap-section-title">Beban Operasional</h4>
              <div class="lap-row" v-for="b in lrData.beban" :key="b.kode_akun">
                <span>{{ b.kode_akun }} - {{ b.nama_akun }}</span><strong>{{ formatRupiah(b.saldo) }}</strong>
              </div>
              <div class="lap-row total red"><span>Total Beban</span><strong>{{ formatRupiah(lrData.total_beban) }}</strong></div>
            </div>
            <!-- Net profit -->
            <div class="lap-row grand-total" :style="{background: lrData.laba_bersih >= 0 ? 'rgba(52,211,153,0.1)' : 'rgba(239,68,68,0.1)'}">
              <span>Laba (Rugi) Bersih</span>
              <strong :style="{color: lrData.laba_bersih >= 0 ? '#34d399' : '#f87171'}">{{ formatRupiah(lrData.laba_bersih) }}</strong>
            </div>
          </div>
          <div v-else class="empty-cell">Tekan tombol Kalkulasi untuk menyusun Laporan Laba Rugi</div>
        </div>

        <!-- NERACA -->
        <div v-if="subTab==='neraca'" class="card">
          <div class="card-header">
            <h3>Laporan Neraca</h3>
            <div class="header-actions">
              <input v-model="neracaParams.tanggal" type="date" class="search-input" />
              <button @click="fetchNeraca" class="btn-primary-purple">Kalkulasi</button>
            </div>
          </div>
          <div class="laporan-wrapper p20" v-if="neracaData">
            <div class="neraca-grid">
              <!-- Aktiva (Aset) -->
              <div class="neraca-column">
                <h4 class="lap-section-title">AKTIVA (ASET)</h4>
                <div class="lap-row" v-for="a in neracaData.aset" :key="a.kode_akun">
                  <span>{{ a.kode_akun }} - {{ a.nama_akun }}</span><strong>{{ formatRupiah(a.saldo) }}</strong>
                </div>
                <div class="lap-row total"><span>Total Aktiva</span><strong>{{ formatRupiah(neracaData.total_aset) }}</strong></div>
              </div>
              <!-- Pasiva (Kewajiban & Ekuitas) -->
              <div class="neraca-column">
                <h4 class="lap-section-title">PASIVA (KEWAJIBAN &amp; EKUITAS)</h4>
                <h5 class="sub-title">Kewajiban / Liabilitas</h5>
                <div class="lap-row" v-for="k in neracaData.kewajiban" :key="k.kode_akun">
                  <span>{{ k.kode_akun }} - {{ k.nama_akun }}</span><strong>{{ formatRupiah(k.saldo) }}</strong>
                </div>
                <div class="lap-row total red"><span>Total Kewajiban</span><strong>{{ formatRupiah(neracaData.total_kewajiban) }}</strong></div>

                <h5 class="sub-title" style="margin-top: 15px">Ekuitas / Modal</h5>
                <div class="lap-row" v-for="e in neracaData.ekuitas" :key="e.kode_akun">
                  <span>{{ e.kode_akun }} - {{ e.nama_akun }}</span><strong>{{ formatRupiah(e.saldo) }}</strong>
                </div>
                <div class="lap-row total"><span>Total Ekuitas</span><strong>{{ formatRupiah(neracaData.total_ekuitas) }}</strong></div>

                <div class="lap-row grand-total" style="margin-top: 20px; background: rgba(96,165,250,0.1)">
                  <span>Total Pasiva</span><strong style="color: #60a5fa">{{ formatRupiah(neracaData.total_pasiva) }}</strong>
                </div>
              </div>
            </div>
            <!-- Check Balance -->
            <div class="neraca-balance-check" :style="{background: isNeracaBalanced ? 'rgba(52,211,153,0.06)' : 'rgba(239,68,68,0.06)'}">
              <span v-if="isNeracaBalanced">🟢 Neraca Seimbang (Balanced)</span>
              <span v-else>🔴 Neraca Tidak Seimbang! Selisih: {{ formatRupiah(Math.abs(neracaData.total_aset - neracaData.total_pasiva)) }}</span>
            </div>
          </div>
          <div v-else class="empty-cell">Tekan tombol Kalkulasi untuk menyusun Laporan Neraca</div>
        </div>
      </section>
    </main>

    <!-- ===== MODAL: AKUN FORM ===== -->
    <div v-if="showAkunForm" class="modal-overlay" @click.self="showAkunForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📋 {{ formAkun.id?'Edit Akun':'Tambah Akun Baru' }}</h3><button @click="showAkunForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg"><label>Kode Akun *</label><input v-model="formAkun.kode_akun" class="fi" placeholder="Contoh: 11101, 51102" /></div>
          <div class="fg"><label>Nama Akun *</label><input v-model="formAkun.nama_akun" class="fi" placeholder="Kas Kecil, Beban Gaji..." /></div>
          <div class="fg"><label>Kategori *</label>
            <select v-model="formAkun.kategori" class="fi">
              <option value="Aset">Aset</option><option value="Kewajiban">Kewajiban</option><option value="Ekuitas">Ekuitas</option><option value="Pendapatan">Pendapatan</option><option value="Beban">Beban</option>
            </select>
          </div>
          <div class="fg"><label>Saldo Normal</label>
            <select v-model="formAkun.saldo_normal" class="fi">
              <option value="Debit">Debit</option><option value="Kredit">Kredit</option>
            </select>
          </div>
          <div class="fg"><label>Status Akun</label>
            <select v-model="formAkun.is_aktif" class="fi">
              <option :value="1">Aktif</option><option :value="0">Nonaktif</option>
            </select>
          </div>
          <div class="fg"><label>Parent Akun (Optional)</label>
            <select v-model="formAkun.parent_id" class="fi">
              <option value="">Tidak ada</option>
              <option v-for="a in akun" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
            </select>
          </div>
        </div>
        <div class="modal-actions"><button @click="showAkunForm=false" class="btn-secondary">Batal</button><button @click="saveAkun" class="btn-primary" :disabled="saving">Simpan</button></div>
      </div>
    </div>

    <!-- ===== MODAL: BATCH JURNAL FORM ===== -->
    <div v-if="showJurnalForm" class="modal-overlay" @click.self="showJurnalForm=false">
      <div class="modal-box" style="width: 720px">
        <div class="modal-head"><h3>📂 Catat Transaksi Jurnal Umum</h3><button @click="showJurnalForm=false" class="modal-close">✕</button></div>
        <div class="p20" style="display: flex; flex-direction: column; gap: 14px">
          <div class="form-grid" style="grid-template-columns: 1fr 1fr">
            <div class="fg"><label>Tanggal Transaksi *</label><input v-model="formJurnal.tanggal" type="date" class="fi" /></div>
            <div class="fg"><label>Nomor Referensi (Opsional)</label><input v-model="formJurnal.referensi" class="fi" placeholder="Kwitansi, invoice..." /></div>
            <div class="fg full"><label>Keterangan Transaksi *</label><input v-model="formJurnal.keterangan" class="fi" placeholder="Penerimaan donasi, pembayaran listrik..." /></div>
          </div>

          <!-- Ledger Detail rows -->
          <div class="ledger-rows-container">
            <div class="ledger-header-row">
              <span class="col-akun">Pilih Akun</span>
              <span class="col-keterangan">Keterangan Item (Opsional)</span>
              <span class="col-nominal">Debit</span>
              <span class="col-nominal">Kredit</span>
              <span class="col-act"></span>
            </div>
            <div class="ledger-row" v-for="(row, idx) in formJurnal.details" :key="idx">
              <select v-model="row.akun_id" class="col-akun fi sub-fi">
                <option value="">-- Pilih --</option>
                <option v-for="a in akun" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
              </select>
              <input v-model="row.keterangan_item" class="col-keterangan fi sub-fi" placeholder="Catatan item..." />
              <input v-model.number="row.debit" type="number" class="col-nominal fi sub-fi" placeholder="0" />
              <input v-model.number="row.kredit" type="number" class="col-nominal fi sub-fi" placeholder="0" />
              <button @click="removeJurnalRow(idx)" class="col-act btn-delete-row">✕</button>
            </div>
          </div>
          <button @click="addJurnalRow" class="btn-add-row">+ Tambah Baris Transaksi</button>

          <!-- Check Balance summary -->
          <div class="balance-bar" :class="isJurnalBalanced ? 'balanced' : 'unbalanced'">
            <div>Total Debit: <strong>{{ formatRupiah(jurnalTotalDebit) }}</strong></div>
            <div>Total Kredit: <strong>{{ formatRupiah(jurnalTotalKredit) }}</strong></div>
            <div v-if="!isJurnalBalanced" class="diff-tag">Mismatch: {{ formatRupiah(Math.abs(jurnalTotalDebit - jurnalTotalKredit)) }}</div>
            <div v-else class="diff-tag matched">✓ Balanced</div>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showJurnalForm=false" class="btn-secondary">Batal</button>
          <button @click="saveJurnal" class="btn-primary" :disabled="saving || !isJurnalBalanced || jurnalTotalDebit <= 0">Posting Jurnal</button>
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
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const API    = 'http://127.0.0.1:8080/api'
const token  = localStorage.getItem('jwt_token')
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab     = ref('akun')
const subTab        = ref('laba_rugi')
const loading       = ref(false)
const loadingGL     = ref(false)
const saving        = ref(false)
const toast         = ref({ show: false, message: '', type: 'success' })

const akun          = ref([])
const jurnals       = ref([])

const glParams      = ref({ akun_id: '', tgl_mulai: dateStr(1), tgl_selesai: dateStr(0) })
const glData        = ref(null)

const lrParams      = ref({ tgl_mulai: dateStr(1), tgl_selesai: dateStr(0) })
const lrData        = ref(null)

const neracaParams  = ref({ tanggal: dateStr(0) })
const neracaData    = ref(null)

const showAkunForm  = ref(false)
const showJurnalForm = ref(false)

const formAkun      = ref({ id: '', kode_akun: '', nama_akun: '', kategori: 'Aset', saldo_normal: 'Debit', is_aktif: 1, parent_id: '' })
const formJurnal    = ref({ tanggal: dateStr(0), keterangan: '', referensi: '', jenis_jurnal: 'Umum', details: [] })

const tabs = [
  { key: 'akun', label: 'Daftar Akun (COA)', icon: '📋', color: 'blue' },
  { key: 'jurnal', label: 'Jurnal Umum', icon: '📂', color: 'purple' },
  { key: 'buku_besar', label: 'Buku Besar', icon: '📖', color: 'teal' },
  { key: 'laporan', label: 'Laporan Keuangan', icon: '⚖️', color: 'green' },
]

// ===== HELPERS =====
function dateStr(monthsAgo=0) {
  const d = new Date()
  if (monthsAgo) d.setMonth(d.getMonth() - monthsAgo)
  return d.toISOString().slice(0, 10)
}
function formatRupiah(n) { if(!n) return 'Rp 0'; return 'Rp ' + parseInt(n).toLocaleString('id-ID') }
function formatDate(d) { if(!d) return '—'; return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) }
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }

function calcJurnalTotal(j) {
  return j.details?.reduce((acc, curr) => acc + (parseFloat(curr.debit) || 0), 0) || 0
}

// ===== COMPUTED JURNAL BALANCE =====
const jurnalTotalDebit = computed(() => {
  return formJurnal.value.details.reduce((sum, item) => sum + (parseFloat(item.debit) || 0), 0)
})
const jurnalTotalKredit = computed(() => {
  return formJurnal.value.details.reduce((sum, item) => sum + (parseFloat(item.kredit) || 0), 0)
})
const isJurnalBalanced = computed(() => {
  const diff = Math.abs(jurnalTotalDebit.value - jurnalTotalKredit.value)
  return diff < 0.01
})

// ===== COMPUTED BUKU BESAR RUNNING BALANCE =====
const computedGLTransactions = computed(() => {
  if (!glData.value) return []
  let balance = parseFloat(glData.value.saldo_awal) || 0
  const isDebitNormal = glData.value.akun?.saldo_normal === 'Debit'

  return glData.value.transaksi.map(t => {
    const d = parseFloat(t.debit) || 0
    const k = parseFloat(t.kredit) || 0
    
    if (isDebitNormal) {
      balance += (d - k)
    } else {
      balance += (k - d)
    }
    return { ...t, running_balance: balance }
  })
})

const isNeracaBalanced = computed(() => {
  if (!neracaData.value) return false
  const diff = Math.abs(neracaData.value.total_aset - neracaData.value.total_pasiva)
  return diff < 0.1
})

// ===== ACTIONS =====
async function switchTab(key) {
  activeTab.value = key
  if (key === 'akun') await fetchAkun()
  if (key === 'jurnal') await fetchJurnals()
}

// === COA FETCH & CRUD ===
async function fetchAkun() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/keuangan/akun`, { headers })
    akun.value = res.data.data || []
  } catch { showNotif('Gagal memuat COA', 'error') }
  finally { loading.value = false }
}

function openAddAkun() {
  formAkun.value = { id: '', kode_akun: '', nama_akun: '', kategori: 'Aset', saldo_normal: 'Debit', is_aktif: 1, parent_id: '' }
  showAkunForm.value = true
}

function openEditAkun(a) {
  formAkun.value = { ...a }
  showAkunForm.value = true
}

async function saveAkun() {
  if (!formAkun.value.kode_akun || !formAkun.value.nama_akun) return showNotif('Kode & nama akun wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/keuangan/akun/save`, formAkun.value, { headers })
    showAkunForm.value = false
    await fetchAkun()
    showNotif('Akun berhasil disimpan!')
  } catch (e) { showNotif(e.response?.data?.message || 'Gagal menyimpan akun', 'error') }
  finally { saving.value = false }
}

async function deleteAkun(id, nama) {
  if (!confirm(`Hapus akun "${nama}"? Semua relasi jurnal akun ini akan terpengaruh!`)) return
  try {
    await axios.delete(`${API}/keuangan/akun/delete/${id}`, { headers })
    await fetchAkun()
    showNotif('Akun berhasil dihapus!')
  } catch { showNotif('Gagal menghapus akun', 'error') }
}

// === JURNAL CRUD ===
async function fetchJurnals() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/keuangan/jurnal`, { headers })
    jurnals.value = res.data.data || []
  } catch { showNotif('Gagal memuat jurnal transaksi', 'error') }
  finally { loading.value = false }
}

function openAddJurnal() {
  formJurnal.value = {
    tanggal: dateStr(0),
    keterangan: '',
    referensi: '',
    jenis_jurnal: 'Umum',
    details: [
      { akun_id: '', debit: '', kredit: '', keterangan_item: '' },
      { akun_id: '', debit: '', kredit: '', keterangan_item: '' }
    ]
  }
  showJurnalForm.value = true
}

function addJurnalRow() {
  formJurnal.value.details.push({ akun_id: '', debit: '', kredit: '', keterangan_item: '' })
}

function removeJurnalRow(idx) {
  if (formJurnal.value.details.length <= 2) return showNotif('Jurnal minimal memiliki 2 baris transaksi', 'error')
  formJurnal.value.details.splice(idx, 1)
}

async function saveJurnal() {
  saving.value = true
  try {
    await axios.post(`${API}/keuangan/jurnal/save`, formJurnal.value, { headers })
    showJurnalForm.value = false
    await fetchJurnals()
    showNotif('Jurnal berhasil diposting!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan transaksi jurnal', 'error')
  } finally { saving.value = false }
}

async function deleteJurnal(id) {
  if (!confirm('Hapus transaksi jurnal ini?')) return
  try {
    await axios.delete(`${API}/keuangan/jurnal/delete/${id}`, { headers })
    await fetchJurnals()
    showNotif('Transaksi jurnal berhasil dihapus!')
  } catch { showNotif('Gagal menghapus jurnal', 'error') }
}

// === BUKU BESAR ===
async function fetchBukuBesar() {
  if (!glParams.value.akun_id) return showNotif('Pilih akun terlebih dahulu', 'error')
  loadingGL.value = true
  try {
    const res = await axios.get(`${API}/keuangan/buku-besar?akun_id=${glParams.value.akun_id}&tgl_mulai=${glParams.value.tgl_mulai}&tgl_selesai=${glParams.value.tgl_selesai}`, { headers })
    glData.value = res.data.data
  } catch { showNotif('Gagal mengambil buku besar', 'error') }
  finally { loadingGL.value = false }
}

// === LAPORAN KEUANGAN ===
async function fetchLabaRugi() {
  try {
    const res = await axios.get(`${API}/keuangan/laporan/laba-rugi?tgl_mulai=${lrParams.value.tgl_mulai}&tgl_selesai=${lrParams.value.tgl_selesai}`, { headers })
    lrData.value = res.data.data
  } catch { showNotif('Gagal kalkulasi laba rugi', 'error') }
}

async function fetchNeraca() {
  try {
    const res = await axios.get(`${API}/keuangan/laporan/neraca?tanggal=${neracaParams.value.tanggal}`, { headers })
    neracaData.value = res.data.data
  } catch { showNotif('Gagal kalkulasi neraca', 'error') }
}

onMounted(() => {
  fetchAkun()
})
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
.active-purple { background: rgba(167,139,250,0.15); color: #a78bfa; border-color: rgba(167,139,250,0.3); font-weight: 600; }
.active-teal { background: rgba(45,212,191,0.15); color: #2dd4bf; border-color: rgba(45,212,191,0.3); font-weight: 600; }
.active-green { background: rgba(52,211,153,0.15); color: #34d399; border-color: rgba(52,211,153,0.3); font-weight: 600; }

/* Sub-tabs */
.sub-header-tabs { display: flex; gap: 10px; margin-bottom: -10px; }
.sub-tab-btn { padding: 8px 16px; font-size: 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); color: #94a3b8; cursor: pointer; transition: all 0.2s; }
.sub-tab-btn.active { background: #7c3aed; color: white; border-color: #7c3aed; font-weight: 600; }

/* Card */
.card { margin: 20px 32px 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
.card-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 12px; }
.card-header h3 { font-size: 16px; font-weight: 600; }
.header-actions { display: flex; gap: 10px; align-items: center; }
.search-input, .filter-select { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 14px; color: #e2e8f0; font-size: 13px; outline: none; }
.filter-select option { background: #1a1d2e; }

/* GL summary */
.gl-summary { display: flex; gap: 32px; background: rgba(255,255,255,0.01); border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; }
.gl-sum-box { display: flex; flex-direction: column; gap: 4px; font-size: 13px; }
.gl-sum-box span { color: #64748b; font-size: 11px; text-transform: uppercase; }

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

/* Sub Table for Journal details */
.sub-table { width: 100%; border-collapse: collapse; font-size: 12px; }
.sub-table td { padding: 4px 8px; border: none !important; }

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
.sub-fi { border-radius: 6px; padding: 6px 10px; font-size: 12px; }
.modal-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.06); }
.muted { color: #64748b; font-size: 11px; }

/* Ledger Input Rows */
.ledger-rows-container { border: 1px solid rgba(255,255,255,0.07); border-radius: 12px; overflow: hidden; background: rgba(0,0,0,0.2); }
.ledger-header-row { display: grid; grid-template-columns: 180px 1fr 120px 120px 40px; background: rgba(255,255,255,0.05); padding: 8px 12px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; }
.ledger-row { display: grid; grid-template-columns: 180px 1fr 120px 120px 40px; padding: 8px 12px; border-bottom: 1px solid rgba(255,255,255,0.05); align-items: center; gap: 8px; }
.ledger-row:last-child { border-bottom: none; }
.btn-delete-row { background: transparent; border: none; color: #f87171; font-weight: bold; cursor: pointer; display: flex; align-items: center; justify-content: center; }
.btn-add-row { padding: 8px 14px; border-radius: 8px; border: 1px dashed rgba(255,255,255,0.2); background: transparent; color: #c4b5fd; font-size: 12px; cursor: pointer; text-align: center; }
.btn-add-row:hover { background: rgba(124,58,237,0.1); border-color: #7c3aed; }

/* Balance Bar */
.balance-bar { display: flex; justify-content: flex-end; align-items: center; gap: 24px; font-size: 13px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); padding: 12px 20px; border-radius: 10px; }
.balance-bar.balanced { border-color: rgba(52,211,153,0.3); }
.balance-bar.unbalanced { border-color: rgba(239,68,68,0.3); }
.diff-tag { font-size: 12px; padding: 2px 8px; border-radius: 6px; font-weight: bold; }
.diff-tag { background: rgba(239,68,68,0.15); color: #f87171; }
.diff-tag.matched { background: rgba(52,211,153,0.15); color: #34d399; }

/* Laporan view layouts */
.laporan-wrapper { display: flex; flex-direction: column; gap: 20px; }
.lap-section { border: 1px solid rgba(255,255,255,0.07); border-radius: 12px; overflow: hidden; background: rgba(255,255,255,0.01); }
.lap-section-title { font-size: 13px; font-weight: 700; color: #c084fc; text-transform: uppercase; background: rgba(255,255,255,0.04); padding: 10px 16px; border-bottom: 1px solid rgba(255,255,255,0.07); }
.lap-row { display: flex; justify-content: justify; align-items: center; padding: 10px 16px; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.03); }
.lap-row span { flex: 1; }
.lap-row strong { font-family: monospace; font-size: 14px; }
.lap-row.total { background: rgba(255,255,255,0.02); font-weight: 600; color: #34d399; }
.lap-row.total.red { color: #f87171; }
.lap-row.grand-total { border-radius: 10px; padding: 14px 20px; font-size: 15px; font-weight: 700; display: flex; }
.lap-row.grand-total span { flex: 1; }
.lap-row.grand-total strong { font-size: 17px; }

/* Neraca Grid Layout */
.neraca-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
.sub-title { font-size: 12px; font-weight: 700; color: #818cf8; text-transform: uppercase; margin: 10px 16px 5px; }
.neraca-balance-check { margin-top: 15px; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 600; text-align: center; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
