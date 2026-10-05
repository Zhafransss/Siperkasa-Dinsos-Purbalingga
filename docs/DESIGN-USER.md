# Dokumentasi Desain — Halaman User (Pegawai)

> Spesifikasi frame sisi user di Figma "DINKES FIX" **dibandingkan dengan yang sudah dibangun** di aplikasi (Laravel/Blade).
> Dibuat dari data mentah Figma (2 Oktober 2026). Token dan komponen: [`DESIGN.md`](DESIGN.md); halaman admin: [`DESIGN-ADMIN.md`](DESIGN-ADMIN.md).
> Rujukan visual: `design/previews/<slug>.png` · teks: `design/texts/<slug>.txt` · struktur: `design/outline/<slug>.txt`.

**Catatan penting:** frame **Status Peminjaman** dan **Footer** sebelumnya tidak pernah bisa dibandingkan (rate limit Figma) — halaman Status dibangun dari
prototipe HTML. Bagian 3.6 dan 3.7 di bawah adalah perbandingan pertama yang berbasis data Figma sebenarnya.

---

## 1. Kerangka sisi user

- **Navbar** tetap di atas: tinggi 80, latar `#f7f9ff`, garis bawah `#c2c6d4`; logo 40 + "Dinkes PPKB Purbalingga" 20/700 `#003f87` di kiri; menu di kanan
  14/600: **Home** (aktif: `#003f87` + garis bawah) · Katalog Mobil · Status Peminjaman · Kontak.
- **Konten** terpusat; lebar maksimum 1280; padding samping 40.
- **Footer** gelap `#181c20`, tinggi 304, padding `32/40`: kolom 1 merek + "Tentang Kami" + hak cipta · kolom 2 **Tautan Cepat** · kolom 3 **Kontak Kami** + **Ikuti Kami**.
- Warna aksi sisi user: **`#003f87`** (bukan navy admin `#002a5d`).

## 2. Peta frame → halaman aplikasi

| Frame Figma (slug) | Rute / view aplikasi | Status |
|--------------------|----------------------|--------|
| `user-beranda` | `GET /` — `resources/views/home.blade.php` | Dibangun, dengan perubahan (3.1) |
| `user-katalog` | `GET /katalog` — `katalog.blade.php`, `components/vehicle-card.blade.php` | Dibangun, banyak perubahan atas permintaan user (3.2) |
| `user-form-verifikasi-nip` | `GET /peminjaman/buat` langkah 1 — `peminjaman/create.blade.php` | Dibangun (3.3) |
| `user-form-verifikasi-nip-error` | langkah 1, keadaan error NIP | Dibangun (3.3) |
| *(langkah 2 dan 3 formulir)* | `peminjaman/create.blade.php` | **Tidak ada frame Figma** — dirancang sendiri dari prototipe |
| `user-konfirmasi-berhasil` | `GET /peminjaman/{kode}/berhasil` — `peminjaman/success.blade.php` | Dibangun, disederhanakan (3.5) |
| `user-status-peminjaman` | `GET /status` — `status/index.blade.php` | Dibangun, dengan perubahan (3.6) |
| `footer-a` | `components/layouts/app.blade.php` | Dibangun, dengan perubahan (3.7) |

## 3. Per halaman: isi Figma dan perbedaan

### 3.1 Beranda — `user-beranda` (1290×2873)
**Isi di Figma (berurutan):**
1. **Hero:** foto ambulans lebar penuh (1290×667) dengan gradasi putih dari kiri; label **LAYANAN INTERNAL** (12/500); judul **"Peminjaman Kendaraan Dinas Jadi Lebih Mudah."** 48/700 (spasi −0,96, lh 56);
   paragraf 18/400 *"Optimalkan mobilitas operasional Anda dengan sistem pemesanan armada yang terintegrasi, transparan, dan efisien untuk staf Dinas Kesehatan PPKB Purbalingga."*; tombol **"Pinjam Sekarang"** (isi biru, 20/600) dan **"Cek Status Peminjaman"** (outline).
