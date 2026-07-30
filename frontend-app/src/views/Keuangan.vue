<template>
  <div class="dashboard-container">
    <Sidebar />

    <main class="main-content">
      <header class="content-header">
        <div>
          <h1>Keuangan &amp; Akuntansi</h1>
          <p>Kelola Chart of Accounts (COA), jurnal umum double-entry, buku besar, dan laporan keuangan</p>
        </div>
        <div class="header-tabs">
          <button v-for="t in tabs" :key="t.key" :class="['tab-btn', activeTab===t.key?'active-'+t.color:'']" @click="switchTab(t.key)">
            {{ t.icon }} {{ t.label }}
          </button>
        </div>
      </header>

      <!-- STATS SUMMARY GRID -->
      <div class="stats-grid">
        <div class="stat-card blue">
          <div class="stat-icon">📋</div>
          <div>
            <div class="stat-num">{{ akun.length }}</div>
            <div class="stat-lbl">Total Akun (COA)</div>
          </div>
        </div>
        <div class="stat-card purple">
          <div class="stat-icon">📂</div>
          <div>
            <div class="stat-num">{{ jurnalTotal }}</div>
            <div class="stat-lbl">Total Jurnal</div>
          </div>
        </div>
        <div class="stat-card green">
          <div class="stat-icon">📈</div>
          <div>
            <div class="stat-num">{{ lrData ? formatRupiah(lrData.laba_bersih) : '—' }}</div>
            <div class="stat-lbl">Laba Bersih</div>
          </div>
        </div>
        <div class="stat-card gold">
          <div class="stat-icon">⚖️</div>
          <div>
            <div class="stat-num">{{ neracaData ? (isNeracaBalanced ? 'Seimbang' : 'Selisih') : 'Neraca' }}</div>
            <div class="stat-lbl">Status Balansi</div>
          </div>
        </div>
      </div>

      <!-- ========== TAB 1: COA / DAFTAR AKUN ========== -->
      <section v-if="activeTab==='akun'">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Daftar Akun (Chart of Accounts)</h3>
              <span class="card-subtitle">Master kode akun untuk pembukuan dan laporan keuangan</span>
            </div>
            <button @click="openAddAkun" class="btn-primary">+ Tambah Akun Baru</button>
          </div>
          
          <div class="search-bar">
            <input v-model="searchAkun" placeholder="🔍 Cari kode atau nama akun..." class="search-input" />
            <select v-model="filterKategoriAkun" class="filter-select">
              <option value="">Semua Kategori</option>
              <option value="Aset">Aset</option>
              <option value="Kewajiban">Kewajiban</option>
              <option value="Ekuitas">Ekuitas</option>
              <option value="Pendapatan">Pendapatan</option>
              <option value="Beban">Beban</option>
            </select>
          </div>

          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Kode Akun</th>
                  <th>Nama Akun</th>
                  <th>Kategori</th>
                  <th>Saldo Normal</th>
                  <th>Status</th>
                  <th style="text-align: center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading"><td colspan="6" class="loading-cell">Memuat data akun...</td></tr>
                <tr v-else-if="filteredAkun.length===0"><td colspan="6" class="empty-cell">Tidak ada akun ditemukan</td></tr>
                <tr v-for="a in filteredAkun" :key="a.id" class="table-row">
                  <td><code class="code-badge">{{ a.kode_akun }}</code></td>
                  <td class="name-cell">
                    <span v-if="a.parent_id" class="indent-tree">└─</span>
                    {{ a.nama_akun }}
                  </td>
                  <td><span :class="['badge', getKategoriBadge(a.kategori)]">{{ a.kategori }}</span></td>
                  <td>
                    <span :class="['balance-type', a.saldo_normal === 'Debit' ? 'debit-text' : 'kredit-text']">
                      {{ a.saldo_normal }}
                    </span>
                  </td>
                  <td><span :class="['badge', a.is_aktif ? 'badge-success' : 'badge-danger']">{{ a.is_aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                  <td style="text-align: center">
                    <div class="action-group">
                      <button @click="openEditAkun(a)" class="ab ab-blue" title="Edit Akun">✏️</button>
                      <button @click="deleteAkun(a.id, a.nama_akun)" class="ab ab-del" title="Hapus Akun">🗑️</button>
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
            <div>
              <h3>Jurnal Umum &amp; Transaksi</h3>
              <span class="card-subtitle">Pencatatan transaksi pembukuan berpasangan (Double-Entry)</span>
            </div>
            <div class="header-actions">
              <select v-model="jurnalLimit" class="filter-select select-limit">
                <option :value="10">10 baris</option>
                <option :value="25">25 baris</option>
                <option :value="50">50 baris</option>
                <option :value="100">100 baris</option>
              </select>
              <button @click="openAddJurnal" class="btn-primary">+ Buat Jurnal Baru</button>
            </div>
          </div>

          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th style="width: 50px">#</th>
                  <th style="width: 140px">Nomor Jurnal</th>
                  <th style="width: 120px">Tanggal</th>
                  <th>Keterangan</th>
                  <th style="width: 110px">Referensi</th>
                  <th style="width: 130px; text-align: right">Total (Rp)</th>
                  <th style="width: 320px">Rincian Transaksi</th>
                  <th style="width: 70px; text-align: center">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loading"><td colspan="8" class="loading-cell">Memuat data jurnal...</td></tr>
                <tr v-else-if="jurnals.length===0"><td colspan="8" class="empty-cell">Belum ada transaksi jurnal dicatat</td></tr>
                <tr v-for="(j, idx) in jurnals" :key="j.id" class="table-row">
                  <td>{{ (jurnalPage - 1) * jurnalLimit + idx + 1 }}</td>
                  <td><span class="badge badge-info">📄 {{ j.nomor_jurnal }}</span></td>
                  <td>{{ formatDate(j.tanggal) }}</td>
                  <td class="name-cell">{{ j.keterangan }}</td>
                  <td><code>{{ j.referensi || '—' }}</code></td>
                  <td style="text-align: right" class="paid-cell">{{ formatRupiah(calcJurnalTotal(j)) }}</td>
                  <td>
                    <div class="jurnal-details-container">
                      <div v-for="d in j.details" :key="d.id" class="jurnal-detail-item">
                        <span class="j-akun">{{ d.kode_akun }} - {{ d.nama_akun }}</span>
                        <div class="j-values">
                          <span v-if="d.debit > 0" class="j-val debit">D: {{ formatRupiah(d.debit) }}</span>
                          <span v-if="d.kredit > 0" class="j-val kredit">K: {{ formatRupiah(d.kredit) }}</span>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td style="text-align: center">
                    <button @click="deleteJurnal(j.id)" class="ab ab-del" title="Hapus Jurnal">🗑️</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="jurnalTotalPages > 1" class="pagination-bar">
            <div class="text-muted">
              Menampilkan <strong>{{ (jurnalPage - 1) * jurnalLimit + 1 }}</strong> - 
              <strong>{{ Math.min(jurnalPage * jurnalLimit, jurnalTotal) }}</strong> dari 
              <strong>{{ jurnalTotal }}</strong> transaksi
            </div>
            
            <div class="pagination-buttons">
              <button @click="changeJurnalPage(1)" :disabled="jurnalPage === 1" class="page-btn">« First</button>
              <button @click="changeJurnalPage(jurnalPage - 1)" :disabled="jurnalPage === 1" class="page-btn">‹ Prev</button>
              
              <button v-for="p in jurnalPaginationRange" :key="p" @click="changeJurnalPage(p)" :class="['page-btn', jurnalPage === p ? 'active' : '']">
                {{ p }}
              </button>
              
              <button @click="changeJurnalPage(jurnalPage + 1)" :disabled="jurnalPage === jurnalTotalPages" class="page-btn">Next ›</button>
              <button @click="changeJurnalPage(jurnalTotalPages)" :disabled="jurnalPage === jurnalTotalPages" class="page-btn">Last »</button>
            </div>
          </div>
        </div>
      </section>

      <!-- ========== TAB 3: BUKU BESAR ========== -->
      <section v-if="activeTab==='buku_besar'">
        <div class="card">
          <div class="card-header">
            <div>
              <h3>Buku Besar (General Ledger)</h3>
              <span class="card-subtitle">Rincian mutasi debet, kredit, dan saldo berjalan per akun</span>
            </div>
            <div class="header-actions">
              <select v-model="glParams.akun_id" class="filter-select" style="min-width: 220px;">
                <option value="">-- Pilih Akun --</option>
                <option v-for="a in akun" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
              </select>
              <input v-model="glParams.tgl_mulai" type="date" class="search-input" />
              <span class="date-sep">s/d</span>
              <input v-model="glParams.tgl_selesai" type="date" class="search-input" />
              <button @click="fetchBukuBesar" class="btn-primary-purple">🔍 Tampilkan</button>
            </div>
          </div>

          <div v-if="glData" class="gl-summary-grid">
            <div class="gl-sum-card">
              <span class="gl-label">Akun Terpilih</span>
              <strong class="gl-val">{{ glData.akun?.kode_akun }} - {{ glData.akun?.nama_akun }}</strong>
            </div>
            <div class="gl-sum-card">
              <span class="gl-label">Saldo Normal</span>
              <strong class="gl-val"><span :class="['balance-type', glData.akun?.saldo_normal === 'Debit' ? 'debit-text' : 'kredit-text']">{{ glData.akun?.saldo_normal }}</span></strong>
            </div>
            <div class="gl-sum-card">
              <span class="gl-label">Saldo Awal Periode</span>
              <strong class="gl-val paid-cell">{{ formatRupiah(glData.saldo_awal) }}</strong>
            </div>
          </div>

          <div class="table-wrapper">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Nomor Jurnal</th>
                  <th>Keterangan Transaksi</th>
                  <th style="text-align: right">Debit</th>
                  <th style="text-align: right">Kredit</th>
                  <th style="text-align: right">Saldo Berjalan</th>
                </tr>
              </thead>
              <tbody>
                <tr v-if="loadingGL"><td colspan="6" class="loading-cell">Memuat buku besar...</td></tr>
                <tr v-else-if="!glData"><td colspan="6" class="empty-cell">Silakan pilih akun dan rentang tanggal untuk melihat buku besar</td></tr>
                <tr v-else-if="glData.transaksi.length===0">
                  <td colspan="6" class="empty-cell">Tidak ada mutasi dalam periode ini. Saldo Akhir: {{ formatRupiah(glData.saldo_awal) }}</td>
                </tr>
                <template v-else>
                  <tr v-for="t in computedGLTransactions" :key="t.id" class="table-row">
                    <td>{{ formatDate(t.tanggal) }}</td>
                    <td><span class="badge badge-info">{{ t.nomor_jurnal }}</span></td>
                    <td class="name-cell">
                      {{ t.ket_jurnal }}
                      <span v-if="t.keterangan_item" class="muted">({{ t.keterangan_item }})</span>
                    </td>
                    <td style="text-align: right; color: #34d399; font-weight: 500;">
                      {{ t.debit > 0 ? formatRupiah(t.debit) : '—' }}
                    </td>
                    <td style="text-align: right; color: #f87171; font-weight: 500;">
                      {{ t.kredit > 0 ? formatRupiah(t.kredit) : '—' }}
                    </td>
                    <td style="text-align: right" class="paid-cell">{{ formatRupiah(t.running_balance) }}</td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      <!-- ========== TAB 4: LAPORAN KEUANGAN ========== -->
      <section v-if="activeTab==='laporan'">
        <div class="laporan-header-nav">
          <button :class="['sub-tab-btn', subTab==='laba_rugi'?'active':'']" @click="subTab='laba_rugi'">
            📈 Laporan Laba Rugi
          </button>
          <button :class="['sub-tab-btn', subTab==='neraca'?'active':'']" @click="subTab='neraca'">
            ⚖️ Laporan Neraca
          </button>
        </div>

        <!-- LABA RUGI -->
        <div v-if="subTab==='laba_rugi'" class="card">
          <div class="card-header">
            <div>
              <h3>Laporan Laba Rugi (Income Statement)</h3>
              <span class="card-subtitle">Ringkasan total pendapatan dan beban operasional</span>
            </div>
            <div class="header-actions">
              <input v-model="lrParams.tgl_mulai" type="date" class="search-input" />
              <span class="date-sep">s/d</span>
              <input v-model="lrParams.tgl_selesai" type="date" class="search-input" />
              <button @click="fetchLabaRugi" class="btn-primary-purple">⚡ Kalkulasi</button>
            </div>
          </div>

          <div class="laporan-wrapper p24" v-if="lrData">
            <!-- Pendapatan -->
            <div class="lap-section">
              <h4 class="lap-section-title text-green">📊 Pendapatan Operasional</h4>
              <div class="lap-row" v-for="p in lrData.pendapatan" :key="p.kode_akun">
                <span class="lap-akun-name"><code>{{ p.kode_akun }}</code> {{ p.nama_akun }}</span>
                <strong class="lap-val">{{ formatRupiah(p.saldo) }}</strong>
              </div>
              <div v-if="!lrData.pendapatan || lrData.pendapatan.length===0" class="empty-lap-row">Tidak ada pendapatan pada periode ini</div>
              <div class="lap-row total green-total">
                <span>Total Pendapatan</span>
                <strong>{{ formatRupiah(lrData.total_pendapatan) }}</strong>
              </div>
            </div>

            <!-- Beban -->
            <div class="lap-section">
              <h4 class="lap-section-title text-red">📉 Beban Operasional</h4>
              <div class="lap-row" v-for="b in lrData.beban" :key="b.kode_akun">
                <span class="lap-akun-name"><code>{{ b.kode_akun }}</code> {{ b.nama_akun }}</span>
                <strong class="lap-val">{{ formatRupiah(b.saldo) }}</strong>
              </div>
              <div v-if="!lrData.beban || lrData.beban.length===0" class="empty-lap-row">Tidak ada beban pada periode ini</div>
              <div class="lap-row total red-total">
                <span>Total Beban</span>
                <strong>{{ formatRupiah(lrData.total_beban) }}</strong>
              </div>
            </div>

            <!-- Net profit -->
            <div class="lap-grand-box" :class="lrData.laba_bersih >= 0 ? 'profit-box' : 'loss-box'">
              <div>
                <span class="grand-label">Laba (Rugi) Bersih Periode Ini</span>
                <p class="grand-sub">Selisih Pendapatan Operasional dikurangi Beban Operasional</p>
              </div>
              <strong class="grand-amount" :style="{color: lrData.laba_bersih >= 0 ? '#34d399' : '#f87171'}">
                {{ formatRupiah(lrData.laba_bersih) }}
              </strong>
            </div>
          </div>
          <div v-else class="empty-cell p40">Tekan tombol <strong>Kalkulasi</strong> untuk menyusun Laporan Laba Rugi</div>
        </div>

        <!-- NERACA -->
        <div v-if="subTab==='neraca'" class="card">
          <div class="card-header">
            <div>
              <h3>Laporan Neraca (Balance Sheet)</h3>
              <span class="card-subtitle">Posisi keuangan Aktiva, Kewajiban, dan Ekuitas</span>
            </div>
            <div class="header-actions">
              <input v-model="neracaParams.tanggal" type="date" class="search-input" />
              <button @click="fetchNeraca" class="btn-primary-purple">⚡ Kalkulasi</button>
            </div>
          </div>

          <div class="laporan-wrapper p24" v-if="neracaData">
            <div class="neraca-grid">
              <!-- Aktiva (Aset) -->
              <div class="neraca-column">
                <div class="lap-section">
                  <h4 class="lap-section-title text-blue">🏛️ AKTIVA (ASET)</h4>
                  <div class="lap-row" v-for="a in neracaData.aset" :key="a.kode_akun">
                    <span class="lap-akun-name"><code>{{ a.kode_akun }}</code> {{ a.nama_akun }}</span>
                    <strong class="lap-val">{{ formatRupiah(a.saldo) }}</strong>
                  </div>
                  <div v-if="!neracaData.aset || neracaData.aset.length===0" class="empty-lap-row">Tidak ada aset</div>
                  <div class="lap-row total blue-total">
                    <span>Total Aktiva</span>
                    <strong>{{ formatRupiah(neracaData.total_aset) }}</strong>
                  </div>
                </div>
              </div>

              <!-- Pasiva (Kewajiban & Ekuitas) -->
              <div class="neraca-column">
                <div class="lap-section">
                  <h4 class="lap-section-title text-purple">⚖️ PASIVA (KEWAJIBAN &amp; EKUITAS)</h4>
                  
                  <div class="neraca-sub-header">Kewajiban / Liabilitas</div>
                  <div class="lap-row" v-for="k in neracaData.kewajiban" :key="k.kode_akun">
                    <span class="lap-akun-name"><code>{{ k.kode_akun }}</code> {{ k.nama_akun }}</span>
                    <strong class="lap-val">{{ formatRupiah(k.saldo) }}</strong>
                  </div>
                  <div v-if="!neracaData.kewajiban || neracaData.kewajiban.length===0" class="empty-lap-row">Tidak ada kewajiban</div>
                  <div class="lap-row total red-total">
                    <span>Total Kewajiban</span>
                    <strong>{{ formatRupiah(neracaData.total_kewajiban) }}</strong>
                  </div>

                  <div class="neraca-sub-header" style="margin-top: 16px;">Ekuitas / Modal</div>
                  <div class="lap-row" v-for="e in neracaData.ekuitas" :key="e.kode_akun">
                    <span class="lap-akun-name"><code>{{ e.kode_akun }}</code> {{ e.nama_akun }}</span>
                    <strong class="lap-val">{{ formatRupiah(e.saldo) }}</strong>
                  </div>
                  <div v-if="!neracaData.ekuitas || neracaData.ekuitas.length===0" class="empty-lap-row">Tidak ada ekuitas</div>
                  <div class="lap-row total purple-total">
                    <span>Total Ekuitas</span>
                    <strong>{{ formatRupiah(neracaData.total_ekuitas) }}</strong>
                  </div>

                  <div class="lap-row total grand-pasiva">
                    <span>Total Pasiva</span>
                    <strong>{{ formatRupiah(neracaData.total_pasiva) }}</strong>
                  </div>
                </div>
              </div>
            </div>

            <!-- Check Balance -->
            <div class="neraca-balance-banner" :class="isNeracaBalanced ? 'balanced' : 'unbalanced'">
              <div v-if="isNeracaBalanced" class="balance-status text-emerald">
                <span>🟢 Neraca Seimbang (Balanced)</span>
                <span class="sub-text">Total Aktiva ({{ formatRupiah(neracaData.total_aset) }}) = Total Pasiva ({{ formatRupiah(neracaData.total_pasiva) }})</span>
              </div>
              <div v-else class="balance-status text-rose">
                <span>🔴 Neraca Tidak Seimbang!</span>
                <span class="sub-text">Selisih: {{ formatRupiah(Math.abs(neracaData.total_aset - neracaData.total_pasiva)) }}</span>
              </div>
            </div>
          </div>
          <div v-else class="empty-cell p40">Tekan tombol <strong>Kalkulasi</strong> untuk menyusun Laporan Neraca</div>
        </div>
      </section>
    </main>

    <!-- ===== MODAL: AKUN FORM ===== -->
    <div v-if="showAkunForm" class="modal-overlay" @click.self="showAkunForm=false">
      <div class="modal-box">
        <div class="modal-head">
          <h3>📋 {{ formAkun.id?'Edit Akun':'Tambah Akun Baru' }}</h3>
          <button @click="showAkunForm=false" class="modal-close">✕</button>
        </div>
        <div class="form-grid p24">
          <div class="fg">
            <label>Kode Akun *</label>
            <input v-model="formAkun.kode_akun" class="fi" placeholder="Contoh: 11101, 51102" />
          </div>
          <div class="fg">
            <label>Nama Akun *</label>
            <input v-model="formAkun.nama_akun" class="fi" placeholder="Kas Kecil, Beban Gaji..." />
          </div>
          <div class="fg">
            <label>Kategori *</label>
            <select v-model="formAkun.kategori" class="fi">
              <option value="Aset">Aset</option>
              <option value="Kewajiban">Kewajiban</option>
              <option value="Ekuitas">Ekuitas</option>
              <option value="Pendapatan">Pendapatan</option>
              <option value="Beban">Beban</option>
            </select>
          </div>
          <div class="fg">
            <label>Saldo Normal</label>
            <select v-model="formAkun.saldo_normal" class="fi">
              <option value="Debit">Debit</option>
              <option value="Kredit">Kredit</option>
            </select>
          </div>
          <div class="fg">
            <label>Status Akun</label>
            <select v-model="formAkun.is_aktif" class="fi">
              <option :value="1">Aktif</option>
              <option :value="0">Nonaktif</option>
            </select>
          </div>
          <div class="fg">
            <label>Parent Akun (Opsional)</label>
            <select v-model="formAkun.parent_id" class="fi">
              <option value="">Tidak ada</option>
              <option v-for="a in akun" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
            </select>
          </div>
        </div>
        <div class="modal-actions">
          <button @click="showAkunForm=false" class="btn-secondary">Batal</button>
          <button @click="saveAkun" class="btn-primary" :disabled="saving">Simpan</button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL: BATCH JURNAL FORM ===== -->
    <div v-if="showJurnalForm" class="modal-overlay" @click.self="showJurnalForm=false">
      <div class="modal-box modal-lg">
        <div class="modal-head">
          <h3>📂 Catat Transaksi Jurnal Umum</h3>
          <button @click="showJurnalForm=false" class="modal-close">✕</button>
        </div>
        <div class="modal-body p24">
          <div class="form-grid header-inputs">
            <div class="fg">
              <label>Tanggal Transaksi *</label>
              <input v-model="formJurnal.tanggal" type="date" class="fi" />
            </div>
            <div class="fg">
              <label>Nomor Referensi (Opsional)</label>
              <input v-model="formJurnal.referensi" class="fi" placeholder="Kwitansi, invoice..." />
            </div>
            <div class="fg full">
              <label>Keterangan Transaksi *</label>
              <input v-model="formJurnal.keterangan" class="fi" placeholder="Penerimaan donasi, pembayaran listrik..." />
            </div>
          </div>

          <!-- Ledger Detail rows -->
          <div class="ledger-rows-container">
            <div class="ledger-header-row">
              <span class="col-akun">Pilih Akun</span>
              <span class="col-keterangan">Keterangan Item (Opsional)</span>
              <span class="col-nominal">Debit (Rp)</span>
              <span class="col-nominal">Kredit (Rp)</span>
              <span class="col-act"></span>
            </div>
            <div class="ledger-row" v-for="(row, idx) in formJurnal.details" :key="idx">
              <select v-model="row.akun_id" class="col-akun fi sub-fi">
                <option value="">-- Pilih --</option>
                <option v-for="a in akun" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
              </select>
              <input v-model="row.keterangan_item" class="col-keterangan fi sub-fi" placeholder="Catatan item..." />
              <input v-model.number="row.debit" type="number" class="col-nominal fi sub-fi text-right" placeholder="0" />
              <input v-model.number="row.kredit" type="number" class="col-nominal fi sub-fi text-right" placeholder="0" />
              <button @click="removeJurnalRow(idx)" class="col-act btn-delete-row" title="Hapus Baris">✕</button>
            </div>
          </div>
          <button @click="addJurnalRow" class="btn-add-row">+ Tambah Baris Transaksi</button>

          <!-- Check Balance summary -->
          <div class="balance-bar" :class="isJurnalBalanced ? 'balanced' : 'unbalanced'">
            <div class="balance-item">Total Debit: <strong>{{ formatRupiah(jurnalTotalDebit) }}</strong></div>
            <div class="balance-item">Total Kredit: <strong>{{ formatRupiah(jurnalTotalKredit) }}</strong></div>
            <div v-if="!isJurnalBalanced" class="diff-tag unbalanced">
              Selisih: {{ formatRupiah(Math.abs(jurnalTotalDebit - jurnalTotalKredit)) }}
            </div>
            <div v-else class="diff-tag balanced">
              ✓ Balanced
            </div>
          </div>
        </div>

        <div class="modal-actions">
          <button @click="showJurnalForm=false" class="btn-secondary">Batal</button>
          <button @click="saveJurnal" class="btn-primary" :disabled="saving || !isJurnalBalanced || jurnalTotalDebit <= 0">
            Posting Jurnal
          </button>
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
import { ref, computed, onMounted, watch } from 'vue'
import axios from 'axios'
import Sidebar from '../components/Sidebar.vue'

const API    = 'http://127.0.0.1:8080/api'
const token  = localStorage.getItem('jwt_token')
const headers = { Authorization: 'Bearer ' + token }

// ===== STATE =====
const activeTab     = ref(localStorage.getItem('active_tab_keuangan') || 'akun')
watch(activeTab, (val) => { localStorage.setItem('active_tab_keuangan', val) })
const subTab        = ref('laba_rugi')
const loading       = ref(false)
const loadingGL     = ref(false)
const saving        = ref(false)
const toast         = ref({ show: false, message: '', type: 'success' })

const akun          = ref([])
const jurnals       = ref([])
const searchAkun    = ref('')
const filterKategoriAkun = ref('')

const jurnalPage = ref(1)
const jurnalLimit = ref(10)
const jurnalTotal = ref(0)
const jurnalTotalPages = ref(0)

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
function formatRupiah(n) { if(n === undefined || n === null) return 'Rp 0'; return 'Rp ' + parseInt(n).toLocaleString('id-ID') }
function formatDate(d) { if(!d) return '—'; return new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'short', year:'numeric'}) }
function showNotif(m, type='success') { toast.value={show:true,message:m,type}; setTimeout(()=>toast.value.show=false, 3000) }

