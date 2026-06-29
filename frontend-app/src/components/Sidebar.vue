<template>
  <aside :class="['sidebar', isMinimized ? 'minimized' : '']" @click="handleSidebarClick">
    <div class="sidebar-brand">
      <div class="logo">SA</div>
      <h2 class="app-title">SuperApp</h2>
      <button @click.stop="toggleMinimize" class="btn-toggle" title="Kecilkan Sidebar">
        ☰
      </button>
    </div>
    <nav class="sidebar-menu">
      <!-- Departemen: hanya superadmin -->
      <router-link v-if="hasPermission('*')" to="/" class="menu-item" active-class="active" exact :title="isMinimized ? 'Departemen' : ''">
        <span class="icon">🏢</span><span class="menu-text">Departemen</span>
      </router-link>
      <!-- Kepegawaian -->
      <router-link v-if="hasPermission('kepegawaian')" to="/kepegawaian" class="menu-item" active-class="active" :title="isMinimized ? 'Kepegawaian' : ''">
        <span class="icon">👥</span><span class="menu-text">Kepegawaian</span>
      </router-link>
      <!-- Akademik -->
      <router-link v-if="hasPermission('akademik')" to="/akademik" class="menu-item" active-class="active" :title="isMinimized ? 'Akademik' : ''">
        <span class="icon">🎓</span><span class="menu-text">Akademik</span>
      </router-link>
      <!-- Al-Qur'an (Tahsin & Tahfidz) -->
      <router-link v-if="hasPermission('alquran')" to="/alquran" class="menu-item" active-class="active" :title="isMinimized ? 'Al-Qur\'an (Tahsin & Tahfidz)' : ''">
        <span class="icon">📖</span><span class="menu-text">Al-Qur'an (Tahsin/Tahfidz)</span>
      </router-link>
      <!-- SPP -->
      <router-link v-if="hasPermission('spp')" to="/spp" class="menu-item" active-class="active" :title="isMinimized ? 'SPP' : ''">
        <span class="icon">💰</span><span class="menu-text">SPP</span>
      </router-link>
      <!-- PPDB -->
      <router-link v-if="hasPermission('ppdb')" to="/ppdb" class="menu-item" active-class="active" :title="isMinimized ? 'PPDB' : ''">
        <span class="icon">📋</span><span class="menu-text">PPDB</span>
      </router-link>
      <!-- Inventaris & Sarpras -->
      <router-link v-if="hasPermission('sarpras')" to="/sarpras" class="menu-item" active-class="active" :title="isMinimized ? 'Inventaris & Sarpras' : ''">
        <span class="icon">📦</span><span class="menu-text">Inventaris & Sarpras</span>
      </router-link>
      <!-- Perizinan Santri -->
      <router-link v-if="hasPermission('perijinan')" to="/perijinan" class="menu-item" active-class="active" :title="isMinimized ? 'Perizinan Santri' : ''">
        <span class="icon">📄</span><span class="menu-text">Perizinan Santri</span>
      </router-link>
      <!-- Poskestren -->
      <router-link v-if="hasPermission('poskestren')" to="/poskestren" class="menu-item" active-class="active" :title="isMinimized ? 'Poskestren' : ''">
        <span class="icon">🏥</span><span class="menu-text">Poskestren</span>
      </router-link>
      <!-- Perpustakaan -->
      <router-link v-if="hasPermission('perpustakaan')" to="/perpustakaan" class="menu-item" active-class="active" :title="isMinimized ? 'Perpustakaan' : ''">
        <span class="icon">📚</span><span class="menu-text">Perpustakaan</span>
      </router-link>
      <!-- Keuangan -->
      <router-link v-if="hasPermission('keuangan')" to="/keuangan" class="menu-item" active-class="active" :title="isMinimized ? 'Keuangan' : ''">
        <span class="icon">📊</span><span class="menu-text">Keuangan</span>
      </router-link>
      <!-- Pengaturan -->
      <router-link v-if="hasPermission('setting')" to="/setting" class="menu-item" active-class="active" :title="isMinimized ? 'Pengaturan' : ''">
        <span class="icon">⚙️</span><span class="menu-text">Pengaturan</span>
      </router-link>
    </nav>
    <div class="sidebar-user">
      <div class="user-avatar" :title="user.nama_lengkap">{{ userInitial }}</div>
      <div class="user-info">
        <span class="user-name">{{ user.nama_lengkap || 'Pengguna' }}</span>
        <span class="user-role">{{ user.role || 'Staff' }}</span>
      </div>
      <button @click.stop="handleLogout" class="btn-logout" :title="isMinimized ? 'Keluar' : 'Keluar dari Aplikasi'">🚪</button>
    </div>
  </aside>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const user = ref({})
const permissions = ref([])
const isMinimized = ref(localStorage.getItem('sidebar_minimized') === 'true')

const userInitial = computed(() => {
  return (user.value.nama_lengkap || 'U').charAt(0).toUpperCase()
})

/**
 * Cek apakah user memiliki permission untuk modul tertentu.
 * Jika user memiliki '*', maka semua modul diizinkan (superadmin).
 * @param {string} perm - kode permission (misal: 'akademik', 'sarpras')
 */
