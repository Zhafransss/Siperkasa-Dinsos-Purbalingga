# Dokumentasi Desain — Design System & Komponen

> Disusun dari Figma "DINKES FIX" (file key `vvnnzaRdWrTlzN1BGseZzr`) pada **2 Oktober 2026**, dari 24 frame / 5.214 node.
> Dokumen ini dibuat agar pekerjaan UI **tidak bergantung pada Figma**: Figma Starter menolak permintaan API selama ±4,6 hari
> setelah kuota habis (lihat [`design/README.md`](design/README.md)).
>
> Dokumen terkait: [`DESIGN-ADMIN.md`](DESIGN-ADMIN.md) (halaman admin) · [`DESIGN-USER.md`](DESIGN-USER.md) (halaman user) ·
> data mentah, pratinjau, teks, dan skrip di [`design/`](design/).

---

## 1. Ringkasan

- Satu keluarga huruf: **Inter** (plus Liberation Mono 13 px hanya untuk label plat). Seluruh frame berukuran **desktop 1280 px**;
  **tidak ada desain ponsel**.
- Dua "wajah": **sisi user** (navbar atas, konten terpusat, footer gelap) dan **sisi admin** (sidebar kiri 256 px + top bar 64 px).
- Gaya: Material-style berwarna biru tua, sudut kecil (4 px untuk kontrol, 8 px untuk kartu), garis tepi tipis abu-abu kebiruan,
  hampir tanpa bayangan.
- Ada **inkonsistensi antar frame** (dua biru utama, dua abu garis tepi, tiga nama merek, beberapa gaya menu aktif). Semuanya
  dicatat di bagian 8 supaya keputusan implementasi disengaja, bukan kebetulan.

## 2. Daftar frame

`Slug` dipakai di seluruh arsip (`design/previews/<slug>.png`, `texts/`, `outline/`, `raw/`).

