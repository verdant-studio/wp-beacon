/**
 * External dependencies.
 */
import React from 'react';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies.
 */
import { Base } from './Base';

export function Airtable() {
	return (
		<Base title={__('Airtable Settings', 'wp-beacon')}>
			<p>{__('Coming soon', 'wp-beacon')}</p>
		</Base>
	);
}
