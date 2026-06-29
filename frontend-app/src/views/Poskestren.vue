<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Pos Kesehatan Pesantren (Poskestren)</h1>
          <p>Layanan klinik kesehatan santri, rekam medis, dan manajemen logistik obat</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- ==================== TAB 1: DASHBOARD & PASIEN AKTIF ==================== -->
      <section v-if="activeTab==='dashboard'" class="content-body">
        <!-- Stats Grid -->
        <div class="stats-grid">
          <div class="stat-card">
            <span class="stat-icon purple">🏥</span>
            <div><span class="stat-val">{{ stats.total_kunjungan || 0 }}</span><span class="stat-lbl">Total Kunjungan</span></div>
          </div>
          <div class="stat-card">
            <span class="stat-icon blue">📅</span>
            <div><span class="stat-val">{{ stats.kunjungan_hari_ini || 0 }}</span><span class="stat-lbl">Kunjungan Hari Ini</span></div>
          </div>
          <div class="stat-card">
            <span class="stat-icon red">🤒</span>
            <div><span class="stat-val">{{ stats.santri_sakit || 0 }}</span><span class="stat-lbl">Santri Sedang Sakit</span></div>
          </div>
          <div class="stat-card">
            <span class="stat-icon orange">💊</span>
            <div><span class="stat-val">{{ stats.stok_obat_low || 0 }}</span><span class="stat-lbl">Stok Obat Menipis (&lt;10)</span></div>
          </div>
        </div>

        <div class="dashboard-grid">
          <!-- Santri Sakit Saat Ini (Observasi/Sakit) -->
          <div class="card grid-main">
            <div class="card-header">
              <h3>🤒 Santri Sedang Sakit / Observasi</h3>
            </div>
            <div class="table-wrapper">
              <table class="data-table">
                <thead><tr><th>Santri</th><th>NIS</th><th>Kelas</th><th>Keluhan / Diagnosa</th><th>Tgl Masuk</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                  <tr v-if="santriSakitList.length===0"><td colspan="7" class="empty-cell">Tidak ada santri yang sedang dirawat / sakit saat ini. 👍</td></tr>
                  <tr v-for="s in santriSakitList" :key="s.id">
                    <td class="name-cell">{{ s.nama_santri }}</td>
                    <td><code>{{ s.nis }}</code></td>
                    <td>{{ s.nama_kelas || '-' }}</td>
                    <td>
                      <div><strong>Keluhan:</strong> {{ s.keluhan }}</div>
                      <div class="text-muted" v-if="s.diagnosa"><strong>Diagnosa:</strong> {{ s.diagnosa }}</div>
                    </td>
                    <td>{{ formatDate(s.tgl_kunjungan) }}</td>
                    <td><span :class="['badge', s.status==='Sakit'?'badge-danger':'badge-warning']">{{ s.status || 'Sakit' }}</span></td>
                    <td>
                      <button @click="openPerkembangan(s)" class="btn-primary" style="padding: 5px 10px; font-size: 11px">Update Kondisi</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Kunjungan Terakhir -->
          <div class="card grid-side">
            <div class="card-header"><h3>⏱️ 5 Kunjungan Terakhir</h3></div>
            <div class="recent-list">
              <div v-if="recentKunjungan.length===0" class="empty-cell" style="padding: 20px">Belum ada riwayat kunjungan</div>
              <div v-for="k in recentKunjungan" :key="k.id" class="recent-item">
                <div class="ri-head">
                  <span class="ri-name">{{ k.nama_santri }}</span>
                  <span :class="['badge', k.status==='Sembuh'?'badge-success':(k.status==='Rujuk'?'badge-danger':'badge-warning')]">{{ k.status }}</span>
                </div>
                <div class="ri-body">
                  <div class="ri-text"><strong>Keluhan:</strong> {{ k.keluhan }}</div>
                  <div class="ri-text" v-if="k.diagnosa"><strong>Diagnosa:</strong> {{ k.diagnosa }}</div>
                  <div class="ri-time">{{ formatDate(k.tgl_kunjungan) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== TAB 2: REKAM MEDIS KUNJUNGAN ==================== -->
      <section v-if="activeTab==='kunjungan'" class="content-body">
        <div class="card">
          <div class="card-header" style="display:flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h3>Riwayat Rekam Medis Kunjungan Santri</h3>
            <div style="display:flex; align-items:center; gap:10px">
              <select v-model="kunjunganLimit" class="fi" style="width: 100px; height: 38px; padding: 4px 8px; font-size: 13px; margin-bottom: 0; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; outline: none;">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
                <option :value="100">100 baris</option>
              </select>
              <input v-model="searchKunjungan" @input="debounceKunjunganSearch" placeholder="Cari nama santri..." class="search-input" />
              <button @click="openAddKunjungan" class="btn-primary">+ Catat Kunjungan Baru</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>Tgl Kunjungan</th><th>Nama Santri</th><th>Kelas</th><th>Keluhan</th><th>Diagnosa</th><th>Tindakan / Terapi</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat data rekam medis...</td></tr>
                <tr v-else-if="kunjunganList.length===0"><td colspan="8" class="empty-cell">Belum ada riwayat kunjungan medis terdaftar</td></tr>
                <tr v-for="k in kunjunganList" :key="k.id">
                  <td>{{ formatDate(k.tgl_kunjungan) }}</td>
                  <td class="name-cell">{{ k.nama_santri }}</td>
                  <td>{{ k.nama_kelas || '-' }}</td>
                  <td>{{ truncateText(k.keluhan) }}</td>
                  <td>{{ k.diagnosa || '-' }}</td>
                  <td>{{ truncateText(k.tindakan) || '-' }}</td>
                  <td>
                    <span :class="['badge', k.status==='Sembuh'?'badge-success':(k.status==='Rujuk'?'badge-danger':'badge-warning')]">
                      {{ k.status || 'Sakit' }}
                    </span>
                  </td>
                  <td>
                    <div class="action-group">
                      <button @click="viewDetailKunjungan(k.id)" class="ab ab-blue" title="Detail Medis">👁️</button>
                      <button @click="deleteKunjungan(k.id)" class="ab ab-del" title="Hapus">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div v-if="kunjunganTotalPages > 1" class="p20" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.06); flex-wrap: wrap; gap: 12px; padding: 20px;">
            <div class="text-muted" style="font-size: 12px;">
              Menampilkan <strong>{{ (kunjunganPage - 1) * kunjunganLimit + 1 }}</strong> - 
              <strong>{{ Math.min(kunjunganPage * kunjunganLimit, kunjunganTotal) }}</strong> dari 
              <strong>{{ kunjunganTotal }}</strong> rekam medis
            </div>
            
            <div style="display: flex; gap: 6px; align-items: center;">
              <button @click="changeKunjunganPage(1)" :disabled="kunjunganPage === 1" class="tab-btn" style="padding: 6px 10px;">« First</button>
              <button @click="changeKunjunganPage(kunjunganPage - 1)" :disabled="kunjunganPage === 1" class="tab-btn" style="padding: 6px 12px;">‹ Prev</button>
              
              <!-- Page numbers -->
              <button v-for="p in kunjunganPaginationRange" :key="p" @click="changeKunjunganPage(p)" :class="['tab-btn', kunjunganPage === p ? 'active-indigo' : '']" style="padding: 6px 12px; font-weight: 500;">
                {{ p }}
              </button>
              
              <button @click="changeKunjunganPage(kunjunganPage + 1)" :disabled="kunjunganPage === kunjunganTotalPages" class="tab-btn" style="padding: 6px 12px;">Next ›</button>
              <button @click="changeKunjunganPage(kunjunganTotalPages)" :disabled="kunjunganPage === kunjunganTotalPages" class="tab-btn" style="padding: 6px 10px;">Last »</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== TAB 3: MASTER DATA OBAT ==================== -->
      <section v-if="activeTab==='obat'" class="content-body">
        <div class="card">
          <div class="card-header" style="display:flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h3>Daftar Persediaan &amp; Master Obat</h3>
            <div style="display:flex; align-items:center; gap:10px">
              <select v-model="obatLimit" class="fi" style="width: 100px; height: 38px; padding: 4px 8px; font-size: 13px; margin-bottom: 0; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; outline: none;">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
                <option :value="100">100 baris</option>
              </select>
              <input v-model="searchObat" @input="debounceObatSearch" placeholder="Cari nama obat..." class="search-input" />
              <button @click="openAddObat" class="btn-primary">+ Daftarkan Obat Baru</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>Nama Obat</th><th>Satuan Takar</th><th>Jumlah Stok</th><th>Status Stok</th><th>Deskripsi</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="6" class="loading-cell">Memuat data obat...</td></tr>
                <tr v-else-if="obatList.length===0"><td colspan="6" class="empty-cell">Belum ada data obat terdaftar</td></tr>
                <tr v-for="o in obatList" :key="o.id">
                  <td class="name-cell">{{ o.nama_obat }}</td>
                  <td><code>{{ o.satuan }}</code></td>
                  <td><strong>{{ o.stok }}</strong></td>
                  <td>
                    <span v-if="o.stok <= 0" class="badge badge-danger">Habis</span>
                    <span v-else-if="o.stok < 10" class="badge badge-warning">Menipis</span>
                    <span v-else class="badge badge-success">Tersedia</span>
                  </td>
                  <td>{{ o.deskripsi || '-' }}</td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditObat(o)" class="ab ab-blue">✏️</button>
                      <button @click="deleteObat(o.id, o.nama_obat)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div v-if="obatTotalPages > 1" class="p20" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.06); flex-wrap: wrap; gap: 12px; padding: 20px;">
            <div class="text-muted" style="font-size: 12px;">
              Menampilkan <strong>{{ (obatPage - 1) * obatLimit + 1 }}</strong> - 
              <strong>{{ Math.min(obatPage * obatLimit, obatTotal) }}</strong> dari 
              <strong>{{ obatTotal }}</strong> obat
            </div>
            
            <div style="display: flex; gap: 6px; align-items: center;">
              <button @click="changeObatPage(1)" :disabled="obatPage === 1" class="tab-btn" style="padding: 6px 10px;">« First</button>
              <button @click="changeObatPage(obatPage - 1)" :disabled="obatPage === 1" class="tab-btn" style="padding: 6px 12px;">‹ Prev</button>
              
              <!-- Page numbers -->
              <button v-for="p in obatPaginationRange" :key="p" @click="changeObatPage(p)" :class="['tab-btn', obatPage === p ? 'active-indigo' : '']" style="padding: 6px 12px; font-weight: 500;">
                {{ p }}
              </button>
              
              <button @click="changeObatPage(obatPage + 1)" :disabled="obatPage === obatTotalPages" class="tab-btn" style="padding: 6px 12px;">Next ›</button>
              <button @click="changeObatPage(obatTotalPages)" :disabled="obatPage === obatTotalPages" class="tab-btn" style="padding: 6px 10px;">Last »</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ==================== TAB 4: MUTASI & STOK ==================== -->
      <section v-if="activeTab==='stok'" class="content-body">
        <div class="mutasi-grid">
          <!-- Form Mutasi (Masuk & Keluar) -->
          <div class="card">
            <div class="card-header">
              <h3>📦 Pencatatan Transaksi Stok Obat</h3>
            </div>
            <div class="p20" style="display: flex; flex-direction: column; gap: 16px">
              <div class="fg">
                <label>Jenis Transaksi Stok</label>
                <select v-model="formStok.tipe" class="fi" @change="resetStokForm">
                  <option value="masuk">Stok Masuk (Pengadaan / Hibah)</option>
                  <option value="keluar">Stok Keluar (Pemusnahan / Kadaluarsa / Manual)</option>
                </select>
              </div>

              <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px">
                <div class="fg">
                  <label>Tanggal Transaksi</label>
                  <input type="datetime-local" v-model="formStok.tanggal" class="fi" />
                </div>
                <div class="fg" v-if="formStok.tipe==='keluar'">
                  <label>Alasan Stok Keluar</label>
                  <select v-model="formStok.jenis" class="fi">
                    <option value="musnah">Kadaluarsa / Rusak (Pemusnahan)</option>
                    <option value="konsumsi">Keperluan Operasional Lain</option>
                  </select>
                </div>
              </div>

              <!-- List Obat Transaksi -->
              <div class="fg">
                <label>Daftar Obat &amp; Kuantitas</label>
                <div v-for="(item, idx) in formStok.items" :key="idx" class="obat-row" style="margin-bottom: 8px">
                  <select v-model="item.obat_id" class="fi" style="flex: 2">
                    <option value="">-- Pilih Obat --</option>
                    <option v-for="o in obatList" :key="o.id" :value="o.id">{{ o.nama_obat }} (Stok: {{ o.stok }} {{ o.satuan }})</option>
                  </select>
                  <input type="number" v-model.number="item.jumlah" class="fi" style="flex: 1" placeholder="Jumlah" min="1" />
                  <button @click="removeStokItem(idx)" class="btn-secondary" style="padding: 0 10px; border-color: rgba(248,113,113,0.3); color: #f87171">✕</button>
                </div>
                <button @click="addStokItem" class="btn-secondary" style="font-size: 11px; align-self: flex-start; margin-top: 4px">+ Tambah Baris Obat</button>
              </div>

              <div class="fg">
                <label>Keterangan / Catatan Mutasi</label>
                <input v-model="formStok.keterangan" class="fi" placeholder="Contoh: Pengadaan rutin bulanan APB, Obat kadaluarsa Paracetamol, dll." />
              </div>

              <div style="text-align: right; margin-top: 8px">
                <button @click="saveMutasiStok" class="btn-primary" :disabled="saving">Simpan Mutasi Stok</button>
              </div>
            </div>
          </div>

          <!-- Kartu Riwayat Mutasi -->
          <div class="card">
            <div class="card-header">
              <h3>📜 Riwayat Kartu Stok</h3>
              <div style="display: flex; gap: 8px; align-items: center">
                <select v-model="filterObatId" class="fi" style="width: 200px" @change="fetchRiwayatStok">
                  <option value="">-- Semua Obat --</option>
                  <option v-for="o in obatList" :key="o.id" :value="o.id">{{ o.nama_obat }}</option>
                </select>
              </div>
            </div>
            <div class="table-wrapper" style="max-height: 500px">
              <table class="data-table">
                <thead><tr><th>Tgl Mutasi</th><th>Nama Obat</th><th>Tipe</th><th>Jumlah</th><th>Stok Awal</th><th>Stok Akhir</th><th>Keterangan</th></tr></thead>
                <tbody>
                  <tr v-if="riwayatStok.length===0"><td colspan="7" class="empty-cell">Belum ada riwayat mutasi stok untuk obat ini.</td></tr>
                  <tr v-for="m in riwayatStok" :key="m.id">
                    <td>{{ formatDateTime(m.created_at) }}</td>
                    <td class="name-cell">{{ m.nama_obat }}</td>
                    <td>
                      <span :class="['badge', m.tipe==='masuk'?'badge-success':'badge-danger']">{{ m.tipe }} ({{ m.jenis }})</span>
                    </td>
                    <td><strong>{{ m.jumlah }}</strong></td>
                    <td>{{ m.stok_sebelum }}</td>
                    <td><strong>{{ m.stok_sesudah }}</strong></td>
                    <td style="font-size: 11px">{{ m.keterangan || '-' }}</td>
                  </tr>
                </tbody>
              </table>
            </div>
            <!-- Paginasi Riwayat Stok -->
            <div v-if="stokTotalPages > 1" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.06); flex-wrap: wrap; gap: 12px; padding: 16px 20px;">
              <div style="display: flex; align-items: center; gap: 10px;">
                <select v-model="stokLimit" @change="fetchRiwayatStok(1)" class="fi" style="width: 100px; height: 36px; padding: 4px 8px; font-size: 12px;">
                  <option :value="10">10 baris</option>
                  <option :value="25">25 baris</option>
                  <option :value="50">50 baris</option>
                </select>
                <span class="text-muted" style="font-size: 12px;">
                  Menampilkan <strong>{{ (stokPage - 1) * stokLimit + 1 }}</strong> – 
                  <strong>{{ Math.min(stokPage * stokLimit, stokTotal) }}</strong> dari 
                  <strong>{{ stokTotal }}</strong> mutasi
                </span>
              </div>
              <div style="display: flex; gap: 5px; align-items: center;">
                <button @click="changeStokPage(1)" :disabled="stokPage === 1" class="tab-btn" style="padding: 5px 9px;">«</button>
                <button @click="changeStokPage(stokPage - 1)" :disabled="stokPage === 1" class="tab-btn" style="padding: 5px 10px;">‹</button>
                <button v-for="p in stokPaginationRange" :key="p" @click="changeStokPage(p)" :class="['tab-btn', stokPage === p ? 'active-indigo' : '']" style="padding: 5px 10px;">{{ p }}</button>
                <button @click="changeStokPage(stokPage + 1)" :disabled="stokPage === stokTotalPages" class="tab-btn" style="padding: 5px 10px;">›</button>
                <button @click="changeStokPage(stokTotalPages)" :disabled="stokPage === stokTotalPages" class="tab-btn" style="padding: 5px 9px;">»</button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- ===== MODAL: FORM KUNJUNGAN BARU ===== -->
    <div v-if="showKunjunganForm" class="modal-overlay" @click.self="showKunjunganForm=false">
      <div class="modal-box" style="width: 640px">
        <div class="modal-head"><h3>🏥 Catat Rekam Medis Kunjungan Baru</h3><button @click="showKunjunganForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full">
            <label>Pilih Santri *</label>
            <select v-model="formKunjungan.nisn" class="fi">
              <option value="">-- Pilih Santri --</option>
              <option v-for="s in santriList" :key="s.nisn" :value="s.nisn">{{ s.nama_lengkap }} (NIS: {{ s.nis }})</option>
            </select>
          </div>
          <div class="fg">
            <label>Tanggal &amp; Waktu Kunjungan *</label>
            <input type="datetime-local" v-model="formKunjungan.tgl_kunjungan" class="fi" />
          </div>
          <div class="fg">
            <label>Status Pasien Awal</label>
            <select v-model="formKunjungan.status" class="fi">
              <option value="Sakit">Sakit (Rawat Jalan)</option>
              <option value="Observasi">Observasi (Rawat Inap Poskestren)</option>
              <option value="Sembuh">Langsung Sembuh</option>
              <option value="Rujuk">Dirujuk ke RS / Pulang</option>
            </select>
          </div>
          <div class="fg full"><label>Keluhan Utama Santri *</label><textarea v-model="formKunjungan.keluhan" class="fi" rows="2" placeholder="Sebutkan keluhan utama pasien..."></textarea></div>
          <div class="fg"><label>Diagnosa Medis (Sakit)</label><input v-model="formKunjungan.diagnosa" class="fi" placeholder="Contoh: Demam, Flu, Dispepsia, dll." /></div>
          <div class="fg"><label>Tindakan / Terapi Fisik</label><input v-model="formKunjungan.tindakan" class="fi" placeholder="Contoh: Istirahat, Kompres hangat, dll." /></div>
          
          <!-- Pemberian Obat -->
          <div class="fg full">
            <label>Pemberian Obat (Resep Klinik)</label>
            <div v-for="(item, idx) in formKunjungan.obat_items" :key="idx" class="obat-row" style="margin-bottom: 8px">
              <select v-model="item.obat_id" class="fi" style="flex: 2">
                <option value="">-- Pilih Obat --</option>
                <option v-for="o in obatAktifList" :key="o.id" :value="o.id">{{ o.nama_obat }} (Tersedia: {{ o.stok }} {{ o.satuan }})</option>
              </select>
              <input type="number" v-model.number="item.jumlah" class="fi" style="flex: 1" placeholder="Qty" min="1" />
              <input v-model="item.dosis" class="fi" style="flex: 1.5" placeholder="Dosis (e.g. 3x1)" />
              <button @click="removePrescriptionItem(idx)" class="btn-secondary" style="padding: 0 10px; border-color: rgba(248,113,113,0.3); color: #f87171">✕</button>
            </div>
            <button @click="addPrescriptionItem" class="btn-secondary" style="font-size: 11px; align-self: flex-start; margin-top: 4px">+ Tambah Obat</button>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showKunjunganForm=false" class="btn-secondary">Batal</button>
          <button @click="saveKunjungan" class="btn-primary" :disabled="saving">Catat Kunjungan</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: UPDATE PERKEMBANGAN PASIEN ===== -->
    <div v-if="showPerkembanganForm" class="modal-overlay" @click.self="showPerkembanganForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>🤒 Update Perkembangan Pasien: {{ activePatientName }}</h3><button @click="showPerkembanganForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full">
            <label>Kondisi / Status Terbaru</label>
            <select v-model="formPerkembangan.status" class="fi">
              <option value="Sakit">Tetap Sakit (Rawat Jalan)</option>
              <option value="Observasi">Observasi (Rawat Inap Poskestren)</option>
              <option value="Sembuh">Sembuh &amp; Kembali ke Asrama</option>
              <option value="Rujuk">Dirujuk ke Puskesmas / Rumah Sakit / Pulang</option>
            </select>
          </div>
          <div class="fg full"><label>Perubahan Diagnosa (Opsional)</label><input v-model="formPerkembangan.diagnosa" class="fi" placeholder="Masukkan jika ada perubahan diagnosa medis" /></div>
          <div class="fg full"><label>Tindakan Baru / Catatan Harian</label><textarea v-model="formPerkembangan.tindakan" class="fi" rows="2" placeholder="Sebutkan tindakan baru atau catatan perkembangan pasien harian..."></textarea></div>

          <!-- Obat Tambahan -->
          <div class="fg full">
            <label>Berikan Obat Tambahan (Jika Diperlukan)</label>
            <div v-for="(item, idx) in formPerkembangan.obat_items" :key="idx" class="obat-row" style="margin-bottom: 8px">
              <select v-model="item.obat_id" class="fi" style="flex: 2">
                <option value="">-- Pilih Obat --</option>
                <option v-for="o in obatAktifList" :key="o.id" :value="o.id">{{ o.nama_obat }} (Tersedia: {{ o.stok }} {{ o.satuan }})</option>
              </select>
              <input type="number" v-model.number="item.jumlah" class="fi" style="flex: 1" placeholder="Qty" min="1" />
              <input v-model="item.dosis" class="fi" style="flex: 1.5" placeholder="Dosis (e.g. 3x1)" />
              <button @click="removePerkembanganObat(idx)" class="btn-secondary" style="padding: 0 10px; border-color: rgba(248,113,113,0.3); color: #f87171">✕</button>
            </div>
            <button @click="addPerkembanganObat" class="btn-secondary" style="font-size: 11px; align-self: flex-start; margin-top: 4px">+ Tambah Obat</button>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showPerkembanganForm=false" class="btn-secondary">Batal</button>
          <button @click="savePerkembangan" class="btn-primary" :disabled="saving">Update Pasien</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: DETAIL REKAM MEDIS LENGKAP ===== -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal=false">
      <div class="modal-box" style="width: 580px">
        <div class="modal-head"><h3>📄 Detail Rekam Medis Santri</h3><button @click="showDetailModal=false" class="modal-close">✕</button></div>
        <div class="p20" style="display: flex; flex-direction: column; gap: 16px; font-size: 13px" v-if="detailMedis.kunjungan">
          <div class="detail-row"><span>Nama Santri:</span><strong>{{ detailMedis.kunjungan.nama_santri }}</strong></div>
          <div class="detail-row"><span>NIS / Kelas:</span><span><code>{{ detailMedis.kunjungan.nis }}</code> / {{ detailMedis.kunjungan.nama_kelas || '-' }}</span></div>
          <div class="detail-row"><span>Tgl Kunjungan:</span><span>{{ formatDateTime(detailMedis.kunjungan.tgl_kunjungan) }}</span></div>
          <div class="detail-row"><span>Kondisi Akhir:</span><span :class="['badge', detailMedis.kunjungan.status==='Sembuh'?'badge-success':(detailMedis.kunjungan.status==='Rujuk'?'badge-danger':'badge-warning')]">{{ detailMedis.kunjungan.status }}</span></div>
          
          <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 14px">
            <h4 style="margin-bottom: 6px; color: #a78bfa; font-size: 14px">🤒 Keluhan Utama:</h4>
            <p style="background: rgba(255,255,255,0.02); padding: 10px; border-radius: 8px; white-space: pre-line">{{ detailMedis.kunjungan.keluhan }}</p>
          </div>

          <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 14px" v-if="detailMedis.kunjungan.diagnosa">
            <h4 style="margin-bottom: 6px; color: #a78bfa; font-size: 14px">📋 Diagnosa Medis:</h4>
            <p style="background: rgba(255,255,255,0.02); padding: 10px; border-radius: 8px">{{ detailMedis.kunjungan.diagnosa }}</p>
          </div>

          <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 14px" v-if="detailMedis.kunjungan.tindakan">
            <h4 style="margin-bottom: 6px; color: #a78bfa; font-size: 14px">📜 Tindakan Medis &amp; Terapi Perkembangan:</h4>
            <p style="background: rgba(255,255,255,0.02); padding: 10px; border-radius: 8px; white-space: pre-line">{{ detailMedis.kunjungan.tindakan }}</p>
          </div>

          <div style="border-top: 1px solid rgba(255,255,255,0.06); padding-top: 14px">
            <h4 style="margin-bottom: 6px; color: #34d399; font-size: 14px">💊 Obat-Obatan yang Diberikan:</h4>
            <div v-if="detailMedis.obat.length===0" class="text-muted" style="padding: 6px">Tidak ada pemberian obat.</div>
            <table v-else class="data-table" style="background: rgba(255,255,255,0.01); border-radius: 8px">
              <thead><tr><th>Nama Obat</th><th>Jumlah</th><th>Aturan Pakai / Dosis</th></tr></thead>
              <tbody>
                <tr v-for="o in detailMedis.obat" :key="o.id">
                  <td>{{ o.nama_obat }}</td>
                  <td><strong>{{ o.jumlah }} {{ o.satuan }}</strong></td>
                  <td><code>{{ o.dosis || '-' }}</code></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showDetailModal=false" class="btn-primary-purple">Tutup Rekam Medis</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: FORM OBAT BARU / EDIT ===== -->
    <div v-if="showObatForm" class="modal-overlay" @click.self="showObatForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>💊 {{ formObat.id ? 'Edit Data Obat' : 'Daftarkan Obat Baru' }}</h3><button @click="showObatForm=false" class="modal-close">✕</button></div>
        <div class="p20" style="display: flex; flex-direction: column; gap: 16px">
          <div class="fg"><label>Nama Obat *</label><input v-model="formObat.nama_obat" class="fi" placeholder="Contoh: Paracetamol 500mg, Amoxicillin, dll." /></div>
          <div class="form-grid" style="grid-template-columns: 1fr 1fr; gap: 16px">
            <div class="fg"><label>Satuan Takar *</label><input v-model="formObat.satuan" class="fi" placeholder="Misal: Tablet, Sirup, Box, Botol" /></div>
            <div class="fg" v-if="!formObat.id"><label>Stok Awal (Opsional)</label><input type="number" v-model.number="formObat.stok_awal" class="fi" min="0" /></div>
          </div>
          <div class="fg"><label>Deskripsi / Aturan Penyimpanan</label><textarea v-model="formObat.deskripsi" class="fi" rows="2" placeholder="Catatan kegunaan atau dosis maksimal obat..."></textarea></div>
        </div>
        <div class="modal-actions">
          <button @click="showObatForm=false" class="btn-secondary">Batal</button>
          <button @click="saveObat" class="btn-primary" :disabled="saving">Simpan Obat</button>
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
import { ref, onMounted, computed, watch } from 'vue'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const API    = 'http://127.0.0.1:8080/api/poskestren'
const token  = localStorage.getItem('jwt_token')
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab          = ref('dashboard')
const loading            = ref(false)
const saving             = ref(false)
const toast              = ref({ show: false, message: '', type: 'success' })

