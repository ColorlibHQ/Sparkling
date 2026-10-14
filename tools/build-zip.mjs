/**
 * Build the distributable theme archive.
 *
 * Replaces the grunt clean -> copy -> compress -> clean chain. Streams straight
 * into the zip instead of copying the tree to build/ first, so there is no
 * intermediate directory to clean up afterwards.
 *
 * Only runtime directories and the known top-level theme files are included.
 * New tests, reports and local files therefore cannot silently enter a release.
 */
import { createWriteStream } from 'node:fs';
import { readdir, readFile, rm, stat } from 'node:fs/promises';
import path from 'node:path';
import { ZipArchive } from 'archiver';

const ROOT = path.resolve(import.meta.dirname, '..');

const THEME_FILES = new Set([
  '404.php', 'archive.php', 'comments.php', 'footer.php', 'functions.php',
  'header.php', 'image.php', 'index.php', 'page-fullwidth.php', 'page.php',
  'search.php', 'searchform.php', 'sidebar-footer.php', 'sidebar-home.php',
  'sidebar.php', 'single.php', 'template-blocks.php', 'style.css', 'theme.json',
  'readme.txt', 'changelog.txt', 'LICENCE.md', 'screenshot.png', 'wpml-config.xml',
]);
const THEME_DIRECTORIES = new Set(['assets', 'inc', 'languages', 'patterns', 'template-parts']);

/** Paths (relative to the theme root) that must never ship to users. */
const EXCLUDE = [
  // Any hidden file or directory, at any depth. WordPress.org rejects themes
  // that contain them, and listing names one by one is how dotfiles slipped
  // into earlier Colorlib theme zips.
  /(^|\/)\.[^/]+/,
  /^\.git(\/|$)/,
  /^\.github(\/|$)/,
  /^node_modules(\/|$)/,
  /^vendor(\/|$)/,
  /^build(\/|$)/,
  /^tools(\/|$)/,
  /^src(\/|$)/,
  /^\.sass-cache(\/|$)/,
  /(^|\/)\.DS_Store$/,
  /\.map$/,
  /\.scss$/,
  /\.zip$/,
  /(^|\/)package(-lock)?\.json$/,
  /(^|\/)composer\.(json|lock)$/,
  /(^|\/)Gruntfile\.js$/,
  /(^|\/)eslint\.config\.mjs$/,
  /(^|\/)\.stylelintrc\.json$/,
  /(^|\/)\.jshintrc$/,
  /(^|\/)\.jscsrc$/,
  /(^|\/)\.jshintignore$/,
  /(^|\/)\.standard\.json$/,
  /(^|\/)\.vscode(\/|$)/,
  /(^|\/)\.idea(\/|$)/,
  /(^|\/)\.editorconfig$/,
  /(^|\/)\.gitignore$/,
  /(^|\/)\.gitmodules$/,
  /(^|\/)\.travis\.yml$/,
  /(^|\/)\.nvmrc$/,
  /(^|\/)phpcs\.xml(\.dist)?$/,
  /(^|\/)set_tags\.sh$/,
  /(^|\/)CLAUDE\.md$/,
  /(^|\/)README\.md$/i,
  /(^|\/)CONTRIBUTING\.md$/i,
  /(^|\/)tsconfig\.json$/,
  /(^|\/)webpack\.config\.js$/,
  // Inputs to .github/build-icon-subset.py; the theme enqueues the subset or
  // all.min.css (and inc/fontawesome.php reads only those two), never these.
  /^assets\/css\/fontawesome\/(fontawesome|solid|brands|regular)\.min\.css$/,
];

const isExcluded = (rel) => EXCLUDE.some((re) => re.test(rel));
const isThemePath = (rel) => THEME_FILES.has(rel) || THEME_DIRECTORIES.has(rel.split('/')[0]);

const pkg = JSON.parse(await readFile(path.join(ROOT, 'package.json'), 'utf8'));
const slug = pkg.name;

// style.css is authoritative for the theme version; warn if package.json drifts.
const styleCss = await readFile(path.join(ROOT, 'style.css'), 'utf8');
const version = styleCss.match(/^\s*Version:\s*(.+)$/m)?.[1].trim() ?? pkg.version;
if (version !== pkg.version) {
  console.warn(`  ! style.css is ${version} but package.json is ${pkg.version}`);
}

const outFile = path.join(ROOT, `${slug}.zip`);
await rm(outFile, { force: true });

const output = createWriteStream(outFile);
const archive = new ZipArchive({ zlib: { level: 9 } });

const closed = new Promise((resolve, reject) => {
  output.on('close', resolve);
  output.on('error', reject);
  archive.on('error', reject);
  archive.on('warning', (err) => {
    if (err.code === 'ENOENT') console.warn('  warning:', err.message);
    else reject(err);
  });
});

archive.pipe(output);

let files = 0;
const walk = async (dir, base = '') => {
  const entries = await readdir(dir, { withFileTypes: true });
  entries.sort((a, b) => a.name.localeCompare(b.name));
  for (const entry of entries) {
    const rel = base ? `${base}/${entry.name}` : entry.name;
    if (!isThemePath(rel) || isExcluded(rel)) continue;
    const abs = path.join(dir, entry.name);
    if (entry.isDirectory()) {
      await walk(abs, rel);
    } else if (entry.isFile()) {
      // WordPress expects the theme nested under a directory named for the slug.
      archive.file(abs, { name: `${slug}/${rel}` });
      files += 1;
    }
  }
};

await walk(ROOT);
await archive.finalize();
await closed;

const { size } = await stat(outFile);
console.log(`${slug}.zip — ${files} files, ${(size / 1024 / 1024).toFixed(2)} MB (theme version ${version})`);