function calcJurnalTotal(j) {
  return j.details?.reduce((acc, curr) => acc + (parseFloat(curr.debit) || 0), 0) || 0
}

function getKategoriBadge(kat) {
  switch(kat) {
    case 'Aset': return 'badge-teal'
    case 'Kewajiban': return 'badge-amber'
    case 'Ekuitas': return 'badge-indigo'
    case 'Pendapatan': return 'badge-purple'
    case 'Beban': return 'badge-rose'
    default: return 'badge-info'
  }
}

const filteredAkun = computed(() => {
  return akun.value.filter(a => {
    const matchSearch = !searchAkun.value || 
      a.kode_akun.toLowerCase().includes(searchAkun.value.toLowerCase()) || 
      a.nama_akun.toLowerCase().includes(searchAkun.value.toLowerCase())
    const matchKategori = !filterKategoriAkun.value || a.kategori === filterKategoriAkun.value
    return matchSearch && matchKategori
  })
})

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
  if (key === 'jurnal') await fetchJurnals(1)
  if (key === 'buku_besar' && akun.value.length === 0) await fetchAkun()
  if (key === 'laporan' && akun.value.length === 0) await fetchAkun()
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
async function fetchJurnals(page = 1) {
  loading.value = true
  jurnalPage.value = page
  try {
    const res = await axios.get(`${API}/keuangan/jurnal`, { 
      params: {
        page: jurnalPage.value,
        limit: jurnalLimit.value
      },
      headers 
    })
    jurnals.value = res.data.data || []
    if (res.data.pagination) {
      jurnalTotal.value = res.data.pagination.total || 0
      jurnalTotalPages.value = res.data.pagination.total_pages || 0
    }
  } catch { showNotif('Gagal memuat jurnal transaksi', 'error') }
  finally { loading.value = false }
}

