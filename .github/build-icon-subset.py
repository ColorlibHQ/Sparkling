#!/usr/bin/env python3
"""Build a Font Awesome subset for one theme: only the glyphs it renders."""
import re, sys, pathlib, subprocess, json

theme_dir = pathlib.Path(sys.argv[1]); base = sys.argv[2]; prefix = sys.argv[3]
fa = theme_dir / base / 'css' / 'fontawesome'
webfonts = theme_dir / base / 'css' / 'webfonts'
out = fa / 'subset'; out.mkdir(exist_ok=True)

def icon_map(path):
    css = (fa / path).read_text(); m = {}
    for sels, cp in re.findall(r'((?:\.fa-[a-z0-9-]+,?)+)\{--fa:"\\([0-9a-f]{4})"\}', css):
        for s in re.findall(r'\.fa-([a-z0-9-]+)', sels): m[s] = cp
    return m
core_map, brand_map = icon_map('fontawesome.min.css'), icon_map('brands.min.css')

MOD = {'solid','regular','brands','classic','light','thin','duotone','sharp','fw','spin','pulse','li','ul','border',
       'pull-left','pull-right','rotate-90','rotate-180','rotate-270','flip-horizontal','flip-vertical','flip-both',
       'stack','stack-1x','stack-2x','inverse','lg','xs','sm','2x','3x','4x','5x','beat','fade','flip','shake',
       'bounce','spin-pulse','spin-reverse','2xs','xl','2xl'}

# 1. icon classes in templates, with the style they are rendered in
used_solid, used_regular, used_brands = set(), set(), set()
for f in list(theme_dir.rglob('*.php')) + list(theme_dir.rglob('*.js')):
    sp = str(f)
    if any(k in sp for k in ('/.git/','node_modules','fontawesome','tgmpa')): continue
    t = f.read_text(errors='ignore')
    for m in re.finditer(r'class\s*=\s*["\']([^"\']*\bfa[a-z-]*\b[^"\']*)["\']', t):
        cls = m.group(1); names = [n for n in re.findall(r'\bfa-([a-z0-9-]+)', cls) if n not in MOD]
        regular = 'fa-regular' in cls or re.search(r'\bfar\b', cls)
        for n in names:
            if n in brand_map: used_brands.add(brand_map[n])
            elif n in core_map: (used_regular if regular else used_solid).add(core_map[n])
    # slugs passed as data: array( 'bluesky', 'brands' ) and the keyed form
    # array( 'slug' => 'rss', 'family' => 'solid' ) both appear in these themes.
    pairs = re.findall(r"['\"]([a-z0-9-]+)['\"]\s*,\s*['\"](brands|solid|regular)['\"]", t)
    pairs += re.findall(r"['\"]slug['\"]\s*=>\s*['\"]([a-z0-9-]+)['\"]\s*,\s*['\"]family['\"]\s*=>\s*['\"](brands|solid|regular)['\"]", t)
    for slug, fam in pairs:
        if fam == 'brands' and slug in brand_map: used_brands.add(brand_map[slug])
        elif slug in core_map: (used_regular if fam == 'regular' else used_solid).add(core_map[slug])

# 2. raw codepoints in the theme's own stylesheets, assigned by the family the rule names
for cssf in [theme_dir/'style.css', theme_dir/base/'css'/'custom.css', theme_dir/'rtl.css']:
    if not cssf.exists(): continue
    css = cssf.read_text(errors='ignore')
    for sels, body in re.findall(r'([^{}]+)\{([^{}]*)\}', css):
        cp = re.search(r'content: *[\'"]\\([0-9a-fA-F]{4})[\'"]', body)
        if not cp: continue
        c = cp.group(1).lower()
        if 'Font Awesome 7 Brands' in body: used_brands.add(c)
        elif 'Font Awesome 7 Free' in body:
            (used_regular if 'font-weight: 400' in body else used_solid).add(c)
        elif c in brand_map.values(): used_brands.add(c)
        elif c in core_map.values(): used_solid.add(c)

faces = {'fa-solid-900': used_solid, 'fa-brands-400': used_brands, 'fa-regular-400': used_regular}
report = {}
for face, cps in faces.items():
    src = webfonts / f'{face}.woff2'
    if not src.exists() or not cps:
        report[face] = {'glyphs': len(cps), 'bytes': 0, 'skipped': True}; continue
    uni = ','.join('U+' + c for c in sorted(cps))
    dst = out / f'{face}.woff2'
    subprocess.run([sys.executable, '-m', 'fontTools.subset', str(src), f'--unicodes={uni}',
                    '--flavor=woff2', f'--output-file={dst}'], check=True, capture_output=True)
    report[face] = {'glyphs': len(cps), 'bytes': dst.stat().st_size, 'from': src.stat().st_size}

# 3. the CSS: Font Awesome's own non-icon rules, the used icon definitions, subset @font-face
full = (fa / 'fontawesome.min.css').read_text()
core_rules = re.sub(r'((?:\.fa-[a-z0-9-]+,?)+)\{--fa:"\\[0-9a-f]{4}"\}', '', full)
defs = []
for cps, mp in ((used_solid | used_regular, core_map), (used_brands, brand_map)):
    rev = {}
    for name, cp in mp.items(): rev.setdefault(cp, []).append(name)
    for c in sorted(cps):
        if c in rev: defs.append(','.join(f'.fa-{n}' for n in sorted(rev[c])) + '{--fa:"\\%s"}' % c)
FACE = ('@font-face{font-family:"%s";font-style:normal;font-weight:%s;font-display:block;'
        'src:url(%s.woff2) format("woff2")}')

HEADER = re.match(r'/\*!.*?\*/', (fa / 'solid.min.css').read_text(), re.S).group(0)

def style_rules(sheet):
    """Everything a style stylesheet declares except its @font-face.

    These carry the :root custom properties and the .fa-solid / .fa-regular /
    .fa-brands rules that bind a class to a family and weight. Leave them out
    and .fa-brands falls back to the Free face, where no brand glyph exists,
    and .fa-regular loses --fa-style:400 and quietly draws the solid variant.
    brands.min.css also carries the full 572-name brand map, which the subset
    emits for itself below, so that goes too.
    """
    css = (fa / sheet).read_text()
    css = re.sub(r'@font-face\{[^}]*\}', '', css)
    css = re.sub(r'((?:\.fa-[a-z0-9-]+,?)+)\{--fa:"[^"]*"\}', '', css)
    return css.replace(HEADER, '').strip()

ff = []
if used_solid:
    ff.append(style_rules('solid.min.css'))
    ff.append(FACE % ('Font Awesome 7 Free', '900', 'fa-solid-900'))
if used_regular:
    ff.append(style_rules('regular.min.css'))
    ff.append(FACE % ('Font Awesome 7 Free', '400', 'fa-regular-400'))
if used_brands:
    ff.append(style_rules('brands.min.css'))
    ff.append(FACE % ('Font Awesome 7 Brands', '400', 'fa-brands-400'))
banner = ("/*!\n * Font Awesome 7.3.1 subset for this theme, built by .github/build-icon-subset.py\n"
          " * Contains only the glyphs the theme renders. Load the complete Font Awesome with:\n"
          " *   add_filter( '%s_full_fontawesome', '__return_true' );\n"
          " * Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT -- https://fontawesome.com/license/free\n */\n" % prefix)
(out / 'fontawesome-subset.min.css').write_text(banner + core_rules.strip() + ''.join(ff) + ''.join(defs) + '\n')
report['css'] = {'bytes': (out / 'fontawesome-subset.min.css').stat().st_size, 'icons': len(defs)}
print(json.dumps(report, indent=1))