2. **"Proses Peminjaman Sederhana"** (32/700) + *"Hanya butuh 3 langkah mudah untuk mendapatkan akses kendaraan."*: tiga kartu — *1. Pilih Armada*, *2. Isi Jadwal*, *3. Ambil Kunci* — dengan angka besar pudar **01/02/03** (60/700) di pojok kanan atas.
3. **"Jadwal Penggunaan Kendaraan"** + *"Pantau ketersediaan seluruh armada secara real-time untuk merencanakan perjalanan dinas Anda."*: kalender **"Oktober 2024"**, hari MIN–SAB, chip per hari: **"8 Tersedia"**, **"5 Dipesan"**, **"3 Dipesan"**, **"Maintenance"**, **"Rapat Prov"**, **"Dinas Luar"**, legenda Tersedia/Dipesan/Perawatan.
4. **"Detail & Edit Penggunaan Armada — Tanggal: 2024-10-03"**: dua kartu penggunaan di bawah kalender, badge **DISETUJUI**, masing-masing berisi plat, nama pemohon, **`NIP:` lengkap (14/700)**, `Bidang:`, waktu `(08:00) s/d (16:00)`, tujuan, keperluan, dan (kartu kedua) kotak kuning **"Catatan Admin: Silahkan ambil kunci ke bagian umum"**. Kartu 1: `R 1 DKS` — dr. Bambang Sulistyo, Sp.P — Bidang P2P — *puskesmas kaligondang*. Kartu 2: `R 1024 DKS` — Siti Rahmawati, S.ST, M.Si — Sekretariat — *Dinas Kesehatan Provinsi Jawa Tengah (Semarang)*.
5. **"Katalog Armada"** + *"Daftar kendaraan operasional dalam kondisi prima."* + tautan **"Lihat Semua"**; 3 kartu: Toyota Avanza 2022 (MPV • R 1234 PA, TERSEDIA, 7 Kursi, Manual), Mitsubishi Pajero (SUV • R 5678 QB, **BOOKING**, 7 Kursi, Automatic), Toyota Innova Zenix (MPV • R 9012 PC, TERSEDIA, 8 Kursi, Automatic).
**Perbedaan di aplikasi:**
| Perbedaan | Sebab |
|-----------|-------|
| Menu "Home" → **"Beranda"** | permintaan user |
| Kalender hanya menampilkan **Dipesan** dan **Perawatan** (tanpa "Tersedia"); klik tanggal berpenanda membuka **modal** "Detail Jadwal Armada" | permintaan user; panel "Detail & Edit Penggunaan Armada" di bawah kalender tidak dipakai |
| Baris statistik armada (24/08/12/04) **tidak ada** | tidak ada di Figma (hanya di prototipe HTML) — dihapus |
| Kartu pratinjau mengikuti desain kartu baru (4 kolom di Katalog; di Beranda tetap 3) | permintaan user (lihat 3.2) |
| **NIP pemohon disamarkan** di modal kalender (`198811••••••••1001`); Figma menampilkan NIP lengkap | keputusan saya demi privasi (halaman ini terbuka bagi semua pegawai) — SRS NFR-002; balikkan bila tidak dikehendaki |
| Modal juga memuat pengajuan **Menunggu** (badge kuning) dan jadwal **perawatan**; Figma hanya kartu Disetujui | agar isi modal sama dengan chip yang dihitung kalender |
| Data kalender dari database (bukan contoh "Oktober 2024") | — |

