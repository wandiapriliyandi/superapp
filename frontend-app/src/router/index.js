import { createRouter, createWebHashHistory } from 'vue-router'
import Login      from '../views/Login.vue'
import Departemen from '../views/Departemen.vue'
import Perijinan  from '../views/Perijinan.vue'
import Kepegawaian from '../views/Kepegawaian.vue'
import Akademik   from '../views/Akademik.vue'
import Spp        from '../views/Spp.vue'
import Ppdb       from '../views/Ppdb.vue'
import Perpustakaan from '../views/Perpustakaan.vue'
import Keuangan     from '../views/Keuangan.vue'
import Setting      from '../views/Setting.vue'
import Poskestren   from '../views/Poskestren.vue'
import Sarpras      from '../views/Sarpras.vue'
import Alquran      from '../views/Alquran.vue'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true }
  },
  {
    path: '/',
    name: 'Departemen',
    component: Departemen,
    meta: { requiresAuth: true, requiredPermission: '*' }
  },
  {
    path: '/perijinan',
    name: 'Perijinan',
    component: Perijinan,
    meta: { requiresAuth: true, requiredPermission: 'perijinan' }
  },
  {
    path: '/poskestren',
    name: 'Poskestren',
    component: Poskestren,
    meta: { requiresAuth: true, requiredPermission: 'poskestren' }
  },
  {
    path: '/sarpras',
    name: 'Sarpras',
    component: Sarpras,
    meta: { requiresAuth: true, requiredPermission: 'sarpras' }
  },
  {
    path: '/kepegawaian',
    name: 'Kepegawaian',
    component: Kepegawaian,
    meta: { requiresAuth: true, requiredPermission: 'kepegawaian' }
  },
  {
    path: '/akademik',
    name: 'Akademik',
    component: Akademik,
    meta: { requiresAuth: true, requiredPermission: 'akademik' }
  },
  {
    path: '/spp',
    name: 'Spp',
    component: Spp,
    meta: { requiresAuth: true, requiredPermission: 'spp' }
  },
  {
    path: '/ppdb',
    name: 'Ppdb',
    component: Ppdb,
    meta: { requiresAuth: true, requiredPermission: 'ppdb' }
  },
  {
    path: '/perpustakaan',
    name: 'Perpustakaan',
    component: Perpustakaan,
    meta: { requiresAuth: true, requiredPermission: 'perpustakaan' }
  },
  {
    path: '/keuangan',
    name: 'Keuangan',
    component: Keuangan,
    meta: { requiresAuth: true, requiredPermission: 'keuangan' }
  },
  {
    path: '/setting',
    name: 'Setting',
    component: Setting,
    meta: { requiresAuth: true, requiredPermission: 'setting' }
  },
  {
    path: '/alquran',
    name: 'Alquran',
    component: Alquran,
    meta: { requiresAuth: true, requiredPermission: 'alquran' }
  }
]


const router = createRouter({
  // Gunakan Hash History agar kompatibel dengan Capacitor dan Electron (tidak mengandalkan server routing)
  history: createWebHashHistory(),
  routes
})

/**
 * Urutan modul untuk redirect otomatis ke halaman pertama yang diizinkan.
 * Gunakan urutan yang paling umum untuk setiap jabatan.
 */
const MODULE_ROUTES = [
  { permission: '*',           name: 'Departemen' },
  { permission: 'akademik',    name: 'Akademik' },
  { permission: 'perijinan',   name: 'Perijinan' },
  { permission: 'spp',         name: 'Spp' },
  { permission: 'ppdb',        name: 'Ppdb' },
  { permission: 'sarpras',     name: 'Sarpras' },
  { permission: 'kepegawaian', name: 'Kepegawaian' },
  { permission: 'keuangan',    name: 'Keuangan' },
  { permission: 'poskestren',  name: 'Poskestren' },
  { permission: 'perpustakaan',name: 'Perpustakaan' },
  { permission: 'alquran',     name: 'Alquran' },
  { permission: 'setting',     name: 'Setting' },
]

/**
 * Dapatkan halaman pertama yang diizinkan berdasarkan permissions user.
 * @param {string[]} perms - array permissions milik user
 * @returns {string} nama route tujuan
 */
function getFirstAllowedRoute(perms) {
  if (perms.includes('*')) return 'Departemen'
  for (const mod of MODULE_ROUTES) {
    if (perms.includes(mod.permission)) return mod.name
  }
  return 'Login'
}

// Navigation guard — cek token JWT dan permission
router.beforeEach((to, from) => {
  const token = localStorage.getItem('jwt_token')

  // Halaman yang butuh login
  if (to.matched.some(record => record.meta.requiresAuth)) {
    if (!token) {
      return { name: 'Login' }
    }

    // Ambil permissions dari localStorage
    const rawUser = localStorage.getItem('user_info') || localStorage.getItem('user')
    const perms = rawUser ? (JSON.parse(rawUser).permissions || []) : []
    const isAdmin = perms.includes('*')

    // Jika halaman ini butuh permission khusus, cek dulu
    const requiredPerm = to.meta.requiredPermission
    if (requiredPerm) {
      const allowed = isAdmin || perms.includes(requiredPerm)
      if (!allowed) {
        // Redirect ke halaman pertama yang diizinkan
        const firstRoute = getFirstAllowedRoute(perms)
        if (firstRoute === 'Login') return { name: 'Login' }
        if (firstRoute !== to.name) return { name: firstRoute }
      }
    }

  } else if (to.matched.some(record => record.meta.requiresGuest)) {
    if (token) {
      // Sudah login — redirect ke halaman pertama sesuai permissions
      const rawUser = localStorage.getItem('user_info') || localStorage.getItem('user')
      const perms = rawUser ? (JSON.parse(rawUser).permissions || []) : []
      return { name: getFirstAllowedRoute(perms) }
    }
  }
})

export default router
