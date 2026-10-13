/**
 * Losslessly re-encode the theme's bundled images.
 *
 * Replaces grunt-contrib-imagemin, which was last published in 2022 and pulled
 * in the abandoned bin-wrapper/download/decompress chain responsible for every
 * npm audit finding in this project. It was also pointed at assets/img/, a
 * directory this theme does not have, so it silently optimised nothing.
 *
 * Pass --write to overwrite in place; the default is a dry run.
 */
import { readdir, readFile, writeFile, stat } from 'node:fs/promises';
import path from 'node:path';
import sharp from 'sharp';

const ROOT = path.resolve(import.meta.dirname, '..');
const TARGETS = ['inc/welcome-screen/img', 'screenshot.png', '.github/social-preview.png'];
const WRITE = process.argv.includes('--write');
const PALETTE = process.argv.includes('--palette');

const files = [];
const collect = async (rel) => {
  const abs = path.join(ROOT, rel);
  let s;
  try { s = await stat(abs); } catch { return; }
  if (s.isFile()) {
    if (/\.(png|jpe?g)$/i.test(rel)) files.push(rel);
    return;
  }
  for (const e of await readdir(abs, { withFileTypes: true })) {
    await collect(path.join(rel, e.name));
  }
};
for (const t of TARGETS) await collect(t);

if (!files.length) {
  console.log('no PNG/JPEG files found under: ' + TARGETS.join(', '));
  process.exit(0);
}

let before = 0, after = 0, changed = 0;

for (const rel of files) {
  const abs = path.join(ROOT, rel);
  const original = await readFile(abs);
  const isPng = /\.png$/i.test(rel);

  /*
   * PNGs are re-encoded losslessly. Palette quantisation saves far more (27%
   * vs 4% on screenshot.png) but reduces the image to 256 colours, which is a
   * poor trade for a theme screenshot: it is the storefront image on
   * WordPress.org and its sky gradients are exactly what banding shows up in.
   * Pass --palette if you have a flat-colour PNG where that is acceptable.
   */
  /*
   * screenshot.jpg is the storefront image on WordPress.org, so it is encoded
   * at a higher quality than the in-theme placeholders. Using the same q82
   * here would silently degrade it a little further on every run.
   */
  const quality = /(^|\/)screenshot\.jpe?g$/i.test(rel) ? 92 : 82;

  const out = isPng
    ? await sharp(original).png({ compressionLevel: 9, effort: 10, palette: PALETTE }).toBuffer()
    : await sharp(original).jpeg({ quality, mozjpeg: true, progressive: true }).toBuffer();

  before += original.length;
  // Never write a bigger file than we started with.
  const keep = out.length < original.length ? out : original;
  after += keep.length;

  if (keep !== original) {
    changed++;
    const saved = ((1 - out.length / original.length) * 100).toFixed(1);
    console.log(`  ${WRITE ? 'optimised' : 'would save'} ${saved.padStart(5)}%  ${rel}`);
    if (WRITE) await writeFile(abs, keep);
  }
}

const pct = before ? ((1 - after / before) * 100).toFixed(1) : '0.0';
console.log(
  `\n${files.length} images, ${changed} improvable — ` +
  `${(before / 1024 / 1024).toFixed(2)} MB -> ${(after / 1024 / 1024).toFixed(2)} MB (${pct}%)`
);
if (!WRITE && changed) console.log('dry run; re-run with --write to apply');
