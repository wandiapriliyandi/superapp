<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Akademik</h1>
          <p>Manajemen data santri, kelas, mata pelajaran, jadwal, absensi, dan nilai akademik</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- ========== TAB 1: SANTRI ========== -->
      <section v-if="activeTab==='santri'">
        <div class="stats-grid">
          <div class="stat-card purple"><div class="stat-icon">👨‍🎓</div><div><div class="stat-num">{{ santriTotal }}</div><div class="stat-lbl">Total Santri</div></div></div>
          <div class="stat-card green"><div class="stat-icon">✅</div><div><div class="stat-num">{{ countSantriByStatus('Aktif') }}</div><div class="stat-lbl">Santri (Hal. Ini)</div></div></div>
          <div class="stat-card gold"><div class="stat-icon">🎓</div><div><div class="stat-num">{{ countSantriByStatus('Alumni') }}</div><div class="stat-lbl">Alumni (Hal. Ini)</div></div></div>
        </div>
        <div class="card">
          <div class="card-header" style="display:flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h3>Daftar Santri</h3>
            <div class="header-actions" style="display: flex; align-items: center; gap: 10px;">
              <select v-model="santriLimit" class="fi" style="width: 100px; height: 38px; padding: 4px 8px; font-size: 13px; margin-bottom: 0; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; outline: none;">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
                <option :value="100">100 baris</option>
              </select>
              <input v-model="searchSantri" @input="debounceSantriSearch" placeholder="Cari nama/NISN..." class="search-input" />
              <button @click="openAddSantri" class="btn-primary">+ Tambah Santri</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Lengkap</th><th>NISN</th><th>Jenis Kelamin</th><th>Tempat, Tgl Lahir</th><th>Tahun Masuk</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="filteredSantri.length===0"><td colspan="8" class="empty-cell">Tidak ada data santri</td></tr>
                <tr v-for="(s, i) in filteredSantri" :key="s.id">
                  <td>{{ (santriPage - 1) * santriLimit + i + 1 }}</td>
                  <td class="name-cell">{{ s.nama_lengkap }}</td>
                  <td><code>{{ s.nisn || '—' }}</code></td>
                  <td>{{ s.jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}</td>
                  <td>{{ s.tempat_lahir || '—' }}, {{ formatDate(s.tanggal_lahir) }}</td>
                  <td>{{ s.nama_tahun_ajaran || '—' }}</td>
                  <td><span :class="['badge', s.status_santri==='Aktif'?'badge-success':'badge-warning']">{{ s.status_santri }}</span></td>
                  <td>
                    <div class="action-group">
                      <button @click="viewSantriDetail(s)" class="ab ab-blue" title="Lihat Detail">👁️</button>
                      <button @click="printSantriCard(s)" class="ab ab-green" title="Cetak Kartu">🪪</button>
                      <button @click="openEditSantri(s)" class="ab ab-blue" title="Edit">✏️</button>
                      <button @click="deleteSantri(s.id, s.nama_lengkap)" class="ab ab-del" title="Hapus">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div v-if="santriTotalPages > 1" class="p20" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.06); flex-wrap: wrap; gap: 12px; padding: 20px;">
            <div class="text-muted" style="font-size: 12px;">
              Menampilkan <strong>{{ (santriPage - 1) * santriLimit + 1 }}</strong> - 
              <strong>{{ Math.min(santriPage * santriLimit, santriTotal) }}</strong> dari 
              <strong>{{ santriTotal }}</strong> santri
            </div>
            
            <div style="display: flex; gap: 6px; align-items: center;">
              <button @click="changeSantriPage(1)" :disabled="santriPage === 1" class="tab-btn" style="padding: 6px 10px;">« First</button>
              <button @click="changeSantriPage(santriPage - 1)" :disabled="santriPage === 1" class="tab-btn" style="padding: 6px 12px;">‹ Prev</button>
              
              <!-- Page numbers -->
              <button v-for="p in santriPaginationRange" :key="p" @click="changeSantriPage(p)" :class="['tab-btn', santriPage === p ? 'active-indigo' : '']" style="padding: 6px 12px; font-weight: 500;">
                {{ p }}
              </button>
              
              <button @click="changeSantriPage(santriPage + 1)" :disabled="santriPage === santriTotalPages" class="tab-btn" style="padding: 6px 12px;">Next ›</button>
              <button @click="changeSantriPage(santriTotalPages)" :disabled="santriPage === santriTotalPages" class="tab-btn" style="padding: 6px 10px;">Last »</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== TAB 2: KELAS ========== -->
      <section v-if="activeTab==='kelas'">
        <div class="card">
          <div class="card-header">
            <h3>Daftar Kelas</h3>
            <button @click="openAddKelas" class="btn-primary">+ Tambah Kelas</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Kelas</th><th>Tingkat</th><th>Wali Kelas</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="5" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="kelas.length===0"><td colspan="5" class="empty-cell">Belum ada data kelas</td></tr>
                <tr v-for="(k, i) in kelas" :key="k.id">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ k.nama_kelas }}</td>
                  <td><span class="badge badge-info">Tingkat {{ k.tingkat || '—' }}</span></td>
                  <td>{{ k.nama_wali_kelas || '—' }}</td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditKelas(k)" class="ab ab-blue">✏️</button>
                      <button @click="deleteKelas(k.id, k.nama_kelas)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 3: MAPEL ========== -->
      <section v-if="activeTab==='mapel'">
        <div class="card">
          <div class="card-header">
            <h3>Daftar Mata Pelajaran</h3>
            <button @click="openAddMapel" class="btn-primary">+ Tambah Mapel</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Kode Mapel</th><th>Nama Mapel</th><th>Kelompok</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="5" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="mapel.length===0"><td colspan="5" class="empty-cell">Belum ada mata pelajaran</td></tr>
                <tr v-for="(m, i) in mapel" :key="m.id">
                  <td>{{ i+1 }}</td>
                  <td><code>{{ m.kode_mapel }}</code></td>
                  <td class="name-cell">{{ m.nama_mapel }}</td>
                  <td><span class="badge badge-info">{{ m.kelompok || 'Umum' }}</span></td>
                  <td>
                    <div class="action-group">
                      <button @click="openEditMapel(m)" class="ab ab-blue">✏️</button>
                      <button @click="deleteMapel(m.id, m.nama_mapel)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 4: JADWAL ========== -->
      <section v-if="activeTab==='jadwal'">
        <div class="card">
          <div class="card-header">
            <h3>Jadwal Pelajaran</h3>
            <div class="header-actions">
              <select v-model="filterKelasJadwal" class="filter-select">
                <option value="">-- Pilih Kelas --</option>
                <option v-for="k in kelas" :key="k.id" :value="k.id">{{ k.nama_kelas }}</option>
              </select>
              <button @click="openAddJadwal" class="btn-primary">+ Tambah Jadwal</button>
            </div>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>Hari</th><th>Jam</th><th>Mata Pelajaran</th><th>Guru Pengampu</th><th>Ruangan</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="6" class="loading-cell">Memuat data...</td></tr>
                <tr v-else-if="jadwal.length===0"><td colspan="6" class="empty-cell">Pilih kelas atau belum ada jadwal pelajaran</td></tr>
                <tr v-for="j in sortedJadwal" :key="j.id">
                  <td><strong>{{ j.hari }}</strong></td>
                  <td><code>{{ j.jam_mulai?.slice(0,5) }} - {{ j.jam_selesai?.slice(0,5) }}</code></td>
                  <td class="name-cell">{{ j.nama_mapel }}</td>
                  <td>{{ j.nama_guru || '—' }}</td>
                  <td><span class="badge badge-info">{{ j.ruangan || '—' }}</span></td>
                  <td>
                    <button @click="deleteJadwal(j.id)" class="btn-danger-sm">🗑️</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 5: ABSENSI ========== -->
      <section v-if="activeTab==='presensi'">
        <div class="card">
          <div class="card-header">
            <h3>Input Absensi Santri</h3>
            <div class="header-actions">
              <select v-model="presensiParams.id_jadwal" class="filter-select">
                <option value="">-- Pilih Jadwal Pelajaran (Hari Ini) --</option>
                <option v-for="j in jadwalHariIni" :key="j.id" :value="j.id">
                  {{ j.nama_kelas }} | {{ j.nama_mapel }} ({{ j.jam_mulai?.slice(0,5) }})
                </option>
              </select>
              <input v-model="presensiParams.tanggal" type="date" class="search-input" />
              <button @click="fetchPresensi" class="btn-primary-purple" :disabled="!presensiParams.id_jadwal">Cari</button>
            </div>
          </div>

          <div v-if="presensiList.length > 0" class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Santri</th><th>NISN</th><th>Status Absensi</th><th>Catatan / Keterangan</th></tr></thead>
              <tbody>
                <tr v-for="(p, i) in presensiList" :key="p.nisn">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ p.nama_lengkap }}</td>
                  <td><code>{{ p.nisn }}</code></td>
                  <td>
                    <select v-model="p.status" class="filter-select mini-select">
                      <option value="Hadir">🟢 Hadir</option>
                      <option value="Sakit">🟡 Sakit</option>
                      <option value="Izin">🔵 Izin</option>
                      <option value="Alpa">🔴 Alpa</option>
                    </select>
                  </td>
                  <td>
                    <input v-model="p.catatan" type="text" class="fi mini-input-text" placeholder="Catatan sakit, izin lambat dll" />
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="p20" style="text-align: right">
              <button @click="savePresensi" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Absensi' }}</button>
            </div>
          </div>
          <div v-else class="empty-cell">Pilih jadwal pelajaran dan tanggal, lalu klik Cari</div>
        </div>
      </section>

      <!-- ========== TAB 6: INPUT NILAI ========== -->
      <section v-if="activeTab==='nilai'">
        <div class="card">
          <div class="card-header">
            <h3>Input Nilai Ujian &amp; Raport</h3>
            <div class="header-actions">
              <select v-model="nilaiParams.id_mapel" class="filter-select">
                <option value="">-- Pilih Mapel --</option>
                <option v-for="m in mapel" :key="m.id" :value="m.id">{{ m.nama_mapel }}</option>
              </select>
              <select v-model="nilaiParams.id_tahun_ajaran" class="filter-select">
                <option value="">-- Pilih Tahun Ajaran --</option>
                <option v-for="ta in tahunAjaran" :key="ta.id" :value="ta.id">{{ ta.tahun_ajaran }}</option>
              </select>
              <button @click="fetchNilai" class="btn-primary-purple" :disabled="!nilaiParams.id_mapel || !nilaiParams.id_tahun_ajaran">Cari</button>
            </div>
          </div>

          <div v-if="nilaiList.length > 0" class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Santri</th><th>NISN</th><th>Tugas (40%)</th><th>UTS (30%)</th><th>UAS (30%)</th><th>Nilai Akhir</th><th>Predikat</th><th>Keterangan</th></tr></thead>
              <tbody>
                <tr v-for="(n, i) in nilaiList" :key="n.nisn">
                  <td>{{ i+1 }}</td>
                  <td class="name-cell">{{ n.nama_lengkap }}</td>
                  <td><code>{{ n.nisn }}</code></td>
                  <td><input v-model.number="n.nilai_tugas" type="number" min="0" max="100" class="mini-input" /></td>
                  <td><input v-model.number="n.nilai_uts" type="number" min="0" max="100" class="mini-input" /></td>
                  <td><input v-model.number="n.nilai_uas" type="number" min="0" max="100" class="mini-input" /></td>
                  <td><strong class="paid-cell">{{ calcNilaiAkhir(n) }}</strong></td>
                  <td><span :class="['badge', gradeClass(n)]">{{ calcGrade(n) }}</span></td>
                  <td><input v-model="n.keterangan" type="text" class="fi mini-input-text" placeholder="Catatan nilai" /></td>
                </tr>
              </tbody>
            </table>
            <div class="p20" style="text-align: right">
              <button @click="saveNilai" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Nilai' }}</button>
            </div>
          </div>
          <div v-else class="empty-cell">Pilih mapel dan tahun ajaran, lalu klik Cari</div>
        </div>
      </section>
    </main>

        <!-- ===== MODAL: SANTRI FORM ===== -->
    <div v-if="showSantriForm" class="modal-overlay" @click.self="showSantriForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📋 {{ formSantri.id?'Edit Data Santri':'Tambah Santri Baru' }}</h3><button @click="showSantriForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Nama Lengkap *</label><input v-model="formSantri.nama_lengkap" class="fi" /></div>
          <div class="fg"><label>NIS (No. Induk Santri)</label><input v-model="formSantri.nis" class="fi" /></div>
          <div class="fg"><label>NISN</label><input v-model="formSantri.nisn" class="fi" /></div>
          <div class="fg"><label>NIK (No. Kependudukan)</label><input v-model="formSantri.nik" class="fi" /></div>
          <div class="fg"><label>Jenis Kelamin</label>
            <select v-model="formSantri.jenis_kelamin" class="fi">
              <option value="L">Laki-Laki</option><option value="P">Perempuan</option>
            </select>
          </div>
          <div class="fg"><label>Tempat Lahir</label><input v-model="formSantri.tempat_lahir" class="fi" /></div>
          <div class="fg"><label>Tanggal Lahir</label><input v-model="formSantri.tanggal_lahir" type="date" class="fi" /></div>
          <div class="fg"><label>No. HP / WA</label><input v-model="formSantri.no_hp" class="fi" /></div>
          <div class="fg"><label>Email</label><input v-model="formSantri.email" type="email" class="fi" /></div>
          <div class="fg full"><label>Alamat</label><input v-model="formSantri.alamat" class="fi" /></div>
          <div class="fg"><label>Tahun Ajaran Masuk</label>
            <select v-model="formSantri.id_tahun_ajaran" class="fi">
              <option value="">-- Pilih --</option>
              <option v-for="ta in tahunAjaran" :key="ta.id" :value="ta.id">{{ ta.tahun_ajaran }}</option>
            </select>
          </div>
          <div class="fg"><label>Status Santri</label>
            <select v-model="formSantri.status_santri" class="fi">
              <option value="Aktif">Aktif</option><option value="Alumni">Alumni</option><option value="Keluar">Keluar</option>
            </select>
          </div>
        </div>
        <div class="modal-actions"><button @click="showSantriForm=false" class="btn-secondary">Batal</button><button @click="saveSantri" class="btn-primary" :disabled="saving">Simpan</button></div>
      </div>
    </div>

    <!-- ===== MODAL: DETAIL SANTRI ===== -->
    <div v-if="showDetailModal" class="modal-overlay" @click.self="showDetailModal=false">
      <div class="modal-box" style="width: 580px">
        <div class="modal-head"><h3>📄 Profil Detail Santri</h3><button @click="showDetailModal=false" class="modal-close">✕</button></div>
        <div class="p20" style="display: flex; flex-direction: column; gap: 14px; font-size: 13px">
          <div class="detail-row"><span>Nama Lengkap:</span><strong>{{ selectedSantri.nama_lengkap }}</strong></div>
          <div class="detail-row"><span>NIS (No. Induk Santri):</span><span><code>{{ selectedSantri.nis || '—' }}</code></span></div>
          <div class="detail-row"><span>NISN:</span><span><code>{{ selectedSantri.nisn || '—' }}</code></span></div>
          <div class="detail-row"><span>NIK (No. Kependudukan):</span><span><code>{{ selectedSantri.nik || '—' }}</code></span></div>
          <div class="detail-row"><span>Jenis Kelamin:</span><span>{{ selectedSantri.jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}</span></div>
          <div class="detail-row"><span>Tempat, Tanggal Lahir:</span><span>{{ selectedSantri.tempat_lahir || '—' }}, {{ formatDate(selectedSantri.tanggal_lahir) }}</span></div>
          <div class="detail-row"><span>No. HP / WA:</span><span>{{ selectedSantri.no_hp || '—' }}</span></div>
          <div class="detail-row"><span>Email:</span><span>{{ selectedSantri.email || '—' }}</span></div>
          <div class="detail-row"><span>Alamat:</span><span>{{ selectedSantri.alamat || '—' }}</span></div>
          <div class="detail-row"><span>Tahun Ajaran Masuk:</span><span>{{ selectedSantri.nama_tahun_ajaran || '—' }}</span></div>
          <div class="detail-row"><span>Status Santri:</span>
            <span :class="['badge', selectedSantri.status_santri==='Aktif'?'badge-success':'badge-warning']">
              {{ selectedSantri.status_santri }}
            </span>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showDetailModal=false" class="btn-primary-purple">Tutup</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: CETAK KARTU SANTRI ===== -->
    <div v-if="showCardModal" class="modal-overlay" @click.self="showCardModal=false">
      <div class="modal-box" style="width: 520px; background: #111827;">
        <div class="modal-head">
          <h3>🪪 Cetak Kartu Tanda Santri</h3>
          <button @click="showCardModal=false" class="modal-close">✕</button>
        </div>
        
        <div class="p20 card-print-wrapper" style="display: flex; flex-direction: column; align-items: center; gap: 20px;">
          <!-- THE PRINTABLE CARD CONTAINER -->
          <div id="printable-student-card" class="student-card-container">
            <!-- Front of Card -->
            <div class="student-card-side card-front">
              <div class="card-wave-bg"></div>
              <div class="card-header-logo">
                <div class="card-logo-icon">SA</div>
                <div class="card-header-text">
                  <h4>SUPERAPP ACADEMY</h4>
                  <p>Pondok Pesantren Modern Al-Hidayah</p>
                </div>
              </div>
              
              <div class="card-body">
                <div class="card-photo-area">
                  <div class="card-photo">
                    <img v-if="selectedSantri.foto" :src="'http://127.0.0.1:8080/uploads/img/' + selectedSantri.foto" alt="Foto" />
                    <div v-else class="card-avatar-placeholder">{{ (selectedSantri.nama_lengkap || 'S').charAt(0) }}</div>
                  </div>
                  <span class="card-status-badge">SANTRI</span>
                </div>
                
                <div class="card-details">
                  <div class="cd-row"><span class="cd-label">NAMA</span><span class="cd-val font-bold">{{ selectedSantri.nama_lengkap }}</span></div>
                  <div class="cd-row"><span class="cd-label">NIS</span><span class="cd-val"><code>{{ selectedSantri.nis || '—' }}</code></span></div>
                  <div class="cd-row"><span class="cd-label">NISN</span><span class="cd-val"><code>{{ selectedSantri.nisn || '—' }}</code></span></div>
                  <div class="cd-row"><span class="cd-label">TTL</span><span class="cd-val">{{ selectedSantri.tempat_lahir || '—' }}, {{ formatDate(selectedSantri.tanggal_lahir) }}</span></div>
                  <div class="cd-row"><span class="cd-label">GENDER</span><span class="cd-val">{{ selectedSantri.jenis_kelamin === 'L' ? 'Laki-Laki' : 'Perempuan' }}</span></div>
                </div>
              </div>
              <div class="card-footer-barcode">
                <div class="fake-barcode">||||| | |||| ||| | ||| |||| | ||||| | |||</div>
                <span class="card-footer-exp">TA: {{ selectedSantri.nama_tahun_ajaran || '—' }}</span>
              </div>
            </div>
            
            <!-- Back of Card -->
            <div class="student-card-side card-back">
              <div class="card-header-logo back-header">
                <h4>KETENTUAN &amp; TATA TERTIB</h4>
              </div>
              <div class="card-back-body">
                <ol>
                  <li>Kartu ini wajib dibawa saat KBM, Ujian, &amp; Perizinan.</li>
                  <li>Tidak boleh dipinjamkan atau disalahgunakan.</li>
                  <li>Jika hilang, segera lapor ke Bagian Kesiswaan/Akademik.</li>
                </ol>
                <div class="signature-area">
                  <div class="sig-title">Kepala Madrasah &amp; Ponpes</div>
                  <div class="sig-space"></div>
                  <div class="sig-name">Dr. H. Ahmad Fauzi, M.Pd.</div>
                </div>
              </div>
              <div class="card-back-footer">
                Alamat: Jl. Raya Pesantren No. 45, Kota Bandung
              </div>
            </div>
          </div>
        </div>

        <div class="modal-actions">
          <button @click="showCardModal=false" class="btn-secondary">Tutup</button>
          <button @click="triggerPrint" class="btn-primary-purple">🖨️ Cetak / Print Kartu</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: KELAS FORM ===== -->
    <div v-if="showKelasForm" class="modal-overlay" @click.self="showKelasForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>🏫 {{ formKelas.id?'Edit Kelas':'Tambah Kelas' }}</h3><button @click="showKelasForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg"><label>Nama Kelas *</label><input v-model="formKelas.nama_kelas" class="fi" /></div>
          <div class="fg"><label>Tingkat</label><input v-model="formKelas.tingkat" class="fi" placeholder="contoh: 7, 8, 10" /></div>
          <div class="fg full"><label>Wali Kelas</label>
            <select v-model="formKelas.id_wali_kelas" class="fi">
              <option value="">-- Pilih Wali Kelas --</option>
              <option v-for="g in guru" :key="g.id" :value="g.id">{{ g.nama_lengkap }}</option>
            </select>
          </div>
        </div>
        <div class="modal-actions"><button @click="showKelasForm=false" class="btn-secondary">Batal</button><button @click="saveKelas" class="btn-primary" :disabled="saving">Simpan</button></div>
      </div>
    </div>

    <!-- ===== MODAL: MAPEL FORM ===== -->
    <div v-if="showMapelForm" class="modal-overlay" @click.self="showMapelForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📖 {{ formMapel.id?'Edit Mapel':'Tambah Mata Pelajaran' }}</h3><button @click="showMapelForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg"><label>Kode Mapel *</label><input v-model="formMapel.kode_mapel" class="fi" /></div>
          <div class="fg"><label>Nama Mapel *</label><input v-model="formMapel.nama_mapel" class="fi" /></div>
          <div class="fg full"><label>Kelompok Mapel</label>
            <select v-model="formMapel.kelompok" class="fi">
              <option value="Umum">Umum</option><option value="Keagamaan">Keagamaan / Diniyah</option><option value="Tahfidz">Tahfidz / Quran</option>
            </select>
          </div>
        </div>
        <div class="modal-actions"><button @click="showMapelForm=false" class="btn-secondary">Batal</button><button @click="saveMapel" class="btn-primary" :disabled="saving">Simpan</button></div>
      </div>
    </div>

    <!-- ===== MODAL: JADWAL FORM ===== -->
    <div v-if="showJadwalForm" class="modal-overlay" @click.self="showJadwalForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📅 Tambah Jadwal Pelajaran</h3><button @click="showJadwalForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg"><label>Kelas *</label>
            <select v-model="formJadwal.id_kelas" class="fi">
              <option value="">-- Pilih --</option>
              <option v-for="k in kelas" :key="k.id" :value="k.id">{{ k.nama_kelas }}</option>
            </select>
          </div>
          <div class="fg"><label>Mata Pelajaran *</label>
            <select v-model="formJadwal.id_mapel" class="fi">
              <option value="">-- Pilih --</option>
              <option v-for="m in mapel" :key="m.id" :value="m.id">{{ m.nama_mapel }}</option>
            </select>
          </div>
          <div class="fg"><label>Guru Pengampu</label>
            <select v-model="formJadwal.id_guru" class="fi">
              <option value="">-- Pilih --</option>
              <option v-for="g in guru" :key="g.id" :value="g.id">{{ g.nama_lengkap }}</option>
            </select>
          </div>
          <div class="fg"><label>Tahun Ajaran</label>
            <select v-model="formJadwal.id_tahun_ajaran" class="fi">
              <option value="">-- Pilih --</option>
              <option v-for="ta in tahunAjaran" :key="ta.id" :value="ta.id">{{ ta.tahun_ajaran }}</option>
            </select>
          </div>
          <div class="fg"><label>Hari *</label>
            <select v-model="formJadwal.hari" class="fi">
              <option value="Senin">Senin</option><option value="Selasa">Selasa</option><option value="Rabu">Rabu</option>
              <option value="Kamis">Kamis</option><option value="Jumat">Jumat</option><option value="Sabtu">Sabtu</option><option value="Minggu">Minggu</option>
            </select>
          </div>
          <div class="fg"><label>Ruangan</label><input v-model="formJadwal.ruangan" class="fi" placeholder="contoh: Kelas VIIA, Lab, Aula" /></div>
          <div class="fg"><label>Jam Mulai</label><input v-model="formJadwal.jam_mulai" type="time" class="fi" /></div>
          <div class="fg"><label>Jam Selesai</label><input v-model="formJadwal.jam_selesai" type="time" class="fi" /></div>
        </div>
        <div class="modal-actions"><button @click="showJadwalForm=false" class="btn-secondary">Batal</button><button @click="saveJadwal" class="btn-primary" :disabled="saving">Simpan</button></div>
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
import { useRouter } from 'vue-router'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const router = useRouter()
const API    = 'http://127.0.0.1:8080/api'
const token  = localStorage.getItem('jwt_token')
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab       = ref('santri')
const loading         = ref(false)
const saving          = ref(false)
const toast           = ref({ show: false, message: '', type: 'success' })

const santri          = ref([])
const kelas           = ref([])
const mapel           = ref([])
const jadwal          = ref([])
const guru            = ref([])
const tahunAjaran     = ref([])

const searchSantri    = ref('')
const filterKelasJadwal = ref('')

const santriPage = ref(1)
const santriLimit = ref(10)
const santriTotal = ref(0)
const santriTotalPages = ref(0)

const showSantriForm  = ref(false)
const showKelasForm   = ref(false)
const showMapelForm   = ref(false)
const showJadwalForm  = ref(false)
const showDetailModal = ref(false)
const showCardModal   = ref(false)
const selectedSantri  = ref({})

const formSantri      = ref({ id: '', nama_lengkap: '', nisn: '', nis: '', nik: '', no_hp: '', email: '', jenis_kelamin: 'L', tempat_lahir: '', tanggal_lahir: '', alamat: '', id_tahun_ajaran: '', status_santri: 'Aktif' })
const formKelas       = ref({ id: '', nama_kelas: '', tingkat: '', id_wali_kelas: '' })
const formMapel       = ref({ id: '', kode_mapel: '', nama_mapel: '', kelompok: 'Umum' })
const formJadwal      = ref({ id_kelas: '', id_mapel: '', id_guru: '', id_tahun_ajaran: '', hari: 'Senin', jam_mulai: '', jam_selesai: '', ruangan: '' })

const presensiParams  = ref({ id_jadwal: '', tanggal: new Date().toISOString().slice(0, 10) })
const presensiList    = ref([])

const nilaiParams     = ref({ id_mapel: '', id_tahun_ajaran: '' })
const nilaiList       = ref([])

const tabs = [
  { key: 'santri', label: 'Santri', icon: '👨‍🎓', color: 'purple' },
  { key: 'kelas', label: 'Kelas', icon: '🏫', color: 'blue' },
  { key: 'mapel', label: 'Mapel', icon: '📖', color: 'teal' },
  { key: 'jadwal', label: 'Jadwal', icon: '📅', color: 'green' },
  { key: 'presensi', label: 'Absensi', icon: '📝', color: 'gold' },
  { key: 'nilai', label: 'Nilai', icon: '🎯', color: 'red' },
]

const hariUrutan = { 'Senin': 1, 'Selasa': 2, 'Rabu': 3, 'Kamis': 4, 'Jumat': 5, 'Sabtu': 6, 'Minggu': 7 }

// ===== COMPUTED =====
const filteredSantri = computed(() => santri.value)

const countSantriByStatus = (status) => santri.value.filter(s => s.status_santri === status).length

const sortedJadwal = computed(() => {
  return [...jadwal.value].sort((a, b) => {
    if (hariUrutan[a.hari] !== hariUrutan[b.hari]) {
      return hariUrutan[a.hari] - hariUrutan[b.hari]
    }
    return (a.jam_mulai || '').localeCompare(b.jam_mulai || '')
  })
})

const jadwalHariIni = computed(() => {
  const hari_map = { 0: 'Minggu', 1: 'Senin', 2: 'Selasa', 3: 'Rabu', 4: 'Kamis', 5: 'Jumat', 6: 'Sabtu' }
  const hari_ini = hari_map[new Date().getDay()]
  return jadwal.value.filter(j => j.hari === hari_ini)
})

// ===== HELPERS =====
function formatDate(d) { if(!d) return '—'; return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) }
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }

function calcNilaiAkhir(n) {
  const t = parseFloat(n.nilai_tugas) || 0
  const ut = parseFloat(n.nilai_uts) || 0
  const ua = parseFloat(n.nilai_uas) || 0
  return Math.round((t * 0.4) + (ut * 0.3) + (ua * 0.3))
}

function calcGrade(n) {
  const fin = calcNilaiAkhir(n)
  if (fin >= 85) return 'A'
  if (fin >= 75) return 'B'
  if (fin >= 65) return 'C'
  if (fin >= 50) return 'D'
  return 'E'
}

function gradeClass(n) {
  const g = calcGrade(n)
  if (g === 'A' || g === 'B') return 'badge-success'
  if (g === 'C') return 'badge-info'
  if (g === 'D') return 'badge-warning'
  return 'badge-danger'
}

// ===== METHODS =====
async function switchTab(key) {
  activeTab.value = key
  if (key === 'santri') await fetchSantri(1)
  if (key === 'kelas') await fetchKelas()
  if (key === 'mapel') await fetchMapel()
  if (key === 'jadwal') {
    await fetchKelas()
    await fetchJadwal()
  }
}

// === FETCH DATA ===
async function fetchSantri(page = 1) {
  loading.value = true
  santriPage.value = page
  try { 
    const res = await axios.get(`${API}/akademik/santri`, { 
      params: {
        page: santriPage.value,
        limit: santriLimit.value,
        q: searchSantri.value
      },
      headers 
    })
    santri.value = res.data.data || []
    if (res.data.pagination) {
      santriTotal.value = res.data.pagination.total || 0
      santriTotalPages.value = res.data.pagination.total_pages || 0
    }
  }
  catch { showNotif('Gagal memuat data santri', 'error') }
  finally { loading.value = false }
}

