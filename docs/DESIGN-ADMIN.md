# Dokumentasi Desain — Halaman Admin

> Spesifikasi lengkap semua frame admin di Figma "DINKES FIX", dipetakan ke **daftar halaman yang diminta user** dan ke
> data yang sudah ada di aplikasi. Dibuat dari data mentah Figma (2 Oktober 2026) agar tetap bisa dipakai saat API Figma kena rate limit.
>
> Rujukan visual: `design/previews/<slug>.png` · semua teks: `design/texts/<slug>.txt` · struktur: `design/outline/<slug>.txt`.
> Token, komponen, dan kerangka layout (sidebar/top bar) ada di [`DESIGN.md`](DESIGN.md) — dokumen ini tidak mengulanginya.
>
> **Status implementasi: belum ada kode admin.** Dokumen ini adalah bahan untuk membangunnya. Kebutuhan fungsional: `SRS.md` bagian 6.2 (FR-A01–A06).

---

## 1. Permintaan user dan pemetaannya ke Figma

User menetapkan lima halaman admin (dan dua pengecualian) :

| # | Halaman yang diminta | Frame Figma yang menjadi acuan | Catatan |
|---|----------------------|--------------------------------|---------|
| 1 | **Beranda** — grafik kendaraan yang sering digunakan; kendaraan yang digunakan hari itu | Kartu grafik dari `admin-laporan-operasional` + panel "Detail & Edit Penggunaan Armada" dari `admin-body-107-1325` | **Tidak ada satu frame yang persis** — gabungan dua frame (lihat 4.3) |
| 2 | **Manajemen Kendaraan** + **Tambah Kendaraan Baru** — *"Administrasi & Pajak" dihilangkan* | `admin-manajemen-armada`, `admin-tambah-kendaraan`, `admin-detail-edit-kendaraan` | Bagian "Administrasi & Pajak" dan baris "Masa Pajak" pada kartu dibuang |
| 3 | **Verifikasi Peminjaman** | `admin-manajemen-booking` (tab Pengajuan Baru) + `admin-riwayat-peminjaman` (tab Riwayat) | Di Figma bernama "Manajemen Reservasi" |
| 4 | **Manajemen Pegawai** | `admin-manajemen-pegawai`, `admin-detail-edit-pegawai` | |
| 5 | **Manajemen Admin** | **Tidak ada frame.** Pola diturunkan dari Manajemen Pegawai + form `admin-daftar` | Perlu keputusan desain (lihat 4.7) |
| — | Masuk Admin | `admin-masuk`, `admin-masuk-background` (modal lupa kata sandi) | Wajib ada agar halaman admin bisa diakses |

Frame yang **tidak masuk** daftar user (tidak dibangun kecuali dikonfirmasi): `admin-laporan-operasional` (halaman Laporan lengkap),
`admin-profil-pengaturan`, `admin-tambah-jadwal`, `admin-daftar` (pendaftaran terbuka), dan varian dashboard A/B/C.

## 2. Navigasi admin

| Figma (sidebar) | Menu yang diminta user | Rute usulan |
|-----------------|------------------------|-------------|
| Dashboard | **Beranda** | `/admin` |
| Kelola Kendaraan | **Manajemen Kendaraan** | `/admin/kendaraan` |
| Manajemen Reservasi | **Verifikasi Peminjaman** | `/admin/peminjaman` |
| Data Pegawai | **Manajemen Pegawai** | `/admin/pegawai` |
| — | **Manajemen Admin** | `/admin/admin` |
| Laporan | *(tidak diminta)* | — |
| Profil Saya | *(tidak diminta)* | — |
| Keluar | Keluar | `POST /admin/keluar` |

Urutan sidebar mengikuti permintaan user. Judul halaman (32/700 `#002a5d`) memakai nama menu. Seluruh rute admin di bawah awalan `/admin`
dan hanya untuk admin yang sudah masuk.

## 3. Halaman autentikasi