| Slug | Node id | Ukuran | Nama frame di Figma | Pratinjau |
|------|---------|--------|---------------------|-----------|
| `user-beranda` | `56:891` | 1290×2873 | Beranda - Foto Mobil Proporsional | [png](design/previews/user-beranda.png) |
| `user-katalog` | `56:1311` | 1280×1136 | Katalog Mobil Dinas | [png](design/previews/user-katalog.png) |
| `user-form-verifikasi-nip` | `56:1738` | 1280×1028 | Formulir Peminjaman - Verifikasi NIP | [png](design/previews/user-form-verifikasi-nip.png) |
| `user-form-verifikasi-nip-error` | `56:2017` | 1280×1146 | …(Error State) | [png](design/previews/user-form-verifikasi-nip-error.png) |
| `user-konfirmasi-berhasil` | `56:1877` | 1280×1241 | Konfirmasi Pengajuan Berhasil | [png](design/previews/user-konfirmasi-berhasil.png) |
| `user-status-peminjaman` | `56:2163` | 1280×1811 | Status Peminjaman - Updated Layout | [png](design/previews/user-status-peminjaman.png) |
| `footer-a` | `84:65` | 1280×304 | Footer | [png](design/previews/footer-a.png) |
| `admin-masuk` | `56:5778` | 1280×811 | Masuk Admin | [png](design/previews/admin-masuk.png) |
| `admin-masuk-background` | `85:191` | 1280×1009 | **Modal "Hubungi Administrator"** (dari "Lupa Kata Sandi?") di atas latar gelap | [png](design/previews/admin-masuk-background.png) |
| `admin-daftar` | `56:5066` | 1280×1029 | Daftar Admin (Registrasi Admin) | [png](design/previews/admin-daftar.png) |
| `admin-body-107-1325` | `107:1325` | 1280×2198 | Dashboard — **varian D, terlengkap** | [png](design/previews/admin-body-107-1325.png) |
| `admin-body-88-474` | `88:474` | 1280×2198 | Dashboard — varian C | [png](design/previews/admin-body-88-474.png) |
| `admin-dashboard-kalender-a` | `56:4635` | 1280×1587 | Dashboard — varian A | [png](design/previews/admin-dashboard-kalender-a.png) |
| `admin-dashboard-kalender-b` | `56:5348` | 1280×1381 | Dashboard — varian B | [png](design/previews/admin-dashboard-kalender-b.png) |
| `admin-manajemen-armada` | `56:6036` | 1280×1088 | Manajemen Armada Kendaraan | [png](design/previews/admin-manajemen-armada.png) |
| `admin-tambah-kendaraan` | `56:6826` | 1280×1526 | Tambah Kendaraan Baru | [png](design/previews/admin-tambah-kendaraan.png) |
| `admin-detail-edit-kendaraan` | `56:6567` | 1280×1204 | Detail & Edit Kendaraan | [png](design/previews/admin-detail-edit-kendaraan.png) |
| `admin-manajemen-booking` | `56:7246` | 1280×1024 | Manajemen Booking Kendaraan ("Manajemen Reservasi") | [png](design/previews/admin-manajemen-booking.png) |
| `admin-riwayat-peminjaman` | `56:7416` | 1280×1024 | Manajemen Booking — tab Riwayat | [png](design/previews/admin-riwayat-peminjaman.png) |
| `admin-manajemen-pegawai` | `56:7569` | 1280×1070 | Manajemen Pegawai & Verifikasi NIP | [png](design/previews/admin-manajemen-pegawai.png) |
| `admin-detail-edit-pegawai` | `56:7829` | 1280×1024 | Detail & Edit Data Pegawai | [png](design/previews/admin-detail-edit-pegawai.png) |
| `admin-profil-pengaturan` | `56:8532` | 1280×1201 | Profil & Pengaturan Akun Admin | [png](design/previews/admin-profil-pengaturan.png) |
| `admin-laporan-operasional` | `56:8696` | 1280×1457 | Laporan Operasional (grafik batang) | [png](design/previews/admin-laporan-operasional.png) |
| `admin-tambah-jadwal` | `56:5828` | 1280×1097 | Tambah Jadwal Baru | [png](design/previews/admin-tambah-jadwal.png) |

## 3. Warna

Angka "dipakai" = jumlah kemunculan di seluruh 5.214 node (sumber: `design/tokens.json`).
Kolom **Token app** adalah nama utilitas Tailwind yang sudah ada di `resources/css/app.css`; "—" berarti belum ada.

### 3.1 Teks

| Hex | Dipakai | Peran | Token app |
|-----|--------:|-------|-----------|
| `#181c20` | 399 | Teks utama, judul formulir | `on-background` / `on-surface` |
| `#434751` | 181 | Teks sekunder, label (admin) | ≈ `on-surface-variant` (`#424752`, selisih 1) |
| `#002a5d` | 157 | **Judul halaman admin**, tautan, angka statistik | — (lihat 8.1) |
| `#737782` | 157 | Teks redup / label kecil huruf kapital | ≈ `outline` (`#727784`, selisih 1) |
| `#424752` | 75 | Teks sekunder (sisi user) | `on-surface-variant` |
| `#40484f` | 70 | Menu sidebar non-aktif + ikon | — |
| `#ffffff` | 62 | Teks di atas biru/hijau/merah | `white` |
| `#6b7280` | 35 | Placeholder input | (Tailwind gray-500) |
| `#ba1a1a` | 30 | Error, tombol Keluar, tolak | `error` |
| `#166534` | 26 | Teks badge hijau | — |
| `#003f87` | 26 | Biru utama sisi user | `primary` |
| `#e0e3e8` | 18 | Teks di footer gelap | `surface-variant` |
| `#1e40af` / `#92400e` | 14 / 10 | Teks chip biru / amber (kalender admin) | — |
| `#22c55e` / `#f59e0b` | 14 / 12 | Hijau sukses / oranye peringatan | `success` / `warning` |
| `#acc7ff` | 8 | Judul kolom footer | `primary-fixed-dim` |

