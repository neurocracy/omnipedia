module.exports = function(grunt) {

  'use strict';

  require('grunt-drupal-delegate')(grunt);

  // ---- Legacy stuff below. ----

  const gruntTargets = {
    aimodules:  'drupal/modules/ambientimpact',
    aithemes:   'drupal/themes/ambientimpact',
    modules:    'drupal/modules/omnipedia',
    themes:     'drupal/themes/omnipedia',
  };

  var config = {shell: {}};

  for (const targetName in gruntTargets) {
    if (!gruntTargets.hasOwnProperty(targetName)) {
      continue;
    }

    // Run the given Grunt command on the specified target.
    //
    // @see https://github.com/sindresorhus/grunt-shell
    config.shell[targetName] = {
      command: gruntCommand => `grunt ${gruntCommand}`,
      options: {
        execOptions: {
          cwd: gruntTargets[targetName]
        }
      }
    };

    // Register each target as a task that can be run with a command. I.e.:
    //
    // grunt target:command
    //
    // @see https://gruntjs.com/api/grunt.task
    grunt.registerTask(targetName, function(gruntCommand) {
      grunt.task.run('shell:' + targetName + ':' + gruntCommand);
    });

  }

  grunt.loadNpmTasks('grunt-shell');

  grunt.initConfig(config);

};
