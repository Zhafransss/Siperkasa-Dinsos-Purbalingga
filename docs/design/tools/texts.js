// Writes every visible text of each frame, in reading order and grouped by region, to docs/design/texts/<slug>.txt
//   (x,y) are pixels relative to the frame's top-left corner; "size/weight" is font size and weight.
const fs = require('fs');
const path = require('path');
const { ROOT, slugs, readFrame } = require('./lib');

function collect(n, acc, ancestors) {
  if (n.visible === false) return;
  const chain = [...ancestors, n.name];
  if (n.type === 'TEXT' && n.absoluteBoundingBox && (n.characters || '').trim()) {
    acc.push({
      x: n.absoluteBoundingBox.x,
      y: n.absoluteBoundingBox.y,
      size: n.style?.fontSize,
      weight: n.style?.fontWeight,
      text: n.characters.replace(/\n/g, ' ⏎ '),
      chain,
    });
  }
  for (const c of n.children || []) collect(c, acc, chain);
}

const regionOf = (t) => (t.chain.some((c) => /^Aside/.test(c)) ? 'SIDEBAR' : t.chain.some((c) => /^Header/.test(c)) ? 'HEADER' : t.chain.some((c) => /^Footer$/.test(c)) ? 'FOOTER' : 'KONTEN');

fs.mkdirSync(path.join(ROOT, 'texts'), { recursive: true });
for (const slug of slugs()) {
  const doc = readFrame(slug).document;
  const { x: ox, y: oy, width, height } = doc.absoluteBoundingBox;
  const acc = [];
  collect(doc, acc, []);

  const rows = acc
    .map((t) => ({ ...t, rx: Math.round(t.x - ox), ry: Math.round(t.y - oy), region: regionOf(t) }))
    .sort((a, b) => a.region.localeCompare(b.region) || a.ry - b.ry || a.rx - b.rx);

  let out = `# ${doc.name}\n# ${Math.round(width)}x${Math.round(height)}\n`;
  let last = '';
  for (const r of rows) {
    if (r.region !== last) { out += `\n[${r.region}]\n`; last = r.region; }
    out += `  (${String(r.rx).padStart(4)},${String(r.ry).padStart(4)}) ${r.size}/${r.weight}  ${r.text}\n`;
  }
  fs.writeFileSync(path.join(ROOT, 'texts', slug + '.txt'), out);
}
console.log('texts written for', slugs().length, 'frames');
