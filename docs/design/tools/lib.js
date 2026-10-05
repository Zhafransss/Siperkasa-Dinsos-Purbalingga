// Shared helpers for the offline design tools. They read docs/design/raw/*.json.gz, so no Figma access is needed.
const fs = require('fs');
const path = require('path');
const zlib = require('zlib');

const ROOT = path.join(__dirname, '..');
const RAW = path.join(ROOT, 'raw');
const frames = JSON.parse(fs.readFileSync(path.join(__dirname, 'frames.json'), 'utf8'));

const slugs = () => frames.map((f) => f.slug).filter((s) => fs.existsSync(path.join(RAW, s + '.json.gz')));

/** The Figma "node" object of one frame: { document, components, styles, ... }. */
const readFrame = (slug) => JSON.parse(zlib.gunzipSync(fs.readFileSync(path.join(RAW, slug + '.json.gz'))).toString('utf8'));

const hex = (c, opacity = 1) => {
  if (!c) return null;
  const h = (v) => Math.round(v * 255).toString(16).padStart(2, '0');
  const a = (c.a ?? 1) * opacity;
  return '#' + h(c.r) + h(c.g) + h(c.b) + (a < 0.995 ? ' @' + a.toFixed(2) : '');
};

const firstSolid = (list) => (list || []).find((x) => x.type === 'SOLID' && x.visible !== false);

const fillOf = (n) => {
  const f = (n.fills || []).find((x) => x.visible !== false);
  if (!f) return null;
  if (f.type === 'SOLID') return hex(f.color, f.opacity ?? 1);
  return f.type === 'IMAGE' ? 'IMAGE' : f.type;
};

const strokeOf = (n) => {
  const s = firstSolid(n.strokes);
  return s ? hex(s.color) + (n.strokeWeight ? ' ' + n.strokeWeight + 'px' : '') : null;
};

module.exports = { ROOT, RAW, frames, slugs, readFrame, hex, firstSolid, fillOf, strokeOf };
