<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Modul Al-Qur'an</h1>
          <p class="subtitle">Kelola bimbingan Tahsin (kefasihan), progres Tahfidz (hafalan), dan Doa-doa harian santri</p>
        </div>
      </header>

      <div class="content-body">
        
        <!-- AUTOCOMPLETE SELECTOR AREA (ALA SELECT2) -->
        <div class="card selector-card">
          <div class="selector-row">
            <div class="selector-field">
              <label class="select-label">Pilih Santri untuk Evaluasi / Setoran:</label>
              
              <!-- Vue Custom Autocomplete Dropdown -->
              <div class="select2-container" ref="dropdownRef">
                <div class="select2-selection" @click="toggleDropdown">
                  <span v-if="!activeSantriId" class="select2-placeholder">
                    🔍 Cari &amp; pilih nama santri (ketik nama/NIS)...
                  </span>
                  <span v-else class="select2-selected-text">
                    🎓 {{ activeSantri.nama_lengkap }} (NIS: {{ activeSantri.nis || '-' }} | Kelas: {{ activeSantri.nama_kelas || '-' }})
                  </span>
                  <span class="select2-arrow">▼</span>
                </div>

                <!-- Floating Dropdown Panel -->
                <div v-show="isDropdownOpen" class="select2-dropdown">
                  <div class="select2-search-box">
                    <input 
                      ref="searchInputRef"
                      v-model="dropdownSearchQuery" 
                      type="text" 
                      placeholder="Ketik nama atau NIS santri..." 
                      class="select2-search-input"
                      @click.stop
                    />
                  </div>
                  <ul class="select2-results">
                    <li 
                      v-for="s in filteredDropdownSantri" 
                      :key="s.id" 
                      class="select2-result-item"
                      @click="selectSantriFromDropdown(s)"
                    >
                      <div class="res-nama">{{ s.nama_lengkap }}</div>
                      <div class="res-meta">NIS: {{ s.nis || '-' }} | Kelas: {{ s.nama_kelas || '-' }}</div>
                    </li>
                    <li v-if="filteredDropdownSantri.length === 0" class="select2-no-results">
                      Santri tidak ditemukan.
                    </li>
                  </ul>
                </div>
              </div>

            </div>
            
            <div class="selector-filter">
              <label class="select-label">Filter Kelas Utama:</label>
              <select v-model="selectedKelas" class="filter-select" @change="loadSantri">
                <option value="">Semua Kelas</option>
                <option v-for="k in kelasList" :key="k.id" :value="k.id">{{ k.nama_kelas }}</option>
              </select>
            </div>
          </div>
        </div>

        <!-- MAIN LAYOUT: JIKA BELUM PILIH SANTRI -->
        <div v-if="!activeSantriId" class="card empty-welcome-card">
          <div class="empty-state">
            <span class="empty-icon">📖</span>
            <h3>Selamat Datang di Modul Al-Qur'an</h3>
            <p>Silakan gunakan kotak pencarian di atas untuk memilih santri terlebih dahulu sebelum melakukan penilaian Tahsin, setoran Tahfidz, atau hafalan Doa.</p>
          </div>
        </div>

        <!-- MAIN LAYOUT: JIKA SUDAH PILIH SANTRI (FULL WIDTH DETAILS) -->
        <div v-else class="active-detail-wrapper">
          
          <!-- Identitas Santri Card -->
          <div class="card santri-profile-card">
            <div class="profile-header">
              <div class="avatar-circle">{{ userInitial(activeSantri.nama_lengkap) }}</div>
              <div class="profile-meta">
                <h2>{{ activeSantri.nama_lengkap }}</h2>
                <p>NISN: {{ activeSantri.nisn || '-' }} | NIS: {{ activeSantri.nis || '-' }} | Kelas: {{ activeSantri.nama_kelas || '-' }} | Status: <span class="status-active">{{ activeSantri.status_santri || 'Aktif' }}</span></p>
              </div>
            </div>

            <!-- Tab Navbar -->
            <div class="tab-navbar">
              <button :class="['tab-link', activeTab==='overview'?'active':'']" @click="activeTab='overview'">📊 Ringkasan &amp; Progres</button>
              <button :class="['tab-link', activeTab==='tahsin'?'active':'']" @click="activeTab='tahsin'">🗣️ Tahsin (Kefasihan Membaca)</button>
              <button :class="['tab-link', activeTab==='tahfidz'?'active':'']" @click="activeTab='tahfidz'">📝 Tahfidz (Setoran Hafalan)</button>
              <button :class="['tab-link', activeTab==='doa'?'active':'']" @click="activeTab='doa'">🤲 Hafalan Doa-doa</button>
            </div>
          </div>

          <!-- ==================== TAB 1: OVERVIEW & PROGRES ==================== -->
          <div v-if="activeTab === 'overview'" class="tab-pane-card card">
            <h3>Statistik Perkembangan</h3>
            <div v-if="loadingStats" class="loading-state">
              <div class="spinner-large"></div>
            </div>
            <div v-else>
              <div class="stats-overview-grid">
                <div class="stat-box-mini bg-violet">
                  <span class="stat-val">{{ stats.total_juz || 0 }}</span>
                  <span class="stat-lbl">Juz Dihafal</span>
                </div>
                <div class="stat-box-mini bg-emerald">
                  <span class="stat-val">{{ stats.total_ziyadah || 0 }}</span>
                  <span class="stat-lbl">Setoran Ziyadah</span>
                </div>
                <div class="stat-box-mini bg-indigo">
                  <span class="stat-val">{{ stats.total_murajaah || 0 }}</span>
                  <span class="stat-lbl">Setoran Murajaah</span>
                </div>
                <div class="stat-box-mini bg-gold">
                  <span class="stat-val">{{ stats.total_doa || 0 }}</span>
                  <span class="stat-lbl">Doa Dihafal</span>
                </div>
              </div>

              <!-- PETA JUZ -->
              <h4 class="section-title">Peta Hafalan Juz (1-30)</h4>
              <p class="section-subtitle">Kotak hijau menunjukkan juz yang sudah disetorkan (Ziyadah):</p>
              <div class="juz-grid">
                <div 
                  v-for="j in 30" 
                  :key="j" 
                  :class="['juz-box', stats.juz_dihafal && stats.juz_dihafal.includes(j) ? 'memorized' : '']"
                  :title="'Juz ' + j + (stats.juz_dihafal && stats.juz_dihafal.includes(j) ? ' (Sudah Setor)' : ' (Belum Setor)')"
                >
                  {{ j }}
                </div>
              </div>

              <!-- CHECKLIST DOA HARIAN -->
              <h4 class="section-title">Peta Hafalan Doa Harian</h4>
              <p class="section-subtitle">Berikut adalah daftar progres hafalan doa-doa harian santri (hijau = hafal lancar):</p>
              <div class="doa-checklist-grid">
                <div 
                  v-for="d in standardDoaList" 
                  :key="d" 
                  :class="['doa-check-item', hasMemorizedDoa(d) ? 'memorized' : '']"
                >
                  <span class="check-icon">{{ hasMemorizedDoa(d) ? '✅' : '⏳' }}</span>
                  <span class="doa-name-txt">{{ d }}</span>
                </div>
              </div>

              <!-- Rangkuman Tahsin Terakhir -->
              <h4 class="section-title">Hasil Evaluasi Tahsin Terakhir</h4>
              <div v-if="activeSantri.tahsin_summary" class="tahsin-summary-display">
                <div class="tahsin-gauge-row">
                  <div class="gauge-card">
                    <span class="gauge-num text-violet">{{ activeSantri.tahsin_summary.makharij }}</span>
                    <span class="gauge-lbl">Makharijul Huruf</span>
                  </div>
                  <div class="gauge-card">
                    <span class="gauge-num text-indigo">{{ activeSantri.tahsin_summary.sifat }}</span>
                    <span class="gauge-lbl">Sifatul Huruf</span>
                  </div>
                  <div class="gauge-card">
                    <span class="gauge-num text-emerald">{{ activeSantri.tahsin_summary.tajwid }}</span>
                    <span class="gauge-lbl">Hukum Tajwid</span>
                  </div>
                  <div class="gauge-card">
                    <span class="gauge-num text-gold">⭐ {{ activeSantri.tahsin_summary.rata_rata }}</span>
                    <span class="gauge-lbl">Rata-rata Total</span>
                  </div>
                </div>
                <p class="summary-eval-date">Evaluasi terakhir pada: {{ formatDate(activeSantri.tahsin_summary.tanggal) }}</p>
              </div>
              <div v-else class="info-alert" style="margin-top: 16px;">
                Santri ini belum pernah melakukan ujian/evaluasi Tahsin.
              </div>
            </div>
          </div>

          <!-- ==================== TAB 2: TAHSIN (DETAILED FORM & HISTORY) ==================== -->
          <div v-if="activeTab === 'tahsin'" class="tab-content-split">
            <!-- Form Ujian Tahsin -->
            <div class="card form-split-card">
              <h4>{{ formTahsin.id ? '✏️ Ubah Evaluasi Tahsin' : 'Input Evaluasi Tahsin Baru' }}</h4>
              <form @submit.prevent="handleSaveTahsin" class="input-form">
                
                <!-- 1. Makharijul Huruf Section -->
                <div class="form-group-section border-violet">
                  <h5 class="section-title-sub text-violet">🗣️ Makharijul Huruf (Rata-rata: {{ formTahsin.makharijul_huruf }})</h5>
                  <div class="sub-fields-grid">
                    <div v-for="m in makharijList" :key="m.key" class="sub-field-row">
                      <span class="sub-lbl">{{ m.label }}</span>
                      <input type="number" v-model="formTahsin.detail_penilaian[m.key]" min="0" max="100" class="fi fi-small" @input="calcMakharij" />
                    </div>
                  </div>
                </div>

                <!-- 2. Sifatul Huruf Section -->
                <div class="form-group-section border-indigo">
                  <h5 class="section-title-sub text-indigo">💪 Sifatul Huruf (Rata-rata: {{ formTahsin.sifat_huruf }})</h5>
                  <div class="sub-fields-grid">
                    <div v-for="s in sifatList" :key="s.key" class="sub-field-row">
                      <span class="sub-lbl">{{ s.label }}</span>
                      <input type="number" v-model="formTahsin.detail_penilaian[s.key]" min="0" max="100" class="fi fi-small" @input="calcSifat" />
                    </div>
                  </div>
                </div>

                <!-- 3. Hukum Tajwid Section (SANGAT LENGKAP) -->
                <div class="form-group-section border-emerald">
                  <h5 class="section-title-sub text-emerald">📜 Hukum Tajwid (Rata-rata: {{ formTahsin.tajwid }})</h5>
                  <div class="sub-fields-grid scrollable-sub-fields">
                    <div v-for="t in tajwidList" :key="t.key" class="sub-field-row">
                      <span class="sub-lbl" :title="t.desc">{{ t.label }}</span>
                      <input type="number" v-model="formTahsin.detail_penilaian[t.key]" min="0" max="100" class="fi fi-small" @input="calcTajwid" />
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label>Tanggal Penilaian</label>
                  <input type="date" v-model="formTahsin.tanggal_penilaian" required class="fi" />
                </div>
                
                <div class="form-group">
                  <label>Catatan Perbaikan</label>
                  <textarea v-model="formTahsin.catatan" rows="2" placeholder="Catatan kesalahan khusus..." class="fi"></textarea>
                </div>
                
                <div class="form-actions-row">
                  <button v-if="formTahsin.id" type="button" @click="resetFormTahsin" class="btn-secondary">Batal</button>
                  <button type="submit" class="btn-primary" :disabled="savingTahsin">
                    {{ savingTahsin ? 'Menyimpan...' : 'Simpan Evaluasi' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Riwayat Nilai Tahsin -->
            <div class="card table-split-card">
              <h4>Riwayat Evaluasi Tahsin</h4>
              <div v-if="loadingTahsin" class="loading-state">
                <div class="spinner-small"></div>
              </div>
              <div v-else-if="tahsinHistory.length === 0" class="empty-state-small">
                <p>Belum ada riwayat penilaian.</p>
              </div>
              <div v-else class="table-responsive">
                <table class="custom-table">
                  <thead>
                    <tr>
                      <th>Tanggal</th>
                      <th>Rata-rata</th>
                      <th>Makharij</th>
                      <th>Sifat</th>
                      <th>Tajwid</th>
                      <th>Detil Sub-Aspek</th>
                      <th>Catatan</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="t in tahsinHistory" :key="t.id">
                      <td class="font-bold small-date">{{ formatDate(t.tanggal_penilaian) }}</td>
                      <td class="text-center font-bold text-gold">⭐ {{ calcAvgRow(t) }}</td>
                      <td class="text-center font-bold text-violet">{{ t.makharijul_huruf }}</td>
                      <td class="text-center font-bold text-indigo">{{ t.sifat_huruf }}</td>
                      <td class="text-center font-bold text-emerald">{{ t.tajwid }}</td>
                      <td class="small-hafalan">
                        <div class="detail-summary-block">
                          <span class="text-violet" :title="getMakharijSummary(t.detail_penilaian)" style="cursor: help;">🗣️ M: {{ getMakharijSummary(t.detail_penilaian) }}</span><br/>
                          <span class="text-indigo" :title="getSifatSummary(t.detail_penilaian)" style="cursor: help;">💪 S: {{ getSifatSummary(t.detail_penilaian) }}</span><br/>
                          <span class="text-emerald" :title="getTajwidSummary(t.detail_penilaian)" style="cursor: help; text-decoration: underline dotted;">📜 T: (Lihat 24 Hukum)</span>
                        </div>
                      </td>
                      <td class="text-muted small-catatan" :title="t.catatan">{{ t.catatan || '-' }}</td>
                      <td>
                        <div class="action-buttons-mini">
                          <button @click="editTahsin(t)" class="btn-edit-mini" title="Edit">✏️</button>
                          <button @click="deleteTahsin(t.id)" class="btn-delete-mini" title="Hapus">🗑️</button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ==================== TAB 3: TAHFIDZ (SETORAN HAFALAN) ==================== -->
          <div v-if="activeTab === 'tahfidz'" class="tab-content-split">
            <!-- Form Setoran Hafalan -->
            <div class="card form-split-card">
              <h4>{{ formTahfidz.id ? '✏️ Ubah Setoran Hafalan' : 'Input Setoran Hafalan Baru' }}</h4>
              <form @submit.prevent="handleSaveTahfidz" class="input-form">
                <div class="form-group-row">
                  <div class="form-group">
                    <label>Tipe Setoran</label>
                    <select v-model="formTahfidz.tipe_setoran" required class="fi">
                      <option value="Ziyadah">Ziyadah (Baru)</option>
                      <option value="Murajaah">Murajaah (Ulang)</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Juz (1 - 30)</label>
                    <input type="number" v-model="formTahfidz.juz" min="1" max="30" required class="fi" />
                  </div>
                </div>

                <div class="form-group-row">
                  <div class="form-group">
                    <label>Surah Mulai</label>
                    <select v-model="formTahfidz.surah_mulai" required class="fi">
                      <option v-for="s in surahs" :key="s.no" :value="s.no">{{ s.no }}. {{ s.name }}</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Ayat Mulai</label>
                    <input type="number" v-model="formTahfidz.ayat_mulai" min="1" required class="fi" />
                  </div>
                </div>

                <div class="form-group-row">
                  <div class="form-group">
                    <label>Surah Selesai</label>
                    <select v-model="formTahfidz.surah_selesai" required class="fi">
                      <option v-for="s in surahs" :key="s.no" :value="s.no">{{ s.no }}. {{ s.name }}</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Ayat Selesai</label>
                    <input type="number" v-model="formTahfidz.ayat_selesai" min="1" required class="fi" />
                  </div>
                </div>

                <div class="form-group-row">
                  <div class="form-group">
                    <label>Kelancaran / Predikat</label>
                    <select v-model="formTahfidz.predikat" required class="fi">
                      <option value="Lancar">Lancar</option>
                      <option value="Sedang">Sedang</option>
                      <option value="Kurang">Kurang</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Tanggal Setoran</label>
                    <input type="date" v-model="formTahfidz.tanggal_setor" required class="fi" />
                  </div>
                </div>

                <div class="form-group">
                  <label>Catatan / Kesalahan Hafalan</label>
                  <textarea v-model="formTahfidz.catatan" rows="2" placeholder="Tulis catatan setoran jika ada..." class="fi"></textarea>
                </div>

                <div class="form-actions-row">
                  <button v-if="formTahfidz.id" type="button" @click="resetFormTahfidz" class="btn-secondary">Batal</button>
                  <button type="submit" class="btn-primary" :disabled="savingTahfidz">
                    {{ savingTahfidz ? 'Menyimpan...' : 'Simpan Setoran' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Riwayat Setoran -->
            <div class="card table-split-card">
              <h4>Riwayat Setoran Hafalan</h4>
              <div v-if="loadingTahfidz" class="loading-state">
                <div class="spinner-small"></div>
              </div>
              <div v-else-if="tahfidzHistory.length === 0" class="empty-state-small">
                <p>Belum ada riwayat setoran.</p>
              </div>
              <div v-else class="table-responsive">
                <table class="custom-table">
                  <thead>
                    <tr>
                      <th>Tanggal</th>
                      <th>Tipe</th>
                      <th>Hafalan</th>
                      <th>Predikat</th>
                      <th>Catatan</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="t in tahfidzHistory" :key="t.id">
                      <td class="font-bold small-date">{{ formatDate(t.tanggal_setor) }}</td>
                      <td>
                        <span :class="['badge-type', t.tipe_setoran==='Ziyadah'?'type-ziyadah':'type-murajaah']">
                          {{ t.tipe_setoran }}
                        </span>
                      </td>
                      <td class="small-hafalan">
                        Juz {{ t.juz }} <br/>
                        <span class="text-primary font-bold">
                          {{ getSurahName(t.surah_mulai) }} ({{ t.ayat_mulai }}) - {{ getSurahName(t.surah_selesai) }} ({{ t.ayat_selesai }})
                        </span>
                      </td>
                      <td>
                        <span :class="['badge-predikat', 'predikat-' + t.predikat.toLowerCase()]">
                          {{ t.predikat }}
                        </span>
                      </td>
                      <td class="text-muted small-catatan" :title="t.catatan">{{ t.catatan || '-' }}</td>
                      <td>
                        <div class="action-buttons-mini">
                          <button @click="editTahfidz(t)" class="btn-edit-mini" title="Edit">✏️</button>
                          <button @click="deleteTahfidz(t.id)" class="btn-delete-mini" title="Hapus">🗑️</button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <!-- ==================== TAB 4: HAFALAN DOA-DOA ==================== -->
          <div v-if="activeTab === 'doa'" class="tab-content-split">
            <!-- Form Hafalan Doa -->
            <div class="card form-split-card">
              <h4>{{ formDoa.id ? '✏️ Ubah Setoran Doa' : '🤲 Input Setoran Doa Baru' }}</h4>
              <form @submit.prevent="handleSaveDoa" class="input-form">
                
                <div class="form-group">
                  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                    <label>Pilih Nama Doa</label>
                    <button type="button" @click="openMasterDoaModal" class="btn-manage-doa-list">⚙️ Kelola Daftar Doa</button>
                  </div>
                  <select v-model="selectedDoaOption" required class="fi">
                    <option v-for="d in standardDoaList" :key="d" :value="d">{{ d }}</option>
                    <option value="Lainnya">-- Doa Lainnya (Ketik Manual) --</option>
                  </select>
                </div>

                <!-- Input nama doa manual jika pilih "Lainnya" -->
                <div v-if="selectedDoaOption === 'Lainnya'" class="form-group">
                  <label>Tulis Nama Doa Kustom</label>
                  <input type="text" v-model="customDoaName" required placeholder="Misal: Doa Masuk Pasar, Doa Safar..." class="fi" />
                </div>

                <div class="form-group-row">
                  <div class="form-group">
                    <label>Status Hafalan</label>
                    <select v-model="formDoa.status" required class="fi">
                      <option value="Lancar / Hafal">🟢 Lancar / Hafal</option>
                      <option value="Hafal Sebagian">🟡 Hafal Sebagian</option>
                      <option value="Belum Hafal">🔴 Belum Hafal</option>
                    </select>
                  </div>
                  <div class="form-group">
                    <label>Tanggal Setoran</label>
                    <input type="date" v-model="formDoa.tanggal_setor" required class="fi" />
                  </div>
                </div>

                <div class="form-group">
                  <label>Catatan / Koreksi Doa</label>
                  <textarea v-model="formDoa.catatan" rows="3" placeholder="Misal: pelafalan makhraj huruf pada baris kedua kurang tepat..." class="fi"></textarea>
                </div>

                <div class="form-actions-row">
                  <button v-if="formDoa.id" type="button" @click="resetFormDoa" class="btn-secondary">Batal</button>
                  <button type="submit" class="btn-primary" :disabled="savingDoa">
                    {{ savingDoa ? 'Menyimpan...' : 'Simpan Setoran Doa' }}
                  </button>
                </div>

              </form>
            </div>

            <!-- Tabel Riwayat Doa -->
            <div class="card table-split-card">
              <h4>Riwayat Setoran Hafalan Doa</h4>
              <div v-if="loadingDoa" class="loading-state">
                <div class="spinner-small"></div>
              </div>
              <div v-else-if="doaHistory.length === 0" class="empty-state-small">
                <p>Belum ada riwayat setoran doa.</p>
              </div>
              <div v-else class="table-responsive">
                <table class="custom-table">
                  <thead>
                    <tr>
                      <th>Tanggal</th>
                      <th>Nama Doa</th>
                      <th>Status</th>
                      <th>Penguji</th>
                      <th>Catatan</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="d in doaHistory" :key="d.id">
                      <td class="font-bold small-date">{{ formatDate(d.tanggal_setor) }}</td>
                      <td class="font-bold text-indigo">{{ d.nama_doa }}</td>
                      <td>
                        <span :class="['badge-predikat', d.status==='Lancar / Hafal'?'predikat-lancar':(d.status==='Hafal Sebagian'?'predikat-sedang':'predikat-kurang')]">
                          {{ d.status }}
                        </span>
                      </td>
                      <td>{{ d.nama_penguji || 'Ustadz' }}</td>
                      <td class="text-muted small-catatan" :title="d.catatan">{{ d.catatan || '-' }}</td>
                      <td>
                        <div class="action-buttons-mini">
                          <button @click="editDoa(d)" class="btn-edit-mini" title="Edit">✏️</button>
                          <button @click="deleteDoa(d.id)" class="btn-delete-mini" title="Hapus">🗑️</button>
                        </div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

        </div>

        <!-- MODAL KELOLA DAFTAR DOA (MASTER DATA) -->
        <div v-if="showMasterDoaModal" class="modal-overlay">
          <div class="modal-card">
            <div class="modal-header">
              <h3>⚙️ Kelola Daftar Doa Standard</h3>
              <button type="button" @click="closeMasterDoaModal" class="btn-close-modal">✕</button>
            </div>
            
            <div class="modal-body">
              <!-- Form tambah / edit master doa -->
              <form @submit.prevent="handleSaveMasterDoa" class="master-doa-form">
                <div class="form-group">
                  <label>{{ formMasterDoa.id ? 'Edit Nama Doa' : 'Tambah Doa Baru ke Daftar' }}</label>
                  <div class="input-action-row">
                    <input type="text" v-model="formMasterDoa.nama_doa" required placeholder="Tulis nama doa..." class="fi" />
                    <button type="submit" class="btn-primary-small">
                      {{ formMasterDoa.id ? 'Simpan' : 'Tambah' }}
                    </button>
                    <button v-if="formMasterDoa.id" type="button" @click="resetFormMasterDoa" class="btn-secondary-small">
                      Batal
                    </button>
                  </div>
                </div>
              </form>

              <!-- List scrollable daftar doa standard -->
              <div class="master-doa-list-container">
                <div v-if="allMasterDoa.length === 0" class="empty-state-small">
                  Belum ada daftar doa.
                </div>
                <div v-else class="master-doa-scrollable">
                  <div v-for="d in allMasterDoa" :key="d.id" class="master-doa-item">
                    <span class="m-doa-name">{{ d.nama_doa }}</span>
                    <div class="m-doa-actions">
                      <button @click="editMasterDoa(d)" class="btn-edit-mini" title="Edit">✏️</button>
                      <button @click="deleteMasterDoa(d.id)" class="btn-delete-mini" title="Hapus">🗑️</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button type="button" @click="closeMasterDoaModal" class="btn-secondary">Selesai</button>
            </div>
          </div>
        </div>

      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

// Base API setup
const API_URL = (window.location.port === "5173" ? "http://127.0.0.1:8080" : window.location.origin + "/superapp/public") + '/api'
const getHeaders = () => ({
  headers: { Authorization: `Bearer ${localStorage.getItem('jwt_token')}` }
})

// === AUTOCOMPLETE SELECT2 STATE & LOGIC ===
const isDropdownOpen = ref(false)
const dropdownSearchQuery = ref('')
const dropdownRef = ref(null)
const searchInputRef = ref(null)

const toggleDropdown = () => {
  isDropdownOpen.value = !isDropdownOpen.value
  if (isDropdownOpen.value) {
    // Focus search input on next tick
    setTimeout(() => {
      if (searchInputRef.value) searchInputRef.value.focus()
    }, 50)
  }
}

const closeDropdown = () => {
  isDropdownOpen.value = false
}

// Click outside handler logic
const clickOutsideHandler = (e) => {
  if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
    closeDropdown()
  }
}

// Filter autocomplete list berdasarkan query pencarian di dropdown
const filteredDropdownSantri = computed(() => {
  const query = dropdownSearchQuery.value.trim().toLowerCase()
  if (!query) {
    return santriList.value.slice(0, 15) // Tampilkan 15 santri pertama jika kosong
  }
  return santriList.value.filter(s => {
    return s.nama_lengkap.toLowerCase().includes(query) || 
           (s.nis && s.nis.toLowerCase().includes(query))
  })
})

const selectSantriFromDropdown = (santri) => {
  selectSantri(santri)
  closeDropdown()
  dropdownSearchQuery.value = '' // reset search query
}

// === LIST SURAH AL-QUR'AN ===
const surahs = [
  { no: 1, name: "Al-Fatihah" }, { no: 2, name: "Al-Baqarah" }, { no: 3, name: "Ali 'Imran" },
  { no: 4, name: "An-Nisa'" }, { no: 5, name: "Al-Ma'idah" }, { no: 6, name: "Al-An'am" },
  { no: 7, name: "Al-A'raf" }, { no: 8, name: "Al-Anfal" }, { no: 9, name: "At-Taubah" },
  { no: 10, name: "Yunus" }, { no: 11, name: "Hud" }, { no: 12, name: "Yusuf" },
  { no: 13, name: "Ar-Ra'd" }, { no: 14, name: "Ibrahim" }, { no: 15, name: "Al-Hijr" },
  { no: 16, name: "An-Nahl" }, { no: 17, name: "Al-Isra'" }, { no: 18, name: "Al-Kahf" },
  { no: 19, name: "Maryam" }, { no: 20, name: "Ta Ha" }, { no: 21, name: "Al-Anbiya'" },
  { no: 22, name: "Al-Hajj" }, { no: 23, name: "Al-Mu'minun" }, { no: 24, name: "An-Nur" },
  { no: 25, name: "Al-Furqan" }, { no: 26, name: "Asy-Syu'ara'" }, { no: 27, name: "An-Naml" },
  { no: 28, name: "Al-Qashash" }, { no: 29, name: "Al-'Ankabut" }, { no: 30, name: "Ar-Rum" },
  { no: 31, name: "Luqman" }, { no: 32, name: "As-Sajdah" }, { no: 33, name: "Al-Ahzab" },
  { no: 34, name: "Saba'" }, { no: 35, name: "Fathir" }, { no: 36, name: "Ya Sin" },
  { no: 37, name: "Ash-Shaffat" }, { no: 38, name: "Shad" }, { no: 39, name: "Az-Zumar" },
  { no: 40, name: "Ghafir" }, { no: 41, name: "Fushshilat" }, { no: 42, name: "Asy-Syura" },
  { no: 43, name: "Az-Zukhruf" }, { no: 44, name: "Ad-Dukhan" }, { no: 45, name: "Al-Jatsiyah" },
  { no: 46, name: "Al-Ahqaf" }, { no: 47, name: "Muhammad" }, { no: 48, name: "Al-Fath" },
  { no: 49, name: "Al-Hujurat" }, { no: 50, name: "Qaf" }, { no: 51, name: "Adz-Dzariyat" },
  { no: 52, name: "Ath-Thur" }, { no: 53, name: "An-Najm" }, { no: 54, name: "Al-Qamar" },
  { no: 55, name: "Ar-Rahman" }, { no: 56, name: "Al-Waqi'ah" }, { no: 57, name: "Al-Hadid" },
  { no: 58, name: "Al-Mujadilah" }, { no: 59, name: "Al-Hasyr" }, { no: 60, name: "Al-Mumtahanah" },
  { no: 61, name: "Ash-Shaff" }, { no: 62, name: "Al-Jumu'ah" }, { no: 63, name: "Al-Munafiqun" },
  { no: 64, name: "At-Taghabun" }, { no: 65, name: "Ath-Thalaq" }, { no: 66, name: "At-Tahrim" },
  { no: 67, name: "Al-Mulk" }, { no: 68, name: "Al-Qalam" }, { no: 69, name: "Al-Haqqah" },
  { no: 70, name: "Al-Ma'arij" }, { no: 71, name: "Nuh" }, { no: 72, name: "Al-Jinn" },
  { no: 73, name: "Al-Muzzammil" }, { no: 74, name: "Al-Muddatstsir" }, { no: 75, name: "Al-Qiyamah" },
  { no: 76, name: "Al-Insan" }, { no: 77, name: "Al-Mursalat" }, { no: 78, name: "An-Naba'" },
  { no: 79, name: "An-Nazi'at" }, { no: 80, name: "'Abasa" }, { no: 81, name: "At-Takwir" },
  { no: 82, name: "Al-Infitar" }, { no: 83, name: "Al-Muthaffifin" }, { no: 84, name: "Al-Insyiqaq" },
  { no: 85, name: "Al-Buruj" }, { no: 86, name: "Ath-Thariq" }, { no: 87, name: "Al-A'la" },
  { no: 88, name: "Al-Ghasyiyah" }, { no: 89, name: "Al-Fajr" }, { no: 90, name: "Al-Balad" },
  { no: 91, name: "Asy-Syams" }, { no: 92, name: "Al-Lail" }, { no: 93, name: "Ad-Duha" },
  { no: 94, name: "Al-Insyirah" }, { no: 95, name: "At-Tin" }, { no: 96, name: "Al-'Alaq" },
  { no: 97, name: "Al-Qadr" }, { no: 98, name: "Al-Bayyinah" }, { no: 99, name: "Az-Zalzalah" },
  { no: 100, name: "Al-'Adiyat" }, { no: 101, name: "Al-Qari'ah" }, { no: 102, name: "At-Takatsur" },
  { no: 103, name: "Al-'Asr" }, { no: 104, name: "Al-Humazah" }, { no: 105, name: "Al-Fil" },
  { no: 106, name: "Quraisy" }, { no: 107, name: "Al-Ma'un" }, { no: 108, name: "Al-Kautsar" },
  { no: 109, name: "Al-Kafirun" }, { no: 110, name: "An-Nashr" }, { no: 111, name: "Al-Masad" },
  { no: 112, name: "Al-Ikhlas" }, { no: 113, name: "Al-Falaq" }, { no: 114, name: "An-Nas" }
]

// === LIST MASTER DOA (DIPENUHI DARI DATABASE SEBAGAI REF DINAIMS) ===
const standardDoaList = ref([])
const allMasterDoa = ref([])

// === STATE ===
const selectedKelas = ref('')
const santriList = ref([])
const kelasList = ref([])
const activeSantriId = ref(null)
const activeSantri = ref(null)
const activeTab = ref('overview')

// Loading states
const loadingStats = ref(false)
const loadingTahsin = ref(false)
const loadingTahfidz = ref(false)
const loadingDoa = ref(false)

const savingTahsin = ref(false)
const savingTahfidz = ref(false)
const savingDoa = ref(false)

// History & Stats Data
const stats = ref({ total_ziyadah: 0, total_murajaah: 0, total_juz: 0, total_doa: 0, juz_dihafal: [], doa_list: [], chart: {} })
const tahsinHistory = ref([])
const tahfidzHistory = ref([])
const doaHistory = ref([])

// Modal Master Doa States
const showMasterDoaModal = ref(false)
const formMasterDoa = ref({ id: null, nama_doa: '' })

// === SUB-ASPEK TAHSIN DETAIL ===
const makharijList = [
  { key: 'al_jauf', label: 'Al-Jauf (Rongga Mulut)' },
  { key: 'al_halq', label: 'Al-Halq (Tenggorokan)' },
  { key: 'al_lisan', label: 'Al-Lisan (Lidah)' },
  { key: 'asy_syafatain', label: 'Asy-Syafatain (Dua Bibir)' },
  { key: 'al_khaisyum', label: 'Al-Khaisyum (Hidung)' }
]

const sifatList = [
  { key: 'hams_jahr', label: 'Hams / Jahr (Nafas)' },
  { key: 'syiddah_rakhawah', label: 'Syiddah / Rakhawah (Suara)' },
  { key: 'istila_istifal', label: 'Isti\'la / Istifal (Lidah)' },
  { key: 'qalqalah', label: 'Qalqalah (Pantulan)' },
  { key: 'lainnya', label: 'Sifat Tunggal Lain (Safir, dll)' }
]

// Kriteria Tajwid yang sangat lengkap (24 kriteria)
const tajwidList = [
  { key: 'idzhar_halqi', label: 'Idzhar Halqi', desc: 'Membaca jelas Nun Sukun / Tanwin' },
  { key: 'idgham_bighunnah', label: 'Idgham Bighunnah', desc: 'Memasukkan suara dengan dengung' },
  { key: 'idgham_bilaghunnah', label: 'Idgham Bilaghunnah', desc: 'Memasukkan suara tanpa dengung' },
  { key: 'iqlab', label: 'Iqlab', desc: 'Mengubah Nun Sukun/Tanwin menjadi Mim' },
  { key: 'ikhfa_haqiqi', label: 'Ikhfa Haqiqi', desc: 'Menyamarkan suara Nun Sukun/Tanwin' },
  { key: 'ikhfa_syafawi', label: 'Ikhfa Syafawi', desc: 'Menyamarkan Mim Sukun bertemu Ba' },
  { key: 'idgham_mimi', label: 'Idgham Mimi', desc: 'Mim Sukun bertemu Mim' },
  { key: 'idzhar_syafawi', label: 'Idzhar Syafawi', desc: 'Mim Sukun bertemu selain Mim/Ba' },
  { key: 'ghunnah_musyaddadah', label: 'Ghunnah Musyaddadah', desc: 'Mim atau Nun bertasydid' },
  { key: 'al_syamsiyah', label: 'Alif Lam Syamsiyah', desc: 'Alif lam lebur ke huruf berikutnya' },
  { key: 'al_qamariyah', label: 'Alif Lam Qamariyah', desc: 'Alif lam dibaca jelas' },
  { key: 'mutamatsilain', label: 'Idgham Mutamatsilain', desc: 'Dua huruf sama makhraj & sifat' },
  { key: 'mutajanisain', label: 'Idgham Mutajanisain', desc: 'Dua huruf sama makhraj beda sifat' },
  { key: 'mutaqaribain', label: 'Idgham Mutaqaribain', desc: 'Dua huruf berdekatan makhraj & sifat' },
  { key: 'qalqalah_sugra', label: 'Qalqalah Sugra', desc: 'Pantulan ringan di tengah kata' },
  { key: 'qalqalah_kubra', label: 'Qalqalah Kubra', desc: 'Pantulan kuat di akhir kata' },
  { key: 'ra_tafkhim', label: 'Ra\' Tafkhim', desc: 'Ra\' dibaca tebal' },
  { key: 'ra_tarqiq', label: 'Ra\' Tarqiq', desc: 'Ra\' dibaca tipis' },
  { key: 'mad_thabii', label: 'Mad Thabi\'i', desc: 'Panjang 2 harakat' },
  { key: 'mad_wajib', label: 'Mad Wajib Muttasil', desc: 'Mad bertemu Hamzah satu kata' },
  { key: 'mad_jaiz', label: 'Mad Jaiz Munfasil', desc: 'Mad bertemu Hamzah beda kata' },
  { key: 'mad_arid', label: 'Mad \'Arid Lissukun', desc: 'Mad bertemu huruf hidup dibaca waqaf' },
  { key: 'mad_lazim_others', label: 'Mad Lazim & Mad Lain', desc: 'Mad Lazim, Badal, Lin, Iwad, dll' },
  { key: 'waqaf_ibtida', label: 'Waqaf & Ibtida\'', desc: 'Ketepatan berhenti & memulai bacaan' }
]

// Form Models & Custom Doa states
const userLogged = ref({})
const formTahsin = ref({
  id: null,
  makharijul_huruf: 80,
  sifat_huruf: 80,
  tajwid: 80,
  detail_penilaian: {
    al_jauf: 80, al_halq: 80, al_lisan: 80, asy_syafatain: 80, al_khaisyum: 80,
    hams_jahr: 80, syiddah_rakhawah: 80, istila_istifal: 80, qalqalah: 80, lainnya: 80,
    idzhar_halqi: 80, idgham_bighunnah: 80, idgham_bilaghunnah: 80, iqlab: 80, ikhfa_haqiqi: 80,
    ikhfa_syafawi: 80, idgham_mimi: 80, idzhar_syafawi: 80, ghunnah_musyaddadah: 80,
    al_syamsiyah: 80, al_qamariyah: 80, mutamatsilain: 80, mutajanisain: 80, mutaqaribain: 80,
    qalqalah_sugra: 80, qalqalah_kubra: 80, ra_tafkhim: 80, ra_tarqiq: 80,
    mad_thabii: 80, mad_wajib: 80, mad_jaiz: 80, mad_arid: 80, mad_lazim_others: 80, waqaf_ibtida: 80
  },
  catatan: '',
  tanggal_penilaian: new Date().toISOString().substring(0,10)
})
const formTahfidz = ref({ id: null, tipe_setoran: 'Ziyadah', juz: 30, surah_mulai: 78, ayat_mulai: 1, surah_selesai: 78, ayat_selesai: 5, predikat: 'Lancar', tanggal_setor: new Date().toISOString().substring(0,10), catatan: '' })

// Form Doa ref
const selectedDoaOption = ref('')
const customDoaName = ref('')
const formDoa = ref({
  id: null,
  status: 'Lancar / Hafal',
  tanggal_setor: new Date().toISOString().substring(0,10),
  catatan: ''
})

// === METHODS ===
const userInitial = (name) => {
  if (!name) return 'U'
  return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

const formatDate = (dateStr) => {
  if (!dateStr) return '-'
  const d = new Date(dateStr)
  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })
}

const calcAvgRow = (t) => {
  const avg = (t.makharijul_huruf + t.sifat_huruf + t.tajwid) / 3
  return round(avg, 1)
}

const round = (val, precision) => {
  const p = Math.pow(10, precision)
  return Math.round(val * p) / p
}

const hasMemorizedDoa = (doaName) => {
  if (!stats.value.doa_list) return false
  return stats.value.doa_list.some(d => d.nama_doa.toLowerCase() === doaName.toLowerCase() && d.status === 'Lancar / Hafal')
}

const loadSantri = async () => {
  try {
    const res = await axios.get(`${API_URL}/alquran/santri?kelas_id=${selectedKelas.value}`, getHeaders())
    if (res.data && res.data.data) {
      santriList.value = res.data.data.santri || []
      kelasList.value = res.data.data.kelas || []
    }
  } catch (err) {
    console.error('Error load santri:', err)
  }
}

const selectSantri = (santri) => {
  activeSantriId.value = santri.id
  activeSantri.value = santri
  // Reset forms
  resetFormTahsin()
  resetFormTahfidz()
  resetFormDoa()
  // Load data for selected santri
  loadSantriDetails()
}

const loadSantriDetails = () => {
  if (!activeSantriId.value) return
  loadStats()
  loadTahsinHistory()
  loadTahfidzHistory()
  loadDoaHistory()
}

const loadStats = async () => {
  loadingStats.value = true
  try {
    const res = await axios.get(`${API_URL}/alquran/stats/${activeSantriId.value}`, getHeaders())
    if (res.data && res.data.data) {
      stats.value = res.data.data
    }
  } catch (err) {
    console.error('Error load stats:', err)
  } finally {
    loadingStats.value = false
  }
}

const loadTahsinHistory = async () => {
  loadingTahsin.value = true
  try {
    const res = await axios.get(`${API_URL}/alquran/tahsin?santri_id=${activeSantriId.value}`, getHeaders())
    if (res.data && res.data.data) {
      tahsinHistory.value = res.data.data
    }
  } catch (err) {
    console.error('Error load tahsin:', err)
  } finally {
    loadingTahsin.value = false
  }
}

const loadTahfidzHistory = async () => {
  loadingTahfidz.value = true
  try {
    const res = await axios.get(`${API_URL}/alquran/tahfidz?santri_id=${activeSantriId.value}`, getHeaders())
    if (res.data && res.data.data) {
      tahfidzHistory.value = res.data.data
    }
  } catch (err) {
    console.error('Error load tahfidz:', err)
  } finally {
    loadingTahfidz.value = false
  }
}

const loadDoaHistory = async () => {
  loadingDoa.value = true
  try {
    const res = await axios.get(`${API_URL}/alquran/doa?santri_id=${activeSantriId.value}`, getHeaders())
    if (res.data && res.data.data) {
      doaHistory.value = res.data.data
    }
  } catch (err) {
    console.error('Error load doa:', err)
  } finally {
    loadingDoa.value = false
  }
}

// === MASTER DATA DOA METHODS ===
const loadMasterDoaList = async () => {
  try {
    const res = await axios.get(`${API_URL}/alquran/master-doa`, getHeaders())
    if (res.data && res.data.data) {
      allMasterDoa.value = res.data.data
      standardDoaList.value = res.data.data.map(d => d.nama_doa)
      
      // Pasang default select option jika ada daftar doa
      if (standardDoaList.value.length > 0 && !selectedDoaOption.value) {
        selectedDoaOption.value = standardDoaList.value[0]
      }
    }
  } catch (err) {
    console.error('Error load master doa:', err)
  }
}

const openMasterDoaModal = () => {
  resetFormMasterDoa()
  showMasterDoaModal.value = true
}

const closeMasterDoaModal = () => {
  showMasterDoaModal.value = false
}

const resetFormMasterDoa = () => {
  formMasterDoa.value = { id: null, nama_doa: '' }
}

const handleSaveMasterDoa = async () => {
  try {
    await axios.post(`${API_URL}/alquran/master-doa/save`, formMasterDoa.value, getHeaders())
    resetFormMasterDoa()
    await loadMasterDoaList()
    // Muat ulang stats agar peta visual sinkron dengan daftar standard doa yang baru
    if (activeSantriId.value) {
      loadStats()
    }
  } catch (err) {
    console.error(err)
    alert(err.response?.data?.message || 'Gagal menyimpan master doa.')
  }
}

const editMasterDoa = (d) => {
  formMasterDoa.value = {
    id: d.id,
    nama_doa: d.nama_doa
  }
}

const deleteMasterDoa = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus doa ini dari daftar standard?')) return
  try {
    await axios.delete(`${API_URL}/alquran/master-doa/delete/${id}`, getHeaders())
    resetFormMasterDoa()
    await loadMasterDoaList()
    if (activeSantriId.value) {
      loadStats()
    }
  } catch (err) {
    console.error(err)
    alert('Gagal menghapus master doa.')
  }
}

// === FORM TAHSIN METHODS ===
const resetFormTahsin = () => {
  formTahsin.value = {
    id: null,
    makharijul_huruf: 80,
    sifat_huruf: 80,
    tajwid: 80,
    detail_penilaian: {
      al_jauf: 80, al_halq: 80, al_lisan: 80, asy_syafatain: 80, al_khaisyum: 80,
      hams_jahr: 80, syiddah_rakhawah: 80, istila_istifal: 80, qalqalah: 80, lainnya: 80,
      idzhar_halqi: 80, idgham_bighunnah: 80, idgham_bilaghunnah: 80, iqlab: 80, ikhfa_haqiqi: 80,
      ikhfa_syafawi: 80, idgham_mimi: 80, idzhar_syafawi: 80, ghunnah_musyaddadah: 80,
      al_syamsiyah: 80, al_qamariyah: 80, mutamatsilain: 80, mutajanisain: 80, mutaqaribain: 80,
      qalqalah_sugra: 80, qalqalah_kubra: 80, ra_tafkhim: 80, ra_tarqiq: 80,
      mad_thabii: 80, mad_wajib: 80, mad_jaiz: 80, mad_arid: 80, mad_lazim_others: 80, waqaf_ibtida: 80
    },
    catatan: '',
    tanggal_penilaian: new Date().toISOString().substring(0,10)
  }
}

const calcMakharij = () => {
  const d = formTahsin.value.detail_penilaian
  const sum = parseInt(d.al_jauf || 0) + parseInt(d.al_halq || 0) + parseInt(d.al_lisan || 0) + parseInt(d.asy_syafatain || 0) + parseInt(d.al_khaisyum || 0)
  formTahsin.value.makharijul_huruf = Math.round(sum / 5)
}

const calcSifat = () => {
  const d = formTahsin.value.detail_penilaian
  const sum = parseInt(d.hams_jahr || 0) + parseInt(d.syiddah_rakhawah || 0) + parseInt(d.istila_istifal || 0) + parseInt(d.qalqalah || 0) + parseInt(d.lainnya || 0)
  formTahsin.value.sifat_huruf = Math.round(sum / 5)
}

// 24 Kriteria Tajwid
const calcTajwid = () => {
  const d = formTahsin.value.detail_penilaian
  const sum = parseInt(d.idzhar_halqi || 0) + parseInt(d.idgham_bighunnah || 0) + parseInt(d.idgham_bilaghunnah || 0) + 
              parseInt(d.iqlab || 0) + parseInt(d.ikhfa_haqiqi || 0) + parseInt(d.ikhfa_syafawi || 0) + 
              parseInt(d.idgham_mimi || 0) + parseInt(d.idzhar_syafawi || 0) + parseInt(d.ghunnah_musyaddadah || 0) + 
              parseInt(d.al_syamsiyah || 0) + parseInt(d.al_qamariyah || 0) + parseInt(d.mutamatsilain || 0) + 
              parseInt(d.mutajanisain || 0) + parseInt(d.mutaqaribain || 0) + parseInt(d.qalqalah_sugra || 0) + 
              parseInt(d.qalqalah_kubra || 0) + parseInt(d.ra_tafkhim || 0) + parseInt(d.ra_tarqiq || 0) + 
              parseInt(d.mad_thabii || 0) + parseInt(d.mad_wajib || 0) + parseInt(d.mad_jaiz || 0) + 
              parseInt(d.mad_arid || 0) + parseInt(d.mad_lazim_others || 0) + parseInt(d.waqaf_ibtida || 0)
  formTahsin.value.tajwid = Math.round(sum / 24)
}

const getDetailObj = (str) => {
  try {
    return str ? JSON.parse(str) : null
  } catch (e) {
    return null
  }
}

const getMakharijSummary = (str) => {
  const d = getDetailObj(str)
  if (!d) return '-'
  return `Juf:${d.al_jauf||0} Hlq:${d.al_halq||0} Lsn:${d.al_lisan||0} Bibr:${d.asy_syafatain||0} Hdg:${d.al_khaisyum||0}`
}

const getSifatSummary = (str) => {
  const d = getDetailObj(str)
  if (!d) return '-'
  return `Nfs:${d.hams_jahr||0} Sra:${d.syiddah_rakhawah||0} Ldh:${d.istila_istifal||0} Ptl:${d.qalqalah||0} Lny:${d.lainnya||0}`
}

const getTajwidSummary = (str) => {
  const d = getDetailObj(str)
  if (!d) return '-'
  return `=== DETAIL 24 HUKUM TAJWID ===
1. Nun Sukun & Tanwin
- Idzhar Halqi: ${d.idzhar_halqi||0}
- Idgham Bighunnah: ${d.idgham_bighunnah||0}
- Idgham Bilaghunnah: ${d.idgham_bilaghunnah||0}
- Iqlab: ${d.iqlab||0}
- Ikhfa Haqiqi: ${d.ikhfa_haqiqi||0}

2. Mim Sukun
- Ikhfa Syafawi: ${d.ikhfa_syafawi||0}
- Idgham Mimi: ${d.idgham_mimi||0}
- Idzhar Syafawi: ${d.idzhar_syafawi||0}

3. Sifat & Hukum Lain
- Ghunnah Musyaddadah: ${d.ghunnah_musyaddadah||0}
- Alif Lam Syamsiyah: ${d.al_syamsiyah||0}
- Alif Lam Qamariyah: ${d.al_qamariyah||0}
- Idgham Mutamatsilain: ${d.mutamatsilain||0}
- Idgham Mutajanisain: ${d.mutajanisain||0}
- Idgham Mutaqaribain: ${d.mutaqaribain||0}
- Qalqalah Sugra: ${d.qalqalah_sugra||0}
- Qalqalah Kubra: ${d.qalqalah_kubra||0}
- Ra' Tafkhim: ${d.ra_tafkhim||0}
- Ra' Tarqiq: ${d.ra_tarqiq||0}

4. Hukum Mad (Panjang)
- Mad Thabi'i: ${d.mad_thabii||0}
- Mad Wajib Muttasil: ${d.mad_wajib||0}
- Mad Jaiz Munfasil: ${d.mad_jaiz||0}
- Mad 'Arid Lissukun: ${d.mad_arid||0}
- Mad Lazim & Lain: ${d.mad_lazim_others||0}
- Waqaf & Ibtida': ${d.waqaf_ibtida||0}`
}

const handleSaveTahsin = async () => {
  savingTahsin.value = true
  try {
    const payload = {
      ...formTahsin.value,
      santri_id: activeSantriId.value,
      penguji_id: userLogged.value.id || 1
    }
    await axios.post(`${API_URL}/alquran/tahsin/save`, payload, getHeaders())
    resetFormTahsin()
    loadTahsinHistory()
    loadSantri()
  } catch (err) {
    console.error('Error save tahsin:', err)
    alert('Gagal menyimpan nilai Tahsin.')
  } finally {
    savingTahsin.value = false
  }
}

const editTahsin = (t) => {
  let details = {}
  try {
    details = t.detail_penilaian ? JSON.parse(t.detail_penilaian) : {}
  } catch (e) {
    details = {}
  }

  formTahsin.value = {
    id: t.id,
    makharijul_huruf: t.makharijul_huruf,
    sifat_huruf: t.sifat_huruf,
    tajwid: t.tajwid,
    detail_penilaian: {
      al_jauf: details.al_jauf || 80,
      al_halq: details.al_halq || 80,
      al_lisan: details.al_lisan || 80,
      asy_syafatain: details.asy_syafatain || 80,
      al_khaisyum: details.al_khaisyum || 80,
      hams_jahr: details.hams_jahr || 80,
      syiddah_rakhawah: details.syiddah_rakhawah || 80,
      istila_istifal: details.istila_istifal || 80,
      qalqalah: details.qalqalah || 80,
      lainnya: details.lainnya || 80,
      idzhar_halqi: details.idzhar_halqi || 80,
      idgham_bighunnah: details.idgham_bighunnah || 80,
      idgham_bilaghunnah: details.idgham_bilaghunnah || 80,
      iqlab: details.iqlab || 80,
      ikhfa_haqiqi: details.ikhfa_haqiqi || 80,
      ikhfa_syafawi: details.ikhfa_syafawi || 80,
      idgham_mimi: details.idgham_mimi || 80,
      idzhar_syafawi: details.idzhar_syafawi || 80,
      ghunnah_musyaddadah: details.ghunnah_musyaddadah || 80,
      al_syamsiyah: details.al_syamsiyah || 80,
      al_qamariyah: details.al_qamariyah || 80,
      mutamatsilain: details.mutamatsilain || 80,
      mutajanisain: details.mutajanisain || 80,
      mutaqaribain: details.mutaqaribain || 80,
      qalqalah_sugra: details.qalqalah_sugra || 80,
      qalqalah_kubra: details.qalqalah_kubra || 80,
      ra_tafkhim: details.ra_tafkhim || 80,
      ra_tarqiq: details.ra_tarqiq || 80,
      mad_thabii: details.mad_thabii || 80,
      mad_wajib: details.mad_wajib || 80,
      mad_jaiz: details.mad_jaiz || 80,
      mad_arid: details.mad_arid || 80,
      mad_lazim_others: details.mad_lazim_others || 80,
      waqaf_ibtida: details.waqaf_ibtida || 80
    },
    catatan: t.catatan || '',
    tanggal_penilaian: t.tanggal_penilaian
  }
}

const deleteTahsin = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus data evaluasi Tahsin ini?')) return
  try {
    await axios.delete(`${API_URL}/alquran/tahsin/delete/${id}`, getHeaders())
    loadTahsinHistory()
    loadSantri()
  } catch (err) {
    console.error(err)
    alert('Gagal menghapus nilai.')
  }
}

