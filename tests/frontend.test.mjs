import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import { createRequire } from 'node:module';
import { test } from 'node:test';
import { JSDOM } from 'jsdom';

const require = createRequire(import.meta.url);
const root = new URL('../', import.meta.url);
const source = (path) => readFileSync(new URL(path, root), 'utf8');

async function waitFor(check) {
  const deadline = Date.now() + 2000;
  while (!check()) {
    if (Date.now() > deadline) throw new Error('Frontend did not reach the expected state');
    await new Promise((resolve) => setTimeout(resolve, 10));
  }
}

async function fixture(t, jquery, reduced = false, width = 1280) {
  const dom = new JSDOM(`<!doctype html><html><head>
    <style>${source('assets/css/bootstrap.css')}${source('style.css')}</style>
    </head><body><nav class="navbar navbar-default">
    <ul class="navbar-nav nav"><li class="dropdown"><a href="#parent">Parent</a>
    <button class="sparkling-dropdown" type="button" aria-expanded="false">Submenu</button>
    <ul class="dropdown-menu"><li><a href="#child">Child</a></li>
    <li class="dropdown"><a href="#nested">Nested parent</a>
    <button class="sparkling-dropdown" type="button" aria-expanded="false">Nested submenu</button>
    <ul class="dropdown-menu"><li><a href="#grandchild">Grandchild</a></li></ul></li>
    </ul></li></ul></nav>
    <div class="flexslider"><ul class="slides">
    <li><a href="#first" tabindex="0">First slide</a></li>
    <li><a href="#second">Second slide</a><button type="button" tabindex="3">Slide action</button></li>
    </ul></div><a id="outside" href="#outside">Outside</a></body></html>`, {
    url: 'https://sparkling.test/', runScripts: 'outside-only', pretendToBeVisual: true,
  });
  t.after(() => dom.window.close());
  const { window } = dom;
  const intervals = new Map();
  const setInterval = window.setInterval.bind(window);
  const clearInterval = window.clearInterval.bind(window);
  window.setInterval = (callback, delay) => {
    const id = setInterval(callback, delay);
    intervals.set(id, { callback, delay });
    return id;
  };
  window.clearInterval = (id) => {
    intervals.delete(id);
    clearInterval(id);
  };
  window.innerWidth = width;
  // jsdom has no layout engine; FlexSlider only advances visible elements.
  const sliderElement = window.document.querySelector('.flexslider');
  Object.defineProperty(sliderElement, 'offsetWidth', { value: 1200 });
  Object.defineProperty(sliderElement, 'offsetHeight', { value: 400 });
  const media = new Map();
  window.matchMedia = (query) => {
    if (!media.has(query)) {
      const listeners = new Set();
      media.set(query, {
        matches: query.includes('reduced-motion') ? reduced : width >= 1024,
        media: query,
        addEventListener: (type, listener) => listeners.add(listener),
        set(value) {
          this.matches = value;
          listeners.forEach((listener) => listener(new window.Event('change')));
        },
      });
    }
    return media.get(query);
  };
  window.eval(readFileSync(require.resolve(jquery), 'utf8'));
  for (const path of [
    'assets/js/jquery-compat.js', 'assets/js/vendor/bootstrap.js',
    'assets/js/vendor/flexslider.js', 'assets/js/flexslider-custom.js', 'assets/js/functions.js',
  ]) window.eval(source(path));
  await waitFor(() => window.document.querySelector('.sparkling-slider-toggle'));
  return { window, document: window.document, $: window.jQuery, media, intervals };
}

for (const jquery of ['jquery', 'jquery4']) {
  test(`${jquery}: autoplay keeps a live timer and actually advances slides`, async (t) => {
    const { document, $, intervals } = await fixture(t, jquery);
    const slider = $('.flexslider').data('flexslider');
    const timer = [...intervals.values()].find(({ delay }) => delay === 7000);
    assert.ok(timer, 'initial playback must not clear the running slideshow timer');
    document.dispatchEvent(new document.defaultView.Event('visibilitychange'));
    assert.ok([...intervals.values()].includes(timer), 'visible notifications must preserve the running timer');
    timer.callback();
    await waitFor(() => slider.currentSlide === 1);
    assert.equal(document.querySelectorAll('.slides > li')[1].getAttribute('aria-hidden'), 'false');
  });

  test(`${jquery}: inactive slides cannot receive focus and saved tabindex is restored`, async (t) => {
    const { document, $ } = await fixture(t, jquery, true);
    const slides = document.querySelectorAll('.slides > li');
    assert.equal(slides[1].getAttribute('aria-hidden'), 'true');
    assert.equal(slides[1].hasAttribute('inert'), true);
    assert.equal(slides[1].querySelector('a').getAttribute('tabindex'), '-1');
    assert.equal(slides[1].querySelector('button').getAttribute('tabindex'), '-1');

    const second = $('.flex-control-nav a').eq(1);
    assert.equal(second.attr('role'), 'button');
    assert.equal(second.attr('aria-label'), 'Go to slide 2');
    second.trigger($.Event('keydown', { key: ' ' }));
    await waitFor(() => $('.flexslider').data('flexslider').currentSlide === 1);
    assert.equal(slides[0].getAttribute('aria-hidden'), 'true');
    assert.equal(slides[0].querySelector('a').getAttribute('tabindex'), '-1');
    assert.equal(slides[1].hasAttribute('inert'), false);
    assert.equal(slides[1].querySelector('a').hasAttribute('tabindex'), false);
    assert.equal(slides[1].querySelector('button').getAttribute('tabindex'), '3');
    assert.equal(second.attr('aria-current'), 'true');

    $('.flex-control-nav a').eq(0).trigger('click');
    await waitFor(() => $('.flexslider').data('flexslider').currentSlide === 0);
    assert.equal(slides[0].querySelector('a').getAttribute('tabindex'), '0');
  });

  test(`${jquery}: reduced motion disables autoplay and animations`, async (t) => {
    const { document, $ } = await fixture(t, jquery, true);
    const slider = $('.flexslider').data('flexslider');
    assert.equal(slider.playing, false);
    assert.equal(slider.vars.animationSpeed, 0);
    assert.equal(slider.vars.fadeFirstSlide, false);
    assert.equal(document.querySelector('.sparkling-slider-toggle').textContent, 'Play slideshow');
  });

  test(`${jquery}: hover suspends autoplay; keyboard focus stops it until explicit play`, async (t) => {
    const { document, $, media } = await fixture(t, jquery);
    const slider = $('.flexslider').data('flexslider');
    const group = $('.sparkling-slider-group');
    assert.equal(slider.playing, true);
    group.trigger('mouseenter');
    assert.equal(slider.playing, false);
    group.trigger('mouseleave');
    assert.equal(slider.playing, true);

    document.querySelector('.slides a').focus();
    assert.equal(slider.playing, false);
    group.trigger('mouseleave');
    assert.equal(slider.playing, false);
    document.querySelector('.sparkling-slider-toggle').click();
    assert.equal(slider.playing, true);

    media.get('(prefers-reduced-motion: reduce)').set(true);
    assert.equal(slider.playing, false);
    assert.equal(slider.vars.animationSpeed, 0);
    document.dispatchEvent(new document.defaultView.Event('visibilitychange'));
    assert.equal(slider.playing, false);
  });
}

