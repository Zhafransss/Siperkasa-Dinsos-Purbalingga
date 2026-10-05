# Arsip Desain Figma — SINDIS

Folder ini menyimpan **salinan lokal** dari desain Figma "DINKES FIX" supaya pekerjaan tidak terhenti saat API Figma
menolak permintaan (rate limit). Diambil pada **2 Oktober 2026** dengan Figma REST API.

> **Mengapa ini ada.** Akun Figma pada paket **Starter** punya kuota API yang sangat kecil. Setelah kuota habis, Figma menjawab
> `429 Too Many Requests` dengan `Retry-After: 399985` detik (±4,6 hari), tipe batas `low`, `plan-tier: starter`.
> Itu bukan batas per menit yang reda dengan menunggu sebentar. Karena itu seluruh isi desain sudah diekstrak ke sini.
> **Jangan memanggil Figma lagi** kecuali desainnya benar-benar berubah.

## Mulai dari mana

| Kebutuhan | Buka |
|-----------|------|
| Warna, huruf, spasi, komponen, kerangka layout admin | [`../DESIGN.md`](../DESIGN.md) |
| Spesifikasi tiap halaman **Admin** (isi, field, tombol, data contoh) + selisihnya dengan data kita | [`../DESIGN-ADMIN.md`](../DESIGN-ADMIN.md) |
| Halaman **User** di Figma vs yang sudah dibangun | [`../DESIGN-USER.md`](../DESIGN-USER.md) |
| Melihat tampilan sebuah halaman | `previews/<slug>.png` |
| Semua teks pada sebuah halaman (berurutan, dengan posisi dan ukuran huruf) | `texts/<slug>.txt` |
| Struktur lengkap (hierarki, ukuran, warna, radius, auto-layout) | `outline/<slug>.txt` |
| Angka token mentah (frekuensi warna, huruf, radius, bayangan) | `tokens.json` |
| Daftar gambar/foto di desain dan node asalnya | `assets.md` |
| Data asli dari Figma (JSON) | `raw/<slug>.json.gz` |

`<slug>` adalah nama pendek frame, daftarnya ada di [`tools/frames.json`](tools/frames.json) dan tabel pada `DESIGN.md` bagian 2.

## Pratinjau bukan ekspor piksel-sempurna

Gambar di `previews/` **dibuat ulang dari data node**, bukan diekspor dari Figma. Tata letak, warna, radius, garis tepi,
bayangan, dan teks akurat; tetapi **ikon digambar sebagai kotak penanda** dan **foto sebagai kotak bergaris berlabel "FOTO"**
(gambar aslinya hanya bisa diunduh lewat API). Gunakan untuk orientasi dan membandingkan proporsi, bukan untuk menyalin ikon.
Untuk ikon, pakai Material Symbols (yang sudah dipakai aplikasi) dengan nama yang paling mendekati.

## Skrip (bekerja offline dari `raw/`)

Semuanya Node.js (CommonJS; `tools/package.json` menimpa `"type": "module"` milik proyek). Jalankan dari akar proyek:

```bash
node docs/design/tools/outline.js           # tulis outline/*.txt  (atau: outline.js <slug> untuk satu frame ke layar)
node docs/design/tools/texts.js             # tulis texts/*.txt
node docs/design/tools/tokens.js            # tulis tokens.json
node docs/design/tools/assets.js            # tulis assets.md
node docs/design/tools/render.js            # tulis previews/*.png (butuh Google Chrome + internet untuk font Inter)
node docs/design/tools/render.js admin-masuk  # satu frame saja
```

`render.js` memakai Chrome di `C:\Program Files\Google\Chrome\Application\chrome.exe`; ubah lewat variabel `CHROME_PATH`.

### Mengunduh ulang dari Figma (hanya bila desain berubah)

```bash
node docs/design/tools/fetch.js              # semua frame, 4 frame per permintaan
node docs/design/tools/fetch.js admin-masuk  # frame tertentu
```

Token Figma dibaca dari `FIGMA_API_KEY` atau dari entri server MCP `figma` di `~/.claude.json`; token **tidak pernah ditulis ke berkas**.
Jika Figma menjawab 429, berhenti dan gunakan arsip ini. Saat kuota pulih, satu permintaan `GET /v1/files/{key}/images`
sekaligus memberi URL semua foto (lihat `assets.md`).

## Isi arsip

24 frame, 5.214 node: 7 frame sisi user (Beranda, Katalog, Formulir + keadaan error, Konfirmasi, Status, Footer) dan
17 frame sisi admin (login, daftar, 4 varian dashboard, armada, detail/tambah kendaraan, booking + riwayat, pegawai +
detail, profil, laporan, tambah jadwal, modal "Hubungi Administrator"). Frame `footer-b` (id `84:128`, duplikat `footer-a`) tidak sempat
terambil saat kuota habis dan tidak diperlukan.

Bagian halaman yang tidak ada di Figma: **Manajemen Admin** (dimintakan user, tidak ada frame-nya) dan semua tampilan
**ponsel** (seluruh frame berukuran desktop 1280 px).