### 3.2 Katalog — `user-katalog` (1280×1136)
**Isi di Figma:** judul **"Katalog Mobil Dinas"** + *"Pilih kendaraan yang tersedia untuk mendukung operasional layanan kesehatan di Kabupaten Purbalingga."*; **sidebar filter** kiri (256): *TIPE KENDARAAN* (MPV / Keluarga, SUV / Operasional, Ambulans, Bus Puskesmas),
*STATUS KETERSEDIAAN* (Tersedia, Sedang Dipakai, Dalam Perawatan), tombol "Bersihkan Filter"; grid **3 kartu per baris** (289×192 foto): badge status di atas foto (TERSEDIA / SEDANG DIPAKAI / PERAWATAN, 10/700),
nama 20/600, deskripsi 14/400, baris spesifikasi (ikon + "7 Kursi", "Otomatis" / "Manual/4WD" / "Lengkap, 4 Personel" / "Hybrid" / "Servis Rutin" / "Klinik Berjalan, 15 Kapasitas"), tombol **"Pilih Kendaraan"** (atau **"Tidak Tersedia"** / **"Sedang Servis"** nonaktif); pager **1 2 3**.
**Perbedaan di aplikasi (semua atas permintaan user):**
| Perubahan | Keterangan |
|-----------|------------|
| Sidebar filter **dihapus** | kartu jadi **4 per baris**, 8 per halaman |
| Desain kartu baru | badge status **di atas foto** (kiri atas); kapasitas ("7 Kursi") sebagai label putih di kiri bawah foto; tombol utama biru penuh; tombol nonaktif abu; **spesifikasi (transmisi, bahan bakar, "Servis Rutin", "4 Personel", "Lengkap", "Klinik Berjalan") dihapus**; ambulans tanpa label kapasitas |
| Warna badge | `#dcfce7/#166534`, `#fef3c7/#92400e`, `#fee2e2/#991b1b` (persis Figma) |
| Judul halaman | aplikasi memakai "Katalog Kendaraan Dinas" (diubah di berkas oleh user) |

### 3.3 Formulir peminjaman, langkah 1 — `user-form-verifikasi-nip` (1280×1028) dan `…-error` (1280×1146)
**Isi di Figma:** judul **"Formulir Peminjaman Kendaraan"** 32/700 `#003f87` + *"Silakan lengkapi detail berikut untuk melakukan reservasi kendaraan dinas."*; **stepper** 3 langkah (lingkaran 40, radius 12; aktif `#003f87`, lain `#e5e8ee`; label **Detail Peminjam · Jadwal & Tujuan · Konfirmasi**);
kartu putih (radius 8, padding 32) berisi ikon perisai hijau (`#dcfce7`/`#15803d`, 64), **"Validasi NIP Pegawai"** 24/600, *"Masukkan NIP Pegawai Anda untuk verifikasi hak peminjaman mobil dinas."*, label **"NIP (Nomor Induk Pegawai) 18 Digit *"** 14/700, input *"Contoh: 198811042012021001"* (48 tinggi), tombol **"Lanjut"** (`#003f87`, 48, ikon panah, rata kanan);
di bawahnya dua kotak bantuan: **"Butuh Bantuan?"** (*Hubungi bagian administrasi di (0281) 123456 jika mengalami kendala sistem.*) dan **"Syarat & Ketentuan"** (*Peminjaman harus diajukan minimal 1 hari kerja sebelum keberangkatan.*).
**Keadaan error:** kotak merah (`#ffdad6`, garis `#ba1a1a`, radius 8, ikon error) di atas label: *"NIP tidak terdaftar dalam Database Pegawai Dinas Kesehatan atau status NIP non-aktif. Harap hubungi Pengelola Admin Dinkes."* (16/500).
**Perbedaan di aplikasi:** teks, warna, dan pesan **sama persis**. Perubahan atas permintaan user: stepper berujung di lingkaran (garis dari tengah lingkaran 1 ke tengah lingkaran 3), jarak atas halaman dan sekitar stepper dikurangi.
Aturan "minimal 1 hari kerja" sekarang ditegakkan server (BR-02) dan petunjuk tanggal paling cepat ditampilkan di langkah 2.

### 3.4 Langkah 2 dan 3 formulir — **tidak ada di Figma**
Dirancang dari prototipe HTML dan keputusan user: langkah 2 (kendaraan terpilih, **Tanggal Keberangkatan**, **Tanggal Kembali**, **Lokasi Tujuan** dengan saran peta, **Keperluan**); langkah 3 (**Ringkasan Pesanan**: Nama Pegawai, NIP, Kendaraan, Jadwal, Tujuan, Keperluan; tombol "Kirim Reservasi"; tanpa kotak persetujuan).
Bila Figma nanti melengkapi kedua langkah ini, cocokkan dengan implementasi.