watch(santriLimit, () => {
  santriPage.value = 1
  fetchSantri(1)
})

let santriSearchTimeout = null
function debounceSantriSearch() {
  if (santriSearchTimeout) clearTimeout(santriSearchTimeout)
  santriSearchTimeout = setTimeout(() => {
    fetchSantri(1)
  }, 400)
}

function changeSantriPage(page) {
  if (page < 1 || page > santriTotalPages.value) return
  santriPage.value = page
  fetchSantri(page)
}

const santriPaginationRange = computed(() => {
  const current = santriPage.value
  const total = santriTotalPages.value
  const delta = 2
  const range = []
  let start = Math.max(1, current - delta)
  let end = Math.min(total, current + delta)
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  return range
})

async function fetchKelas() {
  try { kelas.value = (await axios.get(`${API}/akademik/kelas`, { headers })).data.data || [] }
  catch {}
}

async function fetchMapel() {
  try { mapel.value = (await axios.get(`${API}/akademik/mapel`, { headers })).data.data || [] }
  catch {}
}

async function fetchJadwal() {
  if (!filterKelasJadwal.value) { jadwal.value = []; return }
  loading.value = true
  try { jadwal.value = (await axios.get(`${API}/akademik/jadwal?kelas=${filterKelasJadwal.value}`, { headers })).data.data || [] }
  catch { showNotif('Gagal memuat jadwal', 'error') }
  finally { loading.value = false }
}