watch(jurnalLimit, () => {
  jurnalPage.value = 1
  fetchJurnals(1)
})

function changeJurnalPage(page) {
  if (page < 1 || page > jurnalTotalPages.value) return
  jurnalPage.value = page
  fetchJurnals(page)
}

const jurnalPaginationRange = computed(() => {
  const current = jurnalPage.value
  const total = jurnalTotalPages.value
  const delta = 2
  const range = []
  let start = Math.max(1, current - delta)
  let end = Math.min(total, current + delta)
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  return range
})

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
    await fetchJurnals(1)
    showNotif('Jurnal berhasil diposting!')
  } catch (e) {
    showNotif(e.response?.data?.message || 'Gagal menyimpan transaksi jurnal', 'error')
  } finally { saving.value = false }
}

async function deleteJurnal(id) {
  if (!confirm('Hapus transaksi jurnal ini?')) return
  try {
    await axios.delete(`${API}/keuangan/jurnal/delete/${id}`, { headers })
    await fetchJurnals(jurnalPage.value)
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
  fetchJurnals(1)
})
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

* { margin: 0; padding: 0; box-sizing: border-box; }

.dashboard-container { display: flex; height: 100vh; background: #0f1117; font-family: 'Inter', sans-serif; color: #e2e8f0; overflow: hidden; }

/* Main Content */
.main-content { flex: 1; overflow-y: auto; display: flex; flex-direction: column; }

/* Header */
.content-header { min-height: 80px; height: auto; padding: 16px 32px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(26,29,46,0.5); gap: 16px; flex-shrink: 0; flex-wrap: wrap; }
.content-header h1 { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; }
.content-header p { font-size: 13px; color: #64748b; margin-top: 2px; }

.header-tabs { display: flex; gap: 8px; flex-wrap: wrap; }
.tab-btn { padding: 8px 16px; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); color: #94a3b8; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s ease; white-space: nowrap; }
.tab-btn:hover { background: rgba(255,255,255,0.07); color: #f1f5f9; transform: translateY(-1px); }

.active-blue { background: rgba(96,165,250,0.15) !important; color: #60a5fa !important; border-color: rgba(96,165,250,0.3) !important; font-weight: 600; box-shadow: 0 4px 12px rgba(96,165,250,0.15); }
.active-purple { background: rgba(167,139,250,0.15) !important; color: #a78bfa !important; border-color: rgba(167,139,250,0.3) !important; font-weight: 600; box-shadow: 0 4px 12px rgba(167,139,250,0.15); }
.active-teal { background: rgba(45,212,191,0.15) !important; color: #2dd4bf !important; border-color: rgba(45,212,191,0.3) !important; font-weight: 600; box-shadow: 0 4px 12px rgba(45,212,191,0.15); }
.active-green { background: rgba(52,211,153,0.15) !important; color: #34d399 !important; border-color: rgba(52,211,153,0.3) !important; font-weight: 600; box-shadow: 0 4px 12px rgba(52,211,153,0.15); }

/* Stats Grid */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; padding: 24px 32px 8px; }
.stat-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 16px; padding: 18px 20px; display: flex; align-items: center; gap: 16px; transition: all 0.2s ease; }
.stat-card:hover { transform: translateY(-2px); border-color: rgba(255,255,255,0.15); }
.stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 22px; background: rgba(255,255,255,0.05); }

.stat-card.blue .stat-icon { background: rgba(96,165,250,0.15); }
.stat-card.purple .stat-icon { background: rgba(167,139,250,0.15); }
.stat-card.green .stat-icon { background: rgba(52,211,153,0.15); }
.stat-card.gold .stat-icon { background: rgba(245,158,11,0.15); }

.stat-num { font-size: 18px; font-weight: 700; color: #f8fafc; }
.stat-lbl { font-size: 12px; color: #94a3b8; margin-top: 2px; }

/* Cards & Headers */
.card { margin: 16px 32px 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.07); border-radius: 18px; overflow: hidden; backdrop-filter: blur(10px); }
.card-header { padding: 20px 24px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid rgba(255,255,255,0.06); flex-wrap: wrap; gap: 16px; }
.card-header h3 { font-size: 17px; font-weight: 700; color: #f1f5f9; }
.card-subtitle { font-size: 12px; color: #64748b; margin-top: 2px; display: block; }
.header-actions { display: flex; gap: 10px; align-items: center; flex-wrap: wrap; }

/* Search & Filter Bar */
.search-bar { padding: 14px 24px; display: flex; gap: 12px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(0,0,0,0.1); align-items: center; flex-wrap: wrap; }
.search-input, .filter-select { background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 9px 14px; color: #e2e8f0; font-size: 13px; outline: none; transition: border-color 0.2s; font-family: 'Inter', sans-serif; }
.search-input:focus, .filter-select:focus { border-color: #7c3aed; }
.filter-select option { background: #1a1d2e; color: #e2e8f0; }
.search-input { min-width: 240px; }
.date-sep { color: #64748b; font-size: 12px; font-weight: 500; }

/* Table Styling */
.table-wrapper { overflow-x: auto; }
.data-table { width: 100%; border-collapse: collapse; font-size: 13px; text-align: left; }
.data-table th { padding: 14px 18px; font-size: 11px; font-weight: 700; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.6px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.15); }
.data-table td { padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,0.04); vertical-align: middle; }
.table-row { transition: background 0.15s ease; }
.table-row:hover { background: rgba(255,255,255,0.025); }
.table-row:last-child td { border-bottom: none; }

.code-badge { background: rgba(124,58,237,0.15); color: #c084fc; border: 1px solid rgba(124,58,237,0.25); padding: 3px 8px; border-radius: 6px; font-family: 'SFMono-Regular', Consolas, monospace; font-size: 12px; font-weight: 600; }
.indent-tree { color: #64748b; margin-right: 6px; font-weight: bold; }
.name-cell { font-weight: 500; color: #e2e8f0; }
.paid-cell { color: #34d399; font-weight: 600; font-family: monospace; font-size: 13px; }
.loading-cell, .empty-cell { text-align: center; color: #64748b; padding: 48px !important; font-size: 14px; }
.muted { color: #64748b; font-size: 11px; }

/* Jurnal Details Box */
.jurnal-details-container { display: flex; flex-direction: column; gap: 6px; max-width: 320px; }
.jurnal-detail-item { display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); padding: 5px 10px; border-radius: 6px; font-size: 11px; gap: 8px; }
.j-akun { color: #c4b5fd; font-weight: 500; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; flex: 1; }
.j-values { display: flex; gap: 6px; flex-shrink: 0; }
.j-val { font-family: monospace; font-weight: 600; padding: 1px 6px; border-radius: 4px; font-size: 10px; }
.j-val.debit { background: rgba(52,211,153,0.15); color: #34d399; }
.j-val.kredit { background: rgba(248,113,113,0.15); color: #f87171; }

/* Badges */
.badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; letter-spacing: 0.2px; }
.badge-success { background: rgba(16,185,129,0.15); color: #34d399; border: 1px solid rgba(16,185,129,0.3); }
.badge-danger { background: rgba(239,68,68,0.15); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
.badge-info { background: rgba(99,102,241,0.15); color: #818cf8; border: 1px solid rgba(99,102,241,0.3); }

.badge-teal { background: rgba(45,212,191,0.15); color: #2dd4bf; border: 1px solid rgba(45,212,191,0.3); }
.badge-amber { background: rgba(245,158,11,0.15); color: #fbbf24; border: 1px solid rgba(245,158,11,0.3); }
.badge-indigo { background: rgba(129,140,248,0.15); color: #818cf8; border: 1px solid rgba(129,140,248,0.3); }
.badge-purple { background: rgba(192,132,252,0.15); color: #c084fc; border: 1px solid rgba(192,132,252,0.3); }
.badge-rose { background: rgba(251,113,133,0.15); color: #fb7185; border: 1px solid rgba(251,113,133,0.3); }

.balance-type { font-weight: 600; font-size: 12px; }
.debit-text { color: #34d399; }
.kredit-text { color: #f87171; }

/* Buttons */
.btn-primary { padding: 9px 20px; background: linear-gradient(135deg, #7c3aed, #a855f7); border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(124,58,237,0.25); }
.btn-primary:hover { opacity: 0.95; transform: translateY(-1px); box-shadow: 0 6px 16px rgba(124,58,237,0.35); }
.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

.btn-primary-purple { padding: 9px 18px; background: linear-gradient(135deg, #6366f1, #8b5cf6); border: none; border-radius: 10px; color: white; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(99,102,241,0.25); }
.btn-primary-purple:hover { opacity: 0.95; transform: translateY(-1px); }

.btn-secondary { padding: 9px 18px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; color: #e2e8f0; font-size: 13px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
.btn-secondary:hover { background: rgba(255,255,255,0.1); }

.action-group { display: flex; gap: 6px; justify-content: center; align-items: center; }
.ab { width: 32px; height: 32px; display: flex; align-items: center; justify-content: center; border-radius: 8px; border: 1px solid rgba(255,255,255,0.08); cursor: pointer; font-size: 13px; background: rgba(255,255,255,0.04); transition: all 0.2s; }
.ab:hover { transform: translateY(-1px); }
.ab-blue:hover { background: rgba(96,165,250,0.2); border-color: rgba(96,165,250,0.4); }
.ab-del:hover { background: rgba(248,113,113,0.2); border-color: rgba(248,113,113,0.4); }

/* Pagination */
.pagination-bar { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(255, 255, 255, 0.06); flex-wrap: wrap; gap: 12px; padding: 18px 24px; background: rgba(0,0,0,0.05); }
.text-muted { font-size: 12px; color: #94a3b8; }
.text-muted strong { color: #f1f5f9; }

.pagination-buttons { display: flex; gap: 6px; align-items: center; }
.page-btn { padding: 6px 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); color: #94a3b8; font-size: 12px; cursor: pointer; transition: all 0.2s; }
.page-btn:hover:not(:disabled) { background: rgba(255,255,255,0.08); color: #f1f5f9; }
.page-btn:disabled { opacity: 0.4; cursor: not-allowed; }
.page-btn.active { background: #7c3aed; color: white; border-color: #7c3aed; font-weight: 600; box-shadow: 0 2px 8px rgba(124,58,237,0.3); }

/* GL Summary Grid */
.gl-summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; padding: 20px 24px; background: rgba(0,0,0,0.12); border-bottom: 1px solid rgba(255,255,255,0.06); }
.gl-sum-card { display: flex; flex-direction: column; gap: 4px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); padding: 12px 16px; border-radius: 12px; }
.gl-label { color: #64748b; font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; }
.gl-val { font-size: 14px; font-weight: 600; color: #f1f5f9; }

/* Laporan Section */
.laporan-header-nav { display: flex; gap: 12px; padding: 0 32px; margin-bottom: 16px; }
.sub-tab-btn { padding: 10px 20px; font-size: 13px; font-weight: 500; border-radius: 10px; border: 1px solid rgba(255,255,255,0.1); background: rgba(255,255,255,0.04); color: #94a3b8; cursor: pointer; transition: all 0.2s; }
.sub-tab-btn:hover { background: rgba(255,255,255,0.08); color: #f1f5f9; }
.sub-tab-btn.active { background: linear-gradient(135deg, #7c3aed, #6366f1); color: white; border-color: transparent; font-weight: 600; box-shadow: 0 4px 14px rgba(124,58,237,0.3); }

.laporan-wrapper { display: flex; flex-direction: column; gap: 24px; }
.p24 { padding: 24px; }
.p40 { padding: 40px; }

.lap-section { border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; overflow: hidden; background: rgba(255,255,255,0.015); }
.lap-section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; background: rgba(255,255,255,0.04); padding: 12px 20px; border-bottom: 1px solid rgba(255,255,255,0.08); letter-spacing: 0.5px; }

.text-green { color: #34d399; }
.text-red { color: #f87171; }
.text-blue { color: #60a5fa; }
.text-purple { color: #c084fc; }

.lap-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 20px; font-size: 13px; border-bottom: 1px solid rgba(255,255,255,0.03); }
.lap-akun-name { color: #e2e8f0; font-weight: 500; }
.lap-val { font-family: monospace; font-size: 14px; color: #f8fafc; }
.empty-lap-row { padding: 16px 20px; color: #64748b; font-size: 13px; font-style: italic; }

.lap-row.total { background: rgba(255,255,255,0.03); font-weight: 700; font-size: 14px; border-top: 1px solid rgba(255,255,255,0.1); }
.green-total { color: #34d399; }
.green-total strong { color: #34d399; }
.red-total { color: #f87171; }
.red-total strong { color: #f87171; }
.blue-total { color: #60a5fa; }
.blue-total strong { color: #60a5fa; }
.purple-total { color: #c084fc; }
.purple-total strong { color: #c084fc; }
.grand-pasiva { background: rgba(96,165,250,0.15); color: #60a5fa; border-top: 2px solid rgba(96,165,250,0.3); font-size: 15px; }
.grand-pasiva strong { color: #60a5fa; font-size: 16px; }

.lap-grand-box { border-radius: 14px; padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; border: 1px solid transparent; }
.profit-box { background: rgba(52,211,153,0.1); border-color: rgba(52,211,153,0.25); }
.loss-box { background: rgba(239,68,68,0.1); border-color: rgba(239,68,68,0.25); }
.grand-label { font-size: 16px; font-weight: 700; color: #f8fafc; }
.grand-sub { font-size: 12px; color: #94a3b8; margin-top: 2px; }
.grand-amount { font-size: 22px; font-family: monospace; font-weight: 700; }

.neraca-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
@media (max-width: 900px) { .neraca-grid { grid-template-columns: 1fr; } }
.neraca-sub-header { font-size: 12px; font-weight: 700; color: #818cf8; text-transform: uppercase; padding: 12px 20px 6px; letter-spacing: 0.5px; }

.neraca-balance-banner { padding: 16px 24px; border-radius: 14px; border: 1px solid transparent; }
.neraca-balance-banner.balanced { background: rgba(52,211,153,0.08); border-color: rgba(52,211,153,0.25); }
.neraca-balance-banner.unbalanced { background: rgba(239,68,68,0.08); border-color: rgba(239,68,68,0.25); }
.balance-status { display: flex; flex-direction: column; gap: 4px; font-size: 15px; font-weight: 700; }
.text-emerald { color: #34d399; }
.text-rose { color: #f87171; }
.sub-text { font-size: 13px; font-weight: 400; color: #94a3b8; }

/* Modals */
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); backdrop-filter: blur(6px); display: flex; align-items: center; justify-content: center; z-index: 100; padding: 20px; }
.modal-box { background: linear-gradient(145deg, #1a1d2e, #141724); border: 1px solid rgba(124,58,237,0.3); border-radius: 20px; width: 560px; max-width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 50px rgba(0,0,0,0.5); }
.modal-lg { width: 780px; }
.modal-head { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.08); background: rgba(255,255,255,0.02); }
.modal-head h3 { font-size: 18px; font-weight: 700; color: #c084fc; }
.modal-close { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; width: 30px; height: 30px; color: #94a3b8; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.modal-close:hover { background: rgba(248,113,113,0.2); color: #f87171; border-color: rgba(248,113,113,0.3); }

.modal-body { display: flex; flex-direction: column; gap: 18px; }
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.header-inputs { grid-template-columns: 1fr 1fr; }
.fg { display: flex; flex-direction: column; gap: 6px; }
.full { grid-column: 1 / -1; }
.fg label { font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px; }
.fi { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 10px; padding: 10px 14px; color: #e2e8f0; font-size: 13px; outline: none; width: 100%; font-family: 'Inter', sans-serif; transition: border-color 0.2s; }
.fi:focus { border-color: #7c3aed; }
.fi option { background: #1a1d2e; }
.sub-fi { border-radius: 8px; padding: 8px 12px; font-size: 12px; }
.text-right { text-align: right; }

.modal-actions { display: flex; gap: 12px; justify-content: flex-end; padding: 18px 24px; border-top: 1px solid rgba(255,255,255,0.08); background: rgba(0,0,0,0.15); }

/* Ledger Rows Input */
.ledger-rows-container { border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; overflow: hidden; background: rgba(0,0,0,0.2); }
.ledger-header-row { display: grid; grid-template-columns: 200px 1fr 120px 120px 36px; background: rgba(255,255,255,0.05); padding: 10px 14px; font-size: 11px; font-weight: 700; color: #94a3b8; text-transform: uppercase; gap: 8px; border-bottom: 1px solid rgba(255,255,255,0.06); }
.ledger-row { display: grid; grid-template-columns: 200px 1fr 120px 120px 36px; padding: 10px 14px; border-bottom: 1px solid rgba(255,255,255,0.05); align-items: center; gap: 8px; }
.ledger-row:last-child { border-bottom: none; }
.btn-delete-row { background: rgba(248,113,113,0.1); border: 1px solid rgba(248,113,113,0.2); color: #f87171; font-weight: bold; cursor: pointer; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; transition: all 0.2s; }
.btn-delete-row:hover { background: rgba(248,113,113,0.25); }

.btn-add-row { padding: 10px 16px; border-radius: 10px; border: 1px dashed rgba(124,58,237,0.4); background: rgba(124,58,237,0.06); color: #c4b5fd; font-size: 13px; font-weight: 600; cursor: pointer; text-align: center; transition: all 0.2s; }
.btn-add-row:hover { background: rgba(124,58,237,0.15); border-color: #7c3aed; }

/* Balance Bar */
.balance-bar { display: flex; justify-content: flex-end; align-items: center; gap: 20px; font-size: 13px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); padding: 12px 20px; border-radius: 12px; flex-wrap: wrap; }
.balance-bar.balanced { border-color: rgba(52,211,153,0.3); background: rgba(52,211,153,0.05); }
.balance-bar.unbalanced { border-color: rgba(239,68,68,0.3); background: rgba(239,68,68,0.05); }
.balance-item { color: #94a3b8; }
.balance-item strong { color: #f8fafc; font-family: monospace; font-size: 14px; }
.diff-tag { font-size: 12px; padding: 4px 10px; border-radius: 6px; font-weight: 700; }
.diff-tag.unbalanced { background: rgba(239,68,68,0.2); color: #f87171; border: 1px solid rgba(239,68,68,0.3); }
.diff-tag.balanced { background: rgba(52,211,153,0.2); color: #34d399; border: 1px solid rgba(52,211,153,0.3); }

/* Toast */
.toast { position: fixed; bottom: 24px; right: 24px; padding: 12px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; z-index: 200; box-shadow: 0 10px 25px rgba(0,0,0,0.3); }
.toast-success { background: rgba(16,185,129,0.95); color: white; }
.toast-error { background: rgba(239,68,68,0.95); color: white; }
.toast-enter-active, .toast-leave-active { transition: all 0.3s ease; }
.toast-enter-from, .toast-leave-to { opacity: 0; transform: translateY(10px); }
</style>
