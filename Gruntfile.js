/* jshint node:true */
module.exports = function( grunt ) {
    'use strict';
    var sass = require( 'node-sass' );

    grunt.initConfig({

        // Setting folder templates.
        dirs: {
            css: 'assets/css',
            fonts: 'assets/fonts',
            images: 'assets/images',
            js: 'assets/js',
            php: 'includes'
        },

        // JavaScript linting with JSHint.
        jshint: {
            options: {
                jshintrc: '.jshintrc'
            },
            all: [
                '<%= dirs.js %>/*.js',
                '!<%= dirs.js %>/*.min.js'
            ]
        },

        // Sass linting with Stylelint.
        stylelint: {
            options: {
                configFile: '.stylelintrc'
            },
            all: [
                '<%= dirs.css %>/*.scss'
            ]
        },

        // Minify .js files.
        uglify: {
            options: {
                ie8: true,
                parse: {
                    strict: false
                },
                output: {
                    comments : /@license|@preserve|^!/
                }
            },
            main: {
                files: [{
                    expand: true,
                    cwd: '<%= dirs.js %>/',
                    src: [
                        '*.js',
                        '!*.min.js'
                    ],
                    dest: '<%= dirs.js %>/',
                    ext: '.min.js'
                }]
            }
        },

        // Compile all .scss files.
        sass: {
            compile: {
                options: {
                    implementation: sass,
                    sourceMap: 'none'
                },
                files: [{
                    expand: true,
                    cwd: '<%= dirs.css %>/',
                    src: ['*.scss'],
                    dest: '<%= dirs.css %>/',
                    ext: '.css'
                }]
            }
        },

        // Minify all .css files.
        cssmin: {
            minify: {
                expand: true,
                cwd: '<%= dirs.css %>/',
                src: ['*.css', '!*.min.css'],
                dest: '<%= dirs.css %>/',
                ext: '.min.css'
            }
        },

        // Concatenate select2.css onto the admin.css files.
        concat: {
            main: {
                files: {}
            }
        },

        // Watch changes for assets.
        watch: {
            css: {
                files: ['<%= dirs.css %>/*.scss'],
                tasks: ['sass', 'postcss', 'cssmin', 'concat']
            },
            js: {
                files: [
                    '<%= dirs.js %>/*js',
                    '!<%= dirs.js %>/*.min.js'
                ],
                tasks: ['uglify']
            }
        },

        // Exec shell commands.
        shell: {
            options: {
                stdout: true,
                stderr: true
            }
        },

        // PHP Code Sniffer.
        phpcs: {
            options: {
                bin: 'vendor/bin/phpcs'
            },
            dist: {
                src:  [
                    '**/*.php', // Include all php files.
                    '!includes/libraries/**',
                    '!node_modules/**',
                    '!tests/cli/**',
                    '!tmp/**',
                    '!vendor/**'
                ]
            }
        },

        // Check textdomain errors.
        checktextdomain: {
            options:{
                text_domain: 'woocommerce-trusted-shops',
                keywords: [
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
            files: {
                src:  [
                    '**/*.php',               // Include all files
                    '!node_modules/**',       // Exclude node_modules/
                    '!tests/**',              // Exclude tests/
                    '!vendor/**',             // Exclude vendor/
                    '!tmp/**',                // Exclude tmp/
                    '!packages/*/vendor/**'   // Exclude packages/*/vendor
                ],
                expand: true
            }
        },

        // Autoprefixer.
        postcss: {
            options: {
                processors: [
                    require( 'autoprefixer' )
                ]
            },
            dist: {
                src: [
                    '<%= dirs.css %>/*.css'
                ]
            }
        }
    });

    // Load NPM tasks to be used here.
    grunt.loadNpmTasks( 'grunt-sass' );
    grunt.loadNpmTasks( 'grunt-shell' );
    grunt.loadNpmTasks( 'grunt-phpcs' );
    grunt.loadNpmTasks( 'grunt-rtlcss' );
    grunt.loadNpmTasks( 'grunt-postcss' );
    grunt.loadNpmTasks( 'grunt-stylelint' );
    grunt.loadNpmTasks( 'grunt-wp-i18n' );
    grunt.loadNpmTasks( 'grunt-checktextdomain' );
    grunt.loadNpmTasks( 'grunt-contrib-jshint' );
    grunt.loadNpmTasks( 'grunt-contrib-uglify' );
    grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
    grunt.loadNpmTasks( 'grunt-contrib-concat' );
    grunt.loadNpmTasks( 'grunt-contrib-copy' );
    grunt.loadNpmTasks( 'grunt-contrib-watch' );
    grunt.loadNpmTasks( 'grunt-contrib-clean' );
    grunt.loadNpmTasks( 'grunt-prompt' );

    // Register tasks.
    grunt.registerTask( 'default', [
        'js',
        'css'
    ]);

    grunt.registerTask( 'js', [
        'uglify:main'
    ]);

    grunt.registerTask( 'css', [
        'sass',
        'postcss',
        'cssmin',
        'concat'
    ]);

    grunt.registerTask( 'assets', [
        'js',
        'css'
    ]);

    grunt.registerTask( 'i18n', [
        'checktextdomain'
    ]);

    // Only an alias to 'default' task.
    grunt.registerTask( 'dev', [
        'default'
    ]);
};