// Watchers
watch(filterKelasJadwal, fetchJadwal)

// === SANTRI CRUD ===
function openAddSantri() {
  formSantri.value = { id: '', nama_lengkap: '', nisn: '', nis: '', nik: '', no_hp: '', email: '', jenis_kelamin: 'L', tempat_lahir: '', tanggal_lahir: '', alamat: '', id_tahun_ajaran: '', status_santri: 'Aktif' }
  showSantriForm.value = true
}

function openEditSantri(s) {
  formSantri.value = { ...s }
  showSantriForm.value = true
}

function viewSantriDetail(s) {
  selectedSantri.value = s
  showDetailModal.value = true
}

function printSantriCard(s) {
  selectedSantri.value = s
  showCardModal.value = true
}

function triggerPrint() {
  window.print()
}

async function saveSantri() {
  if (!formSantri.value.nama_lengkap) return showNotif('Nama wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/akademik/santri/save`, formSantri.value, { headers })
    showSantriForm.value = false
    await fetchSantri(1)
    showNotif('Data santri berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan santri', 'error') }
  finally { saving.value = false }
}

async function deleteSantri(id, nama) {
  if (!confirm(`Hapus santri "${nama}"?`)) return
  try {
    await axios.delete(`${API}/akademik/santri/delete/${id}`, { headers })
    await fetchSantri(santriPage.value)
    showNotif('Santri berhasil dihapus!')
  } catch { showNotif('Gagal menghapus santri', 'error') }
}

