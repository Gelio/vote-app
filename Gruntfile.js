module.exports = function(grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        autoprefixer: {
            build: {
                src: [
                    'assets/build/style.css'
                ]
            }
        },

        clean: {
            js: ['assets/build/script.min.js'],

            css: ['assets/build/style.css', 'assets/build/style.min.css']
        },

        cssmin: {
            build: {
                files: {
                    'assets/build/style.min.css': 'assets/build/style.css'
                }
            }
        },

        jshint: {
            build: ['Gruntfile.js', 'assets/js/*.js']
        },

        sass: {
            build: {
                files: {
                    'assets/build/style.css': 'assets/scss/main.scss'
                }
            }
        },

        uglify: {
            build: {
                files: {
                    'assets/build/script.min.js': ['assets/js/*.js']
                }
            }
        },

        scsslint: {
            build: [
                'assets/scss/*.scss'
            ],

            options: {
                colorizeOutput: true
            }
        },

        watch: {
            javascript: {
                files: [
                    'assets/js/*.js'
                ],

                tasks: ['compile-js'],
                options: {
                    spawn: false
                }
            },

            scss: {
                files: ['assets/scss/*.scss'],
                tasks: ['compile-scss'],
                options: {
                    spawn: false
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-jshint');
    grunt.loadNpmTasks('grunt-contrib-sass');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-scss-lint');


    grunt.registerTask('compile-js', ['clean:js', 'jshint', 'uglify']);
    grunt.registerTask('compile-scss', ['clean:css', 'scsslint', 'sass', 'autoprefixer', 'cssmin']);
    grunt.registerTask('default', ['compile-js', 'compile-scss']);

};