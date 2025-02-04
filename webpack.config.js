const path = require( 'path' );

const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );

module.exports       = (env, argv) => {
	const production = argv.mode === 'production';

	return {
		...defaultConfig,
		entry: {
			settings: path.resolve( __dirname, 'assets/js/settings.js' ),
		},
		output: {
			path: path.resolve( __dirname, 'dist' ),
		},
	};
};