### 3.2 Latar / isi (fill)

| Hex | Peran | Token app |
|-----|-------|-----------|
| `#ffffff` | Kartu, input, dropdown | `surface-container-lowest` |
| `#f7f9ff` | Latar halaman, **sidebar admin** | `background` / `surface` |
| `#f1f4f9` | Input pada form edit, kepala tabel, bilah pagination | `surface-container-low` |
| `#ebeef3` | Tile kosong, kotak info netral | `surface-container` |
| `#e5e8ee` | Tombol tonal "Detail" | `surface-container-high` |
| `#dbe3ed` | **Menu aktif sidebar**, avatar inisial | ≈ `secondary-fixed` (`#dbe4ed`) |
| `#d7e2ff` | Badge bidang, kotak info biru | `primary-fixed` |
| `#002a5d` | **Tombol utama admin**, sel hari ini, kepala sidebar | — |
| `#003f87` | Tombol utama sisi user, logo | `primary` |
| `#0056b3` | Tombol hover/aksi sisi user | `primary-container` |
| `#181c20` | Footer user | `on-background` |
| `#22c55e` · `#dcfce7` | Hijau solid · hijau muda (badge, chip) | `success` · — |
| `#f59e0b` · `#fef3c7` | Oranye solid · amber muda | `warning` · — |
| `#ba1a1a` · `#ffdad6` · `#fee2e2` | Merah solid · merah muda (panel peringatan) | `error` · `error-container` · — |
| `#dbeafe` | Chip biru kalender | — |
| `#2d3135` | Toast | — |
| `#575f67` | Tombol abu "Perbarui Kata Sandi" | `secondary` |

### 3.3 Garis tepi (stroke)

`#c3c6d2` (278 kali) adalah garis standar sisi admin; sisi user memakai `#c2c6d4` (88 kali) — dua nilai nyaris sama (token app: `outline-variant`
= `#c2c6d4`). Aksen: `#002a5d` (tab/tombol outline aktif), `#ba1a1a` (tolak, panel peringatan), `#2563eb` / `#16a34a` / `#d97706`
(garis kiri chip kalender), `#fbbf24` (kartu peringatan amber), `#6b7280` (tombol netral).

## 4. Tipografi

Keluarga: **Inter**. `lh` = tinggi baris. Skala yang dipakai (jumlah kemunculan terbanyak dulu):

| Gaya | Ukuran/berat | lh | Dipakai untuk | Token app (`text-…`) |
|------|--------------|----|---------------|----------------------|
| label-sm | 12 / 500 | 16 | Label kecil, kepala kolom, pesan bantu | `label-sm` |
| label-md | 14 / 600 (+0,14 spasi huruf) | 20 | Tombol, label field, menu | `label-md` |
| body-md | 16 / 400 | 24 | Teks isi, input | `body-md` |
| body-sm | 14 / 400 | 20 | Teks isi kecil, sel tabel | `body-sm` |
| headline-sm | 20 / 600 | 28 | Judul kartu/seksi | `headline-sm` |
| micro | 10 / 400–700 | 15 | Chip kalender, subjudul merek, sub-label | — |
| headline-md | 24 / 600 | 32 | Judul kartu login, angka statistik | `headline-md` |
| headline-lg | 32 / 700 (−0,32) | 40 | **Judul halaman** | `headline-lg` |
| brand | 20 / 700 · 20 / 900 | 28 | "Dinkes PPKB" · "SI-ARMADA DINKES" | — |
| headline-xl | 48 / 700 (−0,96) | 56 | Judul hero Beranda | `headline-xl` |
| display | 60 / 700 | 60 | Angka besar "01 02 03" di langkah Beranda | — |
| huruf kapital | 12–16 / 400–500, spasi 0,5–0,8 | — | Label seperti "TOTAL ARMADA", "PEMINJAM" | `uppercase tracking-wider` |
| mono | Liberation Mono 13 / 400 | 20 | Label "Plat No: R 1234 PA" | `font-mono` |