// Dashboard
const stats              = ref({ total_kunjungan: 0, kunjungan_hari_ini: 0, stok_obat_low: 0, santri_sakit: 0 })
const recentKunjungan    = ref([])
const santriSakitList    = ref([])

// Data lists
const kunjunganList      = ref([])
const obatList           = ref([])
const riwayatStok        = ref([])
const santriList         = ref([])
const obatAktifList      = ref([])

const kunjunganPage = ref(1)
const kunjunganLimit = ref(10)
const kunjunganTotal = ref(0)
const kunjunganTotalPages = ref(0)
const searchKunjungan = ref('')

const obatPage = ref(1)
const obatLimit = ref(10)
const obatTotal = ref(0)
const obatTotalPages = ref(0)
const searchObat = ref('')

const stokPage = ref(1)
const stokLimit = ref(10)
const stokTotal = ref(0)
const stokTotalPages = ref(0)

// Form states
const filterObatId       = ref('')
const showKunjunganForm  = ref(false)
const showPerkembanganForm = ref(false)
const showDetailModal    = ref(false)
const showObatForm       = ref(false)

const formKunjungan      = ref({ nisn: '', tgl_kunjungan: '', keluhan: '', diagnosa: '', tindakan: '', status: 'Sakit', obat_items: [] })
const formPerkembangan   = ref({ id: '', status: 'Sakit', diagnosa: '', tindakan: '', obat_items: [] })
const detailMedis        = ref({ kunjungan: null, obat: [] })
const formObat           = ref({ id: '', nama_obat: '', satuan: '', stok_awal: 0, deskripsi: '' })
const formStok           = ref({ tipe: 'masuk', jenis: 'pengadaan', tanggal: '', keterangan: '', items: [] })

