<template>
  <div class="dashboard-container">
    <!-- Sidebar -->
    <Sidebar />

    <!-- Main -->
    <main class="main-content">
      <header class="content-header">
        <div><h1>PPDB</h1><p>Penerimaan Peserta Didik Baru — Kelola pendaftaran, tes, berkas, dan pembayaran</p></div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">{{ t.icon }} {{ t.label }}</button>
        </div>
      </header>

      <!-- ========== STATS ========== -->
      <div class="stats-grid">
        <div class="stat-card s-blue"><div class="si">👥</div><div><div class="sn">{{ stats.total }}</div><div class="sl">Total Pendaftar</div></div></div>
        <div class="stat-card s-yellow"><div class="si">⏳</div><div><div class="sn">{{ stats.pending }}</div><div class="sl">Pending</div></div></div>
        <div class="stat-card s-green"><div class="si">✅</div><div><div class="sn">{{ stats.lulus }}</div><div class="sl">Lulus Seleksi</div></div></div>
        <div class="stat-card s-red"><div class="si">❌</div><div><div class="sn">{{ stats.tidak_lulus }}</div><div class="sl">Tidak Lulus</div></div></div>
        <div class="stat-card s-purple"><div class="si">🎓</div><div><div class="sn">{{ stats.terdaftar }}</div><div class="sl">Jadi Santri</div></div></div>
        <div class="stat-card s-teal"><div class="si">📅</div><div><div class="sn">{{ stats.total_jadwal }}</div><div class="sl">Jadwal Tes</div></div></div>
      </div>

      <!-- ========== TAB: PENDAFTAR ========== -->
      <section v-if="activeTab==='pendaftar'">
        <div class="card">
          <div class="card-header" style="display:flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
            <h3>Daftar Pendaftar</h3>
            <div class="header-right" style="display: flex; align-items: center; gap: 10px;">
              <select v-model="ppdbLimit" class="fi" style="width: 100px; height: 38px; padding: 4px 8px; font-size: 13px; margin-bottom: 0; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #e2e8f0; outline: none;">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
                <option :value="100">100 baris</option>
              </select>
              <input v-model="searchQ" @input="debouncePpdbSearch" placeholder="Cari nama..." class="search-input" />
              <button @click="showAddForm=true" class="btn-primary">+ Tambah</button>
            </div>
          </div>
          <div class="filter-bar-inner">
            <button v-for="f in filterOpts" :key="f.v" :class="['fchip', filterStatus===f.v?'fchip-on-'+f.c:'']" @click="filterStatus=f.v">{{ f.l }}</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>No. Daftar</th><th>Nama</th><th>JK</th><th>HP Ortu</th><th>Thn Ajaran</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat...</td></tr>
                <tr v-else-if="filteredPendaftar.length===0"><td colspan="8" class="empty-cell">Tidak ada data</td></tr>
                <tr v-for="(p,i) in filteredPendaftar" :key="p.id">
                  <td class="num">{{ (ppdbPage - 1) * ppdbLimit + i + 1 }}</td>
                  <td><code>{{ p.nomor_pendaftaran }}</code></td>
                  <td class="name-cell" @click="openDetail(p)" style="cursor:pointer;text-decoration:underline dotted">{{ p.nama_lengkap }}</td>
                  <td>{{ p.jenis_kelamin }}</td>
                  <td>{{ p.no_hp_ortu || '—' }}</td>
                  <td>{{ p.nama_tahun || '—' }}</td>
                  <td><span :class="['spill', 'sp-'+pillClass(p.status_seleksi)]">{{ p.status_seleksi }}</span></td>
                  <td>
                    <div class="action-group">
                      <button v-if="p.status_seleksi==='Pending'" @click="setStatus(p.id,'lulus')" class="ab ab-green" title="Lulus">✅</button>
                      <button v-if="p.status_seleksi==='Pending'" @click="setStatus(p.id,'gagal')" class="ab ab-red" title="Tidak Lulus">❌</button>
                      <button v-if="p.status_seleksi==='Lulus'" @click="setStatus(p.id,'terdaftar')" class="ab ab-purple" title="Jadikan Santri">🎓</button>
                      <button @click="openDetail(p)" class="ab ab-blue" title="Detail">📋</button>
                      <button @click="deletePendaftar(p.id, p.nama_lengkap)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
          <!-- Pagination -->
          <div v-if="ppdbTotalPages > 1" class="p20" style="display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.06); flex-wrap: wrap; gap: 12px; padding: 20px;">
            <div class="text-muted" style="font-size: 12px;">
              Menampilkan <strong>{{ (ppdbPage - 1) * ppdbLimit + 1 }}</strong> - 
              <strong>{{ Math.min(ppdbPage * ppdbLimit, ppdbTotal) }}</strong> dari 
              <strong>{{ ppdbTotal }}</strong> pendaftar
            </div>
            
            <div style="display: flex; gap: 6px; align-items: center;">
              <button @click="changePpdbPage(1)" :disabled="ppdbPage === 1" class="tab-btn" style="padding: 6px 10px;">« First</button>
              <button @click="changePpdbPage(ppdbPage - 1)" :disabled="ppdbPage === 1" class="tab-btn" style="padding: 6px 12px;">‹ Prev</button>
              
              <!-- Page numbers -->
              <button v-for="p in ppdbPaginationRange" :key="p" @click="changePpdbPage(p)" :class="['tab-btn', ppdbPage === p ? 'active-indigo' : '']" style="padding: 6px 12px; font-weight: 500;">
                {{ p }}
              </button>
              
              <button @click="changePpdbPage(ppdbPage + 1)" :disabled="ppdbPage === ppdbTotalPages" class="tab-btn" style="padding: 6px 12px;">Next ›</button>
              <button @click="changePpdbPage(ppdbTotalPages)" :disabled="ppdbPage === ppdbTotalPages" class="tab-btn" style="padding: 6px 10px;">Last »</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== TAB: JADWAL TES ========== -->
      <section v-if="activeTab==='jadwal'">
        <div class="card">
          <div class="card-header">
            <h3>Jadwal Seleksi / Tes</h3>
            <button @click="showJadwalForm=true" class="btn-primary">+ Tambah Jadwal</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Nama Tes</th><th>Tanggal</th><th>Jam</th><th>Tempat</th><th>Kuota</th><th>Peserta</th><th>Aksi</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat...</td></tr>
                <tr v-else-if="jadwal.length===0"><td colspan="8" class="empty-cell">Belum ada jadwal tes</td></tr>
                <tr v-for="(j,i) in jadwal" :key="j.id">
                  <td class="num">{{ i+1 }}</td>
                  <td class="name-cell">{{ j.nama_tes }}</td>
                  <td>{{ formatDate(j.tanggal) }}</td>
                  <td>{{ j.jam || '—' }}</td>
                  <td>{{ j.tempat || '—' }}</td>
                  <td><span class="badge-num">{{ j.kuota }}</span></td>
                  <td>
                    <span :class="['badge-num', j.jumlah_peserta >= j.kuota ? 'quota-full' : '']">
                      {{ j.jumlah_peserta }} / {{ j.kuota }}
                    </span>
                  </td>
                  <td>
                    <div class="action-group">
                      <button @click="openPesertaJadwal(j)" class="ab ab-blue" title="Kelola Peserta">👥</button>
                      <button @click="deleteJadwal(j.id, j.nama_tes)" class="ab ab-del">🗑️</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB: BERKAS ========== -->
      <section v-if="activeTab==='berkas'">
        <div class="two-col">
          <!-- Syarat Master -->
          <div class="card">
            <div class="card-header">
              <h3>⚙️ Master Syarat Berkas</h3>
              <button @click="showSyaratForm=true" class="btn-primary btn-sm">+ Tambah</button>
            </div>
            <ul class="syarat-list">
              <li v-if="syarat.length===0" class="empty-cell">Belum ada syarat berkas</li>
              <li v-for="s in syarat" :key="s.id" class="syarat-item">
                <div>
                  <span class="syarat-nama">{{ s.nama_berkas }}</span>
                  <span v-if="s.is_wajib==1" class="badge-wajib">WAJIB</span>
                </div>
                <button @click="deleteSyarat(s.id, s.nama_berkas)" class="ab ab-del">🗑️</button>
              </li>
            </ul>
          </div>
          <!-- Verifikasi Berkas per Pendaftar Lulus -->
          <div class="card">
            <div class="card-header"><h3>✅ Verifikasi Berkas Pendaftar Lulus</h3></div>
            <div class="table-wrapper">
              <table class="data-table">
                <thead><tr><th>Nama</th><th>Checklist</th></tr></thead>
                <tbody>
                  <tr v-if="pendaftarLulus.length===0"><td colspan="2" class="empty-cell">Tidak ada pendaftar lulus</td></tr>
                  <tr v-for="p in pendaftarLulus" :key="p.id">
                    <td class="name-cell" style="cursor:pointer" @click="openBerkas(p)">{{ p.nama_lengkap }}</td>
                    <td><button @click="openBerkas(p)" class="ab ab-blue">📁 Verifikasi</button></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== TAB: PEMBAYARAN ========== -->
      <section v-if="activeTab==='pembayaran'">
        <div class="card">
          <div class="card-header">
            <h3>Pembayaran Biaya PPDB</h3>
            <button @click="showBayarForm=true" class="btn-primary">+ Catat Pembayaran</button>
          </div>
          <div class="table-wrapper">
            <table class="data-table">
              <thead><tr><th>#</th><th>Pendaftar</th><th>No. Kwitansi</th><th>Jumlah</th><th>Tgl Bayar</th><th>Metode</th><th>Keterangan</th></tr></thead>
              <tbody>
                <tr v-if="loading"><td colspan="7" class="loading-cell">Memuat...</td></tr>
                <tr v-else-if="allPembayaran.length===0"><td colspan="7" class="empty-cell">Belum ada riwayat pembayaran</td></tr>
                <tr v-for="(b,i) in allPembayaran" :key="b.id">
                  <td class="num">{{ i+1 }}</td>
                  <td class="name-cell">{{ b.nama_lengkap }}</td>
                  <td><code>{{ b.nomor_kwitansi }}</code></td>
                  <td class="paid-cell">{{ formatRp(b.jumlah) }}</td>
                  <td>{{ formatDate(b.tanggal_bayar) }}</td>
                  <td><span class="badge-info">{{ b.metode_bayar }}</span></td>
                  <td class="muted">{{ b.keterangan || '—' }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

    </main>

    <!-- ===== MODAL: Tambah Pendaftar ===== -->
    <div v-if="showAddForm" class="modal-overlay" @click.self="showAddForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📋 Tambah Pendaftar Baru</h3><button @click="showAddForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg"><label>Nama Lengkap *</label><input v-model="formPendaftar.nama_lengkap" class="fi" /></div>
          <div class="fg"><label>Jenis Kelamin</label>
            <select v-model="formPendaftar.jenis_kelamin" class="fi">
              <option value="L">Laki-Laki</option><option value="P">Perempuan</option>
            </select>
          </div>
          <div class="fg"><label>Tempat Lahir</label><input v-model="formPendaftar.tempat_lahir" class="fi" /></div>
          <div class="fg"><label>Tanggal Lahir</label><input v-model="formPendaftar.tanggal_lahir" type="date" class="fi" /></div>
          <div class="fg full"><label>Alamat</label><input v-model="formPendaftar.alamat" class="fi" /></div>
          <div class="fg"><label>No. HP Orang Tua</label><input v-model="formPendaftar.no_hp_ortu" class="fi" /></div>
          <div class="fg"><label>Tahun Ajaran</label>
            <select v-model="formPendaftar.id_tahun_ajaran" class="fi">
              <option value="">— Pilih —</option>
              <option v-for="ta in tahunAjaran" :key="ta.id" :value="ta.id">{{ ta.tahun_ajaran }}</option>
            </select>
          </div>
          <div class="fg"><label>Status Awal</label>
            <select v-model="formPendaftar.status_seleksi" class="fi">
              <option value="Pending">Pending</option><option value="Lulus">Lulus</option>
            </select>
          </div>
        </div>
        <div class="modal-actions"><button @click="showAddForm=false" class="btn-secondary">Batal</button><button @click="savePendaftar" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan' }}</button></div>
      </div>
    </div>

    <!-- ===== MODAL: Detail Pendaftar ===== -->
    <div v-if="detailPendaftar" class="modal-overlay" @click.self="detailPendaftar=null">
      <div class="modal-box modal-wide">
        <div class="modal-head">
          <h3>📋 {{ detailPendaftar.nama_lengkap }}</h3>
          <div style="display:flex;gap:8px;align-items:center">
            <span :class="['spill','sp-'+pillClass(detailPendaftar.status_seleksi)]">{{ detailPendaftar.status_seleksi }}</span>
            <button @click="detailPendaftar=null" class="modal-close">✕</button>
          </div>
        </div>
        <div class="detail-tabs">
          <button v-for="dt in ['Info','Tes','Berkas','Pembayaran']" :key="dt" :class="['dtab', detailTab===dt?'dtab-on':'']" @click="detailTab=dt">{{ dt }}</button>
        </div>
        <!-- INFO -->
        <div v-if="detailTab==='Info'" class="detail-grid p20">
          <div class="di"><div class="dl">No. Pendaftaran</div><div class="dv"><code>{{ detailPendaftar.nomor_pendaftaran }}</code></div></div>
          <div class="di"><div class="dl">Jenis Kelamin</div><div class="dv">{{ detailPendaftar.jenis_kelamin === 'L' ? '♂ Laki-Laki' : '♀ Perempuan' }}</div></div>
          <div class="di"><div class="dl">Tempat, Tgl Lahir</div><div class="dv">{{ detailPendaftar.tempat_lahir }}, {{ formatDate(detailPendaftar.tanggal_lahir) }}</div></div>
          <div class="di"><div class="dl">Alamat</div><div class="dv">{{ detailPendaftar.alamat || '—' }}</div></div>
          <div class="di"><div class="dl">No. HP Ortu</div><div class="dv">{{ detailPendaftar.no_hp_ortu || '—' }}</div></div>
          <div class="di"><div class="dl">Tahun Ajaran</div><div class="dv">{{ detailPendaftar.nama_tahun || '—' }}</div></div>
        </div>
        <!-- TES -->
        <div v-if="detailTab==='Tes'" class="p20">
          <div v-if="!detailData.tes" class="empty-cell">Memuat...</div>
          <div v-else-if="detailData.tes.length===0" class="empty-cell">Belum terdaftar di jadwal tes apapun</div>
          <table v-else class="data-table">
            <thead><tr><th>Nama Tes</th><th>Tanggal</th><th>Jam</th><th>Tempat</th><th>Kehadiran</th><th>Nilai</th></tr></thead>
            <tbody>
              <tr v-for="t in detailData.tes" :key="t.id">
                <td class="name-cell">{{ t.nama_tes }}</td>
                <td>{{ formatDate(t.tanggal) }}</td>
                <td>{{ t.jam || '—' }}</td>
                <td>{{ t.tempat || '—' }}</td>
                <td><span :class="['spill', t.kehadiran==='Hadir'?'sp-green':'sp-red']">{{ t.kehadiran || 'Belum' }}</span></td>
                <td><strong class="paid-cell">{{ t.nilai || '—' }}</strong></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- BERKAS -->
        <div v-if="detailTab==='Berkas'" class="p20">
          <div v-if="!detailData.berkas" class="empty-cell">Memuat...</div>
          <div v-else-if="detailData.berkas.length===0" class="empty-cell">Tidak ada syarat berkas yang terdaftar</div>
          <div v-else class="berkas-list">
            <div v-for="b in detailData.berkas" :key="b.id_syarat" class="berkas-item">
              <div class="berkas-info">
                <span class="berkas-nama">{{ b.nama_berkas }}</span>
                <span v-if="b.is_wajib==1" class="badge-wajib">WAJIB</span>
              </div>
              <select :value="b.status" @change="updateBerkas(detailPendaftar.id, b, $event.target.value)" class="berkas-select">
                <option value="Belum Ada">Belum Ada</option>
                <option value="Ada">Ada</option>
                <option value="Terverifikasi">Terverifikasi</option>
                <option value="Ditolak">Ditolak</option>
              </select>
            </div>
          </div>
        </div>
        <!-- PEMBAYARAN -->
        <div v-if="detailTab==='Pembayaran'" class="p20">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px">
            <h4 style="color:#94a3b8">Riwayat Pembayaran</h4>
            <button @click="openBayarForPendaftar(detailPendaftar)" class="btn-primary btn-sm">+ Catat Bayar</button>
          </div>
          <div v-if="!detailData.pembayaran" class="empty-cell">Memuat...</div>
          <div v-else-if="detailData.pembayaran.length===0" class="empty-cell">Belum ada riwayat pembayaran</div>
          <table v-else class="data-table">
            <thead><tr><th>No. Kwitansi</th><th>Jumlah</th><th>Tgl Bayar</th><th>Metode</th></tr></thead>
            <tbody>
              <tr v-for="b in detailData.pembayaran" :key="b.id">
                <td><code>{{ b.nomor_kwitansi }}</code></td>
                <td class="paid-cell">{{ formatRp(b.jumlah) }}</td>
                <td>{{ formatDate(b.tanggal_bayar) }}</td>
                <td><span class="badge-info">{{ b.metode_bayar }}</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: Tambah Jadwal Tes ===== -->
    <div v-if="showJadwalForm" class="modal-overlay" @click.self="showJadwalForm=false">
      <div class="modal-box">
        <div class="modal-head"><h3>📅 Tambah Jadwal Tes</h3><button @click="showJadwalForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Nama Tes *</label><input v-model="formJadwal.nama_tes" class="fi" placeholder="contoh: Tes Tulis Gelombang 1" /></div>
          <div class="fg"><label>Tanggal</label><input v-model="formJadwal.tanggal" type="date" class="fi" /></div>
          <div class="fg"><label>Jam</label><input v-model="formJadwal.jam" type="time" class="fi" /></div>
          <div class="fg"><label>Tempat</label><input v-model="formJadwal.tempat" class="fi" placeholder="Ruang / Lokasi" /></div>
          <div class="fg"><label>Kuota Peserta</label><input v-model="formJadwal.kuota" type="number" class="fi" placeholder="0" /></div>
        </div>
        <div class="modal-actions"><button @click="showJadwalForm=false" class="btn-secondary">Batal</button><button @click="saveJadwal" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Jadwal' }}</button></div>
      </div>
    </div>

    <!-- ===== MODAL: Kelola Peserta Jadwal ===== -->
    <div v-if="selectedJadwal" class="modal-overlay" @click.self="selectedJadwal=null">
      <div class="modal-box modal-wide">
        <div class="modal-head">
          <h3>👥 Peserta — {{ selectedJadwal.nama_tes }}</h3>
          <button @click="selectedJadwal=null" class="modal-close">✕</button>
        </div>
        <div class="p20">
          <div class="peserta-add-bar">
            <select v-model="addPesertaId" class="fi" style="flex:1">
              <option value="">— Pilih Pendaftar (Pending) —</option>
              <option v-for="p in pendaftarPending" :key="p.id" :value="p.id">{{ p.nama_lengkap }} ({{ p.nomor_pendaftaran }})</option>
            </select>
            <button @click="addPeserta" class="btn-primary" :disabled="!addPesertaId">+ Tambah ke Jadwal</button>
          </div>
          <table class="data-table" style="margin-top:16px">
            <thead><tr><th>#</th><th>Pendaftar</th><th>No. Daftar</th><th>Kehadiran</th><th>Nilai</th><th>Aksi</th></tr></thead>
            <tbody>
              <tr v-if="pesertaJadwal.length===0"><td colspan="6" class="empty-cell">Belum ada peserta di jadwal ini</td></tr>
              <tr v-for="(p,i) in pesertaJadwal" :key="p.id">
                <td class="num">{{ i+1 }}</td>
                <td class="name-cell">{{ p.nama_lengkap }}</td>
                <td><code>{{ p.nomor_pendaftaran }}</code></td>
                <td>
                  <select :value="p.kehadiran" @change="updateKehadiran(p.id, $event.target.value, p.nilai)" class="mini-select">
                    <option value="">Belum</option><option value="Hadir">Hadir</option><option value="Tidak Hadir">Tidak Hadir</option>
                  </select>
                </td>
                <td><input :value="p.nilai" @blur="updateKehadiran(p.id, p.kehadiran, $event.target.value)" type="number" class="mini-input" placeholder="0-100" /></td>
                <td><button @click="removePeserta(p.id)" class="ab ab-del">🗑️</button></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: Tambah Syarat Berkas ===== -->
    <div v-if="showSyaratForm" class="modal-overlay" @click.self="showSyaratForm=false">
      <div class="modal-box" style="max-width:420px">
        <div class="modal-head"><h3>📁 Tambah Syarat Berkas</h3><button @click="showSyaratForm=false" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full"><label>Nama Berkas *</label><input v-model="formSyarat.nama_berkas" class="fi" placeholder="contoh: Foto 3x4, Akta Kelahiran..." /></div>
          <div class="fg full" style="flex-direction:row;align-items:center;gap:10px">
            <input v-model="formSyarat.is_wajib" type="checkbox" true-value="1" false-value="0" id="wajib" style="width:16px;height:16px" />
            <label for="wajib" style="font-size:13px;color:#e2e8f0;text-transform:none;letter-spacing:0">Berkas Wajib</label>
          </div>
        </div>
        <div class="modal-actions"><button @click="showSyaratForm=false" class="btn-secondary">Batal</button><button @click="saveSyarat" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan' }}</button></div>
      </div>
    </div>

    <!-- ===== MODAL: Catat Pembayaran ===== -->
    <div v-if="showBayarForm" class="modal-overlay" @click.self="closeBayar">
      <div class="modal-box" style="max-width:500px">
        <div class="modal-head"><h3>💳 Catat Pembayaran PPDB</h3><button @click="closeBayar" class="modal-close">✕</button></div>
        <div class="form-grid p20">
          <div class="fg full" v-if="!formBayar.id_pendaftar">
            <label>Pendaftar</label>
            <select v-model="formBayar.id_pendaftar" class="fi">
              <option value="">— Pilih Pendaftar —</option>
              <option v-for="p in pendaftar" :key="p.id" :value="p.id">{{ p.nama_lengkap }} ({{ p.nomor_pendaftaran }})</option>
            </select>
          </div>
          <div class="fg full" v-else>
            <label>Pendaftar</label>
            <div class="fi" style="color:#c4b5fd;font-weight:600">{{ pendaftar.find(p=>p.id==formBayar.id_pendaftar)?.nama_lengkap || 'Dipilih dari detail' }}</div>
          </div>
          <div class="fg"><label>Jumlah *</label><input v-model="formBayar.jumlah" type="number" class="fi" placeholder="0" /></div>
          <div class="fg"><label>Tanggal Bayar</label><input v-model="formBayar.tanggal_bayar" type="date" class="fi" /></div>
          <div class="fg"><label>Metode</label>
            <select v-model="formBayar.metode_bayar" class="fi">
              <option value="Tunai">Tunai</option><option value="Transfer">Transfer</option><option value="QRIS">QRIS</option>
            </select>
          </div>
          <div class="fg"><label>Keterangan</label><input v-model="formBayar.keterangan" class="fi" /></div>
        </div>
        <div class="modal-actions"><button @click="closeBayar" class="btn-secondary">Batal</button><button @click="savePembayaran" class="btn-primary" :disabled="saving">{{ saving?'Menyimpan...':'Simpan Pembayaran' }}</button></div>
      </div>
    </div>

    <!-- Toast -->
    <transition name="toast">
      <div v-if="toast.show" :class="['toast', 'toast-'+toast.type]">{{ toast.message }}</div>
    </transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const router  = useRouter()
const route   = useRoute()
const API     = 'http://127.0.0.1:8080/api'
const token   = localStorage.getItem('jwt_token')
const user    = JSON.parse(localStorage.getItem('user_info') || '{}')
const userInitial = computed(() => (user.nama_lengkap || 'U').charAt(0).toUpperCase())
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab  = ref('pendaftar')
const loading    = ref(false)
const saving     = ref(false)
const toast      = ref({ show: false, message: '', type: 'success' })

const pendaftar  = ref([])
const jadwal     = ref([])
const syarat     = ref([])
const allPembayaran = ref([])
const tahunAjaran   = ref([])
const stats      = ref({ total:0, pending:0, lulus:0, tidak_lulus:0, terdaftar:0, total_jadwal:0 })

const filterStatus = ref('')
const searchQ    = ref('')

// Modal states
const showAddForm    = ref(false)
const showJadwalForm = ref(false)
const showSyaratForm = ref(false)
const showBayarForm  = ref(false)
const detailPendaftar = ref(null)
const detailTab      = ref('Info')
const detailData     = ref({ tes: null, berkas: null, pembayaran: null })
const selectedJadwal = ref(null)
const pesertaJadwal  = ref([])
const addPesertaId   = ref('')

// Forms
const formPendaftar = ref({ nama_lengkap:'', jenis_kelamin:'L', tempat_lahir:'', tanggal_lahir:'', alamat:'', no_hp_ortu:'', status_seleksi:'Pending', id_tahun_ajaran:'' })
const formJadwal    = ref({ nama_tes:'', tanggal:'', jam:'', tempat:'', kuota:0 })
const formSyarat    = ref({ nama_berkas:'', is_wajib:'0' })
const formBayar     = ref({ id_pendaftar:'', jumlah:'', tanggal_bayar: new Date().toISOString().slice(0,10), metode_bayar:'Tunai', keterangan:'' })

// ===== COMPUTED =====
const tabs = [
  { key:'pendaftar',  label:'Pendaftar', icon:'👥', color:'blue' },
  { key:'jadwal',     label:'Jadwal Tes', icon:'📅', color:'teal' },
  { key:'berkas',     label:'Berkas',    icon:'📁', color:'orange' },
  { key:'pembayaran', label:'Pembayaran', icon:'💳', color:'green' },
]

const filterOpts = [
  { v:'', l:'🔍 Semua', c:'all' },
  { v:'Pending', l:'⏳ Pending', c:'yellow' },
  { v:'Lulus', l:'✅ Lulus', c:'green' },
  { v:'Tidak Lulus', l:'❌ Tidak Lulus', c:'red' },
  { v:'Santri Terdaftar', l:'🎓 Terdaftar', c:'purple' },
]

const ppdbPage = ref(1)
const ppdbLimit = ref(10)
const ppdbTotal = ref(0)
const ppdbTotalPages = ref(0)
const allPendingPendaftar = ref([])

const filteredPendaftar = computed(() => pendaftar.value)

const pendaftarPending = computed(() => allPendingPendaftar.value)
const pendaftarLulus   = computed(() => pendaftar.value.filter(p => p.status_seleksi === 'Lulus' || p.status_seleksi === 'Santri Terdaftar'))

// ===== HELPERS =====
function pillClass(s) {
  if (s==='Lulus') return 'green'
  if (s==='Pending') return 'yellow'
  if (s==='Santri Terdaftar') return 'purple'
  return 'red'
}
function formatDate(d) { if(!d) return '—'; return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) }
function formatRp(n) { if(!n) return 'Rp 0'; return 'Rp ' + parseInt(n).toLocaleString('id-ID') }
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3500) }