Catatan: beberapa frame admin memakai **16/400** untuk judul seksi dan label field (mis. "Informasi Dasar", "Merk & Model"), sementara
frame lain memakai 20/600 dan 14/600. Ini tampak seperti perbedaan antar iterasi desain, bukan aturan; lihat 8.3.

## 5. Bentuk, bayangan, spasi

**Radius** (jumlah): `4` (253) kontrol & tombol · `12` (167) ubin ikon, pil, avatar — dipakai sebagai "penuh" · `8` (133) kartu ·
`2` (62) badge persegi kecil · `6` (16) segmen toggle · `16/20/24` jarang. Token app: `rounded-lg`=4, `rounded-xl`=8, `rounded-full`=12, `rounded-sm`=2.

**Bayangan:** hampir tidak ada. `0 1 2 rgba(0,0,0,.05)` (kartu, 27×) · `0 4 6 -4 rgba(0,0,0,.1)` + `0 10 15 -3 rgba(0,0,0,.1)` (kartu terangkat) ·
versi bertinta biru **`rgba(0,42,93,.2)`** untuk tombol utama admin · `0 25 50 -12 rgba(0,0,0,.25)` (modal "Hubungi Administrator" dan toast) ·
`BACKGROUND_BLUR 12` pada top bar admin dan kartu translusen (kaca).

**Spasi** (auto-layout): jarak antaritem `8` (166×), `12`, `16`, `4`, `24`, `32`. Padding paling umum: `12/16`, `16`, `24`, `32`.
Lebar konten admin **944 px** (kanvas 1024 − 2×40). Kartu seksi form: padding 32, jarak antarseksi 32.

## 6. Kerangka halaman admin (shell)

Semua halaman admin (kecuali login/daftar) memakai kerangka yang sama pada kanvas 1280 px:

```
┌──────────────┬───────────────────────────────────────────────┐
│ SIDEBAR 256  │ TOP BAR 64 (x=256, lebar 1024, kaca blur)     │
│ fill #f7f9ff │───────────────────────────────────────────────│
│ border-kanan │ KONTEN: padding 96/40/32/40, jarak 32          │
│ #c3c6d2      │ (lebar isi 944; 96 = 64 top bar + 32)          │
└──────────────┴───────────────────────────────────────────────┘
```

**Sidebar** (padding atas/bawah 32, tinggi mengikuti halaman):
- Merek (padding 24): ubin logo 40×40 (`#003f87`, radius 4–8) + "Dinkes PPKB" 20/700 `#002a5d` + "PURBALINGGA FLEET" 10/700 `#737782` huruf kapital.
- Menu (jarak 4–8, padding samping 16), tiap item **223×44**, radius 4, padding 12/16, ikon 18–20 px + teks 14/600.
  Non-aktif: `#40484f`. **Aktif:** latar `#dbe3ed`, teks/ikon `#002a5d`, **garis kanan 4 px `#002a5d`**.
- Urutan menu di Figma: **Dashboard · Kelola Kendaraan · Manajemen Reservasi · Data Pegawai · Laporan · Profil Saya**; di dasar: **Keluar**
  (`#ba1a1a`, 14/600–700, ikon merah).

**Top bar** (tinggi 64, padding 0/40, latar putih 80 % + blur 12, garis bawah `#c3c6d2`), isi bervariasi per halaman:
kiri = kolom cari (448×38, `#f1f4f9`, radius 12, ikon kaca pembesar, placeholder berbeda per halaman) **atau** judul merek;
kanan = lonceng notifikasi (titik merah 8 px `#ba1a1a`), satu tombol ikon lain, pemisah vertikal 1×32, nama + subjudul pengguna
(14/600 + 10/400), avatar 40×40 radius 12 bergaris tepi.

**Kanvas konten:** judul halaman 32/700 `#002a5d` + subjudul 16/400 `#434751` (kiri) dan tombol aksi utama (kanan); lalu isi.
Beberapa frame (form detail/tambah) memakai breadcrumb 12/500 di atas judul dan padding `64/12/0/12` + kartu 40 px.

