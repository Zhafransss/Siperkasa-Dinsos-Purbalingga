// Re-downloads the design frames from the Figma REST API into docs/design/raw/*.json.gz.
//   node docs/design/tools/fetch.js [slug ...]      (no slug = every frame listed in frames.json)
//
// IMPORTANT — Figma rate limits: on the Starter plan these endpoints have a very small quota. When it is used up the API
// answers 429 with a Retry-After of several days (seen: 399985 s ≈ 4.6 days, plan tier "starter", limit type "low").
// Everything in this folder was built so that you DO NOT need this script for day-to-day work: read docs/DESIGN*.md,
// docs/design/outline, texts and previews instead. Only run this when the design actually changed, and fetch in a
// few batches (this script sends 4 frames per request) rather than frame by frame.
//
// The token is taken from FIGMA_API_KEY, or from the Figma MCP server entry in ~/.claude.json. It is never written anywhere.
const fs = require('fs');
const os = require('os');
const path = require('path');
const zlib = require('zlib');
const { RAW, frames } = require('./lib');

const FILE_KEY = 'vvnnzaRdWrTlzN1BGseZzr'; // "DINKES FIX"

function token() {
  if (process.env.FIGMA_API_KEY) return process.env.FIGMA_API_KEY;
  const cfg = JSON.parse(fs.readFileSync(path.join(os.homedir(), '.claude.json'), 'utf8'));
  const key = cfg.mcpServers?.figma?.env?.FIGMA_API_KEY;
  if (!key) throw new Error('Set FIGMA_API_KEY (Figma personal access token).');
  return key;
}

(async () => {
  const wanted = process.argv.slice(2);
  const list = frames.filter((f) => !wanted.length || wanted.includes(f.slug));
  const key = token();
  fs.mkdirSync(RAW, { recursive: true });

  for (let i = 0; i < list.length; i += 4) {
    const batch = list.slice(i, i + 4);
    const url = `https://api.figma.com/v1/files/${FILE_KEY}/nodes?ids=${encodeURIComponent(batch.map((f) => f.id).join(','))}`;
    const res = await fetch(url, { headers: { 'X-Figma-Token': key } });

    if (!res.ok) {
      console.error(`Figma answered ${res.status}. Retry-After: ${res.headers.get('retry-after')}s, plan: ${res.headers.get('x-figma-plan-tier')}.`);
      process.exit(2);
    }

    const body = await res.json();
    for (const f of batch) {
      const node = body.nodes?.[f.id];
      if (!node) { console.warn('missing', f.id, f.slug); continue; }
      fs.writeFileSync(path.join(RAW, f.slug + '.json.gz'), zlib.gzipSync(JSON.stringify(node), { level: 9 }));
      console.log('saved', f.slug);
    }
  }
})();
