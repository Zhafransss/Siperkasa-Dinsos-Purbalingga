# Requirement Traceability Matrix (RTM)

> Wajib di-update tiap sesi. Kolom **Status**: `Not Started` → `In Progress` → `Done`.
> Setiap requirement di `docs/SRS.md` harus punya baris di sini. Gunakan tabel ini sebagai
> kontrol scope — permintaan fitur baru yang tidak bisa ditrace ke sini harus ditandai
> sebagai potensi scope creep.

**Terakhir diperbarui:** 2 Oktober 2026 (SRS v0.4).
**Catatan:** tujuan bisnis (BG-xx) masih berstatus *draft* karena belum dikonfirmasi client — lihat `docs/SRS.md` bagian 2.
**Ringkasan:** role User 11/11 FR *Done*; role Admin 0/6 FR (direncanakan, belum dimulai). Test otomatis: **105 lulus** (`vendor/bin/pest`).

## Role User (Pegawai)

| ID | Tujuan Bisnis | Requirement | Fitur/Modul | Prioritas | Status | Test Case |
|----|---------------|-------------|-------------|-----------|--------|-----------|
| FR-U01 | BG-01 | Beranda: hero, alur peminjaman 3 langkah, kalender, pratinjau kendaraan; menu Beranda · Katalog Mobil · Status Peminjaman · Kontak; footer Tentang / Kontak Kami / Jam Layanan | Beranda | Tinggi | Done | `CatalogTest` (home) |
| FR-U02 | BG-01 | Kalender: jumlah **Dipesan** dan **Perawatan** per hari (hari kosong tanpa penanda); klik tanggal berpenanda membuka modal berisi pengajuan (Menunggu/Disetujui) dan jadwal perawatan — sama persis dengan yang dihitung chip; NIP disamarkan | Beranda / Kalender | Tinggi | Done | `CalendarTest` (semua) |
| FR-U03 | BG-02 | Katalog: 4 kartu per baris, 8 per halaman, pager angka; kartu berisi foto, badge status, kapasitas, nama, deskripsi, tombol pilih/nonaktif. Tanpa filter (keputusan user, beda dari Figma) | Katalog | Tinggi | Done | `CatalogTest` |
| FR-U04 | BG-02 | Verifikasi NIP 18 digit terhadap pegawai aktif (langkah 1 formulir) | Formulir | Tinggi | Done | `BookingTest` › NIP verification |
| FR-U05 | BG-02 | Formulir 3 langkah dengan validasi browser + server; jadwal per tanggal tanpa jam; saran lokasi OSM (teks bebas tetap boleh); ringkasan berisi Nama Pegawai, NIP, Kendaraan, Jadwal, Tujuan, Keperluan; tanpa kotak persetujuan | Formulir | Tinggi | Done | `BookingTest` › submitting a booking, › confirmation step; `PlaceSearchTest` |
| FR-U06 | BG-03 | Tolak pengajuan bila kendaraan tidak tersedia, jadwal bentrok (pengajuan lain / perawatan), atau melanggar lead time 1 hari kerja (BR-02) | Formulir | Tinggi | Done | `BookingTest` › overlap, maintenance, lead time, status kendaraan; `BookingWindowTest` |
| FR-U07 | BG-02 | Halaman konfirmasi ringkas (Booking ID, status, pemohon, kendaraan, waktu); hanya pemilik yang bisa membuka | Konfirmasi | Sedang | Done | `BookingTest` › hides the confirmation… |
| FR-U08 | BG-03 | Halaman Status: masuk dengan NIP, daftar pengajuan milik sendiri dengan nama + NIP pemohon, catatan admin; tanpa kartu statistik | Status | Tinggi | Done | `StatusTest` (login, daftar, kepemilikan) |
| FR-U09 | BG-03 | Batalkan pengajuan berstatus Menunggu lewat dialog konfirmasi | Status | Sedang | Done | `StatusTest` › cancel, forbids, not processed |
| FR-U10 | BG-02 | Pencarian pengajuan (NIP, nama, kendaraan, kode booking, tujuan) di server atas semua halaman | Status | Sedang | Done | `StatusTest` › search (server side…) |
| FR-U11 | BG-02 | Pagination Status: 5 per halaman, terbaru dulu, pencarian terbawa antarhalaman, halaman di luar jangkauan dialihkan | Status | Sedang | Done | `StatusTest` › pagination |

## Non-Functional Requirements

| ID | Requirement | Kategori | Status | Bukti / Test Case |
|----|-------------|----------|--------|-------------------|
| NFR-001 | Throttle verifikasi NIP dan masuk Status 10x/menit per IP | Keamanan | Done | Middleware `throttle:10,1` di `routes/web.php` *(belum ada test khusus)* |
| NFR-002 | Escape output; NIP disamarkan di kalender; kata pencarian bukan wildcard | Keamanan / Privasi | Done | `CalendarTest` (escape, masked NIP), `StatusTest` › treats % and _ as plain, › escapes the search term |
| NFR-003 | Cek bentrok atomik (kunci baris kendaraan dalam transaksi) | Integritas data | Done | `app/Services/BookingService.php` *(logika diuji lewat `BookingTest` › overlap; uji konkurensi belum ada)* |
| NFR-004 | Responsif ponsel ≥ 360px sampai desktop 1280px | Kompatibilitas | In Progress | Kelas responsif Tailwind terpasang; **verifikasi visual di ponsel belum dilakukan** |
| NFR-005 | Pegawai dikenali hanya dari NIP (tanpa password) | Keamanan | Open (risiko diterima) | **[Konfirmasi]** — solusi: login/OTP di fase berikutnya |
| NFR-006 | Estimasi pengguna bersamaan, uptime, hosting | Skalabilitas | Open | **[Konfirmasi]** belum ditentukan |
| NFR-007 | Saran lokasi OSM: via server, cache 24 jam, throttle 60/menit, gagal-aman, atribusi | Ketergantungan pihak ketiga | Done | `PlaceSearchTest` |
| NFR-008 | Seluruh perilaku tercakup test otomatis; kode diformat Pint | Maintainability | Done | `vendor/bin/pest` (105 lulus), `vendor/bin/pint` |

