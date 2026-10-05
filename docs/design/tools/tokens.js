// Aggregates design tokens (colours, type, radii, shadows, spacing) over all frames -> docs/design/tokens.json
const fs = require('fs');
const path = require('path');
const { ROOT, slugs, readFrame, hex } = require('./lib');

const colors = { text: {}, fill: {}, stroke: {} };
const fonts = {}, radii = {}, shadows = {}, gaps = {}, pads = {};
const images = {};
let nodes = 0;
const bump = (o, k) => { o[k] = (o[k] || 0) + 1; };

function walk(n, slug) {
  if (n.visible === false) return;
  nodes++;

  if (n.type === 'TEXT') {
    const f = (n.fills || []).find((x) => x.type === 'SOLID' && x.visible !== false);
    if (f) bump(colors.text, hex(f.color));
    const st = n.style || {};
    bump(fonts, `${st.fontFamily} ${st.fontSize}px/${st.fontWeight} lh=${st.lineHeightPx ? Math.round(st.lineHeightPx) : '-'}${st.letterSpacing ? ' ls=' + st.letterSpacing.toFixed(2) : ''}${st.textCase ? ' ' + st.textCase : ''}`);
  } else {
    for (const f of n.fills || []) {
      if (f.visible === false) continue;
      if (f.type === 'SOLID') bump(colors.fill, hex(f.color, f.opacity ?? 1));
      if (f.type === 'IMAGE' && f.imageRef) (images[f.imageRef] ||= new Set()).add(slug);
    }
    for (const s of n.strokes || []) if (s.visible !== false && s.type === 'SOLID') bump(colors.stroke, hex(s.color));
  }

  const r = n.cornerRadius ?? (n.rectangleCornerRadii ? n.rectangleCornerRadii.join('/') : null);
  if (r) bump(radii, String(r));

  for (const e of n.effects || []) {
    if (e.visible === false) continue;
    if (e.type.includes('SHADOW')) bump(shadows, `${e.type} x${e.offset.x} y${e.offset.y} blur${e.radius} spread${e.spread || 0} ${hex(e.color)}`);
    else bump(shadows, `${e.type} ${e.radius}`);
  }

  if (n.layoutMode && n.layoutMode !== 'NONE') {
    if (n.itemSpacing) bump(gaps, String(Math.round(n.itemSpacing)));
    const p = [n.paddingTop, n.paddingRight, n.paddingBottom, n.paddingLeft].map((v) => Math.round(v || 0));
    if (p.some(Boolean)) bump(pads, p.join('/'));
  }
  for (const c of n.children || []) walk(c, slug);
}

for (const slug of slugs()) walk(readFrame(slug).document, slug);

const top = (o, n) => Object.entries(o).sort((a, b) => b[1] - a[1]).slice(0, n).map(([value, count]) => ({ value, count }));
const result = {
  generatedFrom: `${slugs().length} frames, ${nodes} nodes`,
  textColors: top(colors.text, 20),
  fillColors: top(colors.fill, 30),
  strokeColors: top(colors.stroke, 12),
  fonts: top(fonts, 30),
  radii: top(radii, 10),
  shadows: top(shadows, 12),
  gaps: top(gaps, 12),
  paddings: top(pads, 15),
  imageRefs: Object.fromEntries(Object.entries(images).map(([k, v]) => [k, [...v]])),
};
fs.writeFileSync(path.join(ROOT, 'tokens.json'), JSON.stringify(result, null, 2) + '\n');
console.log('tokens.json written:', result.generatedFrom);