### 3.5 Konfirmasi — `user-konfirmasi-berhasil` (1280×1241)
**Isi di Figma:** ilustrasi sukses (ikon centang biru dengan dekorasi, 96×128); judul **"Pengajuan Berhasil Terkirim!"** 32/700; paragraf *"Terima kasih telah menggunakan layanan peminjaman kendaraan operasional. Tim administrasi kami akan segera meninjau permohonan Anda. Anda dapat memantau progres pengajuan melalui dashboard akun Anda."*;
kartu ringkasan (672 lebar): **BOOKING ID** `#BRV-202410-089` (20/600 `#003f87`) + pil **"Menunggu Verifikasi"**; baris **Identitas Pemohon** (*dr. Bambang Sulistyo, Sp.P* / *NIP: 198811042012021001 • Bidang: P2P*), **Tipe Kendaraan** (*Toyota Innova Venturer (Z 1234 XY)*), **Waktu Peminjaman** (*2024-12-12 (08:00) s/d 2024-12-12 (16:00)*);
tombol **"Lihat Status Peminjaman"** dan **"Kembali ke Beranda"**; lalu "bento" dua kartu: **"Butuh Bantuan Cepat?"** (foto + tautan "Hubungi Admin Logistik") dan **"Panduan Penggunaan"** (*Pastikan Anda membawa surat tugas dan SIM yang masih berlaku saat pengambilan kendaraan.* + "Baca Selengkapnya").
**Perbedaan di aplikasi:**
| Perbedaan | Sebab |
|-----------|-------|
| Kedua kartu bantuan **dihapus**; layout dipadatkan agar muat satu layar | permintaan user |
| Kalimat "…melalui **dashboard akun Anda**" → "…melalui **halaman Status Peminjaman**" | user tidak punya akun/dashboard |
| Format Booking ID **`#BRV-YYYYMMDD-NNN`** (Figma: `#BRV-YYYYMM-NNN`) | mengikuti kode booking `DKS-YYYYMMDD-NNN` (BR-06) |
| Waktu tanpa jam ("2026-10-10 s/d 2026-10-30") | keputusan jadwal per tanggal |

### 3.6 Status Peminjaman — `user-status-peminjaman` (1280×1811) — *perbandingan pertama berbasis data Figma*
**Isi di Figma:**
- Judul **"Daftar Peminjaman Saya"** + *"Pantau status pengajuan kendaraan dinas Anda secara real-time."*
- **3 kartu statistik** (384×82, kaca): Total Booking **12** · Disetujui **8** · Menunggu Konfirmasi **3** (ubin ikon radius 4).
- **Bilah cari:** *"Cari Booking ID atau Mobil..."* (384×38) · teks *"Menampilkan 12 Pengajuan Peminjaman"* · tombol **"Filter"**.
- **Kartu pengajuan** (1200×~290–330, putih, garis `#c3c6d2`, radius 8, bayangan): baris atas = **pil status** + `Kode: DKS-20260801-001` + `Diajukan pada: 2026-07-30 09:15`; tiga kolom (362): **KENDARAAN** (nama 18/400 mis. *"Toyota Innova Zenix (Operasional Bidang P2P)"* + chip mono *"Plat No: R 1234 PA"*),
  **PEMOHON (PEGAWAI)** (nama 18/400, **`NIP: …` hijau tebal 14/700 `#22c55e`**, baris *"Bidang P2P"*), **WAKTU & TUJUAN** (tujuan 18/400 + *"2024-12-12 (08:00) s/d 2024-12-12 (16:00)"*); kaki khusus status:
  *Disetujui* = kotak hijau (`#e6f4ea`, garis `#22c55e`) **"Catatan Pengelola Dinkes:"**; *Menunggu* = tombol teks merah **"Batalkan Peminjaman"**; *Ditolak* = kotak merah (`#fce8e8`, garis `#ba1a1a`) **"Alasan Penolakan Pengelola Admin:"**.