// === KELAS CRUD ===
function openAddKelas() {
  formKelas.value = { id: '', nama_kelas: '', tingkat: '', id_wali_kelas: '' }
  showKelasForm.value = true
}

function openEditKelas(k) {
  formKelas.value = { ...k }
  showKelasForm.value = true
}

async function saveKelas() {
  if (!formKelas.value.nama_kelas) return showNotif('Nama kelas wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/akademik/kelas/save`, formKelas.value, { headers })
    showKelasForm.value = false
    await fetchKelas()
    showNotif('Data kelas berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan kelas', 'error') }
  finally { saving.value = false }
}

async function deleteKelas(id, nama) {
  if (!confirm(`Hapus kelas "${nama}"?`)) return
  try {
    await axios.delete(`${API}/akademik/kelas/delete/${id}`, { headers })
    await fetchKelas()
    showNotif('Kelas berhasil dihapus!')
  } catch { showNotif('Gagal menghapus kelas', 'error') }
}

// === MAPEL CRUD ===
function openAddMapel() {
  formMapel.value = { id: '', kode_mapel: '', nama_mapel: '', kelompok: 'Umum' }
  showMapelForm.value = true
}

function openEditMapel(m) {
  formMapel.value = { ...m }
  showMapelForm.value = true
}

async function saveMapel() {
  if (!formMapel.value.nama_mapel || !formMapel.value.kode_mapel) return showNotif('Kode & nama wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/akademik/mapel/save`, formMapel.value, { headers })
    showMapelForm.value = false
    await fetchMapel()
    showNotif('Mata pelajaran berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan mapel', 'error') }
  finally { saving.value = false }
}