## Role Admin — direncanakan (iterasi 2), belum dimulai

Desain Figma sudah diarsipkan: spesifikasi tiap halaman di `docs/DESIGN-ADMIN.md`, token dan komponen di `docs/DESIGN.md`, pratinjau di `docs/design/previews/`.
Daftar halaman dari user; lihat `docs/SRS.md` bagian 6.2 dan pertanyaan terbuka #10–#17. **Belum ada kode admin.**

| ID | Tujuan Bisnis | Requirement | Fitur/Modul | Prioritas | Status | Test Case |
|----|---------------|-------------|-------------|-----------|--------|-----------|
| FR-A01 | BG-03 | Masuk admin; semua halaman admin hanya untuk admin yang masuk (tanpa halaman pendaftaran; akun dibuat di Manajemen Admin) | Autentikasi Admin | Tinggi | Done | tests/Feature/Admin/AuthAndDashboardTest |
| FR-A02 | BG-01 | Beranda Admin: grafik kendaraan paling sering digunakan; kendaraan yang digunakan hari ini | Beranda Admin | Tinggi | Done | tests/Feature/Admin/AuthAndDashboardTest |
| FR-A03 | BG-02 | Manajemen Kendaraan: daftar, tambah, ubah, nonaktifkan; **tanpa "Administrasi & Pajak"** | Manajemen Kendaraan | Tinggi | Done | tests/Feature/Admin/VehicleTest |
| FR-A04 | BG-03 | Verifikasi Peminjaman: setujui/tolak + catatan admin; riwayat tetap tercatat | Verifikasi Peminjaman | Tinggi | Done | tests/Feature/Admin/BookingVerificationTest |
| FR-A05 | BG-02 | Manajemen Pegawai: daftar, tambah, ubah, aktif/nonaktif (dasar verifikasi NIP) | Manajemen Pegawai | Tinggi | Done | tests/Feature/Admin/EmployeeAndAdminUserTest |
| FR-A06 | BG-03 | Manajemen Admin: daftar, tambah, ubah, nonaktifkan akun admin | Manajemen Admin | Tinggi | Done | tests/Feature/Admin/EmployeeAndAdminUserTest |

### Ditunda (ada di Figma, tidak masuk daftar user — butuh konfirmasi bila ingin dikerjakan)

| Halaman Figma | Keterangan |
|---------------|------------|
| Laporan Operasional (grafik batang penggunaan kendaraan) | Sebelumnya FR-A05 / BG-04; tidak ada di daftar user. Sebagian kebutuhan dipenuhi grafik di Beranda Admin |
| Profil & Pengaturan Akun Admin | Tidak ada di daftar user |
| Tambah Jadwal Baru | Tidak ada di daftar user; jadwal perawatan kendaraan lihat pertanyaan #11 |
| Riwayat Peminjaman (admin) | Riwayat diasumsikan menjadi bagian Verifikasi Peminjaman (FR-A04) |

Catatan keterkaitan: kolom `admin_note`, tabel `vehicle_maintenances`, dan akun `users` sudah disiapkan agar modul Admin
bisa dibangun tanpa mengubah alur User.

## Pemetaan berkas test

| Berkas | Mencakup |
|--------|----------|
| `tests/Feature/CatalogTest.php` | Beranda, katalog (8 per halaman, tanpa filter), tombol pilih |
| `tests/Feature/BookingTest.php` | Verifikasi NIP, pengajuan, validasi, bentrok, perawatan, lead time, langkah konfirmasi, kepemilikan |
| `tests/Feature/CalendarTest.php` | Data bulan, rentang multi-hari, perawatan, modal harian, escape, NIP disamarkan |
| `tests/Feature/StatusTest.php` | Masuk/keluar, daftar, pembatalan, pagination, pencarian server-side |
| `tests/Feature/PlaceSearchTest.php` | Saran lokasi OSM (format, cache, gagal-aman, validasi) |
| `tests/Unit/BookingWindowTest.php` | Aturan lead time hari kerja |
| `tests/Feature/RoutesTest.php` | Tidak ada rute `/api/*` (rute bawaan skeleton yang error sudah dihapus) |

Menjalankan test memerlukan MySQL aktif dengan database `sindis_test` (lihat `README.md`).

## Referensi desain
Figma "DINKES FIX" (node `0:1`), diarsipkan penuh pada 2 Okt 2026 di `docs/DESIGN.md`, `docs/DESIGN-ADMIN.md`, `docs/DESIGN-USER.md`, `docs/design/`.
Daftar bagian yang sengaja menyimpang dari Figma: `docs/SRS.md` bagian 10 dan `docs/DESIGN-USER.md` bagian 4. Frame **Status Peminjaman** dan **Footer**
sudah dibandingkan (DESIGN-USER 3.6, 3.7); selisih yang belum diputuskan tercatat di sana.
