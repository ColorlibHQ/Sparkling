/**
 * ESLint flat config.
 *
 * Replaces .jshintrc and .jscsrc -- JSCS was deprecated years ago and told users
 * to migrate to ESLint ("JSCS has merged with ESLint").
 *
 * Only theme-authored JavaScript is linted. The bundled third-party libraries
 * (Bootstrap, FlexSlider) and minified copies are ignored.
 */
import js from '@eslint/js';
import globals from 'globals';

export default [
  {
    ignores: [
      'node_modules/**',
      'vendor/**',
      'build/**',
      '**/*.min.js',
      'assets/js/vendor/**',
    ],
  },
  js.configs.recommended,
  {
    files: ['tools/**/*.mjs', 'eslint.config.mjs', '.github/**/*.mjs'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'module',
      globals: { ...globals.node },
    },
  },
  {
    files: ['assets/js/**/*.js', 'inc/**/*.js'],
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: 'script',
      globals: {
        ...globals.browser,
        jQuery: 'readonly',
        wp: 'readonly',
        // Localized by wp_localize_script().
        Sparkling: 'readonly',
        sparklingWelcomeScreenObject: 'readonly',
      },
    },
    rules: {
      // Carried over from the old .jshintrc so the house style is unchanged.
      curly: 'error',
      eqeqeq: ['error', 'allow-null'],
      quotes: ['error', 'single', { avoidEscape: true }],
      'no-caller': 'error',
      'no-eval': 'error',
      'no-implied-eval': 'error',
      'no-irregular-whitespace': ['error', { skipStrings: true }],
      'no-unused-vars': ['warn', { args: 'none' }],
      'no-undef': 'error',
    },
  },
];