async function deleteMapel(id, nama) {
  if (!confirm(`Hapus mapel "${nama}"?`)) return
  try {
    await axios.delete(`${API}/akademik/mapel/delete/${id}`, { headers })
    await fetchMapel()
    showNotif('Mata pelajaran berhasil dihapus!')
  } catch { showNotif('Gagal menghapus mapel', 'error') }
}

// === JADWAL CRUD ===
function openAddJadwal() {
  formJadwal.value = { id_kelas: filterKelasJadwal.value, id_mapel: '', id_guru: '', id_tahun_ajaran: '', hari: 'Senin', jam_mulai: '', jam_selesai: '', ruangan: '' }
  showJadwalForm.value = true
}

async function saveJadwal() {
  if (!formJadwal.value.id_kelas || !formJadwal.value.id_mapel || !formJadwal.value.hari) return showNotif('Kelas, mapel, & hari wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/akademik/jadwal/save`, formJadwal.value, { headers })
    showJadwalForm.value = false
    await fetchJadwal()
    showNotif('Jadwal pelajaran disimpan!')
  } catch { showNotif('Gagal menyimpan jadwal', 'error') }
  finally { saving.value = false }
}

async function deleteJadwal(id) {
  if (!confirm('Hapus jadwal pelajaran ini?')) return
  try {
    await axios.delete(`${API}/akademik/jadwal/delete/${id}`, { headers })
    await fetchJadwal()
    showNotif('Jadwal berhasil dihapus!')
  } catch { showNotif('Gagal menghapus jadwal', 'error') }
}