// === FORM TAHFIDZ METHODS ===
const resetFormTahfidz = () => {
  formTahfidz.value = {
    id: null,
    tipe_setoran: 'Ziyadah',
    juz: 30,
    surah_mulai: 78,
    ayat_mulai: 1,
    surah_selesai: 78,
    ayat_selesai: 5,
    predikat: 'Lancar',
    tanggal_setor: new Date().toISOString().substring(0,10),
    catatan: ''
  }
}

const handleSaveTahfidz = async () => {
  savingTahfidz.value = true
  try {
    const payload = {
      ...formTahfidz.value,
      santri_id: activeSantriId.value,
      penguji_id: userLogged.value.id || 1
    }
    await axios.post(`${API_URL}/alquran/tahfidz/save`, payload, getHeaders())
    resetFormTahfidz()
    loadTahfidzHistory()
    loadStats()
    loadSantri()
  } catch (err) {
    console.error(err)
    alert('Gagal menyimpan setoran hafalan.')
  } finally {
    savingTahfidz.value = false
  }
}

const editTahfidz = (t) => {
  formTahfidz.value = {
    id: t.id,
    tipe_setoran: t.tipe_setoran,
    juz: t.juz,
    surah_mulai: parseInt(t.surah_mulai),
    ayat_mulai: t.ayat_mulai,
    surah_selesai: parseInt(t.surah_selesai),
    ayat_selesai: t.ayat_selesai,
    predikat: t.predikat,
    tanggal_setor: t.tanggal_setor,
    catatan: t.catatan || ''
  }
}

