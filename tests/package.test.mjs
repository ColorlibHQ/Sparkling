import assert from 'node:assert/strict';
import { createHash, randomUUID } from 'node:crypto';
import { execFileSync } from 'node:child_process';
import { readFileSync, readdirSync, writeFileSync, unlinkSync } from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
import { test } from 'node:test';

const root = fileURLToPath(new URL('../', import.meta.url));

test('subset font URLs change with their contents and every CSS reference resolves', () => {
  const directory = path.join(root, 'assets/css/fontawesome/subset');
  const css = readFileSync(path.join(directory, 'fontawesome-subset.min.css'), 'utf8');
  const fonts = readdirSync(directory).filter((name) => name.endsWith('.woff2'));
  assert.equal(fonts.length, 2);
  for (const font of fonts) {
    const digest = createHash('sha256').update(readFileSync(path.join(directory, font))).digest('hex').slice(0, 12);
    assert.ok(font.endsWith(`.${digest}.woff2`), `${font} has a stale content hash`);
    assert.ok(css.includes(`url(${font})`), `${font} is not referenced by the subset`);
  }
  for (const [, url] of css.matchAll(/url\(([^)]+\.woff2)\)/g)) {
    assert.ok(fonts.includes(url), `Missing subset font ${url}`);
  }
});

test('release ZIP contains runtime files but excludes tests and unexpected local files', () => {
  const sentinel = `local-audit-${randomUUID()}.txt`;
  writeFileSync(path.join(root, sentinel), 'This local report must not ship.');
  try {
    execFileSync(process.execPath, ['tools/build-zip.mjs'], { cwd: root });
    const entries = JSON.parse(execFileSync('python3', ['-c',
      'import zipfile,json; z=zipfile.ZipFile("sparkling.zip"); print(json.dumps(z.namelist()))',
    ], { cwd: root, encoding: 'utf8' }));
    assert.ok(entries.includes('sparkling/functions.php'));
    assert.ok(entries.includes('sparkling/theme.json'));
    assert.ok(entries.some((entry) => entry.startsWith('sparkling/patterns/')));
    assert.ok(!entries.includes(`sparkling/${sentinel}`));
    assert.ok(!entries.some((entry) => /sparkling\/(tests|tools|node_modules|vendor|\.github)\//.test(entry)));
  } finally {
    unlinkSync(path.join(root, sentinel));
  }
});