### 3.1 Masuk Admin — `admin-masuk` (1280×811)
**Tujuan:** gerbang masuk panel admin.
**Tata letak:** latar putih dengan gradien radial halus; satu kartu tengah **448 lebar** (padding 40, radius 8, garis `#c3c6d2`, jarak antarbagian 32).
**Isi (urut):**
1. Logo 80×80 (bayangan) — gambar `7abe94746c…` (lihat `design/assets.md`).
2. Judul **"Masuk Admin"** 24/600 `#002a5d`; subjudul 14/400 `#434751`: *"Silakan masukkan kredensial Anda untuk mengakses panel kontrol."*
3. Field **"ID Administrator"** — placeholder *"Masukkan ID Anda"*, ikon di kiri.
4. Field **"Kata Sandi"** — placeholder `••••••••`, ikon gembok kiri, tombol mata (tampil/sembunyi) kanan; di baris label ada tautan **"Lupa Kata Sandi?"** (12/500 `#002a5d`).
5. Tombol **"Masuk ke Dashboard"** (penuh lebar, 48 tinggi, `#002a5d`, ikon panah).
6. Pemisah, lalu catatan keamanan dengan ikon perisai hijau: *"Sesi masuk Anda dilindungi dengan enkripsi tingkat militer."* (12/500).
7. Di bawah kartu: sebuah titik kecil (tempat tautan bahasa/kembali, tidak berisi).
**Perilaku tersirat:** login dengan *ID Administrator* + kata sandi (bukan email). "Lupa Kata Sandi?" membuka modal 3.2, bukan alur reset email.
**Catatan implementasi:** "ID Administrator" belum didefinisikan — NIP, nama pengguna, atau email? (lihat 7, #1). Kalimat "enkripsi tingkat militer" adalah klaim
pemasaran yang tidak bisa dibuktikan; sebaiknya diganti kalimat netral. Tombol bernama "Masuk ke Dashboard" — sesuaikan jadi "Masuk ke Beranda".

### 3.2 Modal "Hubungi Administrator" — `admin-masuk-background` (1280×1009)
Dibuka dari "Lupa Kata Sandi?". Latar gelap 85 %; kartu 512 lebar radius 24. Isi: ikon lonceng, judul **"Hubungi Administrator"**, teks *"Untuk mereset kata sandi, silakan
hubungi administrator sistem secara langsung atau melalui saluran berikut:"*, tabel detail — **Nama Admin** (Dr. Hendra Kusuma), **Jabatan** (Kepala Dinas Kesehatan),
**Telepon / WA** (081234567890), **Email** (hendra@dinkes-pbg.go.id), **Lokasi** (Kantor Dinkes, Lt. 1, Ruang Kepala) — lalu kotak peringatan kuning
*"Pastikan membawa kartu identitas pegawai saat mengajukan reset kata sandi secara langsung."* dan tombol **"Mengerti"**.
**Implikasi:** **reset kata sandi dilakukan manual oleh admin**, tidak ada fitur reset otomatis. Data kontak di modal itu adalah contoh; isi sebenarnya perlu dari
Dinkes (atau diambil dari data admin utama). Konsisten dengan keputusan "Manajemen Admin" (admin yang mereset akun admin lain).

### 3.3 Daftar Admin — `admin-daftar` (1280×1029) — *tidak dibangun (lihat 7, #2)*
Kartu 520 lebar (putih 85 % + blur) di atas dua kotak dekoratif kabur (`#d7e2ff`, `#dbe3ed`, 384×384). Judul **"Registrasi Admin"**, subjudul *"Buat akun administratif baru untuk portal dinas kesehatan."*
Field: **Nama Lengkap** ("Nama Lengkap Anda"), **NIP (Nomor Induk Pegawai)** ("19920821 202012 1 001"), **Departemen/Bidang** (select "Pilih Departemen/Bidang"),
**Alamat Email** ("admin@purbalinggakab.go.id"), **Kata Sandi** (hint *"Minimal 8 karakter dengan kombinasi angka dan simbol."*). Tombol **"Daftar"**; tautan *"Sudah punya akun? Masuk di sini"*.
**Kegunaan sekarang:** acuan field untuk form **Tambah Admin** (4.7). Pendaftaran publik berisiko; keputusan ada di SRS pertanyaan #10.

## 4. Halaman utama admin

### 4.1 Kerangka bersama
Sidebar 256 + top bar 64 + kanvas konten (padding 96/40/32/40) — lihat `DESIGN.md` bagian 6. Top bar menampilkan merek, lonceng, dan pengguna
("Admin Utama / Logistik Dinkes" pada halaman reservasi). Seragamkan nama merek (DESIGN.md 8.4).

### 4.2 Pola yang berulang
- **Kepala halaman:** judul 32/700 + subjudul 16/400 di kiri, tombol utama (kadang ditambah tombol outline) di kanan, rata atas.
- **Toast** setelah aksi berhasil (mis. "Pengajuan berhasil disetujui!").
- **Kaki tabel/grid:** "Menampilkan 1-10 dari 142 pegawai" + pager.

### 4.3 Beranda Admin — gabungan `admin-body-107-1325` dan `admin-laporan-operasional`
**Diminta user:** (a) grafik kendaraan yang paling sering digunakan; (b) kendaraan yang digunakan pada hari ini. **Tidak ada frame yang memuat hanya itu** —
dashboard Figma jauh lebih padat. Susunan yang diusulkan, dengan bagian asalnya:

| Blok | Asal di Figma | Spesifikasi |
|------|---------------|-------------|
| Kepala: **"Beranda"** + sapaan | dashboard: "Ringkasan Sistem" / *"Selamat datang kembali, Admin. Berikut adalah status armada hari ini."* | judul 32/700 `#002a5d`, sapaan 16/400 |
| **Grafik "Kendaraan Paling Sering Digunakan"** | laporan: kartu 621×400, putih 70 %, garis `#c3c6d2`, radius 8, padding 16 | judul 16–20 `#002a5d`; **pil bulan** ("Oktober 2023", `#ebeef3`, radius 12) di kanan; 4 garis bantu horizontal `#c3c6d2`; **5 batang** `#002a5d` lebar 64 dengan **angka putih 16 di dalam/atas batang**, di bawahnya nama model 16/600 (Avanza, Innova, HiAce, Pajero, Ambulans) dan merek/tipe 10/400 huruf kapital (TOYOTA, ZENIX, COMMUTER, SPORT, GAWAT) |
| Kartu ringkasan samping | laporan: kolom 299 | "Total Perjalanan **127**" (ubin ikon `#003f87`), "Persentase Utilitas **92.4%** (hijau) — Meningkat 5% dari bulan lalu" |
| **Kendaraan yang digunakan hari ini** | dashboard: "Detail & Edit Penggunaan Armada — Tanggal: 2023-10-09" | kartu 278×333 (2 kolom): nama + plat, badge **DISETUJUI** (`#22c55e` 10 %), pemohon (nama + NIP), bidang, waktu, tujuan |

**Catatan khusus grafik:** di Figma semua batang **sama tinggi** (hanya angkanya berbeda) — itu mockup. Implementasi harus **menskalakan tinggi batang**
terhadap nilai terbesar. Garis bantu berjumlah 4; sumbu Y tidak berlabel.
**Tidak dibawa dari dashboard** (tidak diminta): empat kartu statistik, panel "Peringatan Mendesak" (Pajak STNK / Servis Berkala / Ganti Ban — terkait pajak yang dihapus),
kalender bulanan, "Aktivitas Terbaru", widget "Status Server" (varian A), tombol "Ekspor Laporan"/"Tambah Jadwal". Bila ingin dipertahankan, ajukan ke user.
**Data:** grafik = jumlah pengajuan (disetujui? semua?) per kendaraan pada periode — periode & definisi belum ditetapkan (SRS #12); "digunakan hari ini" =
pengajuan `disetujui` yang mencakup tanggal hari ini (`departs_on ≤ hari ini ≤ returns_on`). Waktu pada kartu Figma ("08:00 s/d 14:00") tidak berlaku (jadwal per tanggal).
**Tombol di kartu Figma** ("Tandai Selesai", "Edit Jadwal", ikon hapus) mengubah pengajuan; tidak diminta user — jangan dibawa sebelum ada keputusan.

**Varian dashboard di Figma** (semua 1280 lebar; D paling lengkap): **A** (`56:4635`, 1587 tinggi) — "Tambah Jadwal", panel peringatan di atas, ada widget "STATUS SERVER"
(*Database Terhubung · Beban Server: 32% • Latensi: 24ms*); **B** (`56:5348`, 1381) — tombol "Reservasi Baru", peringatan dipindah ke bawah statistik; **C** (`88:474`) — tanpa "Ekspor Laporan",
peringatan memuat 3 item (tambah "Ganti Ban - R 1234 PA / Jadwal: Besok"); **D** (`107:1325`) — C + "Ekspor Laporan" + kartu penggunaan kedua (Pajero).

### 4.4 Manajemen Kendaraan — `admin-manajemen-armada` (1280×1088)
**Judul:** *"Kelola Armada Kendaraan"* — subjudul *"Pantau dan kelola seluruh aset operasional Dinas Kesehatan."* — tombol **"Tambah Kendaraan"** (`#002a5d`, radius 8, 203×44).
**Bilah cari/filter** (3 kotak putih, tinggi 70, garis `#c3c6d2`, radius 8): kolom cari *"Cari plat nomor atau tipe kendaraan..."* (lebar ±541) · dropdown **"Status: Semua"** · dropdown **"Tipe: Semua Tipe"**.
**Grid kartu** 3 kolom, kartu **299×391**:
- Foto 297×192; **pil status** kiri atas (titik 6 px + teks): *Tersedia* (`#22c55e` 90 %), *Dalam Tugas* (`#002a5d` 90 %), *Maintenance* (`#f59e0b` 90 %, teks gelap); **pil kategori** kanan bawah (hitam 50 %, teks putih 12/500): "Ambulans Gawat Darurat", "Kendaraan Dinas Jabatan", "Kendaraan Operasional".
- Badan (padding 20): **plat** 20/600 (mis. "R 1234 PA") · **model + tahun** 14/400 (mis. "Toyota Hiace Premio 2023") · garis · ~~**"Masa Pajak: 12 Okt 2024"** (merah "Terlambat (5 Hari)" bila lewat)~~ **← DIHAPUS atas permintaan user** · baris tombol: **"Detail"** (tonal `#e5e8ee`, lebar utama), tombol ikon **ubah**, tombol ikon **hapus**.
- **Kartu ke-4 "Tambah Slot Baru"**: isi `#ebeef3`, garis putus-putus, ubin ikon + *"Tambah Slot Baru"* + *"Input data kendaraan baru ke dalam sistem armada."* (jalan pintas ke form tambah).
**Kaki** (kartu `#f1f4f9`, padding 24): legenda titik **"Tersedia: 12"** (hijau) · **"Dalam Tugas: 8"** (navy) · **"Maintenance: 2"** (oranye) + pager 1 2 3.
**Header top bar:** judul merek "SI-ARMADA DINKES" 20/900. Sidebar: "Kelola Kendaraan" aktif.
**Pemetaan data:** status Figma ⇄ `vehicles.status` (`tersedia`/`dipakai`/`perawatan`). "Tipe" Figma (Gawat Darurat / Jabatan / Operasional) adalah klasifikasi **berdasarkan peruntukan**, berbeda dari `vehicles.type`
(`mpv`/`suv`/`ambulans`/`bus`) — perlu keputusan (SRS #11 usulan; lihat 5).

### 4.5 Tambah Kendaraan Baru — `admin-tambah-kendaraan` (1280×1526)
**Tata letak:** padding `64/12/0/12`, kartu konten 1000 (padding 40), jarak antarseksi 32. **Breadcrumb** 12/500: *Fleet Management › **Tambah Kendaraan***. Judul **"Tambah Kendaraan Baru"** 32/700 `#181c20` (bukan navy);
subjudul *"Lengkapi formulir di bawah ini untuk mendaftarkan unit kendaraan baru ke dalam sistem operasional."*
Setiap seksi = kartu putih (garis `#c3c6d2`, radius 8, padding 32) dengan **ubin ikon 32×32** + judul 20/600.

| Seksi | Field (label → placeholder/nilai awal) |
|-------|----------------------------------------|
| **Informasi Dasar** (ubin `#003f87`) | **Nama Kendaraan** → *Contoh: Avanza Operasional 01* · **Merk / Model** → *Contoh: Toyota Avanza G 2023* · **Nomor Plat (Plat Nomor)** → *Contoh: R 1234 AA* · **Tahun Kendaraan** (select, nilai awal 2024) · **Tipe Bahan Bakar** (select, nilai awal Pertalite) |
| **Spesifikasi Teknis** (ubin `#dbe3ed`) | **Kapasitas Penumpang** (angka, awal 7, sufiks *Orang*) · **Kilometer Awal (Odometer)** (angka, awal 0, sufiks *KM*) · **Warna Kendaraan** → *Contoh: Putih Metalik* |
| ~~**Administrasi & Pajak**~~ | ~~Tanggal Jatuh Tempo Pajak (1 Tahunan) · Masa Berlaku STNK (5 Tahunan)~~ **← DIHAPUS atas permintaan user (seluruh seksi)** |
| **Unggah Foto Kendaraan** (ubin `#e5e8ee`) | 4 slot putus-putus: **Sisi Depan · Sisi Samping · Sisi Belakang · Interior**; petunjuk *"Ukuran foto maksimal 5MB per file. Format yang didukung: JPG, PNG."* |

**Aksi** (rata kanan, di atas garis): **"Batal"** (outline) · **"Simpan Kendaraan"** (navy, radius 12, ikon simpan). **Catatan kaki:** *"© 2024 Dinas Kesehatan PPKB Kabupaten Purbalingga. Seluruh data kendaraan dienkripsi secara aman."*
(klaim enkripsi sebaiknya dihapus; tahun harus dinamis). **Top bar:** kolom cari *"Cari unit atau plat nomor..."* + "Fleet Admin".
**Selisih dengan data kita:** lihat tabel 5 (tahun, bahan bakar, odometer, warna, merk/model, 4 foto belum ada kolomnya; `description` dan `capacity_icon` belum ada di form Figma).

### 4.6 Detail & Edit Kendaraan — `admin-detail-edit-kendaraan` (1280×1204)
**Breadcrumb di top bar:** *Manajemen Armada* (16/700 `#002a5d`) › *Detail Kendaraan* (16/400 `#434751`) — di halaman Detail Pegawai (4.9a) tebalnya terbalik
(induk 16/400, halaman saat ini 16/700): inkonsistensi desain, pilih satu (usulan: halaman saat ini tebal). **Hero** (944×66): nama model *"Toyota Hiace Premio 2023"* (16/400 `#002a5d`), **chip plat** navy *"R 1234 PA"*, **chip status** hijau *"Tersedia"* (garis `#22c55e`, isi 10 %, ikon centang); kanan: **"Batal"** (outline) dan **"Simpan Perubahan"** (navy 212×46, ikon).
**Grid detail** (2 kolom 621 + 299, jarak 24; seluruh input isi `#f1f4f9`, tinggi 50, label 16/400 `#434751`):

| Seksi | Field (nilai contoh) |
|-------|----------------------|
| **Informasi Dasar** (621×482) | Merk & Model (*Toyota Hiace Premio*) · Nomor Polisi (*R 1234 PA*) · Tahun Produksi (*2023*, select) · Warna Kendaraan (*Putih Kristal*) · **Status Operasional** (select, nilai *Aktif / Tersedia*, lebar penuh) |
| **Spesifikasi Teknis** (299×482) | Jenis Bahan Bakar (*Diesel (Solar)*) · Kapasitas Penumpang (*12*) · Odometer Saat Ini (Km) (*12,450*) · Terakhir Servis (tanggal, *11/15/2023*) |
| ~~**Administrasi & Pajak**~~ (944×242) | ~~MASA BERLAKU STNK (05/20/2028) · PAJAK TAHUNAN BERIKUTNYA (05/20/2024)~~ **← DIHAPUS** |
| **Galeri Foto Kendaraan** (944×206) | judul + tautan **"Unggah Foto Baru"**; 4 miniatur 163×92 (sorot: lapisan hitam 40 % + tombol bulat putih ✎ (biru) dan 🗑 (merah)); slot putus-putus **"Tambah Foto"** |

**Perilaku tersirat:** halaman yang sama dipakai untuk melihat dan mengubah (input langsung bisa diedit; "Simpan Perubahan" menerapkan). Daftar pilihan **Status Operasional** tidak tergambar —
usulan: Tersedia / Dalam Tugas / Maintenance (= status kendaraan di aplikasi).

### 4.7 Manajemen Admin — **tidak ada frame**
Dimintakan user tetapi tidak didesain. Usulan, diturunkan dari pola yang sudah ada:
- **Daftar** seperti Manajemen Pegawai (4.9): kepala *"Manajemen Admin"* + tombol **"Tambah Admin"**; tabel kolom **Admin** (avatar inisial + nama + email), **ID Administrator / NIP**, **Bidang**, **Status** (Aktif / Nonaktif), **Aksi** (ubah, nonaktifkan, reset kata sandi); kolom cari *"Cari nama atau ID..."*; kaki "Menampilkan 1-10 dari N admin" + pager.
- **Form Tambah/Ubah Admin** memakai field kartu Daftar Admin (3.3): Nama Lengkap, NIP, Bidang, Email, Kata Sandi (+ ID Administrator bila terpisah) dalam kartu seksi seperti 4.5; tombol "Batal" / "Simpan".
- **Aturan:** tidak bisa menonaktifkan diri sendiri atau admin terakhir (SRS FR-A06). Reset kata sandi dilakukan admin lain (selaras modal 3.2).
- Gaya mengikuti `DESIGN.md`. Seluruh teks di atas adalah usulan, bukan salinan Figma — tandai saat meminta persetujuan user.

### 4.8 Verifikasi Peminjaman — `admin-manajemen-booking` + `admin-riwayat-peminjaman` (1280×1024)
**Judul di Figma:** *"Manajemen Reservasi"* — subjudul *"Kelola pengajuan peminjaman armada dinas secara efisien."* — tombol **"Buat Reservasi Baru"** (admin membuat pengajuan; lihat 6, *Tambah Jadwal Baru*; tidak diminta user).
**Tab** (garis bawah; jarak 32): **Pengajuan Baru** (+ penghitung merah "3") · **Jadwal Aktif** · **Riwayat**.
**Tab Pengajuan Baru** — satu kartu per pengajuan (944×178, putih 70 %, padding 24):
`[foto kendaraan 192×128]  PEMINJAM / TUJUAN / ARMADA / WAKTU (4 kolom 125 lebar)  [Setujui] [Tolak] [Detail]`
- **PEMINJAM:** nama (14/600) + `NIP: …`; **TUJUAN:** mis. *Sosialisasi Stunting Kec. Kejobong*; **ARMADA:** *Toyota Innova (R 1234 PA)*; **WAKTU:** *24 Okt 2023, 08:00 - 15:00*.
- Label kolom 12/500 `#737782` huruf kapital. **Setujui** = hijau `#22c55e` (104×36); **Tolak** = outline merah (104×38); **Detail** = tautan `#002a5d`.
- **Toast** (tengah bawah): *"Pengajuan berhasil disetujui!"* setelah menyetujui.
**Tab Riwayat** — tabel (kartu putih 70 %): kolom **Peminjam** (nama + `NIP: …`) · **Tujuan** · **Armada** · **Waktu** · **Status** (pil: **SELESAI** hijau, **DITOLAK** merah, **DIBATALKAN** abu). Kepala `#ebeef3`.
**Tab Jadwal Aktif** — **tidak didesain** (usulan: pengajuan `disetujui` yang belum selesai, dengan kartu seperti Pengajuan Baru tanpa tombol, atau tombol "Detail").
**Kekosongan desain yang harus diputuskan:** (1) **alasan penolakan** — halaman user menampilkan "Alasan Penolakan Pengelola Admin" tetapi Figma admin tidak punya input alasan (usulan: dialog dengan textarea, kolom `bookings.admin_note`);
(2) **catatan saat menyetujui** (opsional; tampil ke pemohon sebagai "Catatan Pengelola Dinkes"); (3) isi **Detail** (halaman atau modal); (4) status **"Selesai"** belum ada di aplikasi (DESIGN.md 7.4a); (5) waktu "08:00 - 15:00" tidak berlaku — jadwal per tanggal.
**Aturan bisnis yang tetap berlaku:** hanya pengajuan `menunggu` yang bisa disetujui/ditolak; saat menyetujui, cek ulang bentrok (BR-04/05) dalam transaksi.

### 4.9 Manajemen Pegawai — `admin-manajemen-pegawai` (1280×1070)
**Kepala:** *"Manajemen Pegawai"* — subjudul *"Kelola basis data staf untuk operasional armada dinas."* — tombol **"Impor Data"** (outline) dan **"Tambah Pegawai"** (navy).
**Kartu statistik** (4 × 218, kaca, bayangan): **TOTAL PEGAWAI 142** (hijau "+3 Bulan ini") · **TERVERIFIKASI 128** ("90.1% dari total") · **MENUNGGU 14** (oranye "Butuh validasi NIP") · **UNIT KERJA 8** ("Bidang aktif").
**Kartu tabel** (garis `#c3c6d2`, radius 8, bayangan):
- Bilah alat: kolom cari *"Cari Nama atau NIP..."* (320×38) · dropdown **"Semua Bidang"** · tautan **"Filter Lanjut"** (kanan).
- Kolom: **Pegawai** (avatar inisial 40×40 + nama 14/600 + email) · **NIP / Identitas** (NIP 16/400 + *"Pangkat: Penata / IIIc"* 12/500) · **Bidang / Unit Kerja** (pil: *Sekretariat*, *Kesmas*, *Pencegahan Penyakit*) · **Status Verifikasi** (titik + *Terverifikasi* hijau / *Menunggu Validasi* oranye) · **Aksi** (ikon, tidak tergambar jelas). Kepala `#f1f4f9`.
- Contoh data: Agus Setiawan — 198503122010011005 — Sekretariat — Terverifikasi; Budi Nugraha — 199207042018031002 — Kesmas — Menunggu Validasi; Dewi Rahayu — 198801252014022001 — Pencegahan Penyakit — Terverifikasi.
- Kaki: *"Menampilkan 1-10 dari 142 pegawai"* + pager **1 2 3 … 15**.
**Kotak info biru** di bawah (judul **"Validasi NIP Terintegrasi"**): *"Sistem Si-Armada secara otomatis memvalidasi format NIP dan data kepegawaian dengan database internal Dinkes. Jika status pegawai masih "Menunggu Validasi", pastikan NIP yang dimasukkan sudah benar sesuai SK terakhir."*
**Top bar:** kolom cari *"Cari NIP untuk validasi..."*.
**Selisih dengan data kita:** email, pangkat/golongan, jabatan, dan **status verifikasi** tidak ada kolomnya (lihat 5). "Impor Data" tidak diminta user; "Filter Lanjut" tidak didefinisikan. Kartu statistik: hanya "Total" dan "Unit Kerja (bidang aktif)" bisa dihitung dari data sekarang.

### 4.9a Detail & Edit Data Pegawai — `admin-detail-edit-pegawai` (1280×1024)
Breadcrumb **Manajemen Pegawai › Detail Pegawai** (16/400 · 16/700 `#002a5d`). Dua kolom: kiri kartu **"Informasi Dasar"** (621×377; judul + garis bawah): **Nama Lengkap**, **NIP**, **Jabatan / Pangkat** (select, *Penata / IIIc*),
**Bidang / Unit Kerja** (select, *Sekretariat*), **Email Dinas**; kanan kartu aksi (299×180, bayangan): **"Simpan Perubahan"** (navy 48 tinggi) dan **"Batal"** (outline). Seluruh input isi `#f1f4f9`, tinggi 42.
**Top bar:** teks pencarian *"Search data..."* dan "Purbalingga Fleet" (sisa teks Inggris di frame ini).

### 4.10 Halaman di luar daftar user (dokumentasi untuk rujukan)
Dicatat agar tidak hilang bila nanti diminta; **jangan dibangun tanpa konfirmasi**.

**Laporan Operasional** — `admin-laporan-operasional` (1280×1457): kepala *"Laporan Operasional"* + *"Analisis utilitas kendaraan dan rekapitulasi pengajuan perjalanan dinas."*;
rentang tanggal *10/01/2023 ke 10/31/2023*, tombol **"Ekspor PDF"** (outline) dan **"Ekspor Excel"** (navy); grafik batang + kartu **Total Perjalanan 127** dan **Persentase Utilitas 92.4%**; tabel **"Rekap Pengajuan Kendaraan"** (cari *"Cari NIP atau Nama..."*; kolom Nama & NIP · Tujuan · Kendaraan · Tanggal · Status [Selesai/Dibatalkan]; *"Menampilkan 1 - 5 dari 127 data"*).
**Profil & Pengaturan Akun Admin** — `admin-profil-pengaturan` (1280×1201): kartu **Informasi Akun** (badge *Aktif*; Nama Lengkap *Drs. Bambang Wijaya*, NIP, Departemen / Bidang *Subag Umum & Kepegawaian*, Alamat Email Resmi; "Simpan Perubahan"); kartu **Keamanan & Kata Sandi** (saat ini / baru *Minimal 8 karakter* / konfirmasi; tombol abu **"Perbarui Kata Sandi"**); kartu **Pengaturan Notifikasi** (dua sakelar: *Notifikasi Email*, *Pemberitahuan Sistem Browser*).
**Tambah Jadwal Baru** — `admin-tambah-jadwal` (1280×1097): form admin membuat jadwal: **Pilih Kendaraan**, **Nama Pengemudi**, **Tanggal Mulai / Jam Mulai / Tanggal Selesai / Jam Selesai**, **Tujuan Perjalanan**, **Keperluan Dinas**, **Catatan Tambahan** (opsional, 0/200 karakter); **"Batal"**, **"Simpan Jadwal"**; kotak info *"Penting: Setiap pengajuan jadwal akan melalui sistem verifikasi ketersediaan armada otomatis. Pastikan data yang dimasukkan sudah benar sesuai dengan Surat Perintah Tugas (SPT)."*
(Ada konsep **pengemudi** dan **jam** yang tidak ada di aplikasi.)

## 5. Selisih desain Figma dengan data aplikasi

Aplikasi saat ini (`database/migrations`): `employees`, `vehicles`, `vehicle_maintenances`, `bookings`, `users` (bawaan Laravel, belum dipakai).

### 5.1 Kendaraan
| Field Figma | Kolom sekarang | Tindakan |
|-------------|----------------|----------|
| Nama Kendaraan | `vehicles.name` | ✓ |
| Merk / Model | — (`name` memuat model; `description` = deskripsi singkat) | **keputusan:** kolom baru `brand_model`, atau pecah `name` |
| Nomor Plat | `vehicles.plate` | ✓ |
| Tahun | — | kolom baru `year` |
| Tipe Bahan Bakar | — | kolom baru `fuel_type` (daftar pilihan: Pertalite, Pertamax, Solar/Diesel, Listrik, Hybrid …) |
| Kapasitas Penumpang (angka + "Orang") | `capacity_label` (teks, boleh kosong), `capacity_icon` | ubah ke angka `capacity` + label turunan, atau tetap teks |
| Kilometer Awal / Odometer Saat Ini | — | kolom `odometer_km` |
| Warna | — | kolom `color` |
| Terakhir Servis | — | kolom `last_serviced_on` (atau diturunkan dari `vehicle_maintenances`) |
| Foto (4 sisi: Depan, Samping, Belakang, Interior) | `image_path` (satu foto) | tabel `vehicle_photos` (`vehicle_id`, `side`, `path`); foto pertama/Depan = `image_path` kartu user |
| Status (Tersedia / Dalam Tugas / Maintenance) | `vehicles.status` | ✓ (label "Dalam Tugas" = `dipakai`) |
| Kategori peruntukan (Gawat Darurat / Jabatan / Operasional) | `vehicles.type` (mpv/suv/ambulans/bus) | **keputusan:** taksonomi berbeda — tambah kolom `category`, atau abaikan |
| ~~Jatuh tempo pajak, Masa berlaku STNK~~ | — | **tidak dibuat** (dihapus atas permintaan user) |

### 5.2 Pegawai
| Field Figma | Kolom sekarang | Tindakan |
|-------------|----------------|----------|
| Nama Lengkap | `employees.name` | ✓ |
| NIP (18 digit) | `employees.nip` | ✓ |
| Bidang / Unit Kerja | `employees.bidang` | ✓ (daftar bidang: tabel `bidangs` atau tetap teks) |
| Email Dinas | — | kolom `email` |
| Pangkat / Golongan (*Penata / IIIc*), Jabatan | — | kolom `pangkat`, `jabatan` |
| Status Verifikasi (Terverifikasi / Menunggu Validasi) | `is_active` (aktif/nonaktif) | **keputusan:** konsep berbeda dari aktif/nonaktif — tambah `verified_at`/`verification_status`, atau pakai `is_active` |
| Avatar inisial | dihitung dari nama | ✓ (turunan) |
| Impor Data | — | tidak diminta |

### 5.3 Admin
| Field Figma | Kolom sekarang | Tindakan |
|-------------|----------------|----------|
| Nama Lengkap, Email, Kata Sandi | `users.name`, `users.email`, `users.password` | ✓ (tabel `users`) |
| NIP, Bidang | — | tambah kolom ke `users` (atau relasi ke `employees`) |
| **ID Administrator** (login) | — | **keputusan:** NIP, username, atau email |
| Aktif / Nonaktif | — | kolom `is_active` |
| Peran | — | tidak perlu bila semua admin setara (SRS #14) |

### 5.4 Pengajuan
| Field Figma | Kolom sekarang | Tindakan |
|-------------|----------------|----------|
| Peminjam, NIP, Tujuan, Armada, Waktu | `employee`, `destination`, `vehicle`, `departs_on`–`returns_on` | ✓ (tanpa jam) |
| Setujui / Tolak | `bookings.status` | ✓ + `admin_note` untuk alasan |
| **SELESAI** | — | turunan (`disetujui` dan `returns_on` < hari ini) **atau** status baru `selesai` |
| Nama Pengemudi, Jam, Catatan Tambahan (Tambah Jadwal) | — | tidak diminta |
| Aktivitas Terbaru | — | butuh log aktivitas (tidak diminta) |

### 5.5 Grafik Beranda
Butuh agregasi `bookings` per `vehicle_id` pada periode: `COUNT(*)` (jumlah pengajuan) atau jumlah hari. **Indeks** yang ada (`bookings(vehicle_id, departs_on, returns_on)`) sudah cukup.

## 6. Usulan urutan pengerjaan

1. **Fondasi:** migrasi (kolom/tabel di bagian 5 yang disetujui), `AdminLayout` (sidebar + top bar), middleware admin, token `navy`.
2. **Masuk Admin** (+ modal lupa kata sandi) dan keluar. Admin pertama lewat seeder. *(FR-A01)*
3. **Manajemen Pegawai** (daftar, cari, filter bidang, tambah, ubah, aktif/nonaktif). *(FR-A05)* — terlebih dulu karena verifikasi NIP user bergantung padanya.
4. **Manajemen Kendaraan** (daftar, cari, filter status, tambah, ubah, foto, nonaktifkan). *(FR-A03)*
5. **Verifikasi Peminjaman** (tab, setujui/tolak + catatan, riwayat). *(FR-A04)* — langsung menghidupkan kalender dan halaman Status user.
6. **Manajemen Admin.** *(FR-A06)*
7. **Beranda Admin** (grafik + kendaraan hari ini). *(FR-A02)* — terakhir karena butuh data dari langkah 3–5.
Setiap langkah: tes Pest, perbarui `SRS.md`/`RTM.md`, dan cocokkan dengan pratinjau di `design/previews`.

## 7. Pertanyaan terbuka khusus desain

| # | Pertanyaan |
|---|-----------|
| 1 | **ID Administrator** pada login = NIP, nama pengguna, atau email? |
| 2 | Pendaftaran admin: halaman **Daftar Admin** ada di Figma, tetapi user meminta "Manajemen Admin". Usulan: tanpa pendaftaran publik (SRS #10). |
| 3 | **Beranda Admin:** apakah empat kartu statistik atas dashboard tetap dipakai? Periode grafik? |
| 4 | **Taksonomi kendaraan:** pakai tipe aplikasi (MPV/SUV/Ambulans/Bus) atau kategori Figma (Gawat Darurat/Jabatan/Operasional)? |
| 5 | **Field kendaraan** yang akan ditambah (tahun, bahan bakar, odometer, warna, merk/model, foto 4 sisi, terakhir servis) — semua atau sebagian? |
| 6 | **Status verifikasi pegawai** (Terverifikasi/Menunggu): perlu, atau cukup aktif/nonaktif? Perlu **email** dan **pangkat/jabatan**? |
| 7 | **Penolakan** wajib disertai alasan? **Persetujuan** boleh dengan catatan? |
| 8 | Status **Selesai**: turunan otomatis atau aksi "Tandai Selesai" manual? |
| 9 | Isi data kontak pada modal **"Hubungi Administrator"** (nama, jabatan, telepon/WA, email, lokasi) — dari mana? |
| 10 | Merek tampilan admin: "SINDIS", "Dinkes PPKB Purbalingga", atau "Si-Armada"? |
| 11 | Tab **Jadwal Aktif** pada Verifikasi Peminjaman: isi dan aksinya? |
| 12 | Perilaku responsif (ponsel/tablet) untuk panel admin: tidak ada desain di Figma. |

## 8. Keputusan user (2 Oktober 2026) — jawaban atas bagian 7

| # | Keputusan | Akibat pada rancangan |
|---|-----------|----------------------|
| 1 | **ID Administrator = NIP** | Login memakai NIP (18 digit) + kata sandi. Tabel `users` mendapat kolom `nip` (unik). |
| 2 | **Tidak ada halaman pendaftaran admin.** Akun admin dibuat lewat **Manajemen Admin** (termasuk Create) | Frame `admin-daftar` hanya jadi acuan field form "Tambah Admin". Admin pertama dibuat lewat seeder. |
| 3 | **Empat kartu statistik tetap ada** (Total Armada, Tersedia, Menunggu, Jadwal Hari Ini) **ditambah** grafik kendaraan paling sering digunakan **dan** daftar kendaraan yang digunakan hari ini | Beranda = 4 kartu + grafik + daftar hari ini. Panel Peringatan, kalender, Aktivitas Terbaru, dan Status Server **tidak** dibawa. |
| 4 | **Kategori kendaraan = Gawat Darurat / Jabatan / Operasional** (bukan MPV/SUV/Ambulans/Bus) | Kolom `category` menggantikan `type`; filter "Tipe" di admin = kategori. |
| 5 | **Semua field kendaraan dari Figma dipakai** (merk/model, tahun, bahan bakar, kapasitas, odometer, warna, terakhir servis, 4 foto) kecuali "Administrasi & Pajak" | Migrasi kolom + tabel `vehicle_photos`. |
| 6 | **Status verifikasi pegawai ditunda** | Tidak dibuat sekarang. Kartu statistik pegawai memakai Total / Aktif / Nonaktif / Unit Kerja. |
| 7 | **Ya**: penolakan **wajib** beralasan; persetujuan boleh dengan catatan | `bookings.admin_note`; alasan tampil ke pemohon di halaman Status. |
| 8 | **Selesai = turunan otomatis** | Pengajuan `disetujui` yang `returns_on`-nya sudah lewat ditampilkan "Selesai"; tidak disimpan. |
| 9 | **Modal "Hubungi Administrator" tidak dipakai** | Tidak ada tautan "Lupa Kata Sandi?"; kata sandi direset admin lain lewat Manajemen Admin. |
| 10 | **Nama aplikasi/merek: "Si Perkasa"** | Menggantikan "SINDIS" / "Si-Armada" di seluruh aplikasi dan dokumen. |
| 11 | **Fitur admin hanya 5**; tab **"Jadwal Aktif" tidak ada** | Verifikasi Peminjaman punya dua tab: **Pengajuan Baru** dan **Riwayat** (Riwayat memuat semua pengajuan yang sudah diproses, termasuk yang disetujui dan belum selesai). |
| 12 | **Responsif diserahkan ke pengembang** | Sidebar menjadi laci di ponsel; tabel dan grid menyesuaikan. |

Catatan "Jadwal Aktif" (jawaban pertanyaan Anda): di Figma itu tab berisi pengajuan yang **sudah disetujui dan belum selesai** (jadwal yang sedang/akan berjalan).
Karena tab itu dibuang, pengajuan yang disetujui tetap terlihat di tab Riwayat (badge "Disetujui") dan di Beranda ("digunakan hari ini").
