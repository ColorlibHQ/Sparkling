'use strict';

module.exports = function( grunt ) {
  // load all tasks
  require( 'load-grunt-tasks' )( grunt, { scope: 'devDependencies' } );

  grunt.config.init( {
    pkg: grunt.file.readJSON( 'package.json' ),

    dirs: {
      css: 'assets/css',
      js: 'assets/js'
    },

    makepot: {
      target: {
        options: {
          domainPath: '/languages/',
          potFilename: '<%= pkg.name %>.pot',
          potHeaders: {
            poedit: true,
            'x-poedit-keywordslist': true
          },
          processPot: function( pot, options ) {
            pot.headers[ 'report-msgid-bugs-to' ] = 'https://www.colorlib.com/';
            pot.headers[ 'language-team' ] = 'Colorlib <office@colorlib.com>';
            pot.headers[ 'last-translator' ] = 'Colorlib <office@colorlib.com>';
            pot.headers[ 'language-team' ] = 'Colorlib <office@colorlib.com>';
            return pot;
          },
          updateTimestamp: true,
          type: 'wp-theme'

        }
      }
    },

    addtextdomain: {
      target: {
        options: {
          updateDomains: true,
          textdomain: '<%= pkg.name %>'
        },
        files: {
          src: [
            '*.php',
            '!node_modules/**'
          ]
        }
      }
    },

    checktextdomain: {
      standard: {
        options: {
          text_domain: [ 'sparkling', 'epsilon-framework' ], //Specify allowed domain(s)
          create_report_file: 'true',
          keywords: [ //List keyword specifications
            '__:1,2d',
            '_e:1,2d',
            '_x:1,2c,3d',
            'esc_html__:1,2d',
            'esc_html_e:1,2d',
            'esc_html_x:1,2c,3d',
            'esc_attr__:1,2d',
            'esc_attr_e:1,2d',
            'esc_attr_x:1,2c,3d',
            '_ex:1,2c,3d',
            '_n:1,2,4d',
            '_nx:1,2,4c,5d',
            '_n_noop:1,2,3d',
            '_nx_noop:1,2,3c,4d'
          ]
        },
        files: [
          {
            src: [
              '**/*.php',
              '!**/node_modules/**',
            ], //all php
            expand: true
          } ]
      }
    },

    clean: {
      init: {
        src: [ 'build/' ]
      },
      build: {
        src: [
          'build/*',
          '!build/<%= pkg.name %>.zip'
        ]
      },
      jsmin: {
          src: [
              'assets/js/*.min.js',
              'assets/js/*.min.js.map'
          ]
      }
    },

    copy: {
      readme: {
        src: 'readme.md',
        dest: 'build/readme.txt'
      },
      build: {
        expand: true,
        src: [
          '**',
          '!node_modules/**',
          '!vendor/**',
          '!build/**',
          '!readme.md',
          '!README.md',
          '!phpcs.ruleset.xml',
          '!Gruntfile.js',
          '!package.json',
          '!composer.json',
          '!composer.lock',
          '!set_tags.sh',
          '!activello.zip',
          '!nbproject/**' ],
        dest: 'build/'
      }
    },

    uglify: {
        options: {
            sourceMap: true,
            compress: true,
        },
        dynamic_mappings: {
            files: [
                {
                    expand: true,     // Enable dynamic expansion.
                    cwd: 'assets/js/',      // Src matches are relative to this path.
                    src: ['**/*.js'], // Actual pattern(s) to match.
                    dest: 'assets/js/',   // Destination path prefix.
                    ext: '.min.js',   // Dest filepaths will have this extension.
                    extDot: 'first'   // Extensions in filenames begin after the first dot
                },
            ],
        },
    },

    compress: {
      build: {
        options: {
          pretty: true,
          archive: '<%= pkg.name %>.zip'
        },
        expand: true,
        cwd: 'build/',
        src: [ '**/*' ],
        dest: '<%= pkg.name %>/'
      }
    },

  } );

  // Check Missing Text Domain Strings
  grunt.registerTask( 'textdomain', [
    'checktextdomain'
  ] );

  // Minify JS
  grunt.registerTask( 'minjs', [
      'clean:jsmin',
      'uglify'
  ]);

  // Build task
  grunt.registerTask( 'build-archive', [
    'minjs',
    'clean:init',
    'copy',
    'compress:build',
    'clean:init'
  ] );
};