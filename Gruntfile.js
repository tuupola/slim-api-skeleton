module.exports = function(grunt) {
    "use strict";

    grunt.initConfig({
        pkg: grunt.file.readJSON("package.json"),
        watch: {
            js: {
                files: ["*.json", "Gruntfile.js"],
                tasks: ["testjs"]
            },
            php: {
                files: ["**/*.php", "!vendor/**/*.php", "!node_modules/**/*.php"],
                tasks: ["testphp"]
            }
        },
        jshint: {
            files: ["*.json", "Gruntfile.js"],
            options: {
                jshintrc: ".jshintrc"
            }
        },
        phpunit: {
            unit: {
                dir: "test"
            },
            options: {
                bin: "vendor/bin/phpunit --bootstrap=vendor/autoload.php --coverage-text --coverage-html ./report",
                //bootstrap: "test/bootstrap.php",
                colors: true,
                testdox: false
            }
        },
        phplint: {
            options: {
                swapPath: "/tmp"
            },
            all: ["**/*.php", "!vendor/**/*.php"]
        },
        phpcs: {
            application: {
                src: ["**/*.php", "!vendor/**/*.php", "!node_modules/**/*.php"]
            },
            options: {
                bin: "vendor/bin/phpcs",
                standard: "PSR2"
            }
        }
    });

    require("load-grunt-tasks")(grunt);

    grunt.registerTask("testjs", ["jshint"]);
    //grunt.registerTask("testphp", ["phplint", "phpcs", "phpunit"]);
    grunt.registerTask("testphp", ["phplint", "phpcs"]);

    grunt.registerTask("default", ["testphp", "testjs"]);

};