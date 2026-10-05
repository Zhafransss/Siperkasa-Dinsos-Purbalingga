# Inventaris aset gambar Figma

> Dibuat otomatis oleh `docs/design/tools/assets.js` dari data mentah. **Berkas gambarnya tidak ada di sini**: gambar hanya bisa diunduh lewat Figma API (`GET /v1/files/{key}/images` memberi semua URL gambar sekaligus dalam **satu** permintaan, berlaku sementara). Lakukan itu satu kali begitu kuota API pulih.

Total: **28 gambar unik** pada 35 node.

| imageRef (awal) | Dipakai di (frame → node id, nama, ukuran) |
|---|---|
| `c097762512…` | admin-dashboard-kalender-a → `56:5017` AB6AXuA0lvve… (40×40)<br>admin-dashboard-kalender-b → `56:5729` AB6AXuA0lvve… (40×40)<br>admin-body-88-474 → `88:894` AB6AXuA0lvve… (40×40)<br>admin-body-107-1325 → `107:1745` AB6AXuA0lvve… (40×40) |
| `f7dcc750d8…` | admin-detail-edit-kendaraan → `56:6717` AB6AXuD26ow1… (161×90) |
| `3efba2e7c0…` | admin-detail-edit-kendaraan → `56:6726` AB6AXuD-AWIB… (161×90) |
| `de59d1a07e…` | admin-detail-edit-kendaraan → `56:6735` AB6AXuDtulLq… (161×90) |
| `f5b2f90d4b…` | admin-detail-edit-kendaraan → `56:6744` AB6AXuC91SV7… (161×90) |
| `de461310cf…` | admin-laporan-operasional → `56:8950` AB6AXuAUbrBI… (38×38) |
| `5c25ba2ecd…` | admin-manajemen-armada → `56:6075` AB6AXuBju11R… (297×192) |
| `1efba22d2b…` | admin-manajemen-armada → `56:6110` AB6AXuDqx1Ec… (297×192) |
| `ecbf7fe212…` | admin-manajemen-armada → `56:6145` AB6AXuBuE_Fq… (297×192) |
| `115e336d54…` | admin-manajemen-armada → `56:6234` AB6AXuBft8zY… (40×40) |
| `f245cacd25…` | admin-manajemen-booking → `56:7274` AB6AXuBTAgVC… (192×128) |
| `5a7566921b…` | admin-manajemen-booking → `56:7312` AB6AXuCbgc1x… (192×128) |
| `f25bb374bf…` | admin-manajemen-booking → `56:7359` AB6AXuAYaLte… (38×38)<br>admin-riwayat-peminjaman → `56:7512` AB6AXuAYaLte… (38×38) |
| `071e240e3e…` | admin-manajemen-pegawai → `56:7791` AB6AXuAPl188… (30×30) |
| `7abe94746c…` | admin-masuk → `56:5785` Logo (80×80)<br>admin-daftar → `56:5074` Dinkes Logo (80×80) |
| `d29114e1df…` | admin-profil-pengaturan → `56:8650` AB6AXuBNzfh9… (30×30) |
| `9dbed0e26f…` | user-beranda → `56:1107` AB6AXuDjH4T9… (385×192) |
| `f960c1c2a5…` | user-beranda → `56:1131` AB6AXuDky5iR… (385×192) |
| `202f68c944…` | user-beranda → `56:1155` AB6AXuDjBGer… (385×192) |
| `bb297ae274…` | user-beranda → `56:1179` AB6AXuAsVIgb… (1290×667) |
| `280192a9a3…` | user-beranda → `56:1299` Logo Dinkes PPKB Purbalingga (40×40)<br>user-katalog → `56:1595` Logo Dinkes PPKB Purbalingga (40×40)<br>user-status-peminjaman → `56:2444` Logo Dinkes PPKB Purbalingga (40×40) |
| `5eb8f0ff33…` | user-katalog → `56:1363` Image (289×192) |
| `c15e2bff9c…` | user-katalog → `56:1390` Image (289×192) |
| `27ec4a885c…` | user-katalog → `56:1417` Image (289×192) |
| `8c0a98855f…` | user-katalog → `56:1443` Image (289×192) |
| `6a4e83422b…` | user-katalog → `56:1469` Image (289×192) |
| `8949b6ee78…` | user-katalog → `56:1495` Image (289×192) |
| `ae014c0714…` | user-konfirmasi-berhasil → `56:1939` AB6AXuC7o6wD… (185×174) |

Sudah diunduh dan dipakai di aplikasi (`public/images/`): logo Kabupaten, foto hero, 6 foto kendaraan (Innova, Pajero, Hiace/ambulans, Ertiga, Avanza, bus puskesmas), ilustrasi sukses. Belum diunduh: foto-foto di halaman admin (kartu armada, foto booking, galeri kendaraan, avatar admin).