test('desktop submenu state stays closed after Escape while its toggle retains focus', async (t) => {
  const { document, window } = await fixture(t, 'jquery', true);
  const toggle = document.querySelector('.sparkling-dropdown');
  const item = toggle.parentNode;
  const submenu = item.querySelector('.dropdown-menu');
  toggle.focus();
  assert.equal(toggle.getAttribute('aria-expanded'), 'true');
  assert.equal(toggle.getAttribute('aria-controls'), submenu.id);
  toggle.dispatchEvent(new window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
  assert.equal(document.activeElement, toggle);
  assert.equal(item.classList.contains('open'), false);
  assert.equal(toggle.getAttribute('aria-expanded'), 'false');
  assert.equal(window.getComputedStyle(submenu).display, 'none');
  toggle.click();
  assert.equal(toggle.getAttribute('aria-expanded'), 'true');
  document.querySelector('#outside').click();
  assert.equal(toggle.getAttribute('aria-expanded'), 'false');
});

test('mobile menu opens on activation and closes on Escape', async (t) => {
  const { document, window } = await fixture(t, 'jquery', true, 375);
  const toggle = document.querySelector('.sparkling-dropdown');
  toggle.focus();
  assert.equal(toggle.getAttribute('aria-expanded'), 'false');
  toggle.click();
  assert.equal(toggle.getAttribute('aria-expanded'), 'true');
  toggle.dispatchEvent(new window.KeyboardEvent('keydown', { key: 'Escape', bubbles: true }));
  assert.equal(toggle.getAttribute('aria-expanded'), 'false');
});

for (const jquery of ['jquery', 'jquery4']) {
  test(`${jquery}: Bootstrap cannot consume nested button Space or close an ancestor on Escape`, async (t) => {
    const { document, window } = await fixture(t, jquery, true, 375);
    const [parent, nested] = document.querySelectorAll('.sparkling-dropdown');
    parent.click();
    nested.focus();
    const space = new window.KeyboardEvent('keydown', {
      key: ' ', keyCode: 32, which: 32, bubbles: true, cancelable: true,
    });
    nested.dispatchEvent(space);
    assert.equal(space.defaultPrevented, false, 'native button activation must remain enabled');
    assert.equal(document.activeElement, nested, 'Bootstrap must not move focus to a sibling link');
    // jsdom does not synthesize the native button click from Space.
    nested.click();
    assert.equal(nested.getAttribute('aria-expanded'), 'true');
    nested.dispatchEvent(new window.KeyboardEvent('keydown', {
      key: 'Escape', keyCode: 27, which: 27, bubbles: true, cancelable: true,
    }));
    assert.equal(nested.getAttribute('aria-expanded'), 'false');
    assert.equal(parent.getAttribute('aria-expanded'), 'true');
    assert.equal(document.activeElement, nested);
  });
}

test('default link focus colour meets contrast on white and the body background', () => {
  const color = source('style.css').match(/a:hover,\s*a:focus\s*\{\s*color:\s*#([a-f\d]{6})/i)[1];
  function luminance(hex) {
    return [0.2126, 0.7152, 0.0722].reduce((sum, weight, i) => {
      const channel = parseInt(hex.slice(i * 2, i * 2 + 2), 16) / 255;
      return sum + weight * (channel <= 0.04045 ? channel / 12.92 : ((channel + 0.055) / 1.055) ** 2.4);
    }, 0);
  }
  for (const background of ['FFFFFF', 'F2F2F2']) {
    const contrast = (luminance(background) + 0.05) / (luminance(color) + 0.05);
    assert.ok(contrast >= 4.5, `${color} on ${background}: ${contrast}`);
  }
  const palette = JSON.parse(source('theme.json')).settings.color.palette;
  assert.equal(palette.find((entry) => entry.slug === 'primary-hover').color.toLowerCase(), `#${color.toLowerCase()}`);
});
