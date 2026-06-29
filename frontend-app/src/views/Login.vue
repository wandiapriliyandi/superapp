<template>
  <div class="login-container">
    <div class="login-card">
      <div class="brand-header">
        <div class="logo">SA</div>
        <h1>SuperApp Portal</h1>
        <p>Silakan masuk untuk mengakses dashboard Anda</p>
      </div>

      <form @submit.prevent="handleLogin" class="login-form">
        <div v-if="errorMessage" class="error-alert">
          <span class="icon">⚠</span>
          <span class="message">{{ errorMessage }}</span>
        </div>

        <div class="form-group">
          <label for="username">Username</label>
          <div class="input-wrapper">
            <span class="input-icon">👤</span>
            <input 
              type="text" 
              id="username" 
              v-model="username" 
              placeholder="Masukkan username" 
              required 
            />
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrapper">
            <span class="input-icon">🔒</span>
            <input 
              type="password" 
              id="password" 
              v-model="password" 
              placeholder="Masukkan password" 
              required 
            />
          </div>
        </div>

        <button type="submit" class="btn-submit" :disabled="isLoading">
          <span v-if="isLoading" class="spinner"></span>
          <span v-else>Masuk Sekarang</span>
        </button>
      </form>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  name: 'LoginView',
  data() {
    return {
      username: '',
      password: '',
      isLoading: false,
      errorMessage: ''
    }
  },
  methods: {
    /**
     * Tentukan halaman pertama yang bisa diakses berdasarkan permissions.
     * Urutan prioritas: superadmin → akademik → perijinan → spp → ppdb → sarpras
     *                   → kepegawaian → keuangan → poskestren → perpustakaan → setting
     */
    getFirstAllowedRoute(permissions) {
      if (permissions.includes('*'))          return '/'
      if (permissions.includes('akademik'))   return '/akademik'
      if (permissions.includes('perijinan'))  return '/perijinan'
      if (permissions.includes('spp'))        return '/spp'
      if (permissions.includes('ppdb'))       return '/ppdb'
      if (permissions.includes('sarpras'))    return '/sarpras'
      if (permissions.includes('kepegawaian'))return '/kepegawaian'
      if (permissions.includes('keuangan'))   return '/keuangan'
      if (permissions.includes('poskestren')) return '/poskestren'
      if (permissions.includes('perpustakaan')) return '/perpustakaan'
      if (permissions.includes('alquran'))     return '/alquran'
      if (permissions.includes('setting'))    return '/setting'
      return '/'
    },
    async handleLogin() {
      this.isLoading = true
      this.errorMessage = ''

      const apiUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8080'

      try {
        const response = await axios.post(`${apiUrl}/api/auth/login`, {
          username: this.username,
          password: this.password
        })

        if (response.data && response.data.data) {
          const { token, user } = response.data.data
          localStorage.setItem('jwt_token', token)
          localStorage.setItem('user', JSON.stringify(user))
          
          // Redirect ke halaman pertama sesuai permissions
          const redirectTo = this.getFirstAllowedRoute(user.permissions || [])
          this.$router.push(redirectTo)
        } else {
          this.errorMessage = 'Respon server tidak terduga.'
        }
      } catch (error) {
        if (error.response && error.response.data) {
          this.errorMessage = error.response.data.messages?.error || error.response.data.message || 'Login gagal.'
        } else {
          this.errorMessage = 'Gagal terhubung ke server backend API.'
        }
      } finally {
        this.isLoading = false
      }
    }
  }
}
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');

.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: radial-gradient(circle at top right, #1e293b, #0f172a);
  font-family: 'Outfit', sans-serif;
  padding: 20px;
}

.login-card {
  background: rgba(30, 41, 59, 0.7);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  border: 1px rgba(255, 255, 255, 0.08) solid;
  border-radius: 24px;
  padding: 40px;
  width: 100%;
  max-width: 440px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
  transition: transform 0.3s ease;
}

.brand-header {
  text-align: center;
  margin-bottom: 32px;
}

.logo {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 60px;
  height: 60px;
  border-radius: 16px;
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
  font-size: 24px;
  font-weight: 700;
  margin-bottom: 16px;
  box-shadow: 0 8px 16px rgba(99, 102, 241, 0.3);
}

.brand-header h1 {
  color: white;
  font-size: 28px;
  font-weight: 700;
  margin: 0 0 8px 0;
  letter-spacing: -0.5px;
}

.brand-header p {
  color: #94a3b8;
  font-size: 14px;
  margin: 0;
}

.login-form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.error-alert {
  background: rgba(239, 68, 68, 0.15);
  border: 1px rgba(239, 68, 68, 0.3) solid;
  padding: 12px 16px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #f87171;
  font-size: 14px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.form-group label {
  color: #cbd5e1;
  font-size: 14px;
  font-weight: 500;
  text-align: left;
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 14px;
  font-size: 18px;
  color: #64748b;
  pointer-events: none;
}

.input-wrapper input {
  width: 100%;
  padding: 14px 14px 14px 44px;
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  color: white;
  font-size: 15px;
  outline: none;
  transition: all 0.3s ease;
}

.input-wrapper input:focus {
  border-color: #6366f1;
  box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
  background: rgba(15, 23, 42, 0.8);
}

.btn-submit {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
  border: none;
  padding: 14px;
  border-radius: 12px;
  font-size: 16px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 10px;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
}

.btn-submit:active:not(:disabled) {
  transform: translateY(0);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  width: 20px;
  height: 20px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}
</style>
