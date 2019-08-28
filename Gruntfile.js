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
                src: ['*.css'],
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

    // Only an alias to 'default' task.
    grunt.registerTask( 'dev', [
        'default'
    ]);
};