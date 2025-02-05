module.exports = ( ctx ) => {
	// Plugins for all environments
	const plugins = {
		'postcss-import': {},
		'@tailwindcss/postcss': {},
	};

	// Production-specific plugins
	if ( ctx.env === 'production' ) {
		plugins[ '@tailwindcss/postcss' ] = { optimize: { minify: true } };
	}

	return { plugins };
};