// ===== TAB SWITCH =====
async function switchTab(key) {
  router.push({ path: '/ppdb', query: { tab: key } })
}

// ===== FETCH =====
async function fetchPendaftar(page = 1) {
  ppdbPage.value = page
  try {
    const res = await axios.get(`${API}/ppdb/pendaftar`, {
      params: {
        page: ppdbPage.value,
        limit: ppdbLimit.value,
        status: filterStatus.value,
        q: searchQ.value
      },
      headers
    })
    pendaftar.value = res.data.data || []
    if (res.data.pagination) {
      ppdbTotal.value = res.data.pagination.total || 0
      ppdbTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch (err) {
    showNotif('Gagal memuat data pendaftar', 'error')
  }
}

async function fetchAll() {
  loading.value = true
  try {
    const [rS, rTA] = await Promise.all([
      axios.get(`${API}/ppdb/stats`, { headers }),
      axios.get(`${API}/ppdb/tahun-ajaran`, { headers }),
      fetchPendaftar(1)
    ])
    stats.value       = rS.data.data || {}
    tahunAjaran.value = rTA.data.data || []
  } catch { showNotif('Gagal memuat data PPDB', 'error') }
  finally { loading.value = false }
}

watch(ppdbLimit, () => {
  ppdbPage.value = 1
  fetchPendaftar(1)
})
watch(filterStatus, () => {
  ppdbPage.value = 1
  fetchPendaftar(1)
})

let ppdbSearchTimeout = null
function debouncePpdbSearch() {
  if (ppdbSearchTimeout) clearTimeout(ppdbSearchTimeout)
  ppdbSearchTimeout = setTimeout(() => {
    fetchPendaftar(1)
  }, 400)
}

function changePpdbPage(page) {
  if (page < 1 || page > ppdbTotalPages.value) return
  ppdbPage.value = page
  fetchPendaftar(page)
}

const ppdbPaginationRange = computed(() => {
  const current = ppdbPage.value
  const total = ppdbTotalPages.value
  const delta = 2
  const range = []
  let start = Math.max(1, current - delta)
  let end = Math.min(total, current + delta)
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  return range
})

async function fetchJadwal() {
  loading.value = true
  try { jadwal.value = (await axios.get(`${API}/ppdb/jadwal`, { headers })).data.data || [] }
  catch { showNotif('Gagal memuat jadwal', 'error') }
  finally { loading.value = false }
}

async function fetchSyarat() {
  try { syarat.value = (await axios.get(`${API}/ppdb/syarat`, { headers })).data.data || [] }
  catch {}
}

async function fetchAllPembayaran() {
  loading.value = true
  try {
    // Ambil pembayaran dari semua pendaftar yang ada pembayarannya
    // (Karena API per pendaftar, kita ambil semua secara parallel)
    const ids = pendaftar.value.map(p => p.id)
    const results = await Promise.all(ids.map(id => axios.get(`${API}/ppdb/pembayaran/${id}`, { headers }).catch(()=>({data:{data:[]}}))))
    const all = []
    results.forEach((r, i) => {
      const nama = pendaftar.value[i]?.nama_lengkap || ''
      ;(r.data.data || []).forEach(b => all.push({ ...b, nama_lengkap: nama }))
    })
    allPembayaran.value = all.sort((a,b) => new Date(b.tanggal_bayar)-new Date(a.tanggal_bayar))
  } catch { showNotif('Gagal memuat pembayaran', 'error') }
  finally { loading.value = false }
}

// ===== PENDAFTAR CRUD =====
async function savePendaftar() {
  if (!formPendaftar.value.nama_lengkap) return showNotif('Nama wajib diisi', 'error')
  saving.value = true
  try {
    const r = await axios.post(`${API}/ppdb/pendaftar/save`, formPendaftar.value, { headers })
    showAddForm.value = false
    formPendaftar.value = { nama_lengkap:'', jenis_kelamin:'L', tempat_lahir:'', tanggal_lahir:'', alamat:'', no_hp_ortu:'', status_seleksi:'Pending', id_tahun_ajaran:'' }
    await fetchAll()
    showNotif(`Pendaftar berhasil ditambahkan! ${r.data.nomor}`)
  } catch { showNotif('Gagal menyimpan pendaftar', 'error') }
  finally { saving.value = false }
}

async function setStatus(id, status) {
  const l = { lulus:'Lulus', gagal:'Tidak Lulus', terdaftar:'Santri Terdaftar' }[status]
  if (!confirm(`Ubah status menjadi "${l}"?`)) return
  try {
    await axios.post(`${API}/ppdb/pendaftar/status/${id}/${status}`, {}, { headers })
    await fetchAll()
    showNotif(`Status berhasil diubah menjadi ${l}!`)
  } catch { showNotif('Gagal mengubah status', 'error') }
}

async function deletePendaftar(id, nama) {
  if (!confirm(`Hapus pendaftar "${nama}"?`)) return
  try {
    await axios.delete(`${API}/ppdb/pendaftar/delete/${id}`, { headers })
    await fetchAll()
    showNotif('Pendaftar berhasil dihapus')
  } catch { showNotif('Gagal menghapus', 'error') }
}

// ===== DETAIL MODAL =====
async function openDetail(p) {
  detailPendaftar.value = p
  detailTab.value = 'Info'
  detailData.value = { tes: null, berkas: null, pembayaran: null }
  const r = await axios.get(`${API}/ppdb/pendaftar/${p.id}`, { headers })
  const d = r.data.data
  detailData.value = {
    tes:        d.riwayat_tes || [],
    berkas:     null, // load saat tab Berkas dibuka
    pembayaran: d.riwayat_pembayaran || []
  }
}

async function loadDetailBerkas(id) {
  const r = await axios.get(`${API}/ppdb/berkas/${id}`, { headers })
  detailData.value.berkas = r.data.data || []
}

// Watch detail tab change
async function watchDetailTab(tab) {
  detailTab.value = tab
  if (tab === 'Berkas' && detailPendaftar.value && detailData.value.berkas === null) {
    await loadDetailBerkas(detailPendaftar.value.id)
  }
}

// ===== JADWAL TES =====
async function saveJadwal() {
  if (!formJadwal.value.nama_tes) return showNotif('Nama tes wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/ppdb/jadwal/save`, formJadwal.value, { headers })
    showJadwalForm.value = false
    formJadwal.value = { nama_tes:'', tanggal:'', jam:'', tempat:'', kuota:0 }
    await fetchJadwal()
    await fetchAll() // update stats
    showNotif('Jadwal tes berhasil disimpan!')
  } catch { showNotif('Gagal menyimpan jadwal', 'error') }
  finally { saving.value = false }
}

async function deleteJadwal(id, nama) {
  if (!confirm(`Hapus jadwal "${nama}"? Semua peserta di jadwal ini juga akan dihapus.`)) return
  try {
    await axios.delete(`${API}/ppdb/jadwal/delete/${id}`, { headers })
    await fetchJadwal()
    await fetchAll()
    showNotif('Jadwal berhasil dihapus')
  } catch { showNotif('Gagal menghapus jadwal', 'error') }
}

async function openPesertaJadwal(j) {
  selectedJadwal.value = j
  addPesertaId.value   = ''
  const [r, rp] = await Promise.all([
    axios.get(`${API}/ppdb/jadwal/${j.id}/peserta`, { headers }),
    axios.get(`${API}/ppdb/pendaftar`, { params: { status: 'Pending', limit: 1000 }, headers })
  ])
  pesertaJadwal.value  = r.data.data || []
  allPendingPendaftar.value = rp.data.data || []
}

async function addPeserta() {
  if (!addPesertaId.value) return
  try {
    await axios.post(`${API}/ppdb/jadwal/peserta/add`, { id_jadwal: selectedJadwal.value.id, id_pendaftar: addPesertaId.value }, { headers })
    addPesertaId.value = ''
    await openPesertaJadwal(selectedJadwal.value)
    showNotif('Peserta berhasil ditambahkan ke jadwal!')
  } catch (e) { showNotif(e.response?.data?.message || 'Gagal menambahkan peserta', 'error') }
}

async function removePeserta(id) {
  if (!confirm('Hapus peserta ini dari jadwal?')) return
  try {
    await axios.delete(`${API}/ppdb/jadwal/peserta/remove/${id}`, { headers })
    await openPesertaJadwal(selectedJadwal.value)
    showNotif('Peserta dihapus dari jadwal')
  } catch { showNotif('Gagal menghapus peserta', 'error') }
}

async function updateKehadiran(id, kehadiran, nilai) {
  try {
    await axios.post(`${API}/ppdb/jadwal/kehadiran`, { id, kehadiran, nilai }, { headers })
    showNotif('Kehadiran/nilai diperbarui')
    await openPesertaJadwal(selectedJadwal.value)
  } catch {}
}

// ===== BERKAS =====
async function openBerkas(p) {
  detailPendaftar.value = p
  detailTab.value = 'Berkas'
  detailData.value = { tes: [], berkas: null, pembayaran: [] }
  await loadDetailBerkas(p.id)
}

async function saveSyarat() {
  if (!formSyarat.value.nama_berkas) return showNotif('Nama berkas wajib diisi', 'error')
  saving.value = true
  try {
    await axios.post(`${API}/ppdb/syarat/save`, formSyarat.value, { headers })
    showSyaratForm.value = false
    formSyarat.value = { nama_berkas:'', is_wajib:'0' }
    await fetchSyarat()
    showNotif('Syarat berkas ditambahkan!')
  } catch { showNotif('Gagal menyimpan syarat', 'error') }
  finally { saving.value = false }
}

async function deleteSyarat(id, nama) {
  if (!confirm(`Hapus syarat berkas "${nama}"?`)) return
  try {
    await axios.delete(`${API}/ppdb/syarat/delete/${id}`, { headers })
    await fetchSyarat()
    showNotif('Syarat berkas dihapus')
  } catch { showNotif('Gagal menghapus syarat', 'error') }
}

async function updateBerkas(id_pendaftar, b, newStatus) {
  try {
    await axios.post(`${API}/ppdb/berkas/update`, { id_pendaftar, id_berkas: b.id_berkas, jenis_berkas: b.nama_berkas, status: newStatus }, { headers })
    await loadDetailBerkas(id_pendaftar)
    showNotif('Status berkas diperbarui!')
  } catch { showNotif('Gagal update berkas', 'error') }
}

// ===== PEMBAYARAN =====
function openBayarForPendaftar(p) {
  formBayar.value = { id_pendaftar: p.id, jumlah:'', tanggal_bayar: new Date().toISOString().slice(0,10), metode_bayar:'Tunai', keterangan:'' }
  showBayarForm.value = true
}

function closeBayar() {
  showBayarForm.value = false
  formBayar.value = { id_pendaftar:'', jumlah:'', tanggal_bayar: new Date().toISOString().slice(0,10), metode_bayar:'Tunai', keterangan:'' }
}

async function savePembayaran() {
  if (!formBayar.value.id_pendaftar || !formBayar.value.jumlah) return showNotif('Pendaftar dan jumlah wajib diisi', 'error')
  saving.value = true
  try {
    const r = await axios.post(`${API}/ppdb/pembayaran/save`, formBayar.value, { headers })
    closeBayar()
    await fetchAll()
    // Refresh detail jika sedang buka
    if (detailPendaftar.value) {
      const rd = await axios.get(`${API}/ppdb/pendaftar/${detailPendaftar.value.id}`, { headers })
      detailData.value.pembayaran = rd.data.data.riwayat_pembayaran || []
    }
    showNotif(`Pembayaran dicatat! Kwitansi: ${r.data.kwitansi}`)
  } catch { showNotif('Gagal menyimpan pembayaran', 'error') }
  finally { saving.value = false }
}

function handleLogout() { localStorage.clear(); router.push('/login') }
watch(() => route.query.tab, async (newTab) => {
  const tab = newTab || 'pendaftar'
  activeTab.value = tab
  if (pendaftar.value.length === 0) {
    await fetchAll()
  }
  if (tab === 'jadwal') await fetchJadwal()
  if (tab === 'berkas') await fetchSyarat()
  if (tab === 'pembayaran') await fetchAllPembayaran()
}, { immediate: true })
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
* { margin:0; padding:0; box-sizing:border-box; }
.dashboard-container { display:flex; height:100vh; background:#0f1117; font-family:'Inter',sans-serif; color:#e2e8f0; overflow:hidden; }

/* Main */
.main-content { flex:1; overflow-y:auto; display:flex; flex-direction:column; }
.content-header { height: 80px; padding: 0 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(26,29,46,0.5); gap: 14px; flex-shrink: 0; }
.content-header h1 { font-size:22px; font-weight:700; }
.content-header p { font-size:13px; color:#64748b; margin-top:3px; }
.header-tabs { display:flex; gap:6px; flex-wrap:wrap; }
.tab-btn { padding:8px 14px; border-radius:8px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:12px; cursor:pointer; font-family:'Inter',sans-serif; transition:all 0.2s; white-space:nowrap; }
.active-blue   { background:rgba(96,165,250,0.15); color:#60a5fa; border-color:rgba(96,165,250,0.3); font-weight:600; }
.active-teal   { background:rgba(45,212,191,0.15); color:#2dd4bf; border-color:rgba(45,212,191,0.3); font-weight:600; }
.active-orange { background:rgba(251,146,60,0.15); color:#fb923c; border-color:rgba(251,146,60,0.3); font-weight:600; }
.active-green  { background:rgba(52,211,153,0.15); color:#34d399; border-color:rgba(52,211,153,0.3); font-weight:600; }

/* Stats */
.stats-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:12px; padding:18px 32px 0; }
.stat-card { background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:14px 16px; display:flex; align-items:center; gap:12px; transition:transform 0.2s; }
.stat-card:hover { transform:translateY(-2px); }
.si { font-size:22px; }
.sn { font-size:22px; font-weight:700; }
.sl { font-size:11px; color:#64748b; margin-top:3px; }
.s-blue   { border-color:rgba(96,165,250,0.2); } .s-blue .sn { color:#60a5fa; }
.s-yellow { border-color:rgba(251,191,36,0.2); } .s-yellow .sn { color:#fbbf24; }
.s-green  { border-color:rgba(52,211,153,0.2); } .s-green .sn { color:#34d399; }
.s-red    { border-color:rgba(248,113,113,0.2); } .s-red .sn { color:#f87171; }
.s-purple { border-color:rgba(167,139,250,0.2); } .s-purple .sn { color:#a78bfa; }
.s-teal   { border-color:rgba(45,212,191,0.2);  } .s-teal .sn { color:#2dd4bf; }

/* Filter inner */
.filter-bar-inner { display:flex; gap:6px; padding:12px 24px; border-bottom:1px solid rgba(255,255,255,0.05); flex-wrap:wrap; }
.fchip { padding:5px 14px; border-radius:20px; border:1px solid rgba(255,255,255,0.1); background:transparent; color:#94a3b8; font-size:11px; cursor:pointer; font-family:'Inter',sans-serif; transition:all 0.2s; }
.fchip-on-all    { background:rgba(255,255,255,0.08); color:#e2e8f0; }
.fchip-on-yellow { background:rgba(251,191,36,0.15); color:#fbbf24; border-color:rgba(251,191,36,0.3); }
.fchip-on-green  { background:rgba(52,211,153,0.15); color:#34d399; border-color:rgba(52,211,153,0.3); }
.fchip-on-red    { background:rgba(248,113,113,0.15); color:#f87171; border-color:rgba(248,113,113,0.3); }
.fchip-on-purple { background:rgba(167,139,250,0.15); color:#a78bfa; border-color:rgba(167,139,250,0.3); }

/* Card */
.card { margin:16px 32px 0; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:16px; overflow:hidden; }
.card:last-child { margin-bottom:24px; }
.card-header { padding:18px 24px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid rgba(255,255,255,0.06); flex-wrap:wrap; gap:10px; }
.card-header h3 { font-size:15px; font-weight:600; }
.header-right { display:flex; gap:10px; align-items:center; }
.two-col { display:grid; grid-template-columns:1fr 1fr; gap:0 16px; padding:0 32px 24px; margin-top:16px; }
.two-col .card { margin:0; }

/* Table */
.table-wrapper { overflow-x:auto; }
.data-table { width:100%; border-collapse:collapse; font-size:13px; }
.data-table th { padding:11px 14px; text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; color:#64748b; letter-spacing:0.5px; border-bottom:1px solid rgba(255,255,255,0.06); white-space:nowrap; }
.data-table td { padding:12px 14px; border-bottom:1px solid rgba(255,255,255,0.04); vertical-align:middle; }
.data-table tr:last-child td { border-bottom:none; }
.data-table tr:hover td { background:rgba(255,255,255,0.02); }
.num { color:#475569; width:32px; }
.name-cell { font-weight:600; color:#c4b5fd; }
.paid-cell { color:#34d399; font-weight:600; }
.muted { color:#64748b; font-size:12px; }
.loading-cell,.empty-cell { text-align:center; color:#64748b; padding:36px !important; }
code { background:rgba(255,255,255,0.07); padding:2px 7px; border-radius:5px; font-size:11px; }

/* Status Pills */
.spill { display:inline-flex; align-items:center; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; white-space:nowrap; }
.sp-green  { background:rgba(52,211,153,0.15); color:#34d399; border:1px solid rgba(52,211,153,0.3); }
.sp-yellow { background:rgba(251,191,36,0.15); color:#fbbf24; border:1px solid rgba(251,191,36,0.3); }
.sp-red    { background:rgba(248,113,113,0.15); color:#f87171; border:1px solid rgba(248,113,113,0.3); }
.sp-purple { background:rgba(167,139,250,0.15); color:#a78bfa; border:1px solid rgba(167,139,250,0.3); }

/* Badge number */
.badge-num { display:inline-block; padding:2px 10px; border-radius:12px; font-size:12px; font-weight:600; background:rgba(255,255,255,0.06); color:#94a3b8; }
.quota-full { background:rgba(248,113,113,0.15); color:#f87171; }
.badge-info { background:rgba(99,102,241,0.15); color:#818cf8; padding:2px 8px; border-radius:12px; font-size:11px; }
.badge-wajib { background:rgba(251,146,60,0.15); color:#fb923c; padding:2px 7px; border-radius:10px; font-size:10px; font-weight:700; margin-left:6px; }

/* Action buttons */
.action-group { display:flex; gap:4px; flex-wrap:wrap; }
.ab { width:28px; height:28px; display:flex; align-items:center; justify-content:center; border-radius:7px; border:1px solid transparent; cursor:pointer; font-size:13px; background:rgba(255,255,255,0.04); transition:all 0.2s; }
.ab:hover { transform:scale(1.1); }
.ab-green:hover  { background:rgba(52,211,153,0.15); }
.ab-red:hover    { background:rgba(248,113,113,0.15); }
.ab-purple:hover { background:rgba(167,139,250,0.15); }
.ab-blue:hover   { background:rgba(96,165,250,0.15); }
.ab-del:hover    { background:rgba(248,113,113,0.1); }

/* Buttons */
.btn-primary { padding:9px 18px; background:linear-gradient(135deg,#6366f1,#8b5cf6); border:none; border-radius:8px; color:white; font-size:13px; font-weight:600; cursor:pointer; font-family:'Inter',sans-serif; transition:opacity 0.2s; white-space:nowrap; }
.btn-primary.btn-sm { padding:6px 14px; font-size:12px; }
.btn-primary:disabled { opacity:0.5; cursor:not-allowed; }
.btn-primary:hover { opacity:0.85; }
.btn-secondary { padding:9px 18px; background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.1); border-radius:8px; color:#e2e8f0; font-size:13px; cursor:pointer; font-family:'Inter',sans-serif; }
.search-input { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:8px 14px; color:#e2e8f0; font-size:13px; outline:none; width:200px; font-family:'Inter',sans-serif; }

/* Modal */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.65); backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center; z-index:100; }
.modal-box { background:linear-gradient(145deg,#1a1d2e,#1e2338); border:1px solid rgba(99,102,241,0.3); border-radius:20px; width:560px; max-width:95vw; max-height:88vh; overflow-y:auto; }
.modal-wide { width:760px; }
.modal-head { display:flex; align-items:center; justify-content:space-between; padding:20px 24px; border-bottom:1px solid rgba(255,255,255,0.07); }
.modal-head h3 { font-size:17px; font-weight:700; color:#a5b4fc; }
.modal-close { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:8px; width:28px; height:28px; color:#94a3b8; cursor:pointer; font-size:13px; display:flex; align-items:center; justify-content:center; }
.modal-close:hover { background:rgba(248,113,113,0.15); color:#f87171; }
.p20 { padding:20px 24px; }
.modal-actions { display:flex; gap:12px; justify-content:flex-end; padding:16px 24px; border-top:1px solid rgba(255,255,255,0.06); }

/* Form */
.form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.fg { display:flex; flex-direction:column; gap:6px; }
.full { grid-column:1/-1; }
.fg label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:0.6px; }
.fi { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:8px; padding:9px 13px; color:#e2e8f0; font-size:13px; outline:none; font-family:'Inter',sans-serif; width:100%; transition:border-color 0.2s; }
.fi:focus { border-color:#6366f1; }
.fi option { background:#1a1d2e; }

/* Detail tabs */
.detail-tabs { display:flex; gap:0; border-bottom:1px solid rgba(255,255,255,0.07); padding:0 24px; }
.dtab { padding:12px 18px; border:none; background:none; color:#64748b; font-size:13px; cursor:pointer; font-family:'Inter',sans-serif; border-bottom:2px solid transparent; transition:all 0.2s; }
.dtab-on { color:#a5b4fc; border-bottom-color:#6366f1; font-weight:600; }

/* Detail grid */
.detail-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
.di { background:rgba(255,255,255,0.03); border-radius:10px; padding:12px 16px; }
.dl { font-size:11px; color:#64748b; font-weight:600; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px; }
.dv { font-size:13px; color:#e2e8f0; }

/* Berkas list */
.berkas-list { display:flex; flex-direction:column; gap:8px; }
.berkas-item { display:flex; align-items:center; justify-content:space-between; gap:10px; background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.06); border-radius:10px; padding:12px 16px; }
.berkas-info { display:flex; align-items:center; flex-wrap:wrap; gap:6px; }
.berkas-nama { font-size:13px; font-weight:500; color:#e2e8f0; }
.berkas-select { background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.1); border-radius:7px; padding:5px 10px; color:#e2e8f0; font-size:12px; outline:none; font-family:'Inter',sans-serif; }

/* Syarat list */
.syarat-list { list-style:none; padding:12px 20px 16px; display:flex; flex-direction:column; gap:8px; }
.syarat-item { display:flex; align-items:center; justify-content:space-between; padding:10px 14px; background:rgba(255,255,255,0.03); border-radius:10px; border:1px solid rgba(255,255,255,0.06); }
.syarat-nama { font-size:13px; font-weight:500; }

/* Peserta jadwal */
.peserta-add-bar { display:flex; gap:10px; align-items:center; }
.mini-select { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:6px; padding:4px 8px; color:#e2e8f0; font-size:12px; outline:none; }
.mini-input { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:6px; padding:4px 8px; color:#e2e8f0; font-size:12px; outline:none; width:70px; }

/* Toast */
.toast { position:fixed; bottom:24px; right:24px; padding:12px 20px; border-radius:12px; font-size:13px; font-weight:600; z-index:200; pointer-events:none; }
.toast-success { background:rgba(52,211,153,0.95); color:#0f1117; }
.toast-error   { background:rgba(248,113,113,0.95); color:#fff; }
.toast-enter-active,.toast-leave-active { transition:all 0.3s ease; }
.toast-enter-from,.toast-leave-to { opacity:0; transform:translateY(10px); }
</style>
