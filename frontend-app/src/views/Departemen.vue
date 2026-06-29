<template>
  <div class="dashboard-container">
    <Sidebar />

    <!-- Main Content Area -->
    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Manajemen Departemen</h1>
          <p class="subtitle">Kelola unit kerja dan struktur organisasi perusahaan</p>
        </div>
      </header>

      <div class="content-body">
        <div class="content-grid">
          <!-- Form Tambah Departemen -->
          <section class="card form-card">
            <h3>Tambah Departemen Baru</h3>
            <form @submit.prevent="handleSave" class="input-form">
              <div v-if="formError" class="alert error-alert">{{ formError }}</div>
              <div v-if="formSuccess" class="alert success-alert">{{ formSuccess }}</div>

              <div class="form-group">
                <label for="nama_departemen">Nama Departemen</label>
                <input 
                  type="text" 
                  id="nama_departemen" 
                  v-model="newDept.nama_departemen" 
                  placeholder="Misal: HRD, IT, Keuangan" 
                  required 
                />
              </div>

              <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea 
                  id="keterangan" 
                  v-model="newDept.keterangan" 
                  placeholder="Penjelasan singkat unit kerja..." 
                  rows="3"
                ></textarea>
              </div>

              <button type="submit" class="btn-primary" :disabled="isSaving">
                <span v-if="isSaving" class="spinner-small"></span>
                <span v-else>Simpan Departemen</span>
              </button>
            </form>
          </section>

          <!-- Tabel Data Departemen -->
          <section class="card table-card">
            <div class="table-header">
              <h3>Daftar Departemen</h3>
              <button @click="fetchDepartemen" class="btn-refresh" :disabled="isLoading">
                🔄 {{ isLoading ? 'Memuat...' : 'Segarkan' }}
              </button>
            </div>

            <div v-if="isLoading" class="loading-state">
              <div class="spinner-large"></div>
              <p>Mengambil data departemen dari backend...</p>
            </div>

            <div v-else-if="departemenList.length === 0" class="empty-state">
              <span class="empty-icon">📁</span>
              <p>Belum ada data departemen yang tersedia.</p>
            </div>

            <div v-else class="table-responsive">
              <table class="custom-table">
                <thead>
                  <tr>
                    <th>No</th>
                    <th>Nama Departemen</th>
                    <th>Keterangan</th>
                    <th class="actions-col">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(dept, index) in departemenList" :key="dept.id">
                    <td>{{ index + 1 }}</td>
                    <td class="font-bold">{{ dept.nama_departemen }}</td>
                    <td class="text-muted">{{ dept.keterangan || '-' }}</td>
                    <td class="actions-col">
                      <button 
                        @click="handleDelete(dept.id)" 
                        class="btn-danger-icon" 
                        title="Hapus"
                        :disabled="isDeletingId === dept.id"
                      >
                        <span v-if="isDeletingId === dept.id" class="spinner-small"></span>
                        <span v-else>🗑</span>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </section>
        </div>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

