module.exports = function (grunt) {
    // Project configuration.
    grunt.initConfig({
        modx: grunt.file.readJSON('_build/config.json'),
        sshconfig: grunt.file.readJSON('/Users/jako/Documents/MODx/partout.json'),
        banner: '/*!\n' +
        ' * <%= modx.name %> - <%= modx.description %>\n' +
        ' * Version: <%= modx.version %>\n' +
        ' * Build date: <%= grunt.template.today("yyyy-mm-dd") %>\n' +
        ' */\n',
        usebanner: {
            dist: {
                options: {
                    position: 'top',
                    banner: '<%= banner %>'
                },
                files: {
                    src: [
                        'assets/components/ajaxupload/js/web/ajaxupload.min.js',
                        'assets/components/ajaxupload/css/web/ajaxupload.min.css'
                    ]
                }
            }
        },
        uglify: {
            ajaxupload: {
                src: [
                    'source/js/web/ajaxupload.js',
                    'source/js/web/fileuploader.js'
                ],
                dest: 'assets/components/ajaxupload/js/web/ajaxupload.min.js'
            }
        },
        sass: {
            options: {
                outputStyle: 'expanded',
                indentType: 'tab',
                indentWidth: 1,
                sourcemap: false
            },
            dist: {
                files: {
                    'source/css/web/ajaxupload.css': 'source/sass/web/ajaxupload.scss'
                }
            }
        },
        postcss: {
            options: {
                processors: [
                    require('pixrem')(),
                    require('autoprefixer')({
                        browsers: 'last 2 versions, ie >= 8'
                    })
                ]
            },
            dist: {
                src: [
                    'source/css/web/ajaxupload.css'
                ]

            }
        },
        cssmin: {
            ajaxupload: {
                src: [
                    'source/css/web/ajaxupload.css'
                ],
                dest: 'assets/components/ajaxupload/css/web/ajaxupload.min.css'
            }
        },
        sftp: {
            css: {
                files: {
                    "./": [
                        'assets/components/ajaxupload/css/web/ajaxupload.min.css'
                    ]
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/ajaxupload/',
                    srcBasePath: 'develop/ajaxupload/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: '<%= sshconfig.privateKey %>',
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            },
            js: {
                files: {
                    "./": [
                        'assets/components/ajaxupload/js/web/ajaxupload.min.js'
                    ]
                },
                options: {
                    path: '<%= sshconfig.hostpath %>develop/ajaxupload/',
                    srcBasePath: 'develop/ajaxupload/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: '<%= sshconfig.privateKey %>',
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            }
        },
        watch: {
            scripts: {
                files: [
                    'source/**/*.js'
                ],
                tasks: ['uglify', 'usebanner:js', 'sftp:js']
            },
            css: {
                files: [
                    'source/**/*.scss'
                ],
                tasks: ['sass', 'postcss', 'cssmin', 'usebanner:css', 'sftp:css']
            }
        },
        bump: {
            copyright: {
                files: [{
                    src: 'core/components/ajaxupload/model/ajaxupload/ajaxupload.class.php',
                    dest: 'core/components/ajaxupload/model/ajaxupload/ajaxupload.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /Copyright 2013(-\d{4})? by/g,
                        replacement: 'Copyright ' + (new Date().getFullYear() > 2013 ? '2013-' : '') + new Date().getFullYear() + ' by'
                    }]
                }
            },
            version: {
                files: [{
                    src: 'core/components/ajaxupload/model/ajaxupload/ajaxupload.class.php',
                    dest: 'core/components/ajaxupload/model/ajaxupload/ajaxupload.class.php'
                }],
                options: {
                    replacements: [{
                        pattern: /version = '\d+.\d+.\d+[-a-z0-9]*'/ig,
                        replacement: 'version = \'' + '<%= modx.version %>' + '\''
                    }]
                }
            }
        }
    });

    //load the packages
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-banner');
    grunt.loadNpmTasks('grunt-ssh');
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-string-replace');
    grunt.renameTask('string-replace', 'bump');

    //register the task
    grunt.registerTask('default', ['bump', 'uglify', 'sass', 'postcss', 'cssmin', 'usebanner', 'sftp']);
};