const deleteTahfidz = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus data setoran Tahfidz ini?')) return
  try {
    await axios.delete(`${API_URL}/alquran/tahfidz/delete/${id}`, getHeaders())
    loadTahfidzHistory()
    loadStats()
    loadSantri()
  } catch (err) {
    console.error(err)
    alert('Gagal menghapus setoran.')
  }
}

// === FORM DOA METHODS ===
const resetFormDoa = () => {
  selectedDoaOption.value = standardDoaList.value.length > 0 ? standardDoaList.value[0] : ''
  customDoaName.value = ''
  formDoa.value = {
    id: null,
    status: 'Lancar / Hafal',
    tanggal_setor: new Date().toISOString().substring(0,10),
    catatan: ''
  }
}

const handleSaveDoa = async () => {
  savingDoa.value = true
  try {
    const doaName = selectedDoaOption.value === 'Lainnya' ? customDoaName.value : selectedDoaOption.value
    if (!doaName) {
      alert('Nama doa wajib diisi!')
      savingDoa.value = false
      return
    }

    const payload = {
      ...formDoa.value,
      nama_doa: doaName,
      santri_id: activeSantriId.value,
      penguji_id: userLogged.value.id || 1
    }

    await axios.post(`${API_URL}/alquran/doa/save`, payload, getHeaders())
    resetFormDoa()
    loadDoaHistory()
    loadStats()
    loadSantri()
  } catch (err) {
    console.error(err)
    alert('Gagal menyimpan setoran doa.')
  } finally {
    savingDoa.value = false
  }
}