export default {
  name: 'DepartemenView',
  components: {
    Sidebar
  },
  data() {
    return {
      user: {},
      departemenList: [],
      newDept: {
        nama_departemen: '',
        keterangan: ''
      },
      isLoading: false,
      isSaving: false,
      isDeletingId: null,
      formError: '',
      formSuccess: ''
    }
  },
  computed: {
    userInitial() {
      if (this.user.nama_lengkap) {
        return this.user.nama_lengkap.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
      }
      return 'US'
    }
  },
  created() {
    // Muat data user dari local storage
    const userData = localStorage.getItem('user')
    if (userData) {
      this.user = JSON.parse(userData)
    }
    this.fetchDepartemen()
  },
  methods: {
    getHeaders() {
      const token = localStorage.getItem('jwt_token')
      return {
        headers: {
          Authorization: `Bearer ${token}`
        }
      }
    },
    getApiUrl() {
      return import.meta.env.VITE_API_URL || 'http://127.0.0.1:8080'
    },
    async fetchDepartemen() {
      this.isLoading = true
      try {
        const response = await axios.get(`${this.getApiUrl()}/api/kepegawaian/departemen`, this.getHeaders())
        if (response.data && response.data.data) {
          this.departemenList = response.data.data
        }
      } catch (error) {
        console.error('Error fetching departemen:', error)
        if (error.response && error.response.status === 401) {
          this.handleLogout()
        }
      } finally {
        this.isLoading = false
      }
    },
    async handleSave() {
      this.isSaving = true
      this.formError = ''
      this.formSuccess = ''
      try {
        const response = await axios.post(
          `${this.getApiUrl()}/api/kepegawaian/departemen/save`,
          this.newDept,
          this.getHeaders()
        )
        
        if (response.data) {
          this.formSuccess = 'Departemen berhasil disimpan!'
          this.newDept.nama_departemen = ''
          this.newDept.keterangan = ''
          this.fetchDepartemen()
        }
      } catch (error) {
        console.error('Error saving departemen:', error)
        if (error.response && error.response.status === 401) {
          this.handleLogout()
        } else {
          this.formError = error.response?.data?.messages?.error || 'Gagal menyimpan departemen.'
        }
      } finally {
        this.isSaving = false
      }
    },
    async handleDelete(id) {
      if (!confirm('Apakah Anda yakin ingin menghapus departemen ini?')) return
      
      this.isDeletingId = id
      try {
        await axios.delete(`${this.getApiUrl()}/api/kepegawaian/departemen/delete/${id}`, this.getHeaders())
        this.fetchDepartemen()
      } catch (error) {
        console.error('Error deleting departemen:', error)
        if (error.response && error.response.status === 401) {
          this.handleLogout()
        } else {
          alert('Gagal menghapus data departemen.')
        }
      } finally {
        this.isDeletingId = null
      }
    },
    handleLogout() {
      localStorage.removeItem('jwt_token')
      localStorage.removeItem('user')
      this.$router.push('/login')
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

.dashboard-container {
  min-height: 100vh;
  display: flex;
  background-color: #0b0f19;
  color: #f8fafc;
  font-family: 'Outfit', sans-serif;
  text-align: left;
}



.main-content {
  flex: 1;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
}

.content-header {
  height: 80px;
  padding: 0 32px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  background: rgba(26, 29, 46, 0.5);
  gap: 16px;
  flex-shrink: 0;
}

.content-header h1 {
  font-size: 22px;
  font-weight: 700;
  margin: 0;
  color: white;
}

.subtitle {
  color: #64748b;
  margin: 0;
  font-size: 13px;
}

.content-body {
  padding: 24px 32px;
}

.content-grid {
  display: grid;
  grid-template-columns: 360px 1fr;
  gap: 30px;
  align-items: start;
}

@media (max-width: 1024px) {
  .content-grid {
    grid-template-columns: 1fr;
  }
}

/* Cards */
.card {
  background: rgba(30, 41, 59, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  padding: 24px;
}

.card h3 {
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 20px 0;
  color: white;
}

/* Forms */
.input-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  font-size: 14px;
  color: #94a3b8;
  font-weight: 500;
}

.form-group input, .form-group textarea {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 10px;
  padding: 12px;
  color: white;
  font-family: inherit;
  font-size: 14px;
  outline: none;
  transition: all 0.3s ease;
}

.form-group input:focus, .form-group textarea:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.15);
}

.btn-primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.alert {
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 13px;
}

.error-alert {
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #f87171;
}

.success-alert {
  background-color: rgba(34, 197, 94, 0.1);
  border: 1px solid rgba(34, 197, 94, 0.2);
  color: #4ade80;
}

/* Tables */
.table-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.table-header h3 {
  margin: 0;
}

.btn-refresh {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #cbd5e1;
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 13px;
  cursor: pointer;
  transition: background 0.3s;
}

.btn-refresh:hover {
  background: rgba(255, 255, 255, 0.1);
}

.table-responsive {
  width: 100%;
  overflow-x: auto;
}

.custom-table {
  width: 100%;
  border-collapse: collapse;
  text-align: left;
}

.custom-table th {
  background-color: rgba(15, 23, 42, 0.4);
  padding: 14px 16px;
  color: #94a3b8;
  font-weight: 600;
  font-size: 14px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.custom-table td {
  padding: 16px;
  font-size: 14px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.03);
  color: #e2e8f0;
}

.custom-table tr:hover td {
  background-color: rgba(255, 255, 255, 0.01);
}

.font-bold {
  font-weight: 600;
  color: white;
}

.text-muted {
  color: #64748b;
}

.actions-col {
  width: 80px;
  text-align: center;
}

.btn-danger-icon {
  background: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.2);
  color: #ef4444;
  width: 32px;
  height: 32px;
  border-radius: 8px;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s;
}

.btn-danger-icon:hover {
  background: rgba(239, 68, 68, 0.25);
  transform: scale(1.05);
}

/* Loading & Empty State */
.loading-state, .empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px 20px;
  color: #64748b;
  gap: 12px;
}

.empty-icon {
  font-size: 48px;
}

.spinner-large {
  width: 40px;
  height: 40px;
  border: 3px solid rgba(99, 102, 241, 0.1);
  border-radius: 50%;
  border-top-color: #6366f1;
  animation: spin 0.8s linear infinite;
}

.spinner-small {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