const activePatientName  = ref('')

const tabs = [
  { key: 'dashboard', label: 'Dashboard & Pasien', icon: '📊', color: 'blue' },
  { key: 'kunjungan', label: 'Rekam Medis Kunjungan', icon: '📋', color: 'purple' },
  { key: 'obat', label: 'Persediaan Obat', icon: '💊', color: 'green' },
  { key: 'stok', label: 'Mutasi & Pengadaan', icon: '📦', color: 'orange' },
]

// ===== HELPERS =====
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }
function truncateText(t, max=60) { if(!t) return ''; return t.length>max?t.substring(0,max)+'...':t; }
function formatDate(d) { if(!d) return '-'; const date = new Date(d); return date.toLocaleDateString('id-ID', {day:'numeric',month:'short',year:'numeric'}) }
function formatDateTime(d) { if(!d) return '-'; const date = new Date(d); return date.toLocaleString('id-ID', {day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'}) }

// ===== METHODS =====
async function switchTab(key) {
  activeTab.value = key
  if (key === 'dashboard') await fetchDashboard()
  if (key === 'kunjungan') await fetchKunjungan(1)
  if (key === 'obat') await fetchObat(1)
  if (key === 'stok') {
    await fetchObat(1)
    await fetchRiwayatStok(1)
    resetStokForm()
  }
}

// === DASHBOARD ===
async function fetchDashboard() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/dashboard`, { headers })
    stats.value = res.data.data.stats
    recentKunjungan.value = res.data.data.recent_kunjungan
    santriSakitList.value = res.data.data.santri_sakit_list
  } catch { showNotif('Gagal memuat statistik dashboard', 'error') }
  finally { loading.value = false }
}

// === KUNJUNGAN ===
async function fetchKunjungan(page = 1) {
  loading.value = true
  kunjunganPage.value = page
  try {
    const res = await axios.get(`${API}/kunjungan`, { 
      params: {
        page: kunjunganPage.value,
        limit: kunjunganLimit.value,
        q: searchKunjungan.value
      },
      headers 
    })
    kunjunganList.value = res.data.data || []
    if (res.data.pagination) {
      kunjunganTotal.value = res.data.pagination.total || 0
      kunjunganTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch { showNotif('Gagal memuat rekam medis kunjungan', 'error') }
  finally { loading.value = false }
}

async function openAddKunjungan() {
  formKunjungan.value = {
    nisn: '',
    tgl_kunjungan: new Date().toISOString().slice(0, 16),
    keluhan: '',
    diagnosa: '',
    tindakan: '',
    status: 'Sakit',
    obat_items: []
  }
  // Load santri and obat aktif
  try {
    const [resS, resO] = await Promise.all([
      axios.get(`${API}/santri`, { headers }),
      axios.get(`${API}/obat-aktif`, { headers })
    ])
    santriList.value = resS.data.data
    obatAktifList.value = resO.data.data
  } catch { showNotif('Gagal memuat data pendukung form', 'error') }
  showKunjunganForm.value = true
}

function addPrescriptionItem() {
  formKunjungan.value.obat_items.push({ obat_id: '', jumlah: 1, dosis: '' })
}

function removePrescriptionItem(idx) {
  formKunjungan.value.obat_items.splice(idx, 1)
}

async function saveKunjungan() {
  const f = formKunjungan.value
  if (!f.nisn || !f.tgl_kunjungan || !f.keluhan) {
    return showNotif('Harap lengkapi semua kolom bertanda bintang (*)', 'error')
  }
  saving.value = true
  try {
    await axios.post(`${API}/kunjungan/save`, f, { headers })
    showKunjunganForm.value = false
    await fetchKunjungan(1)
    showNotif('Rekam medis kunjungan baru berhasil dicatat!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan kunjungan', 'error')
  } finally { saving.value = false }
}

async function viewDetailKunjungan(id) {
  try {
    const res = await axios.get(`${API}/kunjungan/detail/${id}`, { headers })
    detailMedis.value = res.data.data
    showDetailModal.value = true
  } catch { showNotif('Gagal memuat detail rekam medis', 'error') }
}

async function deleteKunjungan(id) {
  if (!confirm('Hapus rekam medis kunjungan ini?')) return
  try {
    await axios.delete(`${API}/kunjungan/delete/${id}`, { headers })
    await fetchKunjungan(kunjunganPage.value)
    showNotif('Rekam medis berhasil dihapus!')
  } catch { showNotif('Gagal menghapus rekam medis', 'error') }
}

// === PERKEMBANGAN PASIEN ===
async function openPerkembangan(s) {
  activePatientName.value = s.nama_santri
  formPerkembangan.value = {
    id: s.id,
    status: s.status || 'Sakit',
    diagnosa: s.diagnosa || '',
    tindakan: '',
    obat_items: []
  }
  try {
    const resO = await axios.get(`${API}/obat-aktif`, { headers })
    obatAktifList.value = resO.data.data
  } catch { showNotif('Gagal memuat data obat aktif', 'error') }
  showPerkembanganForm.value = true
}

function addPerkembanganObat() {
  formPerkembangan.value.obat_items.push({ obat_id: '', jumlah: 1, dosis: '' })
}

function removePerkembanganObat(idx) {
  formPerkembangan.value.obat_items.splice(idx, 1)
}

async function savePerkembangan() {
  const f = formPerkembangan.value
  saving.value = true
  try {
    await axios.post(`${API}/kunjungan/update-perkembangan/${f.id}`, f, { headers })
    showPerkembanganForm.value = false
    await fetchDashboard()
    showNotif('Kondisi perkembangan pasien berhasil diperbarui!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan perkembangan', 'error')
  } finally { saving.value = false }
}

// === MASTER OBAT ===
async function fetchObat(page = 1) {
  loading.value = true
  obatPage.value = page
  try {
    const res = await axios.get(`${API}/obat`, { 
      params: {
        page: obatPage.value,
        limit: obatLimit.value,
        q: searchObat.value
      },
      headers 
    })
    obatList.value = res.data.data || []
    if (res.data.pagination) {
      obatTotal.value = res.data.pagination.total || 0
      obatTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch { showNotif('Gagal memuat data persediaan obat', 'error') }
  finally { loading.value = false }
}

function openAddObat() {
  formObat.value = { id: '', nama_obat: '', satuan: '', stok_awal: 0, deskripsi: '' }
  showObatForm.value = true
}

function openEditObat(o) {
  formObat.value = { ...o }
  showObatForm.value = true
}

async function saveObat() {
  const o = formObat.value
  if (!o.nama_obat || !o.satuan) return showNotif('Nama obat dan satuan takar wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/obat/save`, o, { headers })
    showObatForm.value = false
    await fetchObat(1)
    showNotif('Persediaan obat berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan data obat', 'error') }
  finally { saving.value = false }
}