- Pager: *"Menampilkan 1-3 dari 12 peminjaman"* + **1 2 3**. Footer penuh di bawah.
**Perbedaan di aplikasi:**
| Perbedaan | Status |
|-----------|--------|
| Kartu statistik **dihapus** | permintaan user |
| Pencarian **server-side** atas NIP, nama, kendaraan, kode booking, tujuan; tanpa tombol "Filter" | permintaan user (Figma: cari Booking ID/Mobil + tombol Filter) |
| Pagination **5 per halaman** + teks "Menampilkan X–Y dari N" | permintaan user |
| Kolom **PEMOHON** menampilkan **nama + chip `NIP: …`** (abu, mono) dan label "Pemohon" | permintaan user; **Figma:** label "PEMOHON (PEGAWAI)", NIP hijau tebal, ditambah baris **Bidang** |
| Halaman meminta **NIP dulu** sebelum daftar muncul | tidak ada di Figma (tidak ada akun) — tambahan |
| Status **Dibatalkan** (abu) | tidak ada di Figma — tambahan |
| Waktu tanpa jam | keputusan jadwal per tanggal |
| Jadwal ditulis "10 Okt 2026 – 30 Okt 2026" (format ringkas) | Figma: `2024-12-12 (08:00) s/d …` |
**Kandidat penyelarasan (belum diputuskan):** menambah baris **Bidang** di bawah pemohon; NIP hijau tebal; tombol "Filter"; kartu bergaris bawah status memakai persis warna Figma (sudah).

### 3.7 Footer — `footer-a` (1280×304)
**Isi di Figma:** kolom 1: **"Dinkes PPKB Purbalingga"** 20/700 `#f7f9ff`, **"Tentang Kami"** 14/600 `#acc7ff`, *"Sistem Manajemen Transportasi Terpadu untuk menunjang mobilitas layanan kesehatan masyarakat di Kabupaten Purbalingga."* 14/400 `#e0e3e8`,
*"© 2024 Dinas Kesehatan PPKB Kabupaten Purbalingga. Hak Cipta Dilindungi."* 12/500; kolom 2: **"Tautan Cepat"** — Website Resmi, Portal Purbalingga, Katalog Mobil, Status Peminjaman; kolom 3: **"Kontak Kami"** — *Jl. Letjen S Parman No.21, Bancar, Kec. Purbalingga, Kabupaten Purbalingga, Jawa Tengah 53316*,
`dinkes@purbalinggakab.go.id`, `(0281) 891034`; **"Ikuti Kami"** — ikon **Facebook, Instagram, YouTube, TikTok** (20 px).
**Perbedaan di aplikasi:**
| Perbedaan | Sebab |
|-----------|-------|
| **"Tautan Cepat" dihapus**; "Kontak Kami" pindah ke kolom tengah | permintaan user |
| Kolom ketiga baru **"Jam Layanan"** (jam kerja: **asumsi, belum resmi**) | permintaan user |
| Footer lebih rapat (padding 64→32, jarak antarbaris dikurangi) | permintaan user |
| Hak cipta di baris bawah penuh lebar, tahun dinamis | penyesuaian |
| **Ikon sosial: Facebook, Twitter, Instagram, YouTube** (Figma: **TikTok**, tanpa Twitter) | **selisih yang belum disengaja** — kandidat perbaikan (ganti Twitter → TikTok) |

## 4. Ringkasan: yang menyimpang dari Figma

**Atas permintaan user (disengaja):** filter katalog dihapus; desain kartu kendaraan baru (4 per baris, tanpa spesifikasi); kartu statistik Status dan statistik Beranda dihapus;
kartu bantuan Konfirmasi dihapus; "Tautan Cepat" footer dihapus + "Jam Layanan"; kotak persetujuan formulir dihapus; "Home" → "Beranda"; kalender Dipesan/Perawatan + modal;
jadwal per tanggal tanpa jam; label "Tanggal"; pencarian dan pagination Status; nama pemohon di kartu Status.

**Belum diputuskan / kandidat penyelarasan:** ikon sosial TikTok; baris Bidang dan NIP hijau di kartu Status; tombol "Filter" Status; format Booking ID; langkah 2–3 formulir (tidak ada frame);
tampilan ponsel (tidak ada frame).
