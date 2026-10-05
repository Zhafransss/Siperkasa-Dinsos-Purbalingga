// Writes an indented structure outline of every frame to docs/design/outline/<slug>.txt
//   node docs/design/tools/outline.js            -> all frames
//   node docs/design/tools/outline.js <slug>     -> print one frame to stdout
// Each line: TYPE "name"  WxH  fill/stroke/radius/layout (H|V = auto-layout direction, gap, pad = top/right/bottom/left)
const fs = require('fs');
const path = require('path');
const { ROOT, slugs, readFrame, fillOf, strokeOf } = require('./lib');

function line(n, depth) {
  const b = n.absoluteBoundingBox;
  const parts = [`${n.type} "${n.name.slice(0, 60)}"`, b ? `${Math.round(b.width)}x${Math.round(b.height)}` : ''];

  if (n.type === 'TEXT') {
    const st = n.style || {};
    parts.push(`${st.fontSize}px/${st.fontWeight}`, fillOf(n) || '', JSON.stringify(n.characters));
  } else {
    const fill = fillOf(n), stroke = strokeOf(n);
    if (fill) parts.push('fill=' + fill);
    if (stroke) parts.push('stroke=' + stroke);
    if (n.cornerRadius) parts.push('r=' + n.cornerRadius);
    if (n.layoutMode && n.layoutMode !== 'NONE') {
      parts.push(n.layoutMode[0] + (n.itemSpacing ? ` gap=${Math.round(n.itemSpacing * 100) / 100}` : ''));
      const pad = [n.paddingTop, n.paddingRight, n.paddingBottom, n.paddingLeft].map((v) => Math.round((v || 0) * 100) / 100);
      if (pad.some(Boolean)) parts.push('pad=' + pad.join('/'));
    }
    if (n.effects?.some((e) => e.visible !== false && e.type.includes('SHADOW'))) parts.push('shadow');
  }
  return '  '.repeat(depth) + parts.filter(Boolean).join('  ');
}

function walk(n, depth, out) {
  if (n.visible === false) return;
  out.push(line(n, depth));
  for (const c of n.children || []) walk(c, depth + 1, out);
}

const outline = (slug) => {
  const out = [];
  walk(readFrame(slug).document, 0, out);
  return out.join('\n') + '\n';
};

if (require.main === module) {
  const only = process.argv[2];
  if (only) {
    process.stdout.write(outline(only));
  } else {
    fs.mkdirSync(path.join(ROOT, 'outline'), { recursive: true });
    for (const slug of slugs()) fs.writeFileSync(path.join(ROOT, 'outline', slug + '.txt'), outline(slug));
    console.log('outline written for', slugs().length, 'frames');
  }
}