const editDoa = (d) => {
  formDoa.value = {
    id: d.id,
    status: d.status,
    tanggal_setor: d.tanggal_setor,
    catatan: d.catatan || ''
  }

  // Jika nama doa terdaftar di standar doa, pilih langsung. Jika kustom, pilih 'Lainnya'.
  if (standardDoaList.value.includes(d.nama_doa)) {
    selectedDoaOption.value = d.nama_doa
  } else {
    selectedDoaOption.value = 'Lainnya'
    customDoaName.value = d.nama_doa
  }
}

const deleteDoa = async (id) => {
  if (!confirm('Apakah Anda yakin ingin menghapus data setoran doa ini?')) return
  try {
    await axios.delete(`${API_URL}/alquran/doa/delete/${id}`, getHeaders())
    loadDoaHistory()
    loadStats()
    loadSantri()
  } catch (err) {
    console.error(err)
    alert('Gagal menghapus setoran doa.')
  }
}

// Lifecycle hook
onMounted(() => {
  const userData = localStorage.getItem('user')
  if (userData) {
    userLogged.value = JSON.parse(userData)
  }
  loadSantri()
  loadMasterDoaList()
  
  // Daftarkan event listener untuk click outside select2 autocomplete
  window.addEventListener('click', clickOutsideHandler)
})
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
  border-bottom: 1px solid rgba(255, 255, 255, 0.06);
  background: rgba(26, 29, 46, 0.5);
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
  flex: 1;
}