// === ABSENSI (PRESENSI) ===
async function fetchPresensi() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/akademik/presensi?id_jadwal=${presensiParams.value.id_jadwal}&tanggal=${presensiParams.value.tanggal}`, { headers })
    presensiList.value = res.data.data || []
  } catch { showNotif('Gagal memuat absensi', 'error') }
  finally { loading.value = false }
}

async function savePresensi() {
  saving.value = true
  try {
    await axios.post(`${API}/akademik/presensi/save`, {
      id_jadwal: presensiParams.value.id_jadwal,
      tanggal: presensiParams.value.tanggal,
      records: presensiList.value
    }, { headers })
    showNotif('Presensi santri berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan presensi', 'error') }
  finally { saving.value = false }
}

// === NILAI ===
async function fetchNilai() {
  loading.value = true
  try {
    const res = await axios.get(`${API}/akademik/nilai?id_mapel=${nilaiParams.value.id_mapel}&id_tahun_ajaran=${nilaiParams.value.id_tahun_ajaran}`, { headers })
    nilaiList.value = res.data.data || []
  } catch { showNotif('Gagal memuat data nilai', 'error') }
  finally { loading.value = false }
}

async function saveNilai() {
  saving.value = true
  try {
    await axios.post(`${API}/akademik/nilai/save`, {
      id_mapel: nilaiParams.value.id_mapel,
      id_tahun_ajaran: nilaiParams.value.id_tahun_ajaran,
      records: nilaiList.value
    }, { headers })
    showNotif('Nilai santri berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan nilai', 'error') }
  finally { saving.value = false }
}

// === INIT ===
async function init() {
  loading.value = true
  try {
    const [resT, resG, resTa] = await Promise.all([
      axios.get(`${API}/akademik/santri`, { params: { page: 1, limit: santriLimit.value }, headers }),
      axios.get(`${API}/akademik/guru`, { headers }),
      axios.get(`${API}/akademik/tahun-ajaran`, { headers })
    ])
    santri.value      = resT.data.data || []
    if (resT.data.pagination) {
      santriTotal.value = resT.data.pagination.total || 0
      santriTotalPages.value = resT.data.pagination.total_pages || 0
    }
    guru.value        = resG.data.data || []
    tahunAjaran.value = resTa.data.data || []
    await fetchKelas()
    await fetchMapel()
    // Muat semua jadwal pelajaran agar bisa dicheck untuk presensi
    const resJ = await axios.get(`${API}/akademik/jadwal`, { headers })
    jadwal.value = resJ.data.data || []
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
.header-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
.tab-btn { padding: 8px 14px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: transparent; color: #94a3b8; font-size: 12px; cursor: pointer; transition: all 0.2s; white-space: nowrap; }

.active-purple { background: rgba(167,139,250,0.15); color: #a78bfa; border-color: rgba(167,139,250,0.3); font-weight: 600; }
.active-blue { background: rgba(96,165,250,0.15); color: #60a5fa; border-color: rgba(96,165,250,0.3); font-weight: 600; }
.active-teal { background: rgba(45,212,191,0.15); color: #2dd4bf; border-color: rgba(45,212,191,0.3); font-weight: 600; }
.active-green { background: rgba(52,211,153,0.15); color: #34d399; border-color: rgba(52,211,153,0.3); font-weight: 600; }
.active-gold { background: rgba(251,191,36,0.15); color: #fbbf24; border-color: rgba(251,191,36,0.3); font-weight: 600; }
.active-red { background: rgba(248,113,113,0.15); color: #f87171; border-color: rgba(248,113,113,0.3); font-weight: 600; }

/* Stats */
.stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; padding: 20px 32px 0; }
.stat-card { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.07); border-radius: 12px; padding: 16px 20px; display: flex; gap: 16px; align-items: center; }
.stat-icon { font-size: 28px; }
.stat-num { font-size: 20px; font-weight: 700; }
.stat-lbl { font-size: 12px; color: #64748b; }
.stat-card.purple .stat-num { color: #a78bfa; }
.stat-card.green .stat-num { color: #34d399; }
.stat-card.gold .stat-num { color: #fbbf24; }

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
.ab-del:hover { background: rgba(248,113,113,0.1); }

/* Buttons */
.btn-primary { padding: 9px 18px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-primary:disabled, .btn-primary-purple:disabled { opacity: 0.5; cursor: not-allowed; }
.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.07); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; font-size: 13px; cursor: pointer; }
.btn-danger-sm { padding: 5px 10px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); border-radius: 6px; color: #f87171; cursor: pointer; font-size: 12px; }

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

/* Details row in Detail Modal */
.detail-row { display: flex; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.04); padding-bottom: 8px; }
.detail-row span { color: #94a3b8; }

/* Student Card design */
.student-card-container { display: flex; flex-direction: column; gap: 16px; align-items: center; }
.student-card-side { width: 380px; height: 230px; background: linear-gradient(135deg, #1e293b, #0f172a); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 12px; position: relative; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3); display: flex; flex-direction: column; justify-content: space-between; padding: 14px; text-align: left; }
.card-wave-bg { position: absolute; top: -20px; right: -20px; width: 120px; height: 120px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%); border-radius: 50%; pointer-events: none; }
.card-header-logo { display: flex; align-items: center; gap: 8px; border-bottom: 1px solid rgba(255, 255, 255, 0.1); padding-bottom: 8px; }
.card-logo-icon { width: 28px; height: 28px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 11px; color: white; }
.card-header-text h4 { font-size: 11px; font-weight: 700; color: #fff; letter-spacing: 0.5px; margin: 0; }
.card-header-text p { font-size: 8px; color: #94a3b8; margin: 0; }
.card-body { display: flex; gap: 12px; flex: 1; margin-top: 10px; }
.card-photo-area { display: flex; flex-direction: column; align-items: center; gap: 6px; }
.card-photo { width: 65px; height: 80px; border: 1px solid rgba(255, 255, 255, 0.15); background: rgba(255, 255, 255, 0.05); border-radius: 6px; overflow: hidden; display: flex; align-items: center; justify-content: center; }
.card-photo img { width: 100%; height: 100%; object-fit: cover; }
.card-avatar-placeholder { font-size: 32px; font-weight: 700; color: #818cf8; }
.card-status-badge { font-size: 8px; font-weight: 700; color: #34d399; background: rgba(52, 211, 153, 0.1); padding: 1px 6px; border-radius: 4px; letter-spacing: 0.5px; }
.card-details { flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 4px; }
.cd-row { display: flex; font-size: 9.5px; }
.cd-label { width: 48px; color: #64748b; font-weight: 600; }
.cd-val { flex: 1; color: #e2e8f0; }
.cd-val code { background: transparent; padding: 0; font-size: 9.5px; }
.card-footer-barcode { display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255, 255, 255, 0.07); padding-top: 6px; margin-top: 4px; }
.fake-barcode { font-size: 9px; font-family: monospace; color: #64748b; letter-spacing: -0.5px; }
.card-footer-exp { font-size: 8.5px; color: #94a3b8; }

/* Card Back */
.back-header { border-bottom: none; justify-content: center; padding-bottom: 0; }
.back-header h4 { font-size: 10.5px; color: #818cf8; letter-spacing: 1px; margin: 0; }
.card-back-body { flex: 1; margin-top: 8px; display: flex; flex-direction: column; justify-content: space-between; }
.card-back-body ol { padding-left: 14px; font-size: 8.5px; color: #94a3b8; display: flex; flex-direction: column; gap: 4px; }
.signature-area { align-self: flex-end; text-align: center; margin-top: 6px; width: 130px; }
.sig-title { font-size: 8px; color: #64748b; }
.sig-space { height: 25px; }
.sig-name { font-size: 8.5px; font-weight: 700; color: #e2e8f0; border-top: 0.5px solid rgba(255, 255, 255, 0.2); padding-top: 2px; }
.card-back-footer { font-size: 7.5px; color: #64748b; text-align: center; border-top: 1px solid rgba(255, 255, 255, 0.07); padding-top: 6px; }

/* Print CSS */
@media print {
  body * { visibility: hidden !important; }
  #printable-student-card, #printable-student-card * { visibility: visible !important; }
  #printable-student-card { position: absolute; left: 50%; top: 50px; transform: translateX(-50%); width: auto; height: auto; gap: 20px; background: transparent; }
  .student-card-side { border: 1px solid #000; box-shadow: none; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}

.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 8px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; }
.ab-green:hover { background: rgba(52, 211, 153, 0.15); color: #34d399; }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; }
.toast-success { background: rgba(16,185,129,0.9); color: white; }
.toast-error { background: rgba(239,68,68,0.9); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