async function deleteObat(id, nama) {
  if (!confirm(`Hapus obat "${nama}" dari sistem?`)) return
  try {
    await axios.delete(`${API}/obat/delete/${id}`, { headers })
    await fetchObat(obatPage.value)
    showNotif('Obat berhasil dihapus!')
  } catch { showNotif('Gagal menghapus obat', 'error') }
}

// === MUTASI & STOK ===
async function fetchRiwayatStok(page = 1) {
  stokPage.value = page
  try {
    const res = await axios.get(`${API}/stok/riwayat`, {
      params: { 
        page: stokPage.value,
        limit: stokLimit.value,
        obat_id: filterObatId.value 
      },
      headers
    })
    riwayatStok.value = res.data.data || []
    if (res.data.pagination) {
      stokTotal.value = res.data.pagination.total || 0
      stokTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch { showNotif('Gagal memuat kartu riwayat stok', 'error') }
}

watch(kunjunganLimit, () => {
  kunjunganPage.value = 1
  fetchKunjungan(1)
})
watch(obatLimit, () => {
  obatPage.value = 1
  fetchObat(1)
})
watch(stokLimit, () => {
  stokPage.value = 1
  fetchRiwayatStok(1)
})

let kunjunganSearchTimeout = null
function debounceKunjunganSearch() {
  if (kunjunganSearchTimeout) clearTimeout(kunjunganSearchTimeout)
  kunjunganSearchTimeout = setTimeout(() => {
    fetchKunjungan(1)
  }, 400)
}

let obatSearchTimeout = null
function debounceObatSearch() {
  if (obatSearchTimeout) clearTimeout(obatSearchTimeout)
  obatSearchTimeout = setTimeout(() => {
    fetchObat(1)
  }, 400)
}

function changeKunjunganPage(page) {
  if (page < 1 || page > kunjunganTotalPages.value) return
  kunjunganPage.value = page
  fetchKunjungan(page)
}

function changeObatPage(page) {
  if (page < 1 || page > obatTotalPages.value) return
  obatPage.value = page
  fetchObat(page)
}

function changeStokPage(page) {
  if (page < 1 || page > stokTotalPages.value) return
  stokPage.value = page
  fetchRiwayatStok(page)
}

const kunjunganPaginationRange = computed(() => {
  const current = kunjunganPage.value
  const total = kunjunganTotalPages.value
  const delta = 2
  const range = []
  let start = Math.max(1, current - delta)
  let end = Math.min(total, current + delta)
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  return range
})

const obatPaginationRange = computed(() => {
  const current = obatPage.value
  const total = obatTotalPages.value
  const delta = 2
  const range = []
  let start = Math.max(1, current - delta)
  let end = Math.min(total, current + delta)
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  return range
})

const stokPaginationRange = computed(() => {
  const current = stokPage.value
  const total = stokTotalPages.value
  const delta = 2
  const range = []
  let start = Math.max(1, current - delta)
  let end = Math.min(total, current + delta)
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  return range
})

function resetStokForm() {
  formStok.value = {
    tipe: formStok.value.tipe,
    jenis: formStok.value.tipe === 'masuk' ? 'pengadaan' : 'musnah',
    tanggal: new Date().toISOString().slice(0, 16),
    keterangan: '',
    items: [{ obat_id: '', jumlah: 1 }]
  }
}

function addStokItem() {
  formStok.value.items.push({ obat_id: '', jumlah: 1 })
}

function removeStokItem(idx) {
  formStok.value.items.splice(idx, 1)
}

async function saveMutasiStok() {
  const f = formStok.value
  const emptyItem = f.items.find(i => !i.obat_id || i.jumlah <= 0)
  if (emptyItem) return showNotif('Pilih obat dan tentukan jumlah mutasi yang valid (&gt; 0)', 'error')
  
  saving.value = true
  const endpoint = f.tipe === 'masuk' ? 'pengadaan' : 'keluar'
  try {
    await axios.post(`${API}/stok/${endpoint}`, f, { headers })
    resetStokForm()
    await Promise.all([
      fetchObat(1),
      fetchRiwayatStok(1)
    ])
    showNotif('Mutasi stok obat berhasil dicatat!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan mutasi stok', 'error')
  } finally { saving.value = false }
}

// === INIT ===
onMounted(fetchDashboard)
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
.active-orange { background: rgba(251,146,60,0.15); color: #fb923c; border-color: rgba(251,146,60,0.3); font-weight: 600; }
.active-indigo { background: rgba(99,102,241,0.2); color: #818cf8; border-color: rgba(99,102,241,0.4); font-weight: 700; }
.tab-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.search-input { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 8px 12px; color: #e2e8f0; font-size: 12px; outline: none; width: 180px; font-family: 'Inter', sans-serif; }
.search-input:focus { border-color: rgba(99,102,241,0.5); }
.text-muted { color: #64748b; }

.content-body { padding: 24px 32px; display: flex; flex-direction: column; gap: 24px; }

/* Stats Grid */
.stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; }
.stat-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 18px 24px; display: flex; align-items: center; gap: 18px; }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.stat-icon.purple { background: rgba(124,58,237,0.15); }
.stat-icon.blue { background: rgba(59,130,246,0.15); }
.stat-icon.red { background: rgba(239,68,68,0.15); }
.stat-icon.orange { background: rgba(245,158,11,0.15); }
.stat-val { display: block; font-size: 22px; font-weight: 700; color: #f8fafc; }
.stat-lbl { display: block; font-size: 12px; color: #64748b; margin-top: 2px; }

/* Dashboard Grids */
.dashboard-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 24px; align-items: start; }
.grid-main { margin: 0; }
.grid-side { margin: 0; }

/* Card */
.card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; overflow: hidden; }
.card-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 12px; }
.card-header h3 { font-size: 15px; font-weight: 600; }

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
.badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; text-transform: capitalize; }
.badge-success { background: rgba(16,185,129,0.15); color: #10b981; }
.badge-warning { background: rgba(245,158,11,0.15); color: #fb923c; }
.badge-danger { background: rgba(239,68,68,0.15); color: #f87171; }
.badge-info { background: rgba(99,102,241,0.15); color: #818cf8; }

/* Recent lists on dashboard */
.recent-list { display: flex; flex-direction: column; }
.recent-item { padding: 14px 20px; border-bottom: 1px solid rgba(255,255,255,0.04); }
.recent-item:last-child { border-bottom: none; }
.ri-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; }
.ri-name { font-weight: 600; font-size: 13px; color: #f1f5f9; }
.ri-body { display: flex; flex-direction: column; gap: 4px; }
.ri-text { font-size: 12px; color: #94a3b8; }
.ri-time { font-size: 10px; color: #64748b; margin-top: 4px; }

/* Mutasi layout grid */
.mutasi-grid { display: grid; grid-template-columns: 1fr 1.2fr; gap: 24px; align-items: start; }

/* Medicine form rows */
.obat-row { display: flex; gap: 8px; width: 100%; }

/* Buttons */
.btn-primary { padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; transition: opacity 0.2s; }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; font-size: 13px; cursor: pointer; }

/* Action buttons */
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

/* Details row in Detail Modal */
.detail-row { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.04); padding-bottom: 8px; }
.detail-row span { color: #94a3b8; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
