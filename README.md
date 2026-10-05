# Si Perkasa — Peminjaman Kendaraan Dinas

Aplikasi web untuk **Dinas Kesehatan PPKB Kabupaten Purbalingga**: pegawai melihat ketersediaan armada, mengajukan
peminjaman kendaraan dinas, dan memantau statusnya. Dibangun dengan Laravel 12 (monolith, Blade + Tailwind CSS v4).

> **Status:** **role User (Pegawai)** dan **role Admin** (Beranda, Manajemen Kendaraan, Verifikasi Peminjaman, Manajemen Pegawai, Manajemen Admin) selesai; 148 test lulus. Panel admin di `/admin` (masuk dengan NIP + kata sandi; tidak ada halaman pendaftaran). Akun demo dev: NIP `199001012015011001` / `Admin@12345` (hanya untuk lokal, ganti sebelum produksi).
> Dokumen requirement: [`docs/SRS.md`](docs/SRS.md) · Status pengerjaan: [`docs/RTM.md`](docs/RTM.md)

## Fitur (role User)

- **Beranda** — alur peminjaman, kalender ketersediaan bulanan (chip *Dipesan* / *Perawatan*, klik tanggal untuk detail), pratinjau armada.
- **Katalog** — seluruh kendaraan, 4 kartu per baris, 8 per halaman.
- **Formulir peminjaman 3 langkah** — verifikasi NIP → tanggal & tujuan (dengan saran lokasi dari OpenStreetMap) → ringkasan & kirim.
- **Konfirmasi** — Booking ID `#BRV-YYYYMMDD-NNN`.
- **Status Peminjaman** — daftar pengajuan sendiri, pencarian (NIP, nama, kendaraan, kode booking, tujuan), pagination, pembatalan.

Aturan penting (selengkapnya di SRS bagian 8):
- Pengajuan **paling lambat 1 hari kerja** sebelum berangkat; hari H tidak bisa diajukan; akhir pekan tetap bisa dipakai.
- Jadwal **per tanggal tanpa jam**: satu kendaraan terkunci seharian dari tanggal berangkat sampai kembali.
- Tidak ada bentrok dengan pengajuan lain maupun jadwal perawatan.

## Tech stack

PHP 8.2+ · Laravel 12 · Blade · Tailwind CSS v4 + Vite · MySQL/MariaDB · Pest (test) · Laravel Pint (format).
Font Inter dan ikon Material Symbols dimuat dari Google Fonts, jadi **butuh koneksi internet** (begitu juga saran lokasi).

## Menjalankan di lokal (Windows + XAMPP)

Prasyarat: PHP 8.2+, Composer, Node.js 20+, MySQL/MariaDB (XAMPP) yang sedang berjalan.

```bash
# 1. dependensi
composer install
npm install

# 2. konfigurasi
cp .env.example .env
php artisan key:generate

# 3. database (MySQL harus aktif) — buat dua database kosong
mysql -uroot -e "CREATE DATABASE sindis CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
                 CREATE DATABASE sindis_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 4. tabel + data demo
php artisan migrate --seed

# 5. aset frontend, lalu jalankan
npm run build          # atau: npm run dev (hot reload)
php artisan serve      # http://localhost:8000
```

Pengaturan database ada di `.env` (`DB_*`). Catatan: PHP XAMPP di lingkungan ini **tidak punya `pdo_sqlite`**,
karena itu dev dan test memakai MySQL.

### Data demo

Seeder membuat pegawai fiktif, 6 kendaraan, 4 pengajuan contoh (tanggalnya relatif terhadap hari ini), dan satu jadwal perawatan.