/* CARDS */
.card {
  background: rgba(30, 41, 59, 0.4);
  backdrop-filter: blur(8px);
  -webkit-backdrop-filter: blur(8px);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 20px;
  padding: 24px;
  margin-bottom: 24px;
}

/* AUTOCOMPLETE SELECT2 BAR */
.selector-card {
  padding: 20px 24px;
}

.selector-row {
  display: grid;
  grid-template-columns: 1fr 280px;
  gap: 20px;
  align-items: end;
}

@media (max-width: 768px) {
  .selector-row {
    grid-template-columns: 1fr;
  }
}

.selector-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.selector-filter {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.select-label {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* Select2 Custom Autocomplete CSS */
.select2-container {
  position: relative;
  width: 100%;
}

.select2-selection {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 12px 16px;
  color: white;
  font-size: 14px;
  cursor: pointer;
  display: flex;
  justify-content: space-between;
  align-items: center;
  transition: all 0.3s ease;
}

.select2-selection:hover {
  border-color: #6366f1;
  background: rgba(15, 23, 42, 0.8);
}

.select2-placeholder {
  color: #64748b;
}

.select2-selected-text {
  font-weight: 600;
  color: #a5b4fc;
}

.select2-arrow {
  font-size: 10px;
  color: #64748b;
}

/* Floating Dropdown */
.select2-dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background: #0f172a;
  border: 1px solid rgba(99, 102, 241, 0.3);
  border-radius: 12px;
  margin-top: 8px;
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5);
  z-index: 1000;
  overflow: hidden;
  animation: fadeIn 0.2s ease-out;
}

