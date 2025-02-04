/**
 * External dependencies.
 */
import { createRoot } from '@wordpress/element';

/**
 * Internal dependencies.
 */
import { Settings } from './containers/Settings.js';
import '../css/settings.css';

document.addEventListener('DOMContentLoaded', () => {
	const htmlOutput = document.getElementById('wp-beacon-settings');

	if (htmlOutput) {
		createRoot(htmlOutput).render(<Settings wpObject={window.wpBeaconSettings} />);
	}
});
