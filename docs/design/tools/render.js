// Offline renderer: Figma node JSON -> HTML -> PNG preview (docs/design/previews/<slug>.png).
//   node docs/design/tools/render.js            -> all frames
//   node docs/design/tools/render.js <slug>     -> one frame
// An APPROXIMATION for orientation, not a pixel-perfect export: layout, colours, radii, borders, shadows and text come
// from the data; icons/vectors are drawn as small placeholder blocks and photos as hatched boxes labelled "FOTO".
// Needs Google Chrome (CHROME_PATH, default: the standard Windows install path) and internet for the Inter font.
const fs = require('fs');
const os = require('os');
const path = require('path');
const { spawnSync } = require('child_process');
const { ROOT, slugs, readFrame } = require('./lib');

const esc = (s) => String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
const rgba = (c, o = 1) => `rgba(${Math.round(c.r * 255)},${Math.round(c.g * 255)},${Math.round(c.b * 255)},${+((c.a ?? 1) * o).toFixed(3)})`;

function paint(fill) {
  if (!fill || fill.visible === false) return null;
  if (fill.type === 'SOLID') return rgba(fill.color, fill.opacity ?? 1);
  if (fill.type?.startsWith('GRADIENT')) {
    const stops = (fill.gradientStops || []).map((s) => `${rgba(s.color, fill.opacity ?? 1)} ${Math.round(s.position * 100)}%`).join(',');
    if (fill.type === 'GRADIENT_RADIAL') return `radial-gradient(circle at 50% 50%, ${stops})`;
    const h = fill.gradientHandlePositions || [];
    const angle = h.length >= 2 ? Math.round((Math.atan2(h[1].y - h[0].y, h[1].x - h[0].x) * 180) / Math.PI + 90) : 180;
    return `linear-gradient(${angle}deg, ${stops})`;
  }
  return null;
}