## 7. Komponen

### 7.1 Tombol

| Varian | Spesifikasi | Contoh |
|--------|-------------|--------|
| Utama | isi `#002a5d`, teks putih 14/600, tinggi 36–48, padding 8–12/24–32, radius 4 (kadang 8/12), bayangan bertinta biru | "Tambah Kendaraan", "Simpan Perubahan", "Masuk ke Dashboard" |
| Sekunder (outline) | garis `#002a5d` (atau `#c3c6d2`), teks `#002a5d`/`#434751`, latar putih | "Ekspor Laporan", "Impor Data", "Batal" |
| Sukses | isi `#22c55e`, teks putih, 104×36 | "Setujui" |
| Bahaya (outline) | garis `#ba1a1a`, teks `#ba1a1a`, 104×38 | "Tolak" |
| Tonal | isi `#e5e8ee`, teks `#002a5d` 14/600 | "Detail" pada kartu armada |
| Abu | isi `#575f67`, teks putih, radius 8 | "Perbarui Kata Sandi" |
| Tautan | teks `#002a5d` 14/600 tanpa kotak | "Detail", "Lihat Semua", "Lupa Kata Sandi?" |
| Ikon | kotak 32–40, garis `#c3c6d2`, ikon `#434751`/merah untuk hapus | edit / hapus di kartu |

### 7.2 Input

- **Teks:** tinggi 42–50 (login 46, daftar 42), radius 4, garis `#c3c6d2` 1 px, padding 12/16; **isi `#f1f4f9`** pada form *edit/detail*, **`#ffffff`**
  pada form *tambah*, `#f7f9ff` pada login. Placeholder `#6b7280` 14–16/400.
- **Ikon di dalam input:** ikon 16–20 px di kiri, padding kiri 40–44 (login: ID, kata sandi; daftar: nama, NIP, email).
- **Kata sandi:** ikon gembok kiri + tombol mata kanan (padding kanan 48).
- **Select:** sama dengan input + chevron di kanan (`#6b7280`).
- **Tanggal:** input dengan kontrol native `mm/dd/yyyy` + ikon kalender. **Jam:** `--:-- --` (format 12 jam AM/PM).
- **Dengan satuan:** kolom angka dengan sufiks kecil di kanan ("Orang", "KM"; 12/500 `#434751`).
- **Textarea:** sama, tinggi bebas; penghitung "0/200 karakter" (12/500) di atas kanan.
- **Label:** 14/600 `#434751`, jarak ke input 5,5–9,5 px; tanda wajib tidak ditunjukkan di admin.
- **Pesan bantu:** 12/500 `#575f67` di bawah input ("Minimal 8 karakter dengan kombinasi angka dan simbol.").

### 7.3 Kartu

| Kartu | Spesifikasi |
|-------|-------------|
| Dasar | putih, garis `#c3c6d2`, radius 8, padding 24–32, bayangan `0 1 2 .05` |
| Kaca (statistik) | putih 70–85 % + blur, garis `#c3c6d2`, radius 8, padding 16–24 |
| Statistik | 218×110–134: ubin ikon 45–56×56 (radius 12, warna 10–20 %) + label huruf kapital 12–16/500 `#737782` + angka 24/600 `#002a5d` |
| Armada | 299×391: foto 192 tinggi, pil status kiri atas, pil kategori kanan bawah (hitam 50 %), plat 20/600, model 14/400, garis, baris data, tombol |
| Booking | 944×178, putih 70 %, padding 24: foto 192×128 + 4 kolom label/isi (PEMINJAM, TUJUAN, ARMADA, WAKTU) + kolom aksi |
| Detail penggunaan | 278×333, garis 1 px, padding 16: judul kendaraan 16/700 `#002a5d`, plat 12/500, badge status, pemohon (nama 14/700 + "NIP:" 12/400), bidang, waktu, tujuan, garis, tombol |
| Kosong / tambah | isi `#ebeef3`, **garis putus-putus 6/4 setebal 2 px** `#c3c6d2`, ubin ikon 64, judul 14/600, isi 14/400 `#737782` |
| Panel peringatan | isi `#ffdad6` 10 %, garis `#ba1a1a`, radius 8, judul 20/600 merah; item putih radius 4 bergaris merah/amber |
| Kotak info | isi `#d7e2ff` + garis `#acc7ff` (biru) atau `#ebeef3` + `#c3c6d2` (netral), padding 16, ubin ikon 36 |

