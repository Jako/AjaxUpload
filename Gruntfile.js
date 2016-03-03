module.exports = function (grunt) {
    var hostPath = '/srv/www/revo.partout.info/html/develop/ajaxupload/';

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
                        'assets/components/ajaxupload/js/ajaxupload.min.js',
                        'assets/components/ajaxupload/css/ajaxupload.min.css'
                    ]
                }
            }
        },
        uglify: {
            ajaxupload: {
                src: [
                    'assets/components/ajaxupload/js/ajaxupload.js',
                    'assets/components/ajaxupload/js/fileuploader.js'
                ],
                dest: 'assets/components/ajaxupload/js/ajaxupload.min.js'
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
                    'assets/components/ajaxupload/css/ajaxupload.css': 'assets/components/ajaxupload/sass/ajaxupload.scss'
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
                    'assets/components/ajaxupload/css/ajaxupload.css'
                ]

            }
        },
        cssmin: {
            ajaxupload: {
                src: [
                    'assets/components/ajaxupload/css/ajaxupload.css'
                ],
                dest: 'assets/components/ajaxupload/css/ajaxupload.min.css'
            }
        },
        sftp: {
            css: {
                files: {
                    "./": [
                        'assets/components/ajaxupload/css/ajaxupload.css',
                        'assets/components/ajaxupload/css/ajaxupload.min.css'
                    ]
                },
                options: {
                    path: hostPath,
                    srcBasePath: 'develop/ajaxupload/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: grunt.file.read("/Users/jako/.ssh/id_dsa"),
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            },
            js: {
                files: {
                    "./": [
                        'assets/components/ajaxupload/js/ajaxupload.js',
                        'assets/components/ajaxupload/js/ajaxupload.min.js'
                    ]
                },
                options: {
                    path: hostPath,
                    srcBasePath: 'develop/ajaxupload/',
                    host: '<%= sshconfig.host %>',
                    username: '<%= sshconfig.username %>',
                    privateKey: grunt.file.read("/Users/jako/.ssh/id_dsa"),
                    passphrase: '<%= sshconfig.passphrase %>',
                    showProgress: true
                }
            }
        },
        watch: {
            scripts: {
                files: [
                    'assets/components/ajaxupload/js/ajaxupload.js'
                ],
                tasks: ['uglify', 'usebanner', 'sftp:js']
            },
            css: {
                files: [
                    'assets/components/ajaxupload/sass/ajaxupload.scss'
                ],
                tasks: ['sass', 'postcss', 'cssmin', 'usebanner', 'sftp:css']
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

    //register the task
    grunt.registerTask('default', ['uglify', 'sass', 'postcss', 'cssmin', 'usebanner', 'sftp']);
};