function render(n, parent) {
  if (n.visible === false) return '';
  const b = n.absoluteBoundingBox;
  if (!b) return (n.children || []).map((c) => render(c, parent)).join('');
  const pb = parent.absoluteBoundingBox;

  const css = ['position:absolute', `left:${(b.x - pb.x).toFixed(2)}px`, `top:${(b.y - pb.y).toFixed(2)}px`, `width:${b.width.toFixed(2)}px`, `height:${b.height.toFixed(2)}px`, 'box-sizing:border-box'];
  if (n.opacity !== undefined && n.opacity < 1) css.push(`opacity:${n.opacity}`);

  if (n.type === 'TEXT') {
    const st = n.style || {};
    const fill = (n.fills || []).find((f) => f.type === 'SOLID' && f.visible !== false);
    css.push(`font-family:'${st.fontFamily || 'Inter'}',sans-serif`, `font-size:${st.fontSize}px`, `font-weight:${st.fontWeight}`);
    if (st.lineHeightPx) css.push(`line-height:${st.lineHeightPx}px`);
    if (st.letterSpacing) css.push(`letter-spacing:${st.letterSpacing}px`);
    if (fill) css.push(`color:${rgba(fill.color, fill.opacity ?? 1)}`);
    css.push(`text-align:${(st.textAlignHorizontal || 'LEFT').toLowerCase()}`, 'white-space:pre-wrap');
    if (st.textCase === 'UPPER') css.push('text-transform:uppercase');
    if (st.textDecoration === 'UNDERLINE') css.push('text-decoration:underline');
    return `<div style="${css.join(';')}">${esc(n.characters || '')}</div>`;
  }

  if (['VECTOR', 'BOOLEAN_OPERATION', 'STAR', 'LINE', 'ELLIPSE', 'REGULAR_POLYGON'].includes(n.type)) {
    const fill = (n.fills || []).find((f) => f.type === 'SOLID' && f.visible !== false);
    const stroke = (n.strokes || []).find((f) => f.type === 'SOLID' && f.visible !== false);
    const col = fill ? rgba(fill.color, 0.55) : stroke ? rgba(stroke.color, 0.9) : 'rgba(115,119,130,.5)';
    if (n.type === 'ELLIPSE') css.push(`background:${col}`, 'border-radius:50%');
    else if (n.type === 'LINE') css.push(`border-top:${n.strokeWeight || 1}px solid ${col}`);
    else css.push(`background:${col}`, 'border-radius:3px');
    return `<div style="${css.join(';')}"></div>`;
  }

  const fills = (n.fills || []).filter((f) => f.visible !== false);
  const image = fills.find((f) => f.type === 'IMAGE');
  const bgs = fills.map(paint).filter(Boolean).reverse();
  if (image) css.push('background:repeating-linear-gradient(45deg,#d7dde6,#d7dde6 10px,#e6eaf0 10px,#e6eaf0 20px)');
  else if (bgs.length) css.push(`background:${bgs.join(',')}`);

  const stroke = (n.strokes || []).find((s) => s.type === 'SOLID' && s.visible !== false);
  if (stroke) {
    const w = n.individualStrokeWeights, col = rgba(stroke.color, stroke.opacity ?? 1);
    css.push(w ? `border-style:solid;border-color:${col};border-width:${w.top}px ${w.right}px ${w.bottom}px ${w.left}px` : `border:${n.strokeWeight || 1}px solid ${col}`);
  }
  if (n.rectangleCornerRadii) css.push(`border-radius:${n.rectangleCornerRadii.map((r) => r + 'px').join(' ')}`);
  else if (n.cornerRadius) css.push(`border-radius:${n.cornerRadius}px`);

  const shadows = (n.effects || []).filter((e) => e.visible !== false && e.type === 'DROP_SHADOW');
  if (shadows.length) css.push(`box-shadow:${shadows.map((e) => `${e.offset.x}px ${e.offset.y}px ${e.radius}px ${e.spread || 0}px ${rgba(e.color)}`).join(',')}`);
  if (n.clipsContent) css.push('overflow:hidden');

  const kids = (n.children || []).map((c) => render(c, n)).join('');
  const label = image ? '<span style="position:absolute;left:6px;top:4px;font:600 10px Inter;color:#6b7280">FOTO</span>' : '';
  return `<div style="${css.join(';')}">${label}${kids}</div>`;
}

function html(doc) {
  const { width, height } = doc.absoluteBoundingBox;
  const bg = (doc.fills || []).map(paint).filter(Boolean).reverse().join(',') || '#fff';
  return `<!doctype html><html><head><meta charset="utf-8"><title>${esc(doc.name)}</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">
<style>html,body{margin:0;padding:0}#root{position:relative;width:${Math.ceil(width)}px;height:${Math.ceil(height)}px;overflow:hidden;background:${bg};font-family:Inter,sans-serif}</style></head>
<body><div id="root">${(doc.children || []).map((c) => render(c, doc)).join('')}</div></body></html>`;
}

const chrome = process.env.CHROME_PATH || 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe';
const targets = process.argv[2] ? [process.argv[2]] : slugs();
const tmp = fs.mkdtempSync(path.join(os.tmpdir(), 'design-render-'));
fs.mkdirSync(path.join(ROOT, 'previews'), { recursive: true });

for (const slug of targets) {
  const doc = readFrame(slug).document;
  const file = path.join(tmp, slug + '.html');
  fs.writeFileSync(file, html(doc));
  const { width, height } = doc.absoluteBoundingBox;
  const png = path.join(ROOT, 'previews', slug + '.png');
  const r = spawnSync(chrome, ['--headless=new', '--disable-gpu', '--hide-scrollbars', `--window-size=${Math.ceil(width)},${Math.ceil(height)}`, '--virtual-time-budget=6000', `--screenshot=${png}`, 'file:///' + file.replace(/\\/g, '/')], { stdio: 'ignore' });
  console.log(r.status === 0 && fs.existsSync(png) ? 'ok  ' : 'FAIL', slug);
}
fs.rmSync(tmp, { recursive: true, force: true });