.select2-search-box {
  padding: 10px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.select2-search-input {
  width: 100%;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  padding: 8px 12px;
  color: white;
  font-size: 13px;
  outline: none;
  font-family: inherit;
}

.select2-search-input:focus {
  border-color: #6366f1;
}

.select2-results {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 250px;
  overflow-y: auto;
}

.select2-result-item {
  padding: 10px 16px;
  cursor: pointer;
  transition: all 0.2s;
  border-bottom: 1px solid rgba(255, 255, 255, 0.02);
}

.select2-result-item:hover {
  background: rgba(99, 102, 241, 0.15);
}

.res-nama {
  font-size: 13px;
  font-weight: 600;
  color: white;
}

.res-meta {
  font-size: 11px;
  color: #64748b;
  margin-top: 2px;
}

.select2-no-results, .select2-loading {
  padding: 16px;
  text-align: center;
  color: #64748b;
  font-size: 13px;
}

.filter-select {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 12px;
  padding: 12px;
  color: white;
  font-size: 14px;
  outline: none;
  cursor: pointer;
  width: 100%;
}

.filter-select:hover {
  border-color: #6366f1;
}

/* WELCOME EMPTY CARD */
.empty-welcome-card {
  padding: 100px 24px;
  text-align: center;
}

.empty-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 16px;
}

