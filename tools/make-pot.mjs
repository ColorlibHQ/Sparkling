/**
 * Regenerate languages/sparkling.pot.
 *
 * Uses WP-CLI's `wp i18n make-pot`, the tool WordPress.org itself uses. This
 * replaces grunt-wp-i18n (unmaintained) and wp-pot (npm-deprecated: "Package no
 * longer supported"), which between them accounted for every remaining
 * deprecation warning in the dependency tree.
 *
 * WP-CLI is not an npm package, so it is invoked from PATH.
 * Install: https://wp-cli.org/#installing
 */
import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import { readFile, writeFile } from 'node:fs/promises';
import path from 'node:path';

const run = promisify(execFile);
// WP-CLI from PATH, or wherever WP_CLI points (a wrapper script works too).
const WP = process.env.WP_CLI || 'wp';
const ROOT = path.resolve(import.meta.dirname, '..');
const DEST = path.join(ROOT, 'languages', 'sparkling.pot');

const styleCss = await readFile(path.join(ROOT, 'style.css'), 'utf8');
const version = styleCss.match(/^\s*Version:\s*(.+)$/m)?.[1].trim() ?? '';

try {
  await run(WP, ['--version']);
} catch {
  console.error(
    'WP-CLI not found on PATH.\n' +
    'languages/sparkling.pot is generated with `wp i18n make-pot`.\n' +
    'Install WP-CLI: https://wp-cli.org/#installing'
  );
  process.exit(1);
}

const args = [
  'i18n', 'make-pot', ROOT, DEST,
  '--domain=sparkling',
  /*
   * Just the name. WP-CLI reads the version out of style.css and appends it
   * itself, so passing "Sparkling 2.6.11" here produced the doubled
   * "Project-Id-Version: Sparkling 2.6.11 2.6.11".
   */
  '--package-name=Sparkling',
  '--exclude=node_modules,vendor,build,tools,tests,src,.github,assets/js/vendor',
  '--headers=' + JSON.stringify({
    'Report-Msgid-Bugs-To': 'https://colorlib.com/wp/support/',
    'Last-Translator': 'Colorlib <info@colorlib.com>',
    'Language-Team': 'Colorlib <info@colorlib.com>',
  }),
  '--allow-root',
];

const { stdout, stderr } = await run(WP, args, { cwd: ROOT, maxBuffer: 32 * 1024 * 1024 });
if (stderr.trim()) console.error(stderr.trim());
if (stdout.trim()) console.log(stdout.trim());

// Normalise line endings so diffs stay readable across platforms.
const pot = await readFile(DEST, 'utf8');
if (pot.includes('\r\n')) await writeFile(DEST, pot.replace(/\r\n/g, '\n'), 'utf8');

const strings = (pot.match(/^msgid /gm) || []).length - 1; // minus the header entry
console.log(`languages/sparkling.pot — ${strings} translatable strings (package: Sparkling ${version})`);