| NIP | Nama | Keterangan |
|-----|------|-----------|
| `198811042012021001` | dr. Bambang Sulistyo, Sp.P | Punya beberapa pengajuan contoh (cocok untuk mencoba halaman Status) |
| `198203152008012004` | Siti Rahmawati, S.ST, M.Si | |
| `198501102009021002` | Apt. Hendra Setiawan, S.Farm | |
| `197508122002121003` | dr. Jatmiko Yudo, M.Kes | |
| `196512311990031009` | Pegawai Non-Aktif (Demo) | Untuk mencoba pesan error "NIP non-aktif" |

Reset data demo: `php artisan migrate:fresh --seed`.

## Test dan format kode

```bash
vendor/bin/pest      # 105 test; memakai database sindis_test (dikosongkan otomatis tiap test)
vendor/bin/pint      # rapikan gaya kode
```

## Konfigurasi opsional

| Variabel `.env` | Fungsi | Bawaan |
|-----------------|--------|--------|
| `GEOCODER_URL` | Server pencarian lokasi (kompatibel Photon). Server publik gratis tapi tanpa SLA; ganti ke server sendiri/berbayar bila beban berat | `https://photon.komoot.io/api/` |
| `GEOCODER_USER_AGENT` | Identitas aplikasi saat memanggil geocoder | `SiPerkasa/1.0 (Dinkes PPKB Purbalingga)` |

Pengaturan lain (bias lokasi Purbalingga, batas Indonesia, timeout, lama cache) ada di `config/services.php`. Zona waktu aplikasi: `Asia/Jakarta`.

## Struktur kode

```
app/
  Enums/                 VehicleStatus, VehicleType, BookingStatus (label, warna badge)
  Http/Controllers/      Home, Vehicle (katalog), Calendar, Booking, NipVerification, Status, Place
  Http/Requests/         StoreBookingRequest (aturan + lead time), VerifyNipRequest
  Models/                Employee, Vehicle, VehicleMaintenance, Booking
  Services/              BookingService (buat pengajuan + cek bentrok atomik),
                         BookingWindow (aturan lead time hari kerja), PlaceSearch (saran lokasi OSM)
database/                migrations, factories, seeders (data demo)
resources/
  css/app.css            token desain dari Figma (warna, tipografi, spasi, radius)
  js/                    ui, calendar, booking-wizard, place-autocomplete
  views/                 layout & komponen (components/), halaman, partials, pager (vendor/pagination)
public/images/           logo, hero, foto kendaraan, ilustrasi (diunduh dari Figma)
tests/                   Feature + Unit (Pest)
docs/                    SRS.md, RTM.md
```

## Catatan keamanan yang perlu diketahui

Pegawai dikenali **hanya dari NIP** (tanpa password), dan NIP bukan rahasia. Siapa pun yang tahu NIP rekannya dapat melihat atau
membatalkan pengajuan rekan itu. Ini risiko yang diterima untuk iterasi awal (SRS NFR-005); langkah berikutnya: login/OTP.
Percobaan NIP dibatasi 10 kali per menit per IP.

## Dokumen

- [`docs/SRS.md`](docs/SRS.md) — requirement, aturan bisnis, ERD, peta rute, deviasi dari Figma, pertanyaan terbuka.
- [`docs/RTM.md`](docs/RTM.md) — status setiap requirement dan test case-nya.
- [`docs/DESIGN.md`](docs/DESIGN.md) — design system Figma: warna, huruf, spasi, komponen, kerangka admin, inkonsistensi desain.
- [`docs/DESIGN-ADMIN.md`](docs/DESIGN-ADMIN.md) — spesifikasi tiap halaman admin + selisihnya dengan data aplikasi + urutan pengerjaan.
- [`docs/DESIGN-USER.md`](docs/DESIGN-USER.md) — halaman user di Figma vs yang sudah dibangun.
- [`docs/design/`](docs/design/README.md) — arsip Figma offline (pratinjau PNG, teks, struktur, data mentah, skrip). **Baca ini, bukan API Figma:** kuota Figma Starter mudah habis.
- `index (1).html` — prototipe HTML awal, **hanya referensi** (tidak dipakai aplikasi).