.empty-icon {
  font-size: 48px;
}

.empty-state h3 {
  font-size: 20px;
  margin: 0;
  color: white;
}

.empty-state p {
  color: #64748b;
  max-width: 480px;
  margin: 0;
  font-size: 14px;
  line-height: 1.5;
}

/* ACTIVE DETAILS LAYOUT */
.active-detail-wrapper {
  display: flex;
  flex-direction: column;
  gap: 24px;
  animation: fadeIn 0.4s ease-out;
}

/* PROFILE CARD & TAB BAR */
.santri-profile-card {
  padding: 24px;
}

.profile-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 24px;
}

.avatar-circle {
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(135deg, #6366f1, #8b5cf6);
  color: white;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 22px;
}

.profile-meta h2 {
  font-size: 20px;
  font-weight: 700;
  margin: 0 0 6px 0;
  color: white;
}

.profile-meta p {
  margin: 0;
  font-size: 13px;
  color: #94a3b8;
}

.status-active {
  color: #10b981;
  font-weight: 600;
}

.tab-navbar {
  display: flex;
  gap: 12px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  padding-top: 20px;
}

.tab-link {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 13px;
  font-weight: 600;
  padding: 10px 20px;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.2s;
}

.tab-link:hover {
  color: white;
  background: rgba(255, 255, 255, 0.03);
}

.tab-link.active {
  color: white;
  background: rgba(99, 102, 241, 0.15);
  box-shadow: 0 0 0 1px rgba(99, 102, 241, 0.3);
}

/* TAB CONTENT MINI STATS */
.stats-overview-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  gap: 20px;
  margin-top: 16px;
  margin-bottom: 32px;
}

