// Lists every node that has an image fill (photos, logo, avatars) -> docs/design/assets.md
// The image files themselves can only be downloaded through the Figma API (rate limited), but this inventory records
// WHICH node holds which image (imageRef) so a future download can be done in one request.
const fs = require('fs');
const path = require('path');
const { ROOT, frames, slugs, readFrame } = require('./lib');

const have = new Set(fs.existsSync(path.join(ROOT, '..', '..', 'public', 'images')) ? ['logo-dinkes', 'hero', 'vehicles'] : []);
const rows = [];

function walk(n, slug) {
  if (n.visible === false) return;
  const img = (n.fills || []).find((f) => f.type === 'IMAGE' && f.imageRef && f.visible !== false);
  if (img && n.absoluteBoundingBox) {
    rows.push({ slug, id: n.id, name: n.name.length > 28 ? n.name.slice(0, 12) + '…' : n.name, w: Math.round(n.absoluteBoundingBox.width), h: Math.round(n.absoluteBoundingBox.height), ref: img.imageRef });
  }
  for (const c of n.children || []) walk(c, slug);
}
for (const slug of slugs()) walk(readFrame(slug).document, slug);

// Same image used in several nodes/frames: group by imageRef.
const byRef = new Map();
for (const r of rows) (byRef.get(r.ref) || byRef.set(r.ref, []).get(r.ref)).push(r);

let md = '# Inventaris aset gambar Figma\n\n';
md += '> Dibuat otomatis oleh `docs/design/tools/assets.js` dari data mentah. **Berkas gambarnya tidak ada di sini**: gambar hanya bisa diunduh lewat Figma API ';
md += '(`GET /v1/files/{key}/images` memberi semua URL gambar sekaligus dalam **satu** permintaan, berlaku sementara). Lakukan itu satu kali begitu kuota API pulih.\n\n';
md += `Total: **${byRef.size} gambar unik** pada ${rows.length} node.\n\n`;
md += '| imageRef (awal) | Dipakai di (frame → node id, nama, ukuran) |\n|---|---|\n';
for (const [ref, list] of [...byRef.entries()].sort((a, b) => a[1][0].slug.localeCompare(b[1][0].slug))) {
  const uses = list.map((r) => `${r.slug} → \`${r.id}\` ${r.name} (${r.w}×${r.h})`).join('<br>');
  md += `| \`${ref.slice(0, 10)}…\` | ${uses} |\n`;
}
md += '\nSudah diunduh dan dipakai di aplikasi (`public/images/`): logo Kabupaten, foto hero, 6 foto kendaraan (Innova, Pajero, Hiace/ambulans, Ertiga, Avanza, bus puskesmas), ilustrasi sukses. ';
md += 'Belum diunduh: foto-foto di halaman admin (kartu armada, foto booking, galeri kendaraan, avatar admin).\n';
fs.writeFileSync(path.join(ROOT, 'assets.md'), md);
console.log('assets.md:', byRef.size, 'unique images,', rows.length, 'nodes');