function hasPermission(perm) {
  if (permissions.value.includes('*')) return true
  return permissions.value.includes(perm)
}

function toggleMinimize() {
  isMinimized.value = !isMinimized.value
  localStorage.setItem('sidebar_minimized', isMinimized.value)
}

function handleSidebarClick() {
  if (isMinimized.value) {
    isMinimized.value = false
    localStorage.setItem('sidebar_minimized', 'false')
  }
}

function handleLogout() {
  localStorage.clear()
  router.push('/login')
}

onMounted(() => {
  const userData = localStorage.getItem('user_info') || localStorage.getItem('user')
  if (userData) {
    const parsed = JSON.parse(userData)
    user.value = parsed
    // Ambil permissions dari data user yang disimpan saat login
    permissions.value = parsed.permissions || []
  }
})
</script>

<style scoped>
.sidebar {
  width: 240px;
  background: linear-gradient(180deg, #1a1d2e 0%, #12151f 100%);
  display: flex;
  flex-direction: column;
  padding: 0 0 24px 0;
  border-right: 1px solid rgba(255, 255, 255, 0.05);
  flex-shrink: 0;
  transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.sidebar.minimized {
  width: 72px;
  cursor: pointer;
}
.sidebar.minimized:hover {
  background: linear-gradient(180deg, #1d2138 0%, #151928 100%);
}
.sidebar-brand {
  height: 80px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  padding: 0 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.07);
  overflow: hidden;
}
.sidebar.minimized .sidebar-brand {
  justify-content: center;
  padding: 0;
  gap: 0;
}
.logo {
  width: 38px;
  height: 38px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 14px;
  color: white;
  flex-shrink: 0;
}
.app-title {
  font-size: 16px;
  font-weight: 700;
  background: linear-gradient(135deg, #6366f1, #a78bfa);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  flex: 1;
  margin-left: 8px;
  white-space: nowrap;
  opacity: 1;
  transition: opacity 0.2s ease, width 0.2s ease, margin 0.2s ease;
  overflow: hidden;
}
.sidebar.minimized .app-title {
  opacity: 0;
  width: 0;
  margin: 0;
  pointer-events: none;
  display: none;
}

.btn-toggle {
  background: none;
  border: none;
  cursor: pointer;
  color: #818cf8;
  font-size: 18px;
  padding: 4px;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
  flex-shrink: 0;
  opacity: 1;
}
.btn-toggle:hover {
  background: rgba(255, 255, 255, 0.05);
  color: #e2e8f0;
  transform: scale(1.1);
}
.sidebar.minimized .btn-toggle {
  opacity: 0;
  width: 0;
  padding: 0;
  margin: 0;
  pointer-events: none;
  overflow: hidden;
  display: none;
}

.sidebar-menu {
  flex: 1;
  padding: 16px 12px;
  display: flex;
  flex-direction: column;
  gap: 4px;
  overflow-y: auto;
}
.sidebar.minimized .sidebar-menu {
  padding: 16px 8px;
  align-items: center;
  pointer-events: none;
}

/* Scrollbar styling for sidebar */
.sidebar-menu::-webkit-scrollbar {
  width: 4px;
}
.sidebar-menu::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 4px;
}

.menu-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  border-radius: 10px;
  color: #94a3b8;
  text-decoration: none;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.2s;
  cursor: pointer;
  width: 100%;
  overflow: hidden;
}
.sidebar.minimized .menu-item {
  justify-content: center;
  padding: 10px 0;
  border-radius: 8px;
}

.menu-item:hover, .menu-item.active {
  background: rgba(99, 102, 241, 0.12);
  color: #a5b4fc;
}
.icon {
  font-size: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.menu-text {
  white-space: nowrap;
  opacity: 1;
  transition: opacity 0.2s ease, width 0.2s ease;
}
.sidebar.minimized .menu-text {
  opacity: 0;
  width: 0;
  pointer-events: none;
  display: none;
}

/* User Profile */
.sidebar-user {
  padding: 16px 20px;
  border-top: 1px solid rgba(255, 255, 255, 0.07);
  display: flex;
  align-items: center;
  gap: 10px;
  transition: all 0.3s;
  overflow: hidden;
}
.sidebar.minimized .sidebar-user {
  padding: 16px 10px;
  justify-content: center;
}
.user-avatar {
  width: 32px;
  height: 32px;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 13px;
  color: white;
  flex-shrink: 0;
}
.user-info {
  flex: 1;
  overflow: hidden;
  transition: opacity 0.2s ease, width 0.2s ease;
}
.sidebar.minimized .user-info {
  opacity: 0;
  width: 0;
  pointer-events: none;
  display: none;
}
.user-name {
  display: block;
  font-size: 13px;
  font-weight: 600;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  color: #e2e8f0;
}
.user-role {
  display: block;
  font-size: 11px;
  color: #64748b;
  text-transform: uppercase;
}
.btn-logout {
  background: none;
  border: none;
  cursor: pointer;
  font-size: 16px;
  color: #64748b;
  transition: color 0.2s;
  flex-shrink: 0;
}
.btn-logout:hover {
  color: #f87171;
}
</style>