.stat-box-mini {
  padding: 20px;
  border-radius: 16px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.bg-violet { background: rgba(139, 92, 246, 0.15); border-color: rgba(139, 92, 246, 0.2); }
.bg-emerald { background: rgba(16, 185, 129, 0.15); border-color: rgba(16, 185, 129, 0.2); }
.bg-indigo { background: rgba(99, 102, 241, 0.15); border-color: rgba(99, 102, 241, 0.2); }
.bg-gold { background: rgba(251, 191, 36, 0.15); border-color: rgba(251, 191, 36, 0.2); }

.stat-val {
  font-size: 32px;
  font-weight: 700;
  color: white;
}

.stat-lbl {
  font-size: 12px;
  color: #94a3b8;
  margin-top: 6px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.section-title {
  font-size: 16px;
  font-weight: 600;
  margin: 32px 0 8px 0;
  color: white;
}

.section-subtitle {
  font-size: 13px;
  color: #64748b;
  margin: 0 0 16px 0;
}

/* JUZ GRID MAP */
.juz-grid {
  display: grid;
  grid-template-columns: repeat(15, 1fr);
  gap: 10px;
  margin-bottom: 32px;
}

@media (max-width: 768px) {
  .juz-grid {
    grid-template-columns: repeat(6, 1fr);
  }
}

.juz-box {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 10px;
  aspect-ratio: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  font-weight: 700;
  color: #475569;
  transition: all 0.3s;
}

.juz-box.memorized {
  background: rgba(16, 185, 129, 0.25);
  border-color: #10b981;
  color: #4ade80;
  box-shadow: 0 0 10px rgba(16, 185, 129, 0.25);
}

/* CHECKLIST DOA HARIAN */
.doa-checklist-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
  gap: 12px;
  margin-bottom: 24px;
}

.doa-check-item {
  background: rgba(15, 23, 42, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.04);
  border-radius: 12px;
  padding: 12px 16px;
  display: flex;
  align-items: center;
  gap: 12px;
  transition: all 0.3s;
}

.doa-check-item.memorized {
  background: rgba(16, 185, 129, 0.1);
  border-color: rgba(16, 185, 129, 0.25);
}

.check-icon {
  font-size: 14px;
}

.doa-name-txt {
  font-size: 12px;
  font-weight: 600;
  color: #cbd5e1;
}

.doa-check-item.memorized .doa-name-txt {
  color: #4ade80;
}

/* TAHSIN SUMMARY LAST */
.tahsin-summary-display {
  background: rgba(15, 23, 42, 0.4);
  border: 1px solid rgba(255, 255, 255, 0.05);
  border-radius: 16px;
  padding: 24px;
}

.tahsin-gauge-row {
  display: flex;
  justify-content: space-around;
  gap: 20px;
  margin-bottom: 16px;
}

.gauge-card {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.gauge-num {
  font-size: 26px;
  font-weight: 700;
}

.text-gold {
  color: #fbbf24;
}

.gauge-lbl {
  font-size: 12px;
  color: #64748b;
  margin-top: 4px;
}

/* SPLIT TAB LAYOUT (FORM LEFT, TABLE RIGHT) */
.tab-content-split {
  display: grid;
  grid-template-columns: 580px 1fr;
  gap: 24px;
  align-items: start;
}

@media (max-width: 1024px) {
  .tab-content-split {
    grid-template-columns: 1fr;
  }
}

.form-split-card h4, .table-split-card h4 {
  font-size: 16px;
  font-weight: 600;
  margin: 0 0 20px 0;
  color: white;
}

/* FORMS & INPUTS */
.input-form {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.form-group-section {
  background: rgba(15, 23, 42, 0.3);
  border-left: 4px solid rgba(255, 255, 255, 0.08);
  border-radius: 0 12px 12px 0;
  padding: 14px 16px;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.border-violet { border-left-color: #8b5cf6; }
.border-indigo { border-left-color: #6366f1; }
.border-emerald { border-left-color: #10b981; }

.section-title-sub {
  font-size: 13px;
  font-weight: 700;
  margin: 0;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.sub-fields-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px 16px;
}

@media (max-width: 600px) {
  .sub-fields-grid {
    grid-template-columns: 1fr;
  }
}

.scrollable-sub-fields {
  max-height: 250px;
  overflow-y: auto;
  padding-right: 6px;
}

.scrollable-sub-fields::-webkit-scrollbar {
  width: 4px;
}

.scrollable-sub-fields::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}

.sub-field-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.02);
  padding-bottom: 4px;
}

.sub-lbl {
  font-size: 11px;
  color: #94a3b8;
  font-weight: 500;
}

.fi-small {
  width: 60px;
  padding: 4px 6px;
  font-size: 12px;
  text-align: center;
  height: 28px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.form-group label {
  font-size: 12px;
  color: #94a3b8;
  font-weight: 600;
}

.fi {
  background: rgba(15, 23, 42, 0.6);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 10px;
  padding: 10px 14px;
  color: white;
  font-family: inherit;
  font-size: 13px;
  outline: none;
  transition: all 0.3s ease;
}

.fi:focus {
  border-color: #6366f1;
}

textarea.fi {
  resize: none;
}

/* BUTTONS */
.btn-primary {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
  border: none;
  padding: 12px;
  border-radius: 10px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.35);
}

.btn-secondary {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #cbd5e1;
  padding: 12px;
  border-radius: 10px;
  font-size: 13px;
  cursor: pointer;
}

.btn-secondary:hover {
  background: rgba(255, 255, 255, 0.1);
}

.form-actions-row {
  display: flex;
  gap: 12px;
  margin-top: 10px;
}

.form-actions-row button {
  flex: 1;
}

/* TABLE STYLING */
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
  padding: 12px 14px;
  color: #94a3b8;
  font-weight: 600;
  font-size: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
}

.custom-table td {
  padding: 14px;
  font-size: 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.03);
  color: #e2e8f0;
}

.font-bold {
  font-weight: 600;
}

.text-violet { color: #c084fc; }
.text-indigo { color: #818cf8; }
.text-emerald { color: #34d399; }

.small-date {
  font-size: 11px;
  color: #cbd5e1;
}

.small-catatan {
  font-size: 11px;
  max-width: 150px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.small-hafalan {
  font-size: 11px;
  line-height: 1.4;
}

/* DETAIL TEXT MONOSPACE */
.detail-summary-block {
  font-size: 10px;
  line-height: 1.5;
  font-family: 'Courier New', Courier, monospace;
  background: rgba(15, 23, 42, 0.5);
  border: 1px solid rgba(255, 255, 255, 0.03);
  border-radius: 8px;
  padding: 8px 12px;
  max-width: 250px;
  overflow-x: auto;
}

.detail-summary-block span {
  display: inline-block;
  white-space: nowrap;
}

/* BADGES FOR TAHFIDZ & STATUS */
.badge-type {
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 6px;
  font-weight: 600;
}

.type-ziyadah {
  background-color: rgba(16, 185, 129, 0.15);
  color: #34d399;
  border: 1px solid rgba(16, 185, 129, 0.2);
}

.type-murajaah {
  background-color: rgba(99, 102, 241, 0.15);
  color: #818cf8;
  border: 1px solid rgba(99, 102, 241, 0.2);
}

.badge-predikat {
  font-size: 10px;
  padding: 2px 6px;
  border-radius: 6px;
  font-weight: 600;
}

.predikat-lancar {
  background-color: rgba(52, 211, 153, 0.15);
  color: #34d399;
}

.predikat-sedang {
  background-color: rgba(251, 191, 36, 0.15);
  color: #fbbf24;
}

.predikat-kurang {
  background-color: rgba(248, 113, 113, 0.15);
  color: #f87171;
}

/* MINI ACTION BUTTONS */
.action-buttons-mini {
  display: flex;
  gap: 8px;
}

.btn-edit-mini, .btn-delete-mini {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 2px;
  font-size: 11px;
  transition: transform 0.2s;
}

.btn-edit-mini:hover, .btn-delete-mini:hover {
  transform: scale(1.25);
}

/* LOADING & STATES */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 60px;
  color: #64748b;
  gap: 12px;
}

.empty-state-small {
  text-align: center;
  padding: 30px;
  color: #64748b;
  font-size: 13px;
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
  width: 24px;
  height: 24px;
  border: 2px solid rgba(255, 255, 255, 0.2);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* MODAL STYLING */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(11, 15, 25, 0.8);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
  animation: fadeIn 0.3s ease-out;
}

.modal-card {
  background: #1e293b;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 20px;
  width: 90%;
  max-width: 500px;
  max-height: 85vh;
  display: flex;
  flex-direction: column;
  box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);
  overflow: hidden;
}

.modal-header {
  padding: 20px 24px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.05);
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-header h3 {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
  color: white;
}

.btn-close-modal {
  background: transparent;
  border: none;
  color: #64748b;
  font-size: 16px;
  cursor: pointer;
  transition: color 0.2s;
}

.btn-close-modal:hover {
  color: white;
}

.modal-body {
  padding: 20px 24px;
  overflow-y: auto;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.modal-footer {
  padding: 16px 24px;
  border-top: 1px solid rgba(255, 255, 255, 0.05);
  display: flex;
  justify-content: flex-end;
}

/* Master Doa List Form & Scroll area */
.input-action-row {
  display: flex;
  gap: 8px;
}

.input-action-row .fi {
  flex: 1;
}

.btn-primary-small {
  background: linear-gradient(135deg, #6366f1, #4f46e5);
  color: white;
  border: none;
  padding: 0 16px;
  border-radius: 10px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s;
}

.btn-primary-small:hover {
  box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
}

.btn-secondary-small {
  background: rgba(255, 255, 255, 0.05);
  border: 1px solid rgba(255, 255, 255, 0.08);
  color: #cbd5e1;
  padding: 0 12px;
  border-radius: 10px;
  font-size: 12px;
  cursor: pointer;
}

.btn-secondary-small:hover {
  background: rgba(255, 255, 255, 0.1);
}

.master-doa-list-container {
  border: 1px solid rgba(255, 255, 255, 0.05);
  background: rgba(15, 23, 42, 0.3);
  border-radius: 12px;
  padding: 8px;
}

.master-doa-scrollable {
  max-height: 250px;
  overflow-y: auto;
}

.master-doa-scrollable::-webkit-scrollbar {
  width: 4px;
}

.master-doa-scrollable::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 2px;
}

.master-doa-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 12px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.02);
  transition: background 0.2s;
}

.master-doa-item:hover {
  background: rgba(255, 255, 255, 0.02);
}

.m-doa-name {
  font-size: 13px;
  color: #e2e8f0;
}

.m-doa-actions {
  display: flex;
  gap: 8px;
}

.btn-manage-doa-list {
  background: transparent;
  border: none;
  color: #a5b4fc;
  font-size: 11px;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
  text-decoration: underline dotted;
}

.btn-manage-doa-list:hover {
  color: white;
}
</style>