### 7.4 Badge, pil, chip

| Kegunaan | Bentuk | Warna |
|----------|--------|-------|
| Status armada (kartu admin) | pil radius 12, tinggi 24, titik 6 px + teks 12/500 | **Tersedia** isi `#22c55e` 90 % teks putih · **Dalam Tugas** isi `#002a5d` 90 % teks putih · **Maintenance** isi `#f59e0b` 90 % teks `#181c20` |
| Status armada (katalog user) | persegi radius 2, 10/700 huruf kapital, di atas foto | Tersedia `#dcfce7`/`#166534` · Sedang Dipakai `#fef3c7`/`#92400e` · Perawatan `#fee2e2`/`#991b1b` |
| Status pengajuan (riwayat) | pil radius 12, 10/700 huruf kapital | SELESAI `#dcfce7`/`#15803d` · DITOLAK `#fee2e2`/`#b91c1c` · DIBATALKAN `#dbe3ed`/`#5d656d` |
| Status pengajuan (laporan) | pil radius 12, 12/500 | Selesai `#dcfce7`/`#166534` · **Dibatalkan `#fef3c7`/`#92400e`** (beda dari riwayat!) |
| Status pengajuan (sisi user) | pil radius 12, 12/700 | Disetujui `#e6f4ea`/`#22c55e` · Menunggu `#fff8e1`/`#f59e0b` · Ditolak `#fce8e8`/`#ba1a1a` |
| Verifikasi pegawai | titik 6–8 px + teks 12/500 | Terverifikasi `#22c55e` · Menunggu Validasi `#f59e0b` |
| Bidang pegawai | pil radius 12, 12/500 | `#d7e2ff`/`#0d458d` (aktif) atau `#e0e3e8`/`#434751` |
| Chip kalender | radius 2, 10/400, **garis kiri 1 px berwarna** | Biru (Dinas Luar, Rapat) `#dbeafe` + `#2563eb`, teks `#1e40af` · Hijau (tugas lapangan) `#dcfce7` + `#16a34a`, teks `#166534` · Amber (servis, ganti ban) `#fef3c7` + `#d97706`, teks `#92400e` |
| Penghitung tab | pil 23×16 | isi `#ba1a1a`, teks putih 10/600 |

### 7.4a Peta status → data aplikasi

| Tampilan Figma | Nilai di database aplikasi |
|----------------|---------------------------|
| Tersedia / Dalam Tugas / Maintenance (kendaraan) | `vehicles.status`: `tersedia` / `dipakai` / `perawatan` |
| Menunggu / Disetujui / Ditolak / Dibatalkan (pengajuan) | `bookings.status`: `menunggu` / `disetujui` / `ditolak` / `dibatalkan` |
| **Selesai** (riwayat, laporan) | **Belum ada.** Bisa diturunkan: `disetujui` dan `returns_on` sudah lewat; atau tambah status `selesai` (butuh aksi "Tandai Selesai") |
| Terverifikasi / Menunggu Validasi (pegawai) | **Belum ada** (hanya `employees.is_active`) |

### 7.5 Tabel

Kartu putih radius 8 bergaris tepi; (opsional) bilah alat di atas (cari, filter, "Filter Lanjut") padding 16; kepala kolom 14/600
(isi `#f1f4f9` atau `#ebeef3`); baris padding 16/24 dengan garis pemisah `#c3c6d2`; sel nama = avatar inisial 40×40 radius 12
(`#dbe3ed`/`#d7e2ff`, 16/700 `#002a5d`) + nama 14/600 + email 14/400 `#434751`; NIP 16/400 + sub-baris 12/500 `#434751`.
Kaki tabel: isi `#f1f4f9`, teks "Menampilkan 1-10 dari 142 pegawai" 14/400 di kiri, pager di kanan.

