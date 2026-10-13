/**
 * Verify every gettext call uses an allowed text domain.
 *
 * Replaces grunt-checktextdomain. Catches the two mistakes that actually ship:
 * a wrong/typo'd domain, and a missing domain argument (which silently drops the
 * string from translation).
 */
import { readdir, readFile } from 'node:fs/promises';
import path from 'node:path';

const ROOT = path.resolve(import.meta.dirname, '..');
/*
 * A theme on WordPress.org must use exactly one text domain, matching its
 * slug -- otherwise language packs do not cover the other domain's strings.
 */
const ALLOWED = new Set( [ 'sparkling' ] );
const SKIP = /^(node_modules|vendor|build|\.git|tools)(\/|$)/;

/** fn name -> 1-based index of its $domain argument. */
const FUNCTIONS = {
  __: 2, _e: 2, esc_html__: 2, esc_html_e: 2, esc_attr__: 2, esc_attr_e: 2,
  _x: 3, _ex: 3, esc_html_x: 3, esc_attr_x: 3,
  _n: 4, _n_noop: 3,
  _nx: 5, _nx_noop: 4,
};

const files = [];
const walk = async (dir, base = '') => {
  for (const e of await readdir(dir, { withFileTypes: true })) {
    const rel = base ? `${base}/${e.name}` : e.name;
    if (SKIP.test(rel)) continue;
    const abs = path.join(dir, e.name);
    if (e.isDirectory()) await walk(abs, rel);
    else if (e.isFile() && e.name.endsWith('.php')) files.push([abs, rel]);
  }
};
await walk(ROOT);

/**
 * Blank out PHP comments, keeping every newline so line numbers still match.
 * Prose such as "calls esc_html__() too early" in a comment is not a call.
 */
function stripComments(src) {
  let out = '', quote = null;
  for (let i = 0; i < src.length; i++) {
    const c = src[i];
    if (quote) {
      out += c;
      if (c === '\\') { out += src[++i] ?? ''; continue; }
      if (c === quote) quote = null;
      continue;
    }
    if (c === '"' || c === "'") { quote = c; out += c; continue; }
    if (c === '/' && src[i + 1] === '*') {
      const end = src.indexOf('*/', i + 2);
      const stop = end === -1 ? src.length : end + 2;
      out += src.slice(i, stop).replace(/[^\n]/g, ' ');
      i = stop - 1;
      continue;
    }
    if ((c === '/' && src[i + 1] === '/') || c === '#') {
      let stop = src.indexOf('\n', i);
      if (stop === -1) stop = src.length;
      // A one-line comment ends at a closing PHP tag as well as at the newline.
      const tag = src.indexOf('?>', i);
      if (tag !== -1 && tag < stop) stop = tag;
      out += ' '.repeat(stop - i);
      i = stop - 1;
      continue;
    }
    out += c;
  }
  return out;
}

/** Split a PHP argument list on top-level commas. */
function splitArgs(src, start) {
  const args = [];
  let depth = 0, quote = null, cur = '', i = start;
  for (; i < src.length; i++) {
    const c = src[i], prev = src[i - 1];
    if (quote) {
      cur += c;
      if (c === quote && prev !== '\\') quote = null;
      continue;
    }
    if (c === '"' || c === "'") { quote = c; cur += c; continue; }
    if (c === '(' || c === '[') { depth++; cur += c; continue; }
    if (c === ')' && depth === 0) { args.push(cur); return { args, end: i }; }
    if (c === ')' || c === ']') { depth--; cur += c; continue; }
    if (c === ',' && depth === 0) { args.push(cur); cur = ''; continue; }
    cur += c;
  }
  return { args, end: i };
}

const problems = [];
let calls = 0;

for (const [abs, rel] of files) {
  const src = stripComments(await readFile(abs, 'utf8'));
  const re = new RegExp(`(?<![\\w$>])(${Object.keys(FUNCTIONS).join('|')})\\s*\\(`, 'g');
  let m;
  while ((m = re.exec(src)) !== null) {
    const fn = m[1];
    const { args } = splitArgs(src, m.index + m[0].length);
    calls++;
    const domainArg = (args[FUNCTIONS[fn] - 1] ?? '').trim();
    const line = src.slice(0, m.index).split('\n').length;

    if (!domainArg) {
      problems.push(`${rel}:${line}  ${fn}() is missing its text domain argument`);
      continue;
    }
    const literal = domainArg.match(/^'([^']*)'$|^"([^"]*)"$/);
    if (!literal) continue; // a variable/constant domain — not statically checkable
    const domain = literal[1] ?? literal[2];
    if (!ALLOWED.has(domain)) {
      problems.push(`${rel}:${line}  ${fn}() uses domain '${domain}' (allowed: ${[...ALLOWED].join(', ')})`);
    }
  }
}

console.log(`checked ${calls} gettext calls across ${files.length} PHP files`);
if (problems.length) {
  problems.forEach((p) => console.error('  ✗ ' + p));
  console.error(`\n${problems.length} text domain problem(s)`);
  process.exit(1);
}
console.log('  ✓ all text domains valid');
