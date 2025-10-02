module.exports = function ( grunt ) {
	// Project configuration.
	grunt.initConfig( {
		pkg: grunt.file.readJSON( 'package.json' ),
		clean: {
			build: {
				src: [
					'core/dist',
					`bundle/<%= pkg.version %>`,
					`bundle/<%= pkg.name %>-<%= pkg.version %>.zip`,
				],
			},
		},
		copy: {
			main: {
				files: [
					// includes files within path and its sub-directories
					{
						expand: true,
						src: [
							'./**/*',
							'!./{node_modules,node_modules/**/*}',
							'!./{svn_folder,svn_folder/**/*}',
							'!./{bash,bash/**/*}',
							'!./{bundle,bundle/**/*}',
							'!./{org_assets,org_assets/**/*}',
							'!./{core/src,core/src/**/*}',
							'!./{vendor,vendor/**/*}',
							'!./gulpfile.js',
							'!./composer.json',
							'!./composer.lock',
							'!./gulp-dist.js',
							'!./webpack-config.js',
							'!./yarn-error.log',
							'!./yarn.lock',
							'!./README.md',
							'!./package.json',
							'!./Gruntfile.js',
							'!./package-lock.json',
							'!./webpack-config-lite.js',
							'!./phpcs.xml',
						],
						dest: `bundle/<%= pkg.version %>/<%= pkg.name %>`,
					},
				],
			},
		},
		zip: {
			'using-cwd': {
				cwd: 'bundle/<%= pkg.version %>/',
				// Files will zip to 'hello.js' and 'world.js'
				src: [ `bundle/<%= pkg.version %>/**` ],
				dest: 'bundle/<%= pkg.name %>-<%= pkg.version %>.zip',
			},
			// 'bundle/<%= pkg.name %>-<%= pkg.version %>.zip': [`bundle/<%= pkg.version %>/**`]
		},
	} );

	// Load the plugin that provides the "uglify" task.
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-zip' );

	// Default task(s).
	grunt.registerTask( 'default', [ 'copy', 'zip' ] );
	grunt.registerTask( 'cleanAll', [ 'clean' ] );
};