### 7.6 Pagination

Kotak 32×32 radius 4: aktif isi `#002a5d` teks putih 14/600; lain garis `#c3c6d2` teks `#181c20`; panah di tepi; elipsis "…" antar
nomor ("1 2 3 … 15"). Sisi user: tombol 31–36×44 dengan bentuk sama.

### 7.7 Tab

Garis bawah penuh `#c3c6d2`; tab aktif bergaris bawah `#002a5d` dan teks `#002a5d` 14/600; non-aktif `#434751` 14/600; jarak antartab 32;
tab "Pengajuan Baru" memuat penghitung merah.

### 7.8 Toast

Isi `#2d3135`, radius 8, padding 16/32, teks `#eef1f6` 14/600, ±303×52, di tengah bawah kanvas (mis. "Pengajuan berhasil disetujui!"),
bayangan; awalnya `opacity 0`.

### 7.9 Unggah foto

Slot 202×196, **garis putus-putus 6/4 setebal 2 px `#c3c6d2`**, radius 8, ikon kamera 29 px `#737782` + label 12/500 ("Sisi Depan", "Sisi Samping",
"Sisi Belakang", "Interior"). Petunjuk di bawah: ikon info + "Ukuran foto maksimal 5MB per file. Format yang didukung: JPG, PNG."
Galeri pada halaman detail: miniatur 163×92 radius 4; saat disorot, lapisan hitam 40 % dengan dua tombol bulat putih (edit biru, hapus merah);
slot terakhir "Tambah Foto" bergaris putus-putus.

### 7.10 Modal

Hanya satu modal yang didesain: **"Hubungi Administrator"** (frame `admin-masuk-background`), dibuka dari tautan "Lupa Kata Sandi?" di halaman login.
Lapisan gelap `#000000` 85 % menutup seluruh layar; kartu 512 lebar, **radius 24**, putih, bayangan `0 25 50 -12 .25`; kepala 73 tinggi (tombol tutup ✕ di kiri,
garis bawah `#f3f4f6`); isi padding 32: ikon lonceng biru 32 px, judul 20/700 `#111827`, penjelasan 15/400 `#6b7280`, **tabel detail 2 kolom**
(label 14,5/600 `#003f87` lebar 120 + nilai 14,5/400 `#1f2937`): Nama Admin, Jabatan, Telepon / WA, Email, Lokasi; kotak peringatan kuning
(`#fff9e2`, garis `#fef9c3`, radius 16, ikon `#ca8a04`, teks 14/400); tombol "Mengerti" 448×56, `#003f87`, radius 12, 16/700, bayangan.
Catatan: modal ini memakai palet Tailwind (`#111827`, `#1f2937`, `#6b7280`) dan biru user `#003f87`, bukan navy admin — gaya dari iterasi berbeda.
Pola ini relevan untuk dialog konfirmasi lain (hapus, nonaktifkan); aplikasi sudah punya `#confirm-modal` di layout user.

### 7.11 Kalender admin

Kartu putih radius 8: kepala (judul "Kalender Pemakaian" 20/600, **toggle Bulan | Minggu** 137×34, tombol ‹ ›, "Oktober 2023" 14/600);
baris hari MIN–SAB 16/400 `#737782`; sel **129×90** (padding 8) dengan jarak 1 px di atas latar `#c3c6d2`; tanggal luar bulan `#737782`
dan sel abu; **hari ini** = garis 2 px `#002a5d` + angka 12/700; titik merah 6 px menandai hari dengan agenda mendesak; chip agenda
(lihat 7.4). Sisi user memakai kalender serupa dengan sel berisi "8 Tersedia" / "5 Dipesan" (lihat `DESIGN-USER.md`).

