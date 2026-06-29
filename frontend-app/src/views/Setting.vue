<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Pengaturan Sistem</h1>
          <p>Kelola profil pesantren, manajemen pengguna, dan hak akses modul sistem</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- ========== TAB 1: PROFIL PESANTREN ========== -->
      <section v-if="activeTab==='profil'">
        <div class="card" style="max-width: 680px">
          <div class="card-header">
            <h3>Identitas &amp; Profil Pesantren</h3>
          </div>
          <div class="p20" style="display: flex; flex-direction: column; gap: 16px">
            <div class="fg"><label>Nama Aplikasi</label><input v-model="profil.app_name" class="fi" placeholder="SuperApp" /></div>
            <div class="fg"><label>Nama Pesantren</label><input v-model="profil.pesantren_name" class="fi" placeholder="Ponpes Al-Hidayah" /></div>
            <div class="fg"><label>Alamat Lengkap</label><input v-model="profil.alamat" class="fi" /></div>
            <div class="fg"><label>Nomor Telepon / WA</label><input v-model="profil.telepon" class="fi" /></div>
            <div class="fg"><label>Email Resmi</label><input v-model="profil.email" type="email" class="fi" /></div>
            <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px">
              <div class="fg"><label>Warna Utama (Primary Theme)</label><input v-model="profil.theme_primary" type="color" class="fi color-fi" /></div>
              <div class="fg"><label>Mode Tema</label>
                <select v-model="profil.theme_mode" class="fi">
                  <option value="midnight">Dark / Midnight</option>
                  <option value="light">Light Mode</option>
                </select>
              </div>
            </div>
            <div style="text-align: right; margin-top: 10px">
              <button @click="saveProfil" class="btn-primary" :disabled="saving">Simpan Perubahan</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== TAB 2: USER MANAGEMENT ========== -->
      <section v-if="activeTab==='users'">
        <div class="card">
          <div class="card-header">
            <h3>Manajemen Akun Pengguna</h3>
            <button @click="openAddUser" class="btn-primary">+ Tambah User</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Lengkap</th><th>Username</th><th>Hak Akses / Role</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="5" class="loading-cell">Memuat data pengguna...</td></tr>
                <tr v-else-if="users.length===0"><td colspan="5" class="empty-cell">Belum ada user terdaftar</td></tr>
                <tr v-for="(u, idx) in users" :key="u.id">
                  <td>{{ idx+1 }}</td>
                  <td class="name-cell">{{ u.nama_lengkap }}</td>
                  <td><code>{{ u.username }}</code></td>
                  <td><span class="badge badge-info">{{ u.nama_role || 'No Role / Custom' }}</span></td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditUser(u)" class="ab ab-blue">✏️</button>
                      <button @click="deleteUser(u.id, u.username)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 3: ROLE MANAGEMENT ========== -->
      <section v-if="activeTab==='roles'">
        <div class="card">
          <div class="card-header">
            <h3>Hak Akses Modul (Role Permissions)</h3>
            <button @click="openAddRole" class="btn-primary">+ Tambah Role</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Role / Pangkat</th><th>Izin Diberikan</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="4" class="loading-cell">Memuat data role...</td></tr>
                <tr v-else-if="roles.length===0"><td colspan="4" class="empty-cell">Belum ada role terdaftar</td></tr>
                <tr v-for="(r, idx) in roles" :key="r.id">
                  <td>{{ idx+1 }}</td>
                  <td class="name-cell" style="width: 200px">{{ r.nama_role }}</td>
                  <td>
                    <div v-if="r.permissions && r.permissions.includes('*')" style="display:flex;align-items:center;gap:6px">
                      <span class="badge badge-gold">⭐ Superadmin — Akses Penuh ke Semua Fitur</span>
                    </div>
                    <div v-else class="perm-container">
                      <span v-for="p in (r.permissions || []).slice(0,8)" :key="p" class="badge badge-success perm-badge">
                        {{ permLabelMap[p] || p }}
                      </span>
                      <span v-if="(r.permissions || []).length > 8" class="badge badge-info perm-badge">
                        +{{ r.permissions.length - 8 }} lainnya
                      </span>
                      <span v-if="!(r.permissions || []).length" class="badge badge-danger">Tidak ada izin</span>
                    </div>
                  </td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditRole(r)" class="ab ab-blue">✏️</button>
                      <button v-if="!(r.permissions && r.permissions.includes('*'))" @click="deleteRole(r.id, r.nama_role)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB: LOG AKTIVITAS ========== -->
      <section v-if="activeTab==='activity'">
        <div class="card">
          <div class="card-header" style="justify-content: space-between;">
            <h3>📜 Riwayat &amp; Log Aktivitas Pengguna</h3>
            <button v-if="userPermissions.includes('*') || userPermissions.includes('setting.activity.hapus')" 
                    @click="clearActivityLogs" 
                    class="btn-primary" 
                    style="background: #ef4444;" 
                    :disabled="loading || activityLogs.length === 0">
              🗑️ Bersihkan Semua Log
            </button>
          </div>

          <!-- Filter & Search Panel -->
          <div class="p20" style="background: rgba(255, 255, 255, 0.01); border-bottom: 1px solid rgba(255, 255, 255, 0.05); display: flex; gap: 16px; flex-wrap: wrap; align-items: center;">
            <div style="flex: 1; min-width: 250px; position: relative;">
              <input v-model="activitySearch" @input="debounceSearch" type="text" class="fi" placeholder="Cari nama user, aktivitas, atau detail..." style="padding-left: 36px;" />
              <span style="position: absolute; left: 14px; top: 12px; opacity: 0.5;">🔍</span>
            </div>
            
            <div style="width: 200px;">
              <select v-model="activityModule" @change="fetchActivityLogs(1)" class="fi">
                <option value="">-- Semua Modul --</option>
                <option v-for="mod in activityModules" :key="mod" :value="mod">{{ mod }}</option>
              </select>
            </div>
            
            <div style="width: 120px;">
              <select v-model="activityLimit" @change="fetchActivityLogs(1)" class="fi">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
                <option :value="100">100 baris</option>
              </select>
            </div>

            <button @click="resetActivityFilters" class="btn-secondary" style="height: 38px; display: flex; align-items: center; justify-content: center; gap: 6px;">
              🔄 Reset
            </button>
          </div>

          <!-- Table -->
          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 50px;">#</th>
                  <th style="width: 160px;">Waktu</th>
                  <th style="width: 140px;">Pengguna</th>
                  <th style="width: 120px;">Modul</th>
                  <th>Aktivitas</th>
                  <th>Detail</th>
                  <th style="width: 130px;">IP Address</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loadingActivity"><td colspan="7" class="loading-cell">Memuat data log aktivitas...</td></tr>
                <tr v-else-if="activityLogs.length===0"><td colspan="7" class="empty-cell">Tidak ada log aktivitas ditemukan</td></tr>
                <tr v-for="(log, idx) in activityLogs" :key="log.id" :class="{'expanded-row': expandedLogId === log.id}">
                  <td>{{ (activityPage - 1) * activityLimit + idx + 1 }}</td>
                  <td class="text-muted" style="white-space: nowrap;">{{ formatDate(log.created_at) }}</td>
                  <td><strong style="color: #cbd5e1;">{{ log.user }}</strong></td>
                  <td>
                    <span :class="['badge', getModuleBadgeClass(log.module)]">
                      {{ log.module }}
                    </span>
                  </td>
                  <td style="font-weight: 500;">{{ log.activity }}</td>
                  <td>
                    <div v-if="log.details" style="max-width: 250px;">
                      <div v-if="expandedLogId === log.id" style="white-space: pre-wrap; font-family: monospace; font-size: 11px; color: #a7f3d0; background: rgba(0,0,0,0.2); padding: 8px; border-radius: 6px; margin-top: 4px; max-height: 150px; overflow-y: auto;">
                        {{ log.details }}
                      </div>
                      <span v-else class="text-muted" style="font-size: 12px; cursor: pointer; display: inline-block; max-width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" @click="toggleExpandLog(log.id)">
                        {{ log.details }}
                      </span>
                      <button @click="toggleExpandLog(log.id)" style="background: none; border: none; color: #818cf8; font-size: 11px; padding: 0; cursor: pointer; margin-top: 2px; font-weight: 600; display: block;">
                        {{ expandedLogId === log.id ? 'Sembunyikan' : 'Lihat Detail' }}
                      </button>
                    </div>
                    <span v-else class="text-muted">—</span>
                  </td>
                  <td><code>{{ log.ip_address }}</code></td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="activityTotalPages > 1" class="p20" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.06); flex-wrap: wrap; gap: 12px;">
            <div class="text-muted" style="font-size: 12px;">
              Menampilkan <strong>{{ (activityPage - 1) * activityLimit + 1 }}</strong> - 
              <strong>{{ Math.min(activityPage * activityLimit, activityTotal) }}</strong> dari 
              <strong>{{ activityTotal }}</strong> log
            </div>
            
            <div style="display: flex; gap: 6px; align-items: center;">
              <button @click="changeActivityPage(1)" :disabled="activityPage === 1" class="tab-btn" style="padding: 6px 10px;">« First</button>
              <button @click="changeActivityPage(activityPage - 1)" :disabled="activityPage === 1" class="tab-btn" style="padding: 6px 12px;">‹ Prev</button>
              
              <!-- Page numbers -->
              <button v-for="p in pageNumbers" :key="p" @click="changeActivityPage(p)" :class="['tab-btn', activityPage === p ? 'active-indigo' : '']" style="padding: 6px 12px; font-weight: 500;">
                {{ p }}
              </button>
              
              <button @click="changeActivityPage(activityPage + 1)" :disabled="activityPage === activityTotalPages" class="tab-btn" style="padding: 6px 12px;">Next ›</button>
              <button @click="changeActivityPage(activityTotalPages)" :disabled="activityPage === activityTotalPages" class="tab-btn" style="padding: 6px 10px;">Last »</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== TAB 4: DATABASE MIGRATIONS ========== -->
      <section v-if="activeTab==='migrate'">
        <div class="card">
          <div class="card-header">
            <h3>Pusat Migrasi &amp; Skema Database</h3>
            <div style="display:flex; gap:10px">
              <button @click="pullGit" class="btn-primary" style="background:#475569" :disabled="loadingMigrate">🔄 Pull Git</button>
              <button @click="runMigrationLatest" class="btn-primary" style="background:#10b981" :disabled="loadingMigrate">⚡ Jalankan Migrasi</button>
            </div>
          </div>

          <div class="p20" style="display:flex; flex-direction:column; gap:20px">
            <!-- Connection Status -->
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px">
              <div :class="['alert-box', migrateStatus.db_connected ? 'alert-success' : 'alert-error']">
                <div class="alert-title">
                  <span>🔌 Status Database:</span>
                  <strong>{{ migrateStatus.db_connected ? 'Terkoneksi' : 'Terputus' }}</strong>
                </div>
                <div class="alert-desc" v-if="!migrateStatus.db_connected">
                  Error: {{ migrateStatus.db_error }}
                </div>
                <div class="alert-desc" v-else>
                  Driver/Group: MySQL (default) | Environment: <code>{{ migrateStatus.environment }}</code>
                </div>
              </div>

              <div class="alert-box alert-info-box">
                <div class="alert-title">
                  <span>📦 Tabel Migrasi:</span>
                  <strong>{{ migrateStatus.has_migrations_table ? 'Tersedia' : 'Belum Ada' }}</strong>
                </div>
                <div class="alert-desc">
                  Total batch migrasi tercatat: <code>{{ migrateStatus.history_count }} batch</code>
                </div>
              </div>
            </div>

            <!-- Terminal Output / Action Logs -->
            <div v-if="actionOutput" class="terminal-box">
              <div class="terminal-header">
                <span>💻 Terminal Output</span>
                <button @click="actionOutput = ''" class="terminal-clear">Bersihkan</button>
              </div>
              <pre class="terminal-content"><code>{{ actionOutput }}</code></pre>
            </div>

            <!-- Advanced Actions -->
            <div class="advanced-actions-card">
              <h4>⚠️ Tindakan Lanjutan (Gunakan dengan Hati-hati)</h4>
              <div style="display:flex; gap:12px; margin-top:10px; flex-wrap:wrap">
                <button @click="runMigrationRollback" class="btn-secondary" style="border-color:#ef4444; color:#f87171" :disabled="loadingMigrate">↩️ Rollback 1 Batch</button>
                <button @click="runMigrationRefresh" class="btn-secondary" style="border-color:#dc2626; color:#ef4444; background:rgba(220,38,38,0.05)" :disabled="loadingMigrate">☢️ Refresh Migrasi (Reset &amp; Run)</button>
                <button @click="runSeeder" class="btn-secondary" style="border-color:#3b82f6; color:#60a5fa" :disabled="loadingMigrate">🌱 Jalankan UserSeeder</button>
              </div>
            </div>

            <!-- Migration Files Table -->
            <div>
              <h4 style="margin-bottom:12px">Daftar Berkas Skema Migrasi</h4>
              <div class="table-wrapper" style="border: 1px solid rgba(255,255,255,0.06); border-radius:10px; overflow:hidden">
                <table class="data-table">
                  <thead>
                    <tr>
                      <th>Status</th>
                      <th>Nama Berkas</th>
                      <th>Batch</th>
                      <th>Tanggal Dieksekusi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-if="loading"><td colspan="4" class="loading-cell">Memuat data migrasi...</td></tr>
                    <tr v-else-if="migrateStatus.migration_files.length===0"><td colspan="4" class="empty-cell">Tidak ada berkas migrasi ditemukan</td></tr>
                    <tr v-for="m in migrateStatus.migration_files" :key="m.file">
                      <td>
                        <span :class="['badge', m.is_executed ? 'badge-success' : 'badge-danger']">
                          {{ m.is_executed ? 'Sudah' : 'Belum' }}
                        </span>
                      </td>
                      <td><code>{{ m.file }}</code></td>
                      <td><code>{{ m.batch }}</code></td>
                      <td class="text-muted">{{ m.executed_time }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </section>
    </main>

    <!-- ===== MODAL: USER FORM ===== -->
    <div v-if="showUserForm" class="modal-overlay" @click.self="showUserForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>👥 {{ formUser.id ? 'Edit Pengguna' : 'Tambah Pengguna Baru' }}</h3><button @click="showUserForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Nama Lengkap *</label><input v-model="formUser.nama_lengkap" class="fi" /></div>
          <div class="fg"><label>Username *</label><input v-model="formUser.username" class="fi" /></div>
          <div class="fg"><label>Pilih Role Hak Akses *</label>
            <select v-model="formUser.role_id" class="fi">
              <option value="">-- Pilih Role --</option>
              <option v-for="r in roles" :key="r.id" :value="r.id">{{ r.nama_role }}</option>
            </select>
          </div>
          <div class="fg full">
            <label>Password {{ formUser.id ? '(Kosongkan jika tidak diubah)' : '*' }}</label>
            <input v-model="formUser.password" type="password" class="fi" />
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showUserForm=false" class="btn-secondary">Batal</button>
          <button @click="saveUser" class="btn-primary" :disabled="saving">Simpan</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: ROLE FORM ===== -->
    <div v-if="showRoleForm" class="modal-overlay" @click.self="showRoleForm=false">
      <div class="role-modal-box">
        <!-- Header -->
        <div class="modal-head">
          <h3>🛡️ {{ formRole.id ? 'Edit Role' : 'Tambah Role &amp; Hak Akses' }}</h3>
          <button @click="showRoleForm=false" class="modal-close">✕</button>
        </div>

        <!-- Scrollable body -->
        <div class="role-modal-body">
          <!-- Nama Role -->
          <div class="fg">
            <label>Nama Role *</label>
            <input v-model="formRole.nama_role" class="fi" placeholder="Contoh: Wali Kelas, Bendahara SPP, Kepala Sekolah..." />
          </div>

          <div v-if="!formRole.permissions.includes('*')">
            <!-- Filter Pencarian -->
            <div class="perm-search-box">
              <span class="perm-search-icon">🔍</span>
              <input
                v-model="permSearch"
                class="perm-search-input"
                placeholder="Cari modul atau fitur... (contoh: santri, payroll, peminjaman)"
                @input="onPermSearch"
              />
              <button v-if="permSearch" @click="permSearch=''; onPermSearch()" class="perm-search-clear">✕</button>
            </div>

            <p class="perm-tree-title">Hak Akses Per Submodul &amp; Fitur</p>

            <div v-if="filteredTree.length === 0" class="perm-empty-search">
              Tidak ada modul/fitur yang cocok dengan "<strong>{{ permSearch }}</strong>"
            </div>

            <div class="perm-modul-list-wrapper">
              <div v-for="modul in filteredTree" :key="modul.key" class="perm-modul-block">
                <!-- Level 1: Modul Header -->
                <div class="perm-modul-header" @click="toggleModul(modul.key)">
                  <span class="perm-expand-icon">{{ expandedModuls.includes(modul.key) ? '▾' : '▸' }}</span>
                  <input type="checkbox"
                    :id="'chk-m-' + modul.key"
                    :checked="isModulChecked(modul)"
                    :indeterminate.prop="isModulIndeterminate(modul)"
                    @change="toggleModulAll(modul, $event)"
                    @click.stop
                  />
                  <label :for="'chk-m-' + modul.key" @click.stop class="perm-modul-label">
                    {{ modul.icon }} <strong>{{ modul.label }}</strong>
                  </label>
                </div>

                <!-- Level 2: Submodul (collapsible) -->
                <div v-show="expandedModuls.includes(modul.key)" class="perm-submodul-list">
                  <div v-for="sub in modul.submoduls" :key="sub.key" class="perm-submodul-block">
                    <div class="perm-submodul-header">
                      <input type="checkbox"
                        :id="'chk-s-' + sub.key"
                        :checked="isSubChecked(sub)"
                        :indeterminate.prop="isSubIndeterminate(sub)"
                        @change="toggleSubAll(sub, $event)"
                      />
                      <label :for="'chk-s-' + sub.key" class="perm-sub-label">{{ sub.label }}</label>
                      <code class="perm-route">{{ sub.route }}</code>
                    </div>

                    <!-- Level 3: Aksi CRUD -->
                    <div class="perm-actions-row">
                      <label v-for="aksi in sub.aksi" :key="aksi.key" :class="['perm-aksi-pill', 'aksi-' + aksi.type]">
                        <input type="checkbox" :value="aksi.key" v-model="formRole.permissions" />
                        <span>{{ aksi.label }}</span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Counter -->
          <div class="perm-count-bar">
            Total izin dipilih: <strong>{{ formRole.permissions.length }}</strong>
          </div>
        </div>

        <!-- Footer tombol -->
        <div class="modal-actions">
          <button @click="showRoleForm=false" class="btn-secondary">Batal</button>
          <button @click="saveRole" class="btn-primary" :disabled="saving">Simpan Role</button>
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
const activeTab     = ref('profil')
const loading       = ref(false)
const saving        = ref(false)
const toast         = ref({ show: false, message: '', type: 'success' })

const profil        = ref({ app_name: '', pesantren_name: '', alamat: '', telepon: '', email: '', theme_mode: 'midnight', theme_primary: '#6366f1' })
const users         = ref([])
const roles         = ref([])

const showUserForm  = ref(false)
const showRoleForm  = ref(false)

const formUser      = ref({ id: '', nama_lengkap: '', username: '', role_id: '', password: '' })
const formRole      = ref({ id: '', nama_role: '', permissions: [] })

const user = JSON.parse(localStorage.getItem('user') || '{}')
const userPermissions = user.permissions || []

const tabs = computed(() => {
  const list = [
    { key: 'profil', label: 'Profil Pesantren', icon: '🏢', color: 'blue' },
    { key: 'users', label: 'Pengguna', icon: '👥', color: 'purple' },
    { key: 'roles', label: 'Hak Akses / Role', icon: '🛡️', color: 'green' },
  ]
  if (userPermissions.includes('*') || userPermissions.includes('setting.activity') || userPermissions.includes('setting.activity.baca')) {
    list.push({ key: 'activity', label: 'Log Aktivitas', icon: '📜', color: 'indigo' })
  }
  if (userPermissions.includes('*') || userPermissions.includes('setting.migrate') || userPermissions.includes('setting.migrate.baca')) {
    list.push({ key: 'migrate', label: 'Migrasi Database', icon: '⚡', color: 'orange' })
  }
  return list
})

// === STATE MIGRASI ===
const migrateStatus = ref({ db_connected: false, db_error: '', environment: '', has_migrations_table: false, migration_files: [], history_count: 0 })
const loadingMigrate = ref(false)
const actionOutput  = ref('')

// === STATE LOG AKTIVITAS ===
const activityLogs = ref([])
const activityModules = ref([])
const activityPage = ref(1)
const activityLimit = ref(25)
const activitySearch = ref('')
const activityModule = ref('')
const activityTotal = ref(0)
const activityTotalPages = ref(0)
const loadingActivity = ref(false)
const expandedLogId = ref(null)

const modulesMap = {
  '*': '⭐ Superadmin (Akses Semua)',
  'perijinan': '📄 Perizinan Santri',
  'akademik': '🎓 Siakad & Akademik',
  'spp': '💰 Pembayaran SPP',
  'keuangan': '📊 Keuangan Akuntansi',
  'kepegawaian': '👥 HRM & Kepegawaian',
  'perpustakaan': '📚 Perpustakaan',
  'ppdb': '📋 PPDB (Pendaftaran)',
  'poskestren': '🏥 Poskestren',
  'sarpras': '📦 Inventaris & Sarpras',
  'alquran': '📖 Modul Al-Qur\'an (Tahsin & Tahfidz)',
  'setting': '⚙️ Pengaturan Sistem',
  'activity': '📜 Log Aktivitas Audit'
}

// Permission tree 3-level: Modul > Submodul > Aksi CRUD
const permissionTree = [
  {
    key: 'perijinan', label: 'Perizinan Santri', icon: '📄',
    submoduls: [
      { key: 'perijinan', label: 'Perizinan Santri', route: '/perijinan', aksi: [
        { key: 'perijinan.baca', label: 'Lihat', type: 'read' },
        { key: 'perijinan.tambah', label: 'Tambah', type: 'create' },
        { key: 'perijinan.ubah', label: 'Ubah', type: 'update' },
        { key: 'perijinan.hapus', label: 'Hapus', type: 'delete' },
      ]},
    ]
  },
  {
    key: 'akademik', label: 'Siakad & Akademik', icon: '🎓',
    submoduls: [
      { key: 'akademik.santri', label: 'Data Santri', route: '/akademik#santri', aksi: [
        { key: 'akademik.santri.baca', label: 'Lihat', type: 'read' },
        { key: 'akademik.santri.tambah', label: 'Tambah', type: 'create' },
        { key: 'akademik.santri.ubah', label: 'Ubah', type: 'update' },
        { key: 'akademik.santri.hapus', label: 'Hapus', type: 'delete' },
        { key: 'akademik.santri.cetak', label: 'Cetak Kartu', type: 'print' },
      ]},
      { key: 'akademik.kelas', label: 'Kelas', route: '/akademik#kelas', aksi: [
        { key: 'akademik.kelas.baca', label: 'Lihat', type: 'read' },
        { key: 'akademik.kelas.tambah', label: 'Tambah', type: 'create' },
        { key: 'akademik.kelas.ubah', label: 'Ubah', type: 'update' },
        { key: 'akademik.kelas.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'akademik.mapel', label: 'Mata Pelajaran', route: '/akademik#mapel', aksi: [
        { key: 'akademik.mapel.baca', label: 'Lihat', type: 'read' },
        { key: 'akademik.mapel.tambah', label: 'Tambah', type: 'create' },
        { key: 'akademik.mapel.ubah', label: 'Ubah', type: 'update' },
        { key: 'akademik.mapel.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'akademik.jadwal', label: 'Jadwal Pelajaran', route: '/akademik#jadwal', aksi: [
        { key: 'akademik.jadwal.baca', label: 'Lihat', type: 'read' },
        { key: 'akademik.jadwal.tambah', label: 'Tambah', type: 'create' },
        { key: 'akademik.jadwal.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'akademik.absensi', label: 'Absensi Kelas', route: '/akademik#absensi', aksi: [
        { key: 'akademik.absensi.baca', label: 'Lihat', type: 'read' },
        { key: 'akademik.absensi.input', label: 'Input Absensi', type: 'create' },
      ]},
      { key: 'akademik.nilai', label: 'Nilai & Rapor', route: '/akademik#nilai', aksi: [
        { key: 'akademik.nilai.baca', label: 'Lihat', type: 'read' },
        { key: 'akademik.nilai.input', label: 'Input Nilai', type: 'create' },
        { key: 'akademik.nilai.hapus', label: 'Hapus', type: 'delete' },
      ]},
    ]
  },
  {
    key: 'spp', label: 'Pembayaran SPP', icon: '💰',
    submoduls: [
      { key: 'spp.tagihan', label: 'Tagihan SPP', route: '/spp#tagihan', aksi: [
        { key: 'spp.tagihan.baca', label: 'Lihat', type: 'read' },
        { key: 'spp.tagihan.generate', label: 'Generate', type: 'create' },
        { key: 'spp.tagihan.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'spp.pembayaran', label: 'Pembayaran', route: '/spp#pembayaran', aksi: [
        { key: 'spp.pembayaran.baca', label: 'Lihat', type: 'read' },
        { key: 'spp.pembayaran.bayar', label: 'Konfirmasi Bayar', type: 'create' },
      ]},
      { key: 'spp.tarif', label: 'Tarif SPP', route: '/spp#tarif', aksi: [
        { key: 'spp.tarif.baca', label: 'Lihat', type: 'read' },
        { key: 'spp.tarif.tambah', label: 'Tambah', type: 'create' },
        { key: 'spp.tarif.ubah', label: 'Ubah', type: 'update' },
        { key: 'spp.tarif.hapus', label: 'Hapus', type: 'delete' },
      ]},
    ]
  },
  {
    key: 'keuangan', label: 'Keuangan & Akuntansi', icon: '📊',
    submoduls: [
      { key: 'keuangan.akun', label: 'Chart of Accounts', route: '/keuangan#akun', aksi: [
        { key: 'keuangan.akun.baca', label: 'Lihat', type: 'read' },
        { key: 'keuangan.akun.tambah', label: 'Tambah', type: 'create' },
        { key: 'keuangan.akun.ubah', label: 'Ubah', type: 'update' },
        { key: 'keuangan.akun.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'keuangan.jurnal', label: 'Jurnal Transaksi', route: '/keuangan#jurnal', aksi: [
        { key: 'keuangan.jurnal.baca', label: 'Lihat', type: 'read' },
        { key: 'keuangan.jurnal.tambah', label: 'Input Jurnal', type: 'create' },
        { key: 'keuangan.jurnal.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'keuangan.laporan', label: 'Laporan Keuangan', route: '/keuangan#laporan', aksi: [
        { key: 'keuangan.laporan.baca', label: 'Lihat', type: 'read' },
        { key: 'keuangan.laporan.cetak', label: 'Cetak / Export', type: 'print' },
      ]},
    ]
  },
  {
    key: 'kepegawaian', label: 'HRM & Kepegawaian', icon: '👥',
    submoduls: [
      { key: 'kepegawaian.pegawai', label: 'Data Pegawai / Asatidz', route: '/kepegawaian#pegawai', aksi: [
        { key: 'kepegawaian.pegawai.baca', label: 'Lihat', type: 'read' },
        { key: 'kepegawaian.pegawai.tambah', label: 'Tambah', type: 'create' },
        { key: 'kepegawaian.pegawai.ubah', label: 'Ubah', type: 'update' },
        { key: 'kepegawaian.pegawai.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'kepegawaian.jabatan', label: 'Jabatan & Pangkat', route: '/kepegawaian#jabatan', aksi: [
        { key: 'kepegawaian.jabatan.baca', label: 'Lihat', type: 'read' },
        { key: 'kepegawaian.jabatan.tambah', label: 'Tambah', type: 'create' },
        { key: 'kepegawaian.jabatan.ubah', label: 'Ubah', type: 'update' },
        { key: 'kepegawaian.jabatan.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'kepegawaian.absensi', label: 'Absensi Pegawai', route: '/kepegawaian#absensi', aksi: [
        { key: 'kepegawaian.absensi.baca', label: 'Lihat', type: 'read' },
        { key: 'kepegawaian.absensi.input', label: 'Input Absensi', type: 'create' },
      ]},
      { key: 'kepegawaian.cuti', label: 'Cuti & Izin Pegawai', route: '/kepegawaian#cuti', aksi: [
        { key: 'kepegawaian.cuti.baca', label: 'Lihat', type: 'read' },
        { key: 'kepegawaian.cuti.ajukan', label: 'Ajukan', type: 'create' },
        { key: 'kepegawaian.cuti.setujui', label: 'Setujui/Tolak', type: 'update' },
      ]},
      { key: 'kepegawaian.payroll', label: 'Penggajian (Payroll)', route: '/kepegawaian#payroll', aksi: [
        { key: 'kepegawaian.payroll.baca', label: 'Lihat', type: 'read' },
        { key: 'kepegawaian.payroll.generate', label: 'Generate Slip', type: 'create' },
        { key: 'kepegawaian.payroll.bayar', label: 'Tandai Dibayar', type: 'update' },
      ]},
    ]
  },
  {
    key: 'perpustakaan', label: 'Perpustakaan', icon: '📚',
    submoduls: [
      { key: 'perpustakaan.buku', label: 'Katalog Buku', route: '/perpustakaan#buku', aksi: [
        { key: 'perpustakaan.buku.baca', label: 'Lihat', type: 'read' },
        { key: 'perpustakaan.buku.tambah', label: 'Tambah', type: 'create' },
        { key: 'perpustakaan.buku.ubah', label: 'Ubah', type: 'update' },
        { key: 'perpustakaan.buku.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'perpustakaan.peminjaman', label: 'Peminjaman & Kembali', route: '/perpustakaan#peminjaman', aksi: [
        { key: 'perpustakaan.peminjaman.baca', label: 'Lihat', type: 'read' },
        { key: 'perpustakaan.peminjaman.pinjam', label: 'Catat Pinjam', type: 'create' },
        { key: 'perpustakaan.peminjaman.kembali', label: 'Catat Kembali', type: 'update' },
      ]},
    ]
  },
  {
    key: 'ppdb', label: 'PPDB (Pendaftaran)', icon: '📋',
    submoduls: [
      { key: 'ppdb.pendaftar', label: 'Data Pendaftar', route: '/ppdb#pendaftar', aksi: [
        { key: 'ppdb.pendaftar.baca', label: 'Lihat', type: 'read' },
        { key: 'ppdb.pendaftar.ubah', label: 'Update Status', type: 'update' },
        { key: 'ppdb.pendaftar.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'ppdb.berkas', label: 'Verifikasi Berkas', route: '/ppdb#berkas', aksi: [
        { key: 'ppdb.berkas.baca', label: 'Lihat', type: 'read' },
        { key: 'ppdb.berkas.verifikasi', label: 'Verifikasi', type: 'update' },
      ]},
      { key: 'ppdb.jadwal', label: 'Jadwal Tes', route: '/ppdb#jadwal', aksi: [
        { key: 'ppdb.jadwal.baca', label: 'Lihat', type: 'read' },
        { key: 'ppdb.jadwal.tambah', label: 'Tambah', type: 'create' },
        { key: 'ppdb.jadwal.hapus', label: 'Hapus', type: 'delete' },
      ]},
    ]
  },
  {
    key: 'poskestren', label: 'Poskestren & Klinik', icon: '🏥',
    submoduls: [
      { key: 'poskestren.rekammedis', label: 'Rekam Medis Santri', route: '/poskestren#rekammedis', aksi: [
        { key: 'poskestren.rekammedis.baca', label: 'Lihat', type: 'read' },
        { key: 'poskestren.rekammedis.tambah', label: 'Input Kunjungan', type: 'create' },
        { key: 'poskestren.rekammedis.ubah', label: 'Update Kondisi', type: 'update' },
        { key: 'poskestren.rekammedis.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'poskestren.obat', label: 'Manajemen Obat', route: '/poskestren#obat', aksi: [
        { key: 'poskestren.obat.baca', label: 'Lihat', type: 'read' },
        { key: 'poskestren.obat.tambah', label: 'Tambah Obat', type: 'create' },
        { key: 'poskestren.obat.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'poskestren.stok', label: 'Mutasi & Stok Obat', route: '/poskestren#stok', aksi: [
        { key: 'poskestren.stok.baca', label: 'Lihat', type: 'read' },
        { key: 'poskestren.stok.tambah', label: 'Catat Mutasi', type: 'create' },
      ]},
    ]
  },
  {
    key: 'sarpras', label: 'Inventaris & Sarpras', icon: '📦',
    submoduls: [
      { key: 'sarpras.barang', label: 'Master Barang', route: '/sarpras#barang', aksi: [
        { key: 'sarpras.barang.baca', label: 'Lihat', type: 'read' },
        { key: 'sarpras.barang.tambah', label: 'Tambah', type: 'create' },
        { key: 'sarpras.barang.ubah', label: 'Ubah', type: 'update' },
        { key: 'sarpras.barang.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'sarpras.peminjaman', label: 'Peminjaman Barang', route: '/sarpras#peminjaman', aksi: [
        { key: 'sarpras.peminjaman.baca', label: 'Lihat', type: 'read' },
        { key: 'sarpras.peminjaman.pinjam', label: 'Catat Pinjam', type: 'create' },
        { key: 'sarpras.peminjaman.kembali', label: 'Catat Kembali', type: 'update' },
        { key: 'sarpras.peminjaman.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'sarpras.mutasi', label: 'Mutasi Stok', route: '/sarpras#mutasi', aksi: [
        { key: 'sarpras.mutasi.baca', label: 'Lihat', type: 'read' },
        { key: 'sarpras.mutasi.input', label: 'Input Mutasi', type: 'create' },
      ]},
    ]
  },
  {
    key: 'setting', label: 'Pengaturan Sistem', icon: '⚙️',
    submoduls: [
      { key: 'setting.profil', label: 'Profil Pesantren', route: '/setting#profil', aksi: [
        { key: 'setting.profil.baca', label: 'Lihat', type: 'read' },
        { key: 'setting.profil.ubah', label: 'Ubah', type: 'update' },
      ]},
      { key: 'setting.users', label: 'Manajemen Pengguna', route: '/setting#users', aksi: [
        { key: 'setting.users.baca', label: 'Lihat', type: 'read' },
        { key: 'setting.users.tambah', label: 'Tambah', type: 'create' },
        { key: 'setting.users.ubah', label: 'Ubah', type: 'update' },
        { key: 'setting.users.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'setting.roles', label: 'Hak Akses / Role', route: '/setting#roles', aksi: [
        { key: 'setting.roles.baca', label: 'Lihat', type: 'read' },
        { key: 'setting.roles.tambah', label: 'Tambah', type: 'create' },
        { key: 'setting.roles.ubah', label: 'Ubah', type: 'update' },
        { key: 'setting.roles.hapus', label: 'Hapus', type: 'delete' },
      ]},
      { key: 'setting.activity', label: 'Log Aktivitas', route: '/setting#activity', aksi: [
        { key: 'setting.activity.baca', label: 'Lihat Log', type: 'read' },
        { key: 'setting.activity.hapus', label: 'Bersihkan Log', type: 'delete' },
      ]},
      { key: 'setting.migrate', label: 'Migrasi Database', route: '/setting#migrate', aksi: [
        { key: 'setting.migrate.baca', label: 'Lihat Status', type: 'read' },
        { key: 'setting.migrate.eksekusi', label: 'Eksekusi / Jalankan', type: 'update' },
      ]},
    ]
  },
  {
    key: 'alquran', label: 'Modul Al-Qur\'an', icon: '📖',
    submoduls: [
      { key: 'alquran.tahsin', label: 'Tahsin Qur\'an', route: '/alquran#tahsin', aksi: [
        { key: 'alquran.tahsin.baca', label: 'Lihat', type: 'read' },
        { key: 'alquran.tahsin.tambah', label: 'Tambah Penilaian', type: 'create' },
        { key: 'alquran.tahsin.ubah', label: 'Ubah Penilaian', type: 'update' },
        { key: 'alquran.tahsin.hapus', label: 'Hapus Penilaian', type: 'delete' },
      ]},
      { key: 'alquran.tahfidz', label: 'Tahfidz Qur\'an', route: '/alquran#tahfidz', aksi: [
        { key: 'alquran.tahfidz.baca', label: 'Lihat', type: 'read' },
        { key: 'alquran.tahfidz.tambah', label: 'Tambah Setoran', type: 'create' },
        { key: 'alquran.tahfidz.ubah', label: 'Ubah Setoran', type: 'update' },
        { key: 'alquran.tahfidz.hapus', label: 'Hapus Setoran', type: 'delete' },
      ]},
    ]
  },
]

// Peta kunci permission ke label yang terbaca manusia (untuk tampilan badge)
const permLabelMap = {}

// Level-1: nama modul (misal: 'perijinan' → '📄 Perizinan Santri')
permissionTree.forEach(m => {
  permLabelMap[m.key] = `${m.icon} ${m.label}`
  // Level-3: aksi CRUD granular
  m.submoduls.forEach(s => {
    // Level-2: submodul (misal: 'perijinan.perijinan' → label submodul)
    permLabelMap[s.key] = `${m.icon} ${s.label}`
    s.aksi.forEach(a => {
      permLabelMap[a.key] = `${m.icon} ${s.label} — ${a.label}`
    })
  })
})
permLabelMap['*'] = '⭐ Superadmin'

// Tracking modul yang di-expand di modal
const expandedModuls = ref([])
const permSearch = ref('')

function toggleModul(key) {
  const idx = expandedModuls.value.indexOf(key)
  if (idx >= 0) expandedModuls.value.splice(idx, 1)
  else expandedModuls.value.push(key)
}

// Filter tree berdasarkan pencarian
const filteredTree = ref(permissionTree)
function onPermSearch() {
  const q = permSearch.value.trim().toLowerCase()
  if (!q) {
    filteredTree.value = permissionTree
    // Collapse semua saat pencarian dihapus
    expandedModuls.value = []
    return
  }
  const result = []
  for (const m of permissionTree) {
    const modulMatch = m.label.toLowerCase().includes(q) || m.key.includes(q)
    const matchedSubs = m.submoduls.filter(s =>
      s.label.toLowerCase().includes(q) ||
      s.key.includes(q) ||
      s.aksi.some(a => a.label.toLowerCase().includes(q))
    )
    if (modulMatch) {
      result.push(m)
      if (!expandedModuls.value.includes(m.key)) expandedModuls.value.push(m.key)
    } else if (matchedSubs.length > 0) {
      result.push({ ...m, submoduls: matchedSubs })
      if (!expandedModuls.value.includes(m.key)) expandedModuls.value.push(m.key)
    }
  }
  filteredTree.value = result
}

// Cek apakah semua aksi di submodul terpilih
function isSubChecked(sub) {
  return sub.aksi.every(a => formRole.value.permissions.includes(a.key))
}
function isSubIndeterminate(sub) {
  const some = sub.aksi.some(a => formRole.value.permissions.includes(a.key))
  return some && !isSubChecked(sub)
}
function toggleSubAll(sub, e) {
  const keys = sub.aksi.map(a => a.key)
  if (e.target.checked) {
    keys.forEach(k => { if (!formRole.value.permissions.includes(k)) formRole.value.permissions.push(k) })
  } else {
    formRole.value.permissions = formRole.value.permissions.filter(p => !keys.includes(p))
  }
}

// Cek apakah semua aksi di modul terpilih
function isModulChecked(modul) {
  return modul.submoduls.every(s => isSubChecked(s))
}
function isModulIndeterminate(modul) {
  const someAny = modul.submoduls.some(s => s.aksi.some(a => formRole.value.permissions.includes(a.key)))
  return someAny && !isModulChecked(modul)
}
function toggleModulAll(modul, e) {
  const keys = modul.submoduls.flatMap(s => s.aksi.map(a => a.key))
  if (e.target.checked) {
    keys.forEach(k => { if (!formRole.value.permissions.includes(k)) formRole.value.permissions.push(k) })
  } else {
    formRole.value.permissions = formRole.value.permissions.filter(p => !keys.includes(p))
  }
}

function toggleSuperadmin(e) {
  if (e.target.checked) {
    formRole.value.permissions = ['*']
  } else {
    formRole.value.permissions = []
  }
}

// ===== HELPERS =====
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }

// ===== METHODS =====
async function switchTab(key) {
  activeTab.value = key
  if (key === 'profil') await fetchProfil()
  if (key === 'users') await fetchUsers()
  if (key === 'roles') await fetchRoles()
  if (key === 'activity') await fetchActivityLogs()
  if (key === 'migrate') await fetchMigrateStatus()
}

// === USER ACTIVITY LOG METHODS ===
async function fetchActivityLogs(page = 1) {
  loadingActivity.value = true
  activityPage.value = page
  try {
    const params = {
      page: activityPage.value,
      limit: activityLimit.value,
      search: activitySearch.value,
      module: activityModule.value
    }
    const res = await axios.get(`${API}/setting/activity`, { headers, params })
    if (res.data && res.data.status === 200) {
      activityLogs.value = res.data.data.logs || []
      activityTotal.value = res.data.data.pagination.total || 0
      activityTotalPages.value = res.data.data.pagination.total_pages || 0
      activityModules.value = res.data.data.modules || []
    }
  } catch {
    showNotif('Gagal memuat log aktivitas', 'error')
  } finally {
    loadingActivity.value = false
  }
}

function resetActivityFilters() {
  activitySearch.value = ''
  activityModule.value = ''
  activityPage.value = 1
  fetchActivityLogs(1)
}

function toggleExpandLog(id) {
  if (expandedLogId.value === id) {
    expandedLogId.value = null
  } else {
    expandedLogId.value = id
  }
}

function changeActivityPage(page) {
  if (page >= 1 && page <= activityTotalPages.value) {
    fetchActivityLogs(page)
  }
}

async function clearActivityLogs() {
  const confirm1 = confirm('PERINGATAN!\n\nTindakan ini akan menghapus seluruh catatan log aktivitas secara permanen.\n\nApakah Anda yakin ingin melanjutkan?')
  if (!confirm1) return
  
  const confirm2 = prompt('Ketik "BERSIHKAN" untuk mengonfirmasi:')
  if (confirm2 !== 'BERSIHKAN') return showNotif('Konfirmasi salah, tindakan dibatalkan.', 'error')

  loading.value = true
  try {
    const res = await axios.delete(`${API}/setting/activity/clear`, { headers })
    if (res.data && res.data.status === 200) {
      showNotif(res.data.message || 'Log aktivitas berhasil dibersihkan!')
      resetActivityFilters()
    }
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal membersihkan log aktivitas'
    showNotif(msg, 'error')
  } finally {
    loading.value = false
  }
}

function formatDate(dateStr) {
  if (!dateStr) return '—'
  try {
    return new Date(dateStr).toLocaleString('id-ID', { 
      dateStyle: 'medium', 
      timeStyle: 'short' 
    })
  } catch {
    return dateStr
  }
}

function getModuleBadgeClass(mod) {
  const m = String(mod).toLowerCase()
  if (m.includes('sistem') || m.includes('auth')) return 'badge-info'
  if (m.includes('db') || m.includes('database')) return 'badge-danger'
  if (m.includes('spp') || m.includes('keuangan')) return 'badge-success'
  return 'badge-gold'
}

let searchTimeout = null
function debounceSearch() {
  if (searchTimeout) clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchActivityLogs(1)
  }, 400)
}

const pageNumbers = computed(() => {
  const current = activityPage.value
  const total = activityTotalPages.value
  const delta = 2
  const range = []
  
  let start = Math.max(1, current - delta)
  let end = Math.min(total, current + delta)
  
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  return range
})

// === DATABASE MIGRATION METHODS ===
async function fetchMigrateStatus() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/setting/migrate`, { headers })
    if (res.data && res.data.data) {
      migrateStatus.value = res.data.data
    }
  } catch {
    showNotif('Gagal memuat status migrasi', 'error')
  } finally {
    loading.value = false
  }
}

async function runMigrationLatest() {
  loadingMigrate.value = true
  actionOutput.value = 'Menjalankan migrasi database...'
  try {
    const res = await axios.post(`${API}/setting/migrate/latest`, {}, { headers })
    showNotif(res.data.message || 'Migrasi berhasil dijalankan!')
    actionOutput.value = res.data.message || 'Sukses menjalankan migrasi.'
    await fetchMigrateStatus()
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal menjalankan migrasi'
    showNotif(msg, 'error')
    actionOutput.value = 'ERROR:\n' + msg
  } finally {
    loadingMigrate.value = false
  }
}

async function runMigrationRollback() {
  if (!confirm('Apakah Anda yakin ingin membatalkan (rollback) 1 batch migrasi terakhir?')) return
  loadingMigrate.value = true
  actionOutput.value = 'Melakukan rollback migrasi...'
  try {
    const res = await axios.post(`${API}/setting/migrate/rollback`, {}, { headers })
    showNotif(res.data.message || 'Rollback berhasil!')
    actionOutput.value = res.data.message || 'Sukses melakukan rollback.'
    await fetchMigrateStatus()
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal melakukan rollback'
    showNotif(msg, 'error')
    actionOutput.value = 'ERROR:\n' + msg
  } finally {
    loadingMigrate.value = false
  }
}

async function runMigrationRefresh() {
  const check = confirm('PERINGATAN KERAS!\n\nTindakan ini akan menghapus (drop) seluruh tabel skema migrasi dan membuatnya ulang dari nol. Seluruh data Anda akan terhapus!\n\nApakah Anda yakin ingin melanjutkan?')
  if (!check) return
  const check2 = prompt('Untuk mengonfirmasi, ketik kata "REFRESH" di bawah ini:')
  if (check2 !== 'REFRESH') return showNotif('Konfirmasi salah, tindakan dibatalkan.', 'error')

  loadingMigrate.value = true
  actionOutput.value = 'Mereset dan membangun ulang seluruh tabel migrasi...'
  try {
    const res = await axios.post(`${API}/setting/migrate/refresh`, {}, { headers })
    showNotif(res.data.message || 'Refresh migrasi sukses!')
    actionOutput.value = res.data.message || 'Sukses melakukan refresh migrasi.'
    await fetchMigrateStatus()
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal melakukan refresh migrasi'
    showNotif(msg, 'error')
    actionOutput.value = 'ERROR:\n' + msg
  } finally {
    loadingMigrate.value = false
  }
}

async function pullGit() {
  loadingMigrate.value = true
  actionOutput.value = 'Melakukan Git Pull dari repository origin main...'
  try {
    const res = await axios.post(`${API}/setting/migrate/pull`, {}, { headers })
    showNotif(res.data.message || 'Git pull berhasil!')
    actionOutput.value = `Output Git Pull:\n\n${res.data.output || 'No output'}`
    await fetchMigrateStatus()
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal melakukan git pull'
    showNotif(msg, 'error')
    actionOutput.value = `ERROR:\n${msg}\n\nOutput:\n${err.response?.data?.output || ''}`
  } finally {
    loadingMigrate.value = false
  }
}

async function runSeeder() {
  loadingMigrate.value = true
  actionOutput.value = 'Menjalankan UserSeeder...'
  try {
    const res = await axios.post(`${API}/setting/migrate/run-seeder`, {}, { headers })
    showNotif(res.data.message || 'UserSeeder berhasil dijalankan!')
    actionOutput.value = res.data.message || 'Sukses menjalankan UserSeeder.'
  } catch (err) {
    const msg = err.response?.data?.message || 'Gagal menjalankan seeder'
    showNotif(msg, 'error')
    actionOutput.value = 'ERROR:\n' + msg
  } finally {
    loadingMigrate.value = false
  }
}

// === PROFIL ===
async function fetchProfil() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/setting/profil`, { headers })
    if (res.data.data) {
      profil.value = res.data.data
    }
  } catch { showNotif('Gagal memuat profil pesantren', 'error') }
  finally { loading.value = false }
}

async function saveProfil() {
  if (!profil.value.app_name || !profil.value.pesantren_name) {
    return showNotif('Nama aplikasi & nama pesantren wajib diisi', 'error')
  }
  saving.value = true
  try {
    await axios.post(`${API}/setting/profil/save`, profil.value, { headers })
    showNotif('Profil pesantren berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan profil pesantren', 'error') }
  finally { saving.value = false }
}

// === USERS ===
async function fetchUsers() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/setting/users`, { headers })
    users.value = res.data.data || []
  } catch { showNotif('Gagal memuat pengguna', 'error') }
  finally { loading.value = false }
}

function openAddUser() {
  formUser.value = { id: '', nama_lengkap: '', username: '', role_id: '', password: '' }
  showUserForm.value = true
}

function openEditUser(u) {
  formUser.value = { ...u, password: '' }
  showUserForm.value = true
}

async function saveUser() {
  if (!formUser.value.nama_lengkap || !formUser.value.username) {
    return showNotif('Nama lengkap & username wajib diisi', 'error')
  }
  saving.value = true
  try {
    await axios.post(`${API}/setting/users/save`, formUser.value, { headers })
    showUserForm.value = false
    await fetchUsers()
    showNotif('Pengguna berhasil disimpan!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan pengguna', 'error')
  } finally { saving.value = false }
}

async function deleteUser(id, username) {
  const curUser = JSON.parse(localStorage.getItem('user_info') || localStorage.getItem('user') || '{}')
  if (curUser.username === username) {
    return showNotif('Anda tidak diperbolehkan menghapus akun Anda sendiri!', 'error')
  }
  if (!confirm(`Hapus pengguna "${username}"?`)) return
  try {
    await axios.delete(`${API}/setting/users/delete/${id}`, { headers })
    await fetchUsers()
    showNotif('Pengguna berhasil dihapus!')
  } catch { showNotif('Gagal menghapus pengguna', 'error') }
}

// === ROLES ===
async function fetchRoles() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/setting/roles`, { headers })
    roles.value = res.data.data || []
  } catch { showNotif('Gagal memuat role', 'error') }
  finally { loading.value = false }
}

function openAddRole() {
  formRole.value = { id: '', nama_role: '', permissions: [] }
  showRoleForm.value = true
}

function openEditRole(r) {
  formRole.value = { ...r }
  showRoleForm.value = true
}

async function saveRole() {
  if (!formRole.value.nama_role) return showNotif('Nama role wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/setting/roles/save`, formRole.value, { headers })
    showRoleForm.value = false
    await fetchRoles()
    showNotif('Role berhasil disimpan!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan role', 'error')
  } finally { saving.value = false }
}

async function deleteRole(id, nama) {
  if (!confirm(`Hapus role "${nama}"? Semua pengguna yang memiliki role ini akan kehilangan hak akses!`)) return
  try {
    await axios.delete(`${API}/setting/roles/delete/${id}`, { headers })
    await fetchRoles()
    showNotif('Role berhasil dihapus!')
  } catch { showNotif('Gagal menghapus role', 'error') }
}

// === INIT ===
async function init() {
  loading.value = true
  try {
    await Promise.all([
      fetchProfil(),
      fetchRoles(),
      fetchUsers()
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
.content-header { padding: 28px 32px 20px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(26,29,46,0.5); gap: 16px; flex-wrap: wrap; }
.content-header h1 { font-size: 22px; font-weight: 700; }
.content-header p { font-size: 13px; color: #64748b; margin-top: 2px; }
.header-tabs { display: flex; gap: 6px; }
.tab-btn { padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 12px; cursor: pointer; transition: all 0.2s; white-space: nowrap; }

.active-blue { background: rgba(96,165,250,0.15); color: #60a5fa; border-color: rgba(96,165,250,0.3); font-weight: 600; }
.active-purple { background: rgba(167,139,250,0.15); color: #a78bfa; border-color: rgba(167,139,250,0.3); font-weight: 600; }
.active-green { background: rgba(52,211,153,0.15); color: #34d399; border-color: rgba(52,211,153,0.3); font-weight: 600; }
.active-indigo { background: rgba(129,140,248,0.15); color: #818cf8; border-color: rgba(129,140,248,0.3); font-weight: 600; }
.expanded-row td { background: rgba(129, 140, 248, 0.03); }

/* Card */
.card { margin: 20px 32px 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
.card-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 12px; }
.card-header h3 { font-size: 16px; font-weight: 600; }

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

/* Badges */
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
.badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
.badge-info { background: rgba(99,102,241,0.15); color: #818cf8; }
.badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }

/* Permission Tree - Modal Khusus (scroll keseluruhan) */
.role-modal-box {
  background: linear-gradient(135deg, #1a1d2e, #1e2235);
  border: 1px solid rgba(124,58,237,0.3);
  border-radius: 20px;
  width: clamp(950px, 90vw, 1300px);
  max-height: 86vh;
  overflow-y: auto;
  overflow-x: hidden;
  display: flex;
  flex-direction: column;
}
.role-modal-box::-webkit-scrollbar { width: 6px; }
.role-modal-box::-webkit-scrollbar-track { background: transparent; }
.role-modal-box::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); border-radius: 4px; }

.role-modal-body {
  padding: 20px 24px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.perm-tree-title {
  font-size: 11px;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  font-weight: 700;
  margin-bottom: -4px;
}

/* Search box permission */
.perm-search-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(255,255,255,0.12);
  border-radius: 10px;
  padding: 8px 14px;
  margin-bottom: 12px;
  transition: border-color 0.2s;
}
.perm-search-box:focus-within { border-color: rgba(124,58,237,0.5); }
.perm-search-icon { font-size: 14px; flex-shrink: 0; opacity: 0.6; }
.perm-search-input {
  flex: 1;
  background: transparent;
  border: none;
  outline: none;
  color: #e2e8f0;
  font-size: 13px;
  font-family: 'Inter', sans-serif;
}
.perm-search-input::placeholder { color: #475569; }
.perm-search-clear {
  background: rgba(255,255,255,0.07);
  border: none;
  border-radius: 5px;
  color: #94a3b8;
  font-size: 11px;
  padding: 2px 7px;
  cursor: pointer;
  flex-shrink: 0;
}
.perm-search-clear:hover { background: rgba(239,68,68,0.15); color: #f87171; }
.perm-empty-search {
  text-align: center;
  padding: 20px;
  color: #64748b;
  font-size: 13px;
  background: rgba(255,255,255,0.02);
  border: 1px dashed rgba(255,255,255,0.08);
  border-radius: 10px;
}
.perm-empty-search strong { color: #94a3b8; }

.perm-modul-list-wrapper { display: flex; flex-direction: column; gap: 4px; }
.perm-modul-block {
  border: 1px solid rgba(255,255,255,0.08);
  border-radius: 10px;
  overflow: hidden;
  background: rgba(255,255,255,0.02);
}
.perm-modul-header {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 11px 14px;
  background: rgba(255,255,255,0.03);
  cursor: pointer;
  user-select: none;
  transition: background 0.15s;
}
.perm-modul-header:hover { background: rgba(255,255,255,0.07); }
.perm-modul-header input[type=checkbox] { width: 15px; height: 15px; accent-color: #7c3aed; flex-shrink: 0; cursor: pointer; }
.perm-modul-label { display: flex; align-items: center; gap: 7px; font-size: 13.5px; cursor: pointer; flex: 1; color: #e2e8f0; }
.perm-modul-label strong { color: #c4b5fd; }
.perm-expand-icon { font-size: 11px; color: #64748b; width: 14px; text-align: center; flex-shrink: 0; }

.perm-submodul-list { padding: 8px 12px 10px; display: flex; flex-direction: column; gap: 7px; }
.perm-submodul-block {
  background: rgba(0,0,0,0.2);
  border: 1px solid rgba(255,255,255,0.05);
  border-radius: 8px;
  padding: 10px 14px;
}
.perm-submodul-header {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-bottom: 10px;
  flex-wrap: wrap;
}
.perm-submodul-header input[type=checkbox] { width: 14px; height: 14px; accent-color: #7c3aed; flex-shrink: 0; cursor: pointer; }
.perm-sub-label { font-size: 13px; font-weight: 600; color: #cbd5e1; cursor: pointer; }
.perm-route { font-size: 10.5px; color: #64748b; background: rgba(255,255,255,0.05); padding: 2px 7px; border-radius: 4px; font-family: monospace; }

.perm-actions-row { display: flex; gap: 7px; flex-wrap: wrap; }
.perm-aksi-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12.5px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 7px;
  cursor: pointer;
  user-select: none;
  transition: opacity 0.15s;
  border: 1px solid transparent;
}
.perm-aksi-pill:hover { opacity: 0.75; }
.perm-aksi-pill input[type=checkbox] { width: 13px; height: 13px; cursor: pointer; margin: 0; flex-shrink: 0; }
.perm-aksi-pill span { white-space: nowrap; }

.aksi-read { background: rgba(96,165,250,0.12); color: #93c5fd; border-color: rgba(96,165,250,0.2); }
.aksi-read input { accent-color: #60a5fa; }
.aksi-create { background: rgba(52,211,153,0.12); color: #6ee7b7; border-color: rgba(52,211,153,0.2); }
.aksi-create input { accent-color: #34d399; }
.aksi-update { background: rgba(251,191,36,0.12); color: #fcd34d; border-color: rgba(251,191,36,0.2); }
.aksi-update input { accent-color: #fbbf24; }
.aksi-delete { background: rgba(239,68,68,0.12); color: #fca5a5; border-color: rgba(239,68,68,0.2); }
.aksi-delete input { accent-color: #f87171; }
.aksi-print { background: rgba(167,139,250,0.12); color: #c4b5fd; border-color: rgba(167,139,250,0.2); }
.aksi-print input { accent-color: #a78bfa; }

.superadmin-toggle {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  background: rgba(251,191,36,0.07);
  border: 1px solid rgba(251,191,36,0.25);
  border-radius: 10px;
  cursor: pointer;
}
.superadmin-toggle input { width: 18px; height: 18px; accent-color: #f59e0b; flex-shrink: 0; }
.superadmin-toggle label { font-size: 13.5px; cursor: pointer; color: #e2e8f0; }

.superadmin-active-note {
  margin: 0;
  padding: 18px;
  background: rgba(251,191,36,0.08);
  border: 1px solid rgba(251,191,36,0.25);
  border-radius: 10px;
  font-size: 14px;
  color: #fcd34d;
  text-align: center;
}

.perm-count-bar {
  font-size: 13px;
  color: #94a3b8;
  text-align: center;
  padding: 8px;
  background: rgba(124,58,237,0.07);
  border-radius: 8px;
}
.perm-count-bar strong { color: #c084fc; }

.badge-gold { background: rgba(251,191,36,0.15); color: #fbbf24; }

/* Permission list style (tabel role) */
.perm-container { display: flex; flex-wrap: wrap; gap: 5px; max-width: 500px; }
.perm-badge { font-size: 10.5px; padding: 2px 8px; border-radius: 4px; max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }

/* Action buttons */
.action-group { display: flex; gap: 4px; align-items: center; }
.ab { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; border-radius: 7px; border: 1px solid transparent; cursor: pointer; font-size: 13px; background: rgba(255,255,255,0.04); transition: all 0.2s; }
.ab:hover { transform: scale(1.1); }
.ab-blue:hover { background: rgba(96,165,250,0.15); }
.ab-del:hover { background: rgba(248,113,113,0.1); }

/* Buttons */
.btn-primary { padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity 0.2s; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; font-size: 13px; cursor: pointer; }

/* Modal Base */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.modal-box { background: linear-gradient(135deg, #1a1d2e, #1e2235); border: 1px solid rgba(124,58,237,0.3); border-radius: 20px; padding: 0; width: 560px; max-width: 95vw; max-height: 85vh; overflow-y: auto; }
.modal-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.07); flex-shrink: 0; }
.modal-head h3 { font-size: 16px; font-weight: 700; color: #c084fc; }
.modal-close { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; width: 28px; height: 28px; color: #94a3b8; cursor: pointer; font-size: 13px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.modal-close:hover { background: rgba(248,113,113,0.15); color: #f87171; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.p20 { padding: 20px 24px; }
.fg { display: flex; flex-direction: column; gap: 6px; }
.full { grid-column: 1 / -1; }
.fg label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.fi { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: #e2e8f0; font-size: 13px; outline: none; width: 100%; font-family: 'Inter', sans-serif; }
.fi:focus { border-color: #7c3aed; }
.fi option { background: #1a1d2e; }
.color-fi { padding: 4px 10px; height: 38px; cursor: pointer; }
.modal-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 16px 24px; border-top: 1px solid rgba(255,255,255,0.06); }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
/* Alert Box */
.alert-box { padding: 14px 18px; border-radius: 12px; border: 1px solid transparent; font-size: 13px; display: flex; flex-direction: column; gap: 4px; }
.alert-success { background: rgba(16,185,129,0.08); border-color: rgba(16,185,129,0.25); color: #10b981; }
.alert-error { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.25); color: #f87171; }
.alert-info-box { background: rgba(99,102,241,0.08); border-color: rgba(99,102,241,0.25); color: #818cf8; }
.alert-title { display: flex; justify-content: space-between; align-items: center; }
.alert-desc { font-size: 12px; opacity: 0.8; margin-top: 2px; }

/* Terminal Output Box */
.terminal-box { background: #090a0f; border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; overflow: hidden; font-family: monospace; }
.terminal-header { background: rgba(255,255,255,0.03); padding: 8px 14px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 11px; color: #64748b; }
.terminal-clear { background: transparent; border: none; color: #94a3b8; cursor: pointer; font-size: 11px; }
.terminal-clear:hover { color: #f87171; }
.terminal-content { padding: 14px; max-height: 200px; overflow-y: auto; margin: 0; font-size: 12px; color: #34d399; white-space: pre-wrap; word-break: break-all; }

/* Advanced Actions Card */
.advanced-actions-card { background: rgba(239,68,68,0.02); border: 1px dashed rgba(239,68,68,0.2); border-radius: 12px; padding: 16px 20px; }
.advanced-actions-card h4 { font-size: 13px; font-weight: 700; color: #fca5a5; }
</style>
