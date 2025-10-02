const path = require( 'path' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config' );
const baseDIR = process.cwd();

module.exports = () => {
	return {
		...defaultConfig,
		entry: {
			'admin/dashboard/index': path.resolve(
				baseDIR,
				'src/admin/dashboard/index.js'
			),
		},
		output: {
			filename: '[name].js',
			path: path.resolve( baseDIR, 'build' ),
		},
	};
};