## 8. Inkonsistensi desain yang perlu keputusan

### 8.1 Dua biru utama
Sisi user memakai **`#003f87`** (tombol, logo, judul formulir); sisi admin memakai **`#002a5d`** (tombol, judul halaman, angka). Aplikasi saat ini
hanya mendefinisikan `primary = #003f87`. **Usulan:** tambah token `--color-navy: #002a5d` untuk admin (jangan mengganti `primary` agar halaman user
tidak berubah).

### 8.2 Abu garis tepi
`#c3c6d2` (admin) vs `#c2c6d4` (user): selisih 1–2 tingkat, tidak terlihat. **Usulan:** pakai satu (`outline-variant`).

### 8.3 Ukuran huruf judul seksi/label
Form *tambah* kendaraan: judul seksi 20/600 + label 14/600. Form *detail/edit* kendaraan dan pegawai, Laporan, Profil, Tambah Jadwal:
16/400 untuk judul seksi dan label. **Usulan:** satu gaya (20/600 + 14/600) agar konsisten dengan sisa aplikasi.

### 8.4 Nama merek
"Dinkes PPKB Purbalingga" (user) · "Dinkes PPKB / PURBALINGGA FLEET" (sidebar) · "SI-ARMADA DINKES" / "Si-Armada Dinkes" / "Fleet Admin"
(top bar) · "Sistem Si-Armada" (kotak info pegawai). Aplikasi bernama **SINDIS**. **Usulan:** seragamkan ke "SINDIS" atau "Dinkes PPKB Purbalingga".

### 8.5 Menu sidebar
Figma: Dashboard, Kelola Kendaraan, Manajemen Reservasi, Data Pegawai, Laporan, Profil Saya, Keluar. Permintaan user: **Beranda, Manajemen Kendaraan,
Verifikasi Peminjaman, Manajemen Pegawai, Manajemen Admin** (+ Keluar). Lihat `DESIGN-ADMIN.md` bagian 2.

### 8.6 Radius tombol
Tombol utama: 4 px pada sebagian besar frame, 8 px pada "Tambah Kendaraan"/"Tambah Pegawai", 12 px pada form tambah kendaraan.
**Usulan:** 4 px (selaras dengan sisi user).

### 8.7 Warna status "Dibatalkan"
`#dbe3ed`/`#5d656d` (abu) di tab Riwayat, `#fef3c7`/`#92400e` (amber) di Laporan. **Usulan:** abu di semua tempat.

### 8.8 Isi data contoh bertanggal 2023/2024
Seluruh data contoh (kalender Oktober 2023, "© 2024") hanya ilustrasi — jangan disalin ke aplikasi.

### 8.9 Hanya desktop
Tidak ada frame ponsel/tablet. Perilaku responsif (sidebar menjadi laci, tabel menjadi kartu) harus diputuskan saat implementasi.

## 9. Pedoman penerapan di aplikasi

1. Token sudah ada di `resources/css/app.css` (`@theme`). Tambahkan hanya yang belum ada: `navy` (`#002a5d`), `sidebar-text` (`#40484f`),
   `green-badge` (`#dcfce7`/`#166534`), dst. — atau pakai kelas Tailwind bawaan (`bg-green-100 text-green-800` ≈ `#dcfce7`/`#166534`).
2. Ikon: Material Symbols Outlined (sudah dimuat). Pemetaan nama ikon dibuat saat membangun tiap halaman; pratinjau hanya memberi kotak penanda.
3. Komponen Blade yang layak dibuat bersama: `admin-layout` (sidebar + top bar), `page-header`, `stat-card`, `status-pill`, `data-table`, `pagination`
   (sudah ada `vendor/pagination/sindis.blade.php`), `form-section`, `toast` (sudah ada di layout user).
4. Setiap halaman admin harus menandai menu aktif lewat `request()->routeIs()` seperti navbar user.
5. Gambar: lihat `design/assets.md`; foto admin belum diunduh.